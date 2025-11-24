<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Coach;
use App\Models\Camp;
use App\Models\Player;
use App\Models\Sport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FinancesExport;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function finances(Request $request)
    {
        $sportId = $request->get('sport_id');
        $campId = $request->get('camp_id');
        $paymentStatus = $request->get('payment_status');

        // Get all sports and camps for the filter dropdowns
        $sports = Sport::with('camps')->get();

        // Filter camps based on selected sport
        if ($sportId) {
            $camps = Camp::with('sport')->where('Sport_ID', $sportId)->orderBy('Camp_Name')->get();
        } else {
            $camps = Camp::with('sport')->whereNotNull('Sport_ID')->orderBy('Camp_Name')->get();
        }

        // Start with all orders
        $query = \App\Models\Order::with(['player', 'parent', 'camp.sport']);

        // Apply filters
        if ($sportId) {
            $query->whereHas('camp', function ($q) use ($sportId) {
                $q->where('Sport_ID', $sportId);
            });
        }

        if ($campId) {
            $query->where('Camp_ID', $campId);
        }

        // Apply payment status filter at query level for better pagination
        if ($paymentStatus) {
            if ($paymentStatus === 'paid') {
                $query->whereRaw('Item_Amount_Paid >= Item_Amount AND Item_Amount > 0');
            } elseif ($paymentStatus === 'not_paid') {
                $query->whereRaw('Item_Amount_Paid < Item_Amount OR Item_Amount_Paid IS NULL');
            } elseif ($paymentStatus === 'partial') {
                $query->whereRaw('Item_Amount_Paid > 0 AND Item_Amount_Paid < Item_Amount');
            } elseif ($paymentStatus === 'pending') {
                $query->where(function($q) {
                    $q->where('Item_Amount_Paid', '=', 0)
                      ->orWhereNull('Item_Amount_Paid');
                });
            }
        }

        // Get paginated orders (25 per page)
        $paginatedOrders = $query->orderBy('Order_Date', 'desc')->paginate(25);
        $orders = $paginatedOrders->getCollection();

        // Calculate financial statistics for all filtered orders (not just current page)
        $allFilteredQuery = \App\Models\Order::query();
        
        // Apply the same filters for statistics calculation
        if ($sportId) {
            $allFilteredQuery->whereHas('camp', function ($q) use ($sportId) {
                $q->where('Sport_ID', $sportId);
            });
        }

        if ($campId) {
            $allFilteredQuery->where('Camp_ID', $campId);
        }

        if ($paymentStatus) {
            if ($paymentStatus === 'paid') {
                $allFilteredQuery->whereRaw('Item_Amount_Paid >= Item_Amount AND Item_Amount > 0');
            } elseif ($paymentStatus === 'not_paid') {
                $allFilteredQuery->whereRaw('Item_Amount_Paid < Item_Amount OR Item_Amount_Paid IS NULL');
            } elseif ($paymentStatus === 'partial') {
                $allFilteredQuery->whereRaw('Item_Amount_Paid > 0 AND Item_Amount_Paid < Item_Amount');
            } elseif ($paymentStatus === 'pending') {
                $allFilteredQuery->where(function($q) {
                    $q->where('Item_Amount_Paid', '=', 0)
                      ->orWhereNull('Item_Amount_Paid');
                });
            }
        }

        $totalAmount = $allFilteredQuery->sum('Item_Amount') ?? 0;
        $totalPaid = $allFilteredQuery->sum('Item_Amount_Paid') ?? 0;
        $totalOutstanding = $totalAmount - $totalPaid;

        // Count orders by payment status
        $paidOrdersCount = (clone $allFilteredQuery)->whereRaw('Item_Amount_Paid >= Item_Amount AND Item_Amount > 0')->count();
        $partiallyPaidOrdersCount = (clone $allFilteredQuery)->whereRaw('Item_Amount_Paid > 0 AND Item_Amount_Paid < Item_Amount')->count();
        $pendingOrdersCount = (clone $allFilteredQuery)->where(function($q) {
            $q->where('Item_Amount_Paid', '=', 0)->orWhereNull('Item_Amount_Paid');
        })->count();

        return view('admin.finances', compact(
            'sports',
            'camps',
            'orders',
            'paginatedOrders',
            'totalAmount',
            'totalPaid',
            'totalOutstanding',
            'paidOrdersCount',
            'partiallyPaidOrdersCount',
            'pendingOrdersCount',
            'sportId',
            'campId',
            'paymentStatus'
        ));
    }

    public function inviteCoach()
    {
        return view('admin.invite-coach');
    }

    public function manageCoaches()
    {
        $coaches = Coach::with('sport')->get();
        return view('admin.manage-coaches', compact('coaches'));
    }

    public function editCoach($id)
    {
        $coach = Coach::with('sport', 'user')->findOrFail($id);
        $sports = Sport::orderBy('Sport_Name')->get();
        return view('admin.edit-coach', compact('coach', 'sports'));
    }

    public function updateCoach(Request $request, $id)
    {
        $coach = Coach::findOrFail($id);

        $validated = $request->validate([
            'coach_firstname' => 'required|string|max:255',
            'coach_lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'sport' => 'required|exists:Sports,Sport_ID',
            'admin' => 'nullable|boolean',
        ]);

        $coach->Coach_FirstName = $validated['coach_firstname'];
        $coach->Coach_LastName = $validated['coach_lastname'];
        $coach->Sport_ID = $validated['sport'];
        $coach->admin = $validated['admin'] ?? false;
        $coach->save();

        // Update the associated user's email if changed
        if ($coach->user && $coach->user->email !== $validated['email']) {
            $coach->user->email = $validated['email'];
            $coach->user->save();
        }

        return redirect()->route('admin.manage-coaches')
            ->with('success', 'Coach updated successfully!');
    }

    public function deleteCoach($id)
    {
        $coach = Coach::findOrFail($id);
        $coach->delete();
        return redirect()->route('admin.manage-coaches')
            ->with('success', 'Coach deleted successfully!');
    }

    public function exportFinances(Request $request)
    {
        $sportId = $request->get('sport_id');
        $campId = $request->get('camp_id');
        $paymentStatus = $request->get('payment_status');

        // Apply the same filters as in the finances method
        $query = \App\Models\Order::with(['player', 'parent', 'camp.sport']);

        // Apply filters
        if ($sportId) {
            $query->whereHas('camp', function ($q) use ($sportId) {
                $q->where('Sport_ID', $sportId);
            });
        }

        if ($campId) {
            $query->where('Camp_ID', $campId);
        }

        $orders = $query->orderBy('Order_Date', 'desc')->get();

        // Apply payment status filter after getting orders (since it requires model methods)
        if ($paymentStatus) {
            if ($paymentStatus === 'paid') {
                $orders = $orders->filter(function ($order) {
                    return $order->isFullyPaid();
                });
            } elseif ($paymentStatus === 'not_paid') {
                $orders = $orders->filter(function ($order) {
                    return !$order->isFullyPaid();
                });
            } elseif ($paymentStatus === 'partial') {
                $orders = $orders->filter(function ($order) {
                    return $order->isPartiallyPaid();
                });
            } elseif ($paymentStatus === 'pending') {
                $orders = $orders->filter(function ($order) {
                    return !$order->isFullyPaid() && !$order->isPartiallyPaid();
                });
            }
        }

        return Excel::download(new FinancesExport($orders), 'finances-report.xlsx');
    }
}
