<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Player;
use App\Models\Camp;
use App\Models\CampDiscount;
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
    public function show($playerId)
    {
        try {
            Log::info("PaymentController show method called with playerId: $playerId");
            
            // Get player and registration details using Player_ID
            $player = Player::with('parent')->where('Player_ID', $playerId)->first();
            Log::info("Player lookup result: " . ($player ? "Found player {$player->Camper_FirstName} {$player->Camper_LastName}" : "No player found"));
            
            if (!$player) {
                Log::error("Player not found with Player_ID: $playerId");
                return redirect()->route('home')
                    ->with('error', 'Player registration not found.');
            }
            
            Log::info("Player parent relationship: " . ($player->parent ? "Parent found: {$player->parent->Parent_FirstName}" : "No parent found"));
            
            // For now, we'll track payment status in session/cache instead of database
            $paymentStatus = session("payment_status_player_{$playerId}", 'pending');
            if ($paymentStatus === 'paid') {
                return redirect()->route('payment.success')
                    ->with('success', 'Payment has already been processed for this registration.');
            }

            // Get registration data from session or reconstruct from player
            $sessionData = session('registration_data');
            Log::info("Session registration_data: " . json_encode($sessionData));
            
            $registration = session('registration_data', [
                'camper_name' => $player->Camper_FirstName . ' ' . $player->Camper_LastName,
                'division_name' => $player->Division_Name ?? 'Camp Registration',
                'parent_name' => $player->parent ? $player->parent->Parent_FirstName . ' ' . $player->parent->Parent_LastName : '',
                'email' => $player->parent->Email ?? '',
                'address' => $player->parent->Address ?? '',
                'city' => $player->parent->City ?? '',
                'state' => $player->parent->State ?? '',
                'postal_code' => $player->parent->Postal_Code ?? ''
            ]);

            // Get the actual camp name from Camp_ID for display
            $campName = 'Camp Registration'; // Default fallback
            if (isset($registration['camp_id'])) {
                $camp = Camp::find($registration['camp_id']);
                if ($camp) {
                    $campName = $camp->Camp_Name;
                }
            }

            // Calculate amount (in cents for Stripe)
            $amount = $this->calculateRegistrationAmount($player);

            return view('payment', [
                'playerId' => $playerId,
                'amount' => $amount,
                'registration' => $registration,
                'player' => $player,
                'campName' => $campName
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
            
            // Check if already paid
            $paymentStatus = session("payment_status_player_{$player->Player_ID}", 'pending');
            if ($paymentStatus === 'paid') {
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

    /**
     * Payment success page
     */
    public function success()
    {
        return view('payment-success');
    }

    /**
     * Payment cancelled page
     */
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
    private function calculateRegistrationAmount($player)
    {
        Log::info("Calculating registration amount for player {$player->Player_ID}");
        
        // Default registration fee in cents ($150.00)
        $baseAmount = 15000;

        // Try to get Camp_ID from session data first
        $registrationData = session('registration_data');
        $camp = null;
        
        Log::info("Registration data for calculation: " . json_encode($registrationData));
        
        if ($registrationData && isset($registrationData['camp_id'])) {
            // Use Camp_ID from session (preferred method)
            $camp = Camp::find($registrationData['camp_id']);
            Log::info("Looking up camp by ID from session: {$registrationData['camp_id']}" . ($camp ? " - Found: {$camp->Camp_Name}" : " - Not found"));
        } else {
            Log::warning("No camp_id found in session data for player {$player->Player_ID}");
        }
        
        if ($camp) {
            // Use camp's price if available, otherwise use default
            if (isset($camp->Price)) {
                $baseAmount = $camp->Price * 100; // Convert dollars to cents
                Log::info("Using camp price: $" . $camp->Price . " -> " . $baseAmount . " cents");
            } else {
                Log::info("No price set for camp {$camp->Camp_Name}, using default: " . $baseAmount . " cents");
            }
            
            // Apply the best available discount for this camp
            $discountedAmount = $camp->getDiscountedPrice($baseAmount);
            
            Log::info("Camp pricing for {$camp->Camp_Name} (ID: {$camp->Camp_ID}): Base: $" . number_format($baseAmount/100, 2) . 
                     ", Discounted: $" . number_format($discountedAmount/100, 2));
            
            return $discountedAmount;
        }

        Log::warning("No camp found for player {$player->Player_ID}, using default amount: $" . number_format($baseAmount/100, 2));
        return $baseAmount;
    }

    /**
     * Update player payment status after successful payment
     */
    private function updatePlayerPaymentStatus($player, $paymentIntent, $request)
    {
        // Store payment info in session for now (until we add payment table)
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
            ]
        ]);

        // You might also want to send confirmation emails here
        // Mail::to($request->receipt_email)->send(new RegistrationConfirmation($player));
    }

    /**
     * Handle successful payment from webhook
     */
    private function handleSuccessfulPayment($paymentIntent)
    {
        if (isset($paymentIntent['metadata']['player_id'])) {
            $playerId = $paymentIntent['metadata']['player_id'];
            $player = Player::where('Player_ID', $playerId)->first();
            
            $paymentStatus = session("payment_status_player_{$playerId}", 'pending');
            if ($player && $paymentStatus !== 'paid') {
                session([
                    "payment_status_player_{$playerId}" => 'paid',
                    "payment_webhook_confirmed_{$playerId}" => true
                ]);

                Log::info("Payment confirmed via webhook for player {$playerId}: {$paymentIntent['id']}");
            }
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
                session([
                    "payment_status_player_{$playerId}" => 'failed',
                    "payment_id_player_{$playerId}" => $paymentIntent['id'],
                ]);

                Log::warning("Payment failed via webhook for player {$playerId}: {$paymentIntent['id']}");
            }
        }
    }
}