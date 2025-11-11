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
        
        // Calculate financial statistics
        $totalAmount = $orders->sum('Item_Amount') ?? 0;
        $totalPaid = $orders->sum('Item_Amount_Paid') ?? 0;
        $totalOutstanding = $totalAmount - $totalPaid;
        
        $paidOrders = $orders->filter(function ($order) {
            return $order->isFullyPaid();
        });
        
        $partiallyPaidOrders = $orders->filter(function ($order) {
            return $order->isPartiallyPaid();
        });
        
        $pendingOrders = $orders->filter(function ($order) {
            return !$order->isFullyPaid() && !$order->isPartiallyPaid();
        });
        
        return view('admin.finances', compact(
            'sports',
            'camps',
            'orders',
            'totalAmount',
            'totalPaid',
            'totalOutstanding',
            'paidOrders',
            'partiallyPaidOrders',
            'pendingOrders',
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
        return view('admin.manage-coaches');
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
