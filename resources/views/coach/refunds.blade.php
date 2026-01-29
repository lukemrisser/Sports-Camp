<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Process Refunds - {{ config('app.name', 'Falcon Camps') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .refund-search-section {
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .form-group select,
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .orders-table {
            display: none;
            margin-top: 2rem;
        }

        .orders-table.show {
            display: block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        table th,
        table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        table tr:hover {
            background-color: #f8f9fa;
        }

        table small {
            color: #666;
            font-size: 0.875rem;
        }

        .session-status,
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .session-status {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .refund-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .refund-modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 1rem;
        }

        .modal-header h2 {
            margin: 0;
            color: #333;
        }

        .no-refund {
            color: #dc3545;
            font-weight: 600;
        }

        .refund-details {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .refund-details p {
            margin: 0.5rem 0;
            color: #333;
        }

        .refund-details strong {
            color: #333;
        }
    </style>
</head>

<body>
    @include('partials.header', [
        'title' => 'Process Refunds',
        'title_class' => 'welcome-title',
    ])

    <div class="registration-page">
        <div class="registration-container">
            <div class="registration-form-wrapper">
                <div class="registration-header">
                    <h2 class="registration-title">Process Refunds</h2>
                </div>

                <!-- Messages -->
                <div id="messageContainer"></div>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Search Section -->
                <div class="refund-search-section">
                    <p style="color: #666; margin-bottom: 1.5rem;">Select a camp and search for a parent by name or email to view their orders.</p>

                    <form id="searchForm">
                        <div class="form-group">
                            <label for="camp_id" class="form-label">Select Camp</label>
                            <select id="camp_id" name="camp_id" class="form-control" required>
                                <option value="">-- Select a Camp --</option>
                                @foreach ($camps as $camp)
                                    <option value="{{ $camp->Camp_ID }}">{{ $camp->Camp_Name }} -
                                        {{ $camp->Start_Date->format('M d, Y') }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="search_term" class="form-label">Search Parent (Name or Email)</label>
                            <input type="text" id="search_term" name="search_term" class="form-control" placeholder="Enter parent name or email"
                                required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Search Orders</button>
                        </div>
                    </form>
                </div>

                <!-- Orders Table -->
                <div class="orders-table" id="ordersTable">
                    <h3 style="margin-top: 1rem; margin-bottom: 1rem;">Found Orders</h3>
                    <div id="ordersTableContent">
                        <!-- Table will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Refund Modal -->
    <div class="refund-modal" id="refundModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Process Refund</h2>
            </div>
            <form id="refundForm">
                <input type="hidden" id="refund_order_id" name="order_id">

                <div id="refundDetails"></div>

                <div class="form-group">
                    <label for="refund_amount" class="form-label">Refund Amount ($)</label>
                    <input type="number" id="refund_amount" name="refund_amount" class="form-control" step="0.01" min="0.01"
                        required>
                </div>

                <div class="form-group">
                    <label for="refund_reason" class="form-label">Refund Reason (Optional)</label>
                    <textarea id="refund_reason" name="refund_reason" class="form-control" rows="3"></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Process Refund</button>
                    <button type="button" class="btn btn-secondary" onclick="closeRefundModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    @include('partials.footer')

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Search form submission
        document.getElementById('searchForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const campId = document.getElementById('camp_id').value;
            const searchTerm = document.getElementById('search_term').value;

            showMessage('Searching...', 'info');

            try {
                const response = await fetch('{{ route('refunds.search') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        camp_id: campId,
                        search_term: searchTerm
                    })
                });

                const data = await response.json();

                if (data.success) {
                    displayOrders(data.orders);
                    clearMessage();
                } else {
                    showMessage(data.message, 'error');
                    hideOrdersTable();
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('An error occurred while searching. Please try again.', 'error');
            }
        });

        // Display orders in table
        function displayOrders(orders) {
            const tableContent = document.getElementById('ordersTableContent');
            const ordersTable = document.getElementById('ordersTable');

            if (orders.length === 0) {
                tableContent.innerHTML = '<p>No orders found.</p>';
                ordersTable.classList.add('show');
                return;
            }

            let html = `
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Parent</th>
                            <th>Player</th>
                            <th>Total Amount</th>
                            <th>Amount Paid</th>
                            <th>Refunded</th>
                            <th>Refundable</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            orders.forEach(order => {
                html += `
                    <tr>
                        <td>${order.order_id}</td>
                        <td>${order.parent_name}<br><small>${order.parent_email}</small></td>
                        <td>${order.player_name}</td>
                        <td>$${order.total_amount}</td>
                        <td>$${order.amount_paid}</td>
                        <td>$${order.refund_amount}</td>
                        <td>$${order.refundable_amount}</td>
                        <td>
                            ${order.can_refund ? 
                                `<button class="btn btn-primary" onclick='openRefundModal(${JSON.stringify(order)})'>Create Refund</button>` :
                                `<span class="no-refund">N/A</span>`
                            }
                        </td>
                    </tr>
                `;
            });

            html += `
                    </tbody>
                </table>
            `;

            tableContent.innerHTML = html;
            ordersTable.classList.add('show');
        }

        // Hide orders table
        function hideOrdersTable() {
            document.getElementById('ordersTable').classList.remove('show');
        }

        // Open refund modal
        function openRefundModal(order) {
            document.getElementById('refund_order_id').value = order.order_id;
            document.getElementById('refund_amount').max = order.refundable_amount.replace(/,/g, '');
            document.getElementById('refund_amount').value = '';
            document.getElementById('refund_reason').value = '';

            const refundDetails = document.getElementById('refundDetails');
            refundDetails.innerHTML = `
                <div class="refund-details">
                    <p><strong>Parent:</strong> ${order.parent_name}</p>
                    <p><strong>Player:</strong> ${order.player_name}</p>
                    <p><strong>Camp:</strong> ${order.camp_name}</p>
                    <p><strong>Amount Paid:</strong> $${order.amount_paid}</p>
                    <p><strong>Already Refunded:</strong> $${order.refund_amount}</p>
                    <p><strong>Max Refundable:</strong> $${order.refundable_amount}</p>
                </div>
            `;

            document.getElementById('refundModal').classList.add('show');
        }

        // Close refund modal
        function closeRefundModal() {
            document.getElementById('refundModal').classList.remove('show');
        }

        // Refund form submission
        document.getElementById('refundForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const orderId = document.getElementById('refund_order_id').value;
            const refundAmount = document.getElementById('refund_amount').value;
            const refundReason = document.getElementById('refund_reason').value;

            if (!confirm(`Are you sure you want to refund $${refundAmount}? This action cannot be undone.`)) {
                return;
            }

            try {
                const response = await fetch('{{ route('refunds.process') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        refund_amount: refundAmount,
                        refund_reason: refundReason
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showMessage(data.message, 'success');
                    closeRefundModal();
                    // Refresh the search to update the table
                    document.getElementById('searchForm').dispatchEvent(new Event('submit'));
                } else {
                    showMessage(data.error, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('An error occurred while processing the refund. Please try again.', 'error');
            }
        });

        // Show message
        function showMessage(message, type) {
            const messageContainer = document.getElementById('messageContainer');
            messageContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;

            // Auto-clear success messages after 5 seconds
            if (type === 'success') {
                setTimeout(clearMessage, 5000);
            }
        }

        // Clear message
        function clearMessage() {
            document.getElementById('messageContainer').innerHTML = '';
        }

        // Close modal when clicking outside
        document.getElementById('refundModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRefundModal();
            }
        });
    </script>
</body>

</html>
