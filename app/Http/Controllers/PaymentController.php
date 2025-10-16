<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Player;
use App\Models\Camp;
use App\Models\CampDiscount;
use App\Models\Order;
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
    public function show($playerId, $campId)
    {
        try {
            
            $player = Player::with('parent')->where('Player_ID', $playerId)->first();
            
            if (!$player) {
                return redirect()->route('home')
                    ->with('error', 'Player registration not found.');
            }

            // Get camp information from URL parameter
            $camp = Camp::find($campId);
            if (!$camp) {
                return redirect()->route('home')
                    ->with('error', 'Camp information not found.');
            }
                    
            // Check if there's already a paid order for this player
            $existingOrder = $this->findOrCreateOrder($player, $campId);
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
            $amount = $this->calculateRegistrationAmount($player, $campId);
            
            // Get or create order for tracking
            $order = $existingOrder ?: $this->findOrCreateOrder($player, $campId);

            return view('payment', [
                'playerId' => $playerId,
                'amount' => $amount,
                'registration' => $registration,
                'player' => $player,
                'campName' => $campName,
                'order' => $order
            ]);

        } catch (\Exception $e) {
            Log::error('Payment form error: ' . $e->getMessage());
            return redirect()->route('home')
                ->with('error', 'Unable to load payment form. Please try again.');
        }
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
            'payment_method_id' => 'required|string',
            'cardholder_name' => 'required|string|max:255',
            'receipt_email' => 'required|email',
            'billing_address' => 'required|string|max:255',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'required|string|max:100',
            'billing_zip' => 'required|string|max:20',
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
            
            // Check if already paid using Order model
            $order = $this->findOrCreateOrder($player, $request->camp_id);
            if ($order && $order->isFullyPaid()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Payment has already been processed for this registration.'
                ]);
            }

            // Create payment intent
            $paymentIntent = PaymentIntent::create([
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

            // Handle the payment result
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
                $this->updatePlayerPaymentStatus($player, $paymentIntent, $request);
                
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
    private function calculateRegistrationAmount($player, $campId)
    {
        $camp = Camp::find($campId);

        if (!$camp) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                redirect()->route('home')->with('error', 'Camp not found. Please start over.')
            );
        }

        $baseAmount = $camp->Price * 100; // Convert dollars to cents
        $discountedAmount = $camp->getDiscountedPrice($baseAmount);

        return $discountedAmount;
    }

    /**
     * Find existing order or create a new one for the player
     */
    private function findOrCreateOrder(Player $player, $campId): ?Order
    {
        $order = Order::where('Player_ID', $player->Player_ID)
                     ->where('Camp_ID', $campId)
                     ->first();

        if (!$order) {
            // Create new order for this specific player
            $amount = $this->calculateRegistrationAmount($player, $campId) / 100; // Convert cents to dollars
            
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
     * Get order details for a player
     */
    public function getOrderDetails($playerId, $campId): ?Order
    {
        $player = Player::where('Player_ID', $playerId)->first();
        if (!$player) {
            return null;
        }

        return $this->findOrCreateOrder($player, $campId);
    }

    /**
     * Update player payment status after successful payment
     */
    private function updatePlayerPaymentStatus($player, $paymentIntent, $request)
    {
        // Update the Order record with payment
        $order = $this->findOrCreateOrder($player, $request->camp_id);
        
        if ($order) {
            // Add payment to the order
            $paymentAmountDollars = $paymentIntent->amount / 100; // Convert cents to dollars
            $order->addPayment($paymentAmountDollars);

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
                $order = $this->findOrCreateOrder($player, $campId);
                
                if ($order && !$order->isFullyPaid()) {
                    // Add payment to the order
                    $paymentAmountDollars = $paymentIntent['amount'] / 100;
                    $order->addPayment($paymentAmountDollars);

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
}