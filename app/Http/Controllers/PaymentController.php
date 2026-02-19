<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\Player;
use App\Models\Camp;
use App\Models\CampDiscount;
use App\Models\PromoCode;
use App\Models\Order;
use App\Models\ExtraFee;
use App\Models\OrderExtraFee;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\CardException;
use Stripe\Exception\InvalidRequestException;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set Stripe secret key
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Show payment form for a registered player
     */
    public function show($playerId, $campId, $discountAmount, $addOns = '')
    {
        $player = Player::with('parent')->where('Player_ID', $playerId)->first();
        $camp = Camp::find($campId);

        // Parse selected add-ons FIRST before creating order
        $selectedAddOns = [];
        $addOnsTotal = 0;
        if ($addOns) {
            $addOnIds = array_filter(explode(',', $addOns));
            if (!empty($addOnIds)) {
                $selectedAddOns = ExtraFee::whereIn('Fee_ID', $addOnIds)->get();
                $addOnsTotal = $selectedAddOns->sum('Fee_Amount');
            }
        }

        // Check if there's already a paid order for this player
        $existingOrder = $this->findOrCreateOrder($player, $campId, $discountAmount, $addOnsTotal);
        if ($existingOrder && $existingOrder->isFullyPaid()) {
            return redirect()->route('payment.success')
                ->with('success', 'Payment has already been processed for this registration.');
        }

        // Build registration data from database instead of session
        $registration = [
            'camper_name' => $player->Camper_FirstName . ' ' . $player->Camper_LastName,
            'division_name' => $player->Division_Name,
            'camp_id' => $campId,
            'parent_name' => $player->parent ? $player->parent->Parent_FirstName . ' ' . $player->parent->Parent_LastName : '',
            'email' => $player->parent->Email ?? '',
            'address' => $player->parent->Address ?? '',
            'city' => $player->parent->City ?? '',
            'state' => $player->parent->State ?? '',
            'postal_code' => $player->parent->Postal_Code ?? ''
        ];

        // Get the camp name for display
        $campName = $camp->Camp_Name;

        // Calculate amount (in cents for Stripe)
        $amount = $this->calculateRegistrationAmount($player, $campId, $discountAmount, $addOnsTotal);

        // Get or create order for tracking
        $order = $existingOrder ?: $this->findOrCreateOrder($player, $campId, $discountAmount, $addOnsTotal);

        return view('payment', [
            'playerId' => $playerId,
            'amount' => $amount,
            'registration' => $registration,
            'player' => $player,
            'campName' => $campName,
            'order' => $order,
            'campId' => $campId,
            'discountAmount' => $discountAmount,
            'selectedAddOns' => $selectedAddOns,
            'addOnsTotal' => $addOnsTotal,
            'addOnsString' => $addOns
        ]);
    }

    /**
     * Process the payment
     */
    public function process(Request $request)
    {
        $request->validate([
            'player_id' => 'required|exists:Players,Player_ID',
            'camp_id' => 'required|exists:Camps,Camp_ID',
            'amount' => 'required|numeric|min:1',
            'promo_code' => 'nullable|string',
            'payment_method_id' => 'required|string',
            'cardholder_name' => 'required|string|max:255',
            'receipt_email' => 'required|email',
            'billing_address' => 'required|string|max:255',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'required|string|max:100',
            'billing_zip' => 'required|string|max:20',
            'selected_add_ons' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $player = Player::where('Player_ID', $request->player_id)->first();

            if (!$player) {
                return response()->json([
                    'success' => false,
                    'error' => 'Player registration not found.'
                ]);
            }

            // Calculate add-ons total
            $addOnsTotal = $this->calculateAddOnsTotal($request->input('selected_add_ons', ''));

            // Check if already paid using Order model
            $order = $this->findOrCreateOrder($player, $request->camp_id, 0, $addOnsTotal);
            if ($order && $order->isFullyPaid()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Payment has already been processed for this registration.'
                ]);
            }

            // Create payment intent with final amount
            $paymentIntent = $this->createPaymentIntent($player, $request);

            // Handle the payment result
            return $this->handlePaymentIntentResponse($paymentIntent, $player, $order, $request, $addOnsTotal);
        } catch (CardException $e) {
            DB::rollBack();
            Log::error('Stripe Card Exception: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getError()->message
            ]);
        } catch (InvalidRequestException $e) {
            DB::rollBack();
            Log::error('Stripe Invalid Request: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Invalid payment request. Please check your information and try again.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'An error occurred processing your payment. Please try again.'
            ]);
        }
    }

    /**
     * Calculate total add-ons amount from selected add-ons string
     */
    private function calculateAddOnsTotal($addOnsString): float
    {
        $addOnsTotal = 0;
        if ($addOnsString) {
            $addOnIds = array_filter(explode(',', $addOnsString));
            if (!empty($addOnIds)) {
                $addOnsTotal = ExtraFee::whereIn('Fee_ID', $addOnIds)->sum('Fee_Amount');
            }
        }
        return $addOnsTotal;
    }

    /**
     * Create a Stripe payment intent for the registration
     * Ignore the internal limitation warning for method complexity
     */
    private function createPaymentIntent($player, Request $request)
    {
        return PaymentIntent::create([
            'amount' => $request->amount,
            'currency' => 'usd',
            'payment_method' => $request->payment_method_id,
            'confirmation_method' => 'manual',
            'confirm' => true,
            'description' => "Sports Camp Registration - {$player->Camper_FirstName} {$player->Camper_LastName}",
            'metadata' => [
                'player_id' => $player->Player_ID,
                'camp_id' => $request->camp_id,
                'player_name' => $player->Camper_FirstName . ' ' . $player->Camper_LastName,
                'division_name' => $player->Division_Name,
            ],
            'receipt_email' => $request->receipt_email,
            'return_url' => route('payment.success'),
        ]);
    }

    /**
     * Handle payment intent response based on status
     */
    private function handlePaymentIntentResponse($paymentIntent, $player, $order, Request $request, $addOnsTotal)
    {
        if ($paymentIntent->status === 'requires_action') {
            // 3D Secure authentication required
            return response()->json([
                'success' => false,
                'requires_action' => true,
                'payment_intent' => [
                    'id' => $paymentIntent->id,
                    'client_secret' => $paymentIntent->client_secret,
                ]
            ]);
        } else if ($paymentIntent->status === 'succeeded') {
            // Payment successful
            $this->handlePaymentSucceeded($player, $paymentIntent, $request, $order);

            DB::commit();

            Log::info("Payment successful for player {$player->Player_ID}: {$paymentIntent->id}");

            return response()->json([
                'success' => true,
                'redirect_url' => route('payment.success')
            ]);
        } else {
            // Payment failed
            Log::warning("Payment failed for player {$player->Player_ID}. Status: {$paymentIntent->status}");

            return response()->json([
                'success' => false,
                'error' => 'Payment was not successful. Please try again.'
            ]);
        }
    }

    /**
     * Handle successful payment: update status and save add-ons
     */
    private function handlePaymentSucceeded($player, $paymentIntent, Request $request, $order)
    {
        // Update player payment status
        $this->updatePlayerPaymentStatus($player, $paymentIntent, $request);

        // Save selected add-ons to OrderExtraFee
        $addOnsString = $request->input('selected_add_ons', '');
        if ($addOnsString && $order) {
            $addOnIds = array_filter(explode(',', $addOnsString));
            if (!empty($addOnIds)) {
                foreach ($addOnIds as $feeId) {
                    OrderExtraFee::create([
                        'Order_ID' => $order->Order_ID,
                        'Fee_ID' => $feeId,
                    ]);
                }
            }
        }

        // Send payment confirmation email
        if ($order) {
            $orderWithRelations = Order::with(['player.parent', 'camp'])->find($order->Order_ID);
            if ($orderWithRelations) {
                $this->sendConfirmationEmailForOrder($orderWithRelations);
            }
        }
    }

    /**
     * Send payment confirmation email for a given order
     */
    private function sendConfirmationEmailForOrder(Order $order): void
    {
        $parentEmail = $order->player->parent->Email ?? null;

        if (!$parentEmail) {
            Log::warning("No parent email found for Order {$order->Order_ID}, skipping confirmation email.");
            return;
        }

        try {
            Mail::send('emails.payment-confirm-email', [
                'parentName' => $order->player->parent->Parent_FirstName . ' ' . $order->player->parent->Parent_LastName,
                'playerName' => $order->player->Camper_FirstName . ' ' . $order->player->Camper_LastName,
                'campName' => $order->camp->Camp_Name,
                'amount' => number_format((float) $order->Item_Amount, 2),
                'orderDate' => \Carbon\Carbon::parse($order->Order_Date)->format('m/d/Y'),
                'orderId' => $order->Order_ID,
            ], function ($mail) use ($parentEmail) {
                $mail->to($parentEmail)
                    ->subject('Payment Confirmation - ' . config('app.name'))
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            Log::info("Payment confirmation email sent for Order {$order->Order_ID} to {$parentEmail}");
        } catch (\Exception $e) {
            Log::error("Failed to send payment confirmation email for Order {$order->Order_ID}: " . $e->getMessage());
        }
    }

    public function success()
    {

        return view('payment-success');
    }
    public function cancelled()
    {
        return view('payment-cancelled');
    }

    /**
     * Stripe webhook handler
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload in Stripe webhook');
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid signature in Stripe webhook');
            return response('Invalid signature', 400);
        }

        // Handle the event
        switch ($event['type']) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event['data']['object'];
                $this->handleSuccessfulPayment($paymentIntent);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event['data']['object'];
                $this->handleFailedPayment($paymentIntent);
                break;

            default:
                Log::info('Received unknown Stripe webhook event type: ' . $event['type']);
        }

        return response('Webhook handled', 200);
    }

    /**
     * Calculate registration amount based on player and camp
     */
    private function calculateRegistrationAmount($player, $campId, $discount = 0, $addOnsTotal = 0)
    {
        /** @var Camp|null $camp */
        $camp = Camp::find($campId);

        if (!$camp) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                redirect()->route('home')->with('error', 'Camp not found. Please start over.')
            );
        }

        $addOnsAmount = $addOnsTotal * 100; // Convert add-ons to cents

        $baseAmount = $camp->Price * 100;
        $bestDiscount = $camp->getBestDiscount();
        $earlyDiscount = $bestDiscount ? (float) $bestDiscount->Discount_Amount : 0;
        $promoDiscount = (float) $discount;

        $discountedAmount = ($baseAmount + $addOnsAmount) - (($earlyDiscount + $promoDiscount) * 100);
        return max(0, $discountedAmount);
    }

    /**
     * Find existing order or create a new one for the player
     */
    private function findOrCreateOrder(Player $player, $campId, $discount = 0, $addOnsTotal = 0): ?Order
    {
        $order = Order::where('Player_ID', $player->Player_ID)
            ->where('Camp_ID', $campId)
            ->first();

        if (!$order) {
            // Create new order for this specific player
            $amount = $this->calculateRegistrationAmount($player, $campId, $discount, $addOnsTotal) / 100; // Convert cents to dollars

            $order = Order::create([
                'Player_ID' => $player->Player_ID,
                'Parent_ID' => $player->Parent_ID,
                'Camp_ID' => $campId,
                'Order_Date' => now()->toDateString(),
                'Item_Amount' => $amount,
                'Item_Amount_Paid' => 0.00,
            ]);
        }

        return $order;
    }

    /**
     * Update player payment status after successful payment
     */
    private function updatePlayerPaymentStatus($player, $paymentIntent, $request)
    {
        // Calculate add-ons total from selected add-ons
        $addOnsTotal = 0;
        $addOnsString = $request->input('selected_add_ons', '');
        if ($addOnsString) {
            $addOnIds = array_filter(explode(',', $addOnsString));
            if (!empty($addOnIds)) {
                $addOnsTotal = ExtraFee::whereIn('Fee_ID', $addOnIds)->sum('Fee_Amount');
            }
        }

        // Update the Order record with payment
        $order = $this->findOrCreateOrder($player, $request->camp_id, 0, $addOnsTotal);

        if ($order) {
            // Add payment to the order
            $paymentAmountDollars = $paymentIntent->amount / 100; // Convert cents to dollars
            $order->addPayment($paymentAmountDollars);

            // Store payment intent ID and charge ID
            $chargeId = !empty($paymentIntent->charges->data) ? $paymentIntent->charges->data[0]->id : null;
            $order->update([
                'Payment_Intent_ID' => $paymentIntent->id,
                'Charge_ID' => $chargeId,
            ]);

            Log::info("Updated order {$order->Order_ID} with payment amount: $" . number_format($paymentAmountDollars, 2));
        }

        // Keep session data for backwards compatibility and additional payment metadata
        session([
            "payment_status_player_{$player->Player_ID}" => 'paid',
            "payment_data_player_{$player->Player_ID}" => [
                'payment_method' => 'stripe',
                'payment_id' => $paymentIntent->id,
                'payment_amount' => $paymentIntent->amount,
                'payment_date' => now()->toDateTimeString(),
                'billing_address' => $request->billing_address,
                'billing_city' => $request->billing_city,
                'billing_state' => $request->billing_state,
                'billing_zip' => $request->billing_zip,
                'receipt_email' => $request->receipt_email,
                'order_id' => $order ? $order->Order_ID : null,
            ]
        ]);
    }

    /**
     * Handle successful payment from webhook
     */
    private function handleSuccessfulPayment($paymentIntent)
    {
        if (isset($paymentIntent['metadata']['player_id']) && isset($paymentIntent['metadata']['camp_id'])) {
            $playerId = $paymentIntent['metadata']['player_id'];
            $campId = $paymentIntent['metadata']['camp_id'];
            $player = Player::where('Player_ID', $playerId)->first();

            if ($player) {
                // Note: Webhook doesn't have add-ons info, so we find existing order only
                $order = Order::where('Player_ID', $playerId)
                    ->where('Camp_ID', $campId)
                    ->first();

                if ($order && !$order->isFullyPaid()) {
                    // Add payment to the order
                    $paymentAmountDollars = $paymentIntent['amount'] / 100;
                    $order->addPayment($paymentAmountDollars);

                    // Store payment intent ID and charge ID
                    $chargeId = !empty($paymentIntent['charges']['data']) ? $paymentIntent['charges']['data'][0]['id'] : null;
                    $order->update([
                        'Payment_Intent_ID' => $paymentIntent['id'],
                        'Charge_ID' => $chargeId,
                    ]);

                    Log::info("Payment confirmed via webhook for player {$playerId}, camp {$campId}: {$paymentIntent['id']}, Order {$order->Order_ID} updated");
                }
            }
        } else {
            Log::warning("Missing player_id or camp_id in webhook metadata");
        }
    }

    /**
     * Handle failed payment from webhook
     */
    private function handleFailedPayment($paymentIntent)
    {
        if (isset($paymentIntent['metadata']['player_id'])) {
            $playerId = $paymentIntent['metadata']['player_id'];
            $player = Player::where('Player_ID', $playerId)->first();

            if ($player) {
                // Order remains with Item_Amount_Paid = 0 for failed payments
                // The order was already created in findOrCreateOrder, so no changes needed
                session([
                    "payment_status_player_{$playerId}" => 'failed',
                    "payment_id_player_{$playerId}" => $paymentIntent['id'],
                ]);

                Log::warning("Payment failed via webhook for player {$playerId}: {$paymentIntent['id']}");
            }
        }
    }

    /**
     * Validate promo code and return discount amount
     */
    public function validatePromoCode(Request $request)
    {
        $request->validate([
            'camp_id' => 'required|exists:Camps,Camp_ID',
            'code' => 'required|string',
        ]);

        $campId = $request->input('camp_id');
        $code = $request->input('code');

        try {
            // Find the promo code
            $promoCode = PromoCode::findValidPromoCodeForCamp($campId, $code);

            if (!$promoCode) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Promo code not found for this camp.'
                ]);
            }

            // Check if the promo code is valid (not expired)
            if (!$promoCode->isValid()) {
                return response()->json([
                    'valid' => false,
                    'message' => 'This promo code has expired.'
                ]);
            }

            return response()->json([
                'valid' => true,
                'discount_amount' => $promoCode->Discount_Amount
            ]);
        } catch (\Exception $e) {
            Log::error('Promo code validation error: ' . $e->getMessage());
            return response()->json([
                'valid' => false,
                'message' => 'An error occurred while validating the promo code.'
            ], 500);
        }
    }

    /**
     * Send payment confirmation email to parent/player
     */
    public function sendPaymentConfirmationEmail(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:Orders,Order_ID',
        ]);

        // Debug: log entry so we can confirm this method is reached
        Log::debug('sendPaymentConfirmationEmail entered', [
            'order_id' => $request->input('order_id'),
            'user_id' => optional(Auth::user())->id,
        ]);

        try {
            $order = Order::with(['player.parent', 'camp'])->find($request->order_id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found.'
                ], 404);
            }

            $parentEmail = $order->player->parent->Email ?? null;

            if (!$parentEmail) {
                return response()->json([
                    'success' => false,
                    'message' => 'No email address found for this parent.'
                ], 400);
            }

            $this->sendConfirmationEmailForOrder($order);

            return response()->json([
                'success' => true,
                'message' => 'Payment confirmation email sent successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Payment confirmation email error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the email.'
            ], 500);
        }
    }
}
