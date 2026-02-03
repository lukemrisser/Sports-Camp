<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Camp;
use App\Models\Order;
use App\Models\ParentModel;
use Stripe\Stripe;
use Stripe\Refund;
use Stripe\Exception\CardException;
use Stripe\Exception\InvalidRequestException;

class RefundController extends Controller
{
    public function __construct()
    {
        // Set Stripe secret key
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Show the refund page with camp selection
     */
    public function index()
    {
        $user = Auth::user();
        $coach = $user->coach;

        if (!$coach) {
            return redirect()->route('coach-dashboard')
                ->with('error', 'Coach profile not found.');
        }

        // Get camps associated with this coach's sport
        $camps = Camp::where('Sport_ID', $coach->Sport_ID)
            ->orderBy('Start_Date', 'desc')
            ->get();

        return view('coach.refunds', compact('camps'));
    }

    /**
     * Search for orders by parent information
     */
    public function searchOrders(Request $request)
    {
        $request->validate([
            'camp_id' => 'required|exists:Camps,Camp_ID',
            'search_term' => 'required|string|min:2',
        ]);

        $campId = $request->camp_id;
        $searchTerm = $request->search_term;

        // Search for parents by email, first name, or last name
        $parents = ParentModel::where(function ($query) use ($searchTerm) {
            $query->where('Email', 'like', "%{$searchTerm}%")
                ->orWhere('Parent_FirstName', 'like', "%{$searchTerm}%")
                ->orWhere('Parent_LastName', 'like', "%{$searchTerm}%");
        })->get();

        if ($parents->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No parents found matching your search.'
            ]);
        }

        // Get orders for these parents in the selected camp
        $parentIds = $parents->pluck('Parent_ID');
        $orders = Order::whereIn('Parent_ID', $parentIds)
            ->where('Camp_ID', $campId)
            ->with(['parent', 'player', 'camp'])
            ->get()
            ->map(function ($order) {
                $refundAmount = $order->Refund_Amount ?? 0;
                $amountPaid = $order->Item_Amount_Paid ?? 0;
                $refundableAmount = $amountPaid - $refundAmount;

                return [
                    'order_id' => $order->Order_ID,
                    'parent_name' => $order->parent ? $order->parent->Parent_FirstName . ' ' . $order->parent->Parent_LastName : 'N/A',
                    'parent_email' => $order->parent->Email ?? 'N/A',
                    'player_name' => $order->player ? $order->player->Camper_FirstName . ' ' . $order->player->Camper_LastName : 'N/A',
                    'camp_name' => $order->camp->Camp_Name ?? 'N/A',
                    'total_amount' => number_format($order->Item_Amount, 2),
                    'amount_paid' => number_format($amountPaid, 2),
                    'refund_amount' => number_format($refundAmount, 2),
                    'refundable_amount' => number_format($refundableAmount, 2),
                    'payment_intent_id' => $order->Payment_Intent_ID,
                    'can_refund' => $refundableAmount > 0 && $order->Payment_Intent_ID,
                ];
            });

        if ($orders->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No orders found for this parent in the selected camp.'
            ]);
        }

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    /**
     * Process a refund
     */
    public function processRefund(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:Orders,Order_ID',
            'refund_amount' => 'required|numeric|min:0.01',
            'refund_reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $order = Order::with(['parent', 'player', 'camp'])->findOrFail($request->order_id);

            // Verify coach has access to this camp's sport
            $user = Auth::user();
            $coach = $user->coach;
            if (!$coach || $order->camp->Sport_ID !== $coach->Sport_ID) {
                return response()->json([
                    'success' => false,
                    'error' => 'You do not have permission to refund orders for this camp.'
                ]);
            }

            // Check if payment intent exists
            if (!$order->Payment_Intent_ID) {
                return response()->json([
                    'success' => false,
                    'error' => 'No payment intent found for this order. Cannot process refund.'
                ]);
            }

            // Calculate refundable amount
            $amountPaid = $order->Item_Amount_Paid ?? 0;
            $alreadyRefunded = $order->Refund_Amount ?? 0;
            $refundableAmount = $amountPaid - $alreadyRefunded;

            if ($request->refund_amount > $refundableAmount) {
                return response()->json([
                    'success' => false,
                    'error' => 'Refund amount exceeds the refundable amount of $' . number_format($refundableAmount, 2)
                ]);
            }

            // Process refund through Stripe using Payment Intent ID
            $refundAmountCents = (int) ($request->refund_amount * 100);
            $refund = Refund::create([
                'payment_intent' => $order->Payment_Intent_ID,
                'amount' => $refundAmountCents,
                'reason' => 'requested_by_customer',
                'metadata' => [
                    'order_id' => $order->Order_ID,
                    'player_name' => $order->player ? $order->player->Camper_FirstName . ' ' . $order->player->Camper_LastName : '',
                    'camp_name' => $order->camp->Camp_Name ?? '',
                    'refund_reason' => $request->refund_reason ?? 'No reason provided',
                    'refunded_by' => $coach->Coach_FirstName . ' ' . $coach->Coach_LastName,
                ],
            ]);

            // Update order with refund amount
            $newRefundTotal = $alreadyRefunded + $request->refund_amount;
            $order->update([
                'Refund_Amount' => $newRefundTotal,
            ]);

            DB::commit();

            Log::info("Refund processed for Order {$order->Order_ID}: $" . number_format($request->refund_amount, 2) . " (Refund ID: {$refund->id})");

            return response()->json([
                'success' => true,
                'message' => 'Refund of $' . number_format($request->refund_amount, 2) . ' processed successfully.',
                'refund_id' => $refund->id,
                'new_refund_total' => number_format($newRefundTotal, 2),
                'remaining_refundable' => number_format($refundableAmount - $request->refund_amount, 2),
            ]);

        } catch (CardException $e) {
            DB::rollBack();
            Log::error('Stripe Card Exception during refund: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getError()->message
            ]);

        } catch (InvalidRequestException $e) {
            DB::rollBack();
            Log::error('Stripe Invalid Request during refund: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Invalid refund request. ' . $e->getMessage()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund processing error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'An error occurred processing the refund. Please try again.'
            ]);
        }
    }
}
