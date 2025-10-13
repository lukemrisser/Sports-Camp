<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Player;
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
            // Get player and registration details
            $player = Player::with('parent')->findOrFail($playerId);
            
            // For now, we'll track payment status in session/cache instead of database
            $paymentStatus = session("payment_status_player_{$playerId}", 'pending');
            if ($paymentStatus === 'paid') {
                return redirect()->route('payment.success')
                    ->with('success', 'Payment has already been processed for this registration.');
            }

            // Get registration data from session or reconstruct from player
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

            // Calculate amount (in cents for Stripe)
            $amount = $this->calculateRegistrationAmount($player);

            return view('payment', [
                'playerId' => $playerId,
                'amount' => $amount,
                'registration' => $registration,
                'player' => $player
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
            'player_id' => 'required|exists:players,id',
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
            $player = Player::findOrFail($request->player_id);
            
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
        // Default registration fee in cents ($150.00)
        $baseAmount = 15000;

        // You can add logic here to vary pricing based on:
        // - Camp type
        // - Early bird discounts
        // - Multi-child discounts
        // - Special promotions

        if ($player->camp && isset($player->camp->price)) {
            $baseAmount = $player->camp->price * 100; // Convert to cents
        }

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
            $player = Player::find($playerId);
            
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
            $player = Player::find($playerId);
            
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