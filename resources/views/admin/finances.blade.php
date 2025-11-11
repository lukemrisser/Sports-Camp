<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Finances - {{ config('app.name', 'Falcon Teams') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .filter-btn:hover {
            background-color: #1d4ed8 !important;
            transform: translateY(-1px);
        }
        .clear-btn:hover {
            background-color: #4b5563 !important;
            transform: translateY(-1px);
        }
        .filter-btn:active, .clear-btn:active {
            transform: translateY(0);
        }
        .filter-btn, .clear-btn {
            transition: all 0.2s ease-in-out;
        }
        .export-btn:hover {
            background-color: #1d4ed8 !important;
            transform: translateY(-1px);
        }
        .export-btn:active {
            transform: translateY(0);
        }
    </style>
</head>

<body>
    <header class="main-header">
        <div class="header-container">
            <div class="header-content">
                <h1 class="welcome-title">Admin Finances</h1>
                <p class="welcome-subtitle">Financial reports and revenue management</p>
            </div>

            <div class="header-buttons">
                @if (Auth::user()->isCoachAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="header-btn dashboard-btn">Admin Dashboard</a>
                @endif
                @if (Auth::user()->isCoach())
                    <a href="{{ route('coach-dashboard') }}" class="header-btn dashboard-btn">Coach Dashboard</a>
                @endif
                <a href="{{ route('dashboard') }}" class="header-btn login-btn">Account</a>
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="header-btn logout-btn">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" action="{{ route('admin.finances') }}">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-48">
                        <label for="sport_id" class="block text-sm font-medium text-gray-700 mb-1">Sport</label>
                        <select name="sport_id" id="sport_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Sports</option>
                            @foreach($sports as $sport)
                                <option value="{{ $sport->Sport_ID }}" {{ $sportId == $sport->Sport_ID ? 'selected' : '' }}>
                                    {{ $sport->Sport_Name ?? 'Unknown Sport' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex-1 min-w-48">
                        <label for="camp_id" class="block text-sm font-medium text-gray-700 mb-1">Camp</label>
                        <select name="camp_id" id="camp_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Camps</option>
                            @foreach($camps as $camp)
                                <option value="{{ $camp->Camp_ID }}" {{ $campId == $camp->Camp_ID ? 'selected' : '' }}>
                                    {{ $camp->Camp_Name }} @if($camp->sport && $camp->sport->Sport_Name)({{ $camp->sport->Sport_Name }})@endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-1 min-w-48">
                        <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                        <select name="payment_status" id="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Orders</option>
                            <option value="paid" {{ $paymentStatus == 'paid' ? 'selected' : '' }}>Fully Paid</option>
                            <option value="not_paid" {{ $paymentStatus == 'not_paid' ? 'selected' : '' }}>Not Fully Paid</option>
                            <option value="partial" {{ $paymentStatus == 'partial' ? 'selected' : '' }}>Partially Paid</option>
                            <option value="pending" {{ $paymentStatus == 'pending' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-3" style="margin-top: 20px;">
                        <button type="submit" class="filter-btn" style="background-color: #2563eb; color: white; padding: 6px 16px; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); white-space: nowrap; height: 38px; display: flex; align-items: center; justify-content: center;">
                            Filter Orders
                        </button>
                        
                        @if($sportId || $campId || $paymentStatus)
                            <a href="{{ route('admin.finances') }}" class="clear-btn" style="background-color: #6b7280; color: white; padding: 6px 16px; border-radius: 6px; font-weight: 500; text-decoration: none; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); white-space: nowrap; height: 38px;">
                                Clear Filters
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Financial Summary -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-base font-semibold">Financial Summary</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Paid</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outstanding</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Orders</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fully Paid</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Partially Paid</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unpaid</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-semibold text-gray-900">
                                ${{ number_format($totalAmount, 2) }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-semibold text-gray-900">
                                ${{ number_format($totalPaid, 2) }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-semibold text-gray-900">
                                ${{ number_format($totalOutstanding, 2) }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ $orders->count() }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-semibold text-green-600">
                                {{ $paidOrders->count() }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-semibold text-yellow-600">
                                {{ $partiallyPaidOrders->count() }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-semibold text-red-600">
                                {{ $pendingOrders->count() }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-base font-semibold">Order Details</h3>
                @if($orders->count() > 0)
                    <form method="POST" action="{{ route('admin.finances.export') }}" style="display: inline;">
                        @csrf
                        <input type="hidden" name="sport_id" value="{{ $sportId }}">
                        <input type="hidden" name="camp_id" value="{{ $campId }}">
                        <input type="hidden" name="payment_status" value="{{ $paymentStatus }}">
                        <button type="submit" class="export-btn text-sm" style="background-color: #2563eb; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: all 0.2s ease-in-out;">
                            Export to Excel
                        </button>
                    </form>
                @endif
            </div>
            
            @if($orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Player</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Camp</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sport</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outstanding</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #{{ $order->Order_ID }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                        @if($order->player)
                                            {{ $order->player->Camper_FirstName }} {{ $order->player->Camper_LastName }}
                                        @else
                                            <span class="text-gray-500">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                        @if($order->parent)
                                            {{ $order->parent->Parent_FirstName }} {{ $order->parent->Parent_LastName }}
                                        @else
                                            <span class="text-gray-500">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                        @if($order->camp)
                                            {{ $order->camp->Camp_Name }}
                                        @else
                                            <span class="text-gray-500">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                        @if($order->camp && $order->camp->sport)
                                            {{ $order->camp->sport->Sport_Name }}
                                        @else
                                            <span class="text-gray-500">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->Order_Date ? $order->Order_Date->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($order->Item_Amount ?? 0, 2) }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($order->Item_Amount_Paid ?? 0, 2) }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($order->remaining_amount ?? 0, 2) }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        @if($order->payment_status === 'paid')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Paid
                                            </span>
                                        @elseif($order->payment_status === 'partial')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Partial
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-4 py-6 text-center">
                    <p class="text-gray-500">No orders found matching the selected filters.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Create a mapping of camps to sports for JavaScript filtering
        const campSportMapping = {
            @foreach($camps as $camp)
                {{ $camp->Camp_ID }}: {{ $camp->Sport_ID ?? 'null' }},
            @endforeach
        };

        // Dynamic camp filtering based on selected sport
        document.getElementById('sport_id').addEventListener('change', function() {
            const sportId = this.value;
            const campSelect = document.getElementById('camp_id');
            const allOptions = Array.from(campSelect.options);
            
            // Show all camps if no sport is selected
            if (!sportId) {
                allOptions.forEach(option => {
                    option.style.display = '';
                });
                return;
            }
            
            // Filter camps based on selected sport
            allOptions.forEach(option => {
                if (option.value === '') {
                    option.style.display = ''; // Always show "All Camps" option
                } else {
                    const campId = parseInt(option.value);
                    const campSportId = campSportMapping[campId];
                    
                    // Show camp only if it belongs to the selected sport
                    if (campSportId && campSportId == sportId) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                }
            });
            
            // Reset camp selection if the currently selected camp doesn't belong to the selected sport
            const currentCampId = parseInt(campSelect.value);
            if (currentCampId && campSportMapping[currentCampId] != sportId) {
                campSelect.value = '';
            }
        });

        // Add some visual feedback for the filter form
        document.querySelector('form:first-of-type').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Filtering...';
        });
    </script>
</body>

</html>