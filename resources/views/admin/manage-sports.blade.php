<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sports - {{ config('app.name', 'Falcon Teams') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    @include('partials.header', [
        'title' => 'Manage Sports',
        'subtitle' => 'Add, edit, and delete sports available for camps',
    ])



    <div class="registration-page">
        <div class="registration-container">
            <div class="registration-form-wrapper">
                <div class="registration-header">
                    <h2 class="registration-title">Manage Sports</h2>
                </div>

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

                @if ($errors->any())
                    <div class="alert alert-error">
                        <ul class="error-list">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Add Sport Section -->
                <form method="POST" action="{{ route('admin.sports.store') }}" class="registration-form">
                    @csrf
                    <div class="form-section">
                        <h3 class="section-title">Add New Sport</h3>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="sport_name" class="form-label">Sport Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="sport_name" id="sport_name" class="form-input"
                                    placeholder="e.g., Soccer, Basketball, etc." required
                                    value="{{ old('sport_name') }}">
                            </div>
                            <div class="form-group" style="display: flex; align-items: end;">
                                <button type="submit" class="submit-button">Add Sport</button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Sports List -->
                <div class="form-section">
                    <h3 class="section-title">Current Sports</h3>

                    @if ($sports->count() > 0)
                        <div class="sports-table-wrapper">
                            <table class="sports-table">
                                <thead>
                                    <tr>
                                        <th>Sport Name</th>
                                        <th>Usage</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sports as $sport)
                                        <tr>
                                            <td class="sport-name">{{ $sport->Sport_Name }}</td>
                                            <td class="sport-usage">
                                                {{ $sport->camps->count() }} camps, {{ $sport->coaches->count() }}
                                                coaches
                                            </td>
                                            <td class="sport-actions">
                                                <button class="action-btn edit-btn"
                                                    onclick="editSport({{ $sport->Sport_ID }}, '{{ addslashes($sport->Sport_Name) }}')">
                                                    Edit
                                                </button>
                                                <form method="POST"
                                                    action="{{ route('admin.sports.destroy', $sport->Sport_ID) }}"
                                                    style="display: inline;"
                                                    onsubmit="return confirm('Delete {{ addslashes($sport->Sport_Name) }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn delete-btn">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <p>No sports added yet. Add your first sport above!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>



    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Sport</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="editForm" method="POST" class="registration-form">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="edit_sport_name" class="form-label">Sport Name</label>
                    <input type="text" name="sport_name" id="edit_sport_name" class="form-input" required>
                </div>
                <div class="submit-section">
                    <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="submit-button">Update Sport</button>
                </div>
            </form>
        </div>
    </div>



    <style>
        /* Sports Table Styles */
        .sports-table-wrapper {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            margin-top: 20px;
        }

        .sports-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sports-table th {
            background: #f8fafc;
            padding: 16px 24px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        .sports-table td {
            padding: 16px 24px;
            border-bottom: 1px solid #f3f4f6;
        }

        .sports-table tr:hover {
            background: #f9fafb;
        }

        .sport-name {
            font-weight: 600;
            color: #1f2937;
        }

        .sport-usage {
            color: #6b7280;
            font-size: 14px;
        }

        .sport-actions {
            white-space: nowrap;
        }

        /* Action Buttons */
        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            margin-right: 8px;
            transition: all 0.2s ease;
        }

        .edit-btn {
            background: #3b82f6;
            color: white;
        }

        .edit-btn:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .delete-btn {
            background: #ef4444;
            color: white;
        }

        .delete-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            margin-top: 20px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 32px;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
        }

        .close {
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            color: #9ca3af;
            transition: color 0.2s ease;
        }

        .close:hover {
            color: #6b7280;
        }

        .modal .registration-form {
            padding: 32px;
        }

        .submit-section {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }

        .cancel-btn {
            background: #6b7280;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .cancel-btn:hover {
            background: #4b5563;
            transform: translateY(-1px);
        }

        /* Red asterisk for required fields */
        .text-red-500 {
            color: #ef4444;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sports-table-wrapper {
                overflow-x: auto;
            }

            .sports-table th,
            .sports-table td {
                padding: 12px 16px;
            }

            .action-btn {
                padding: 6px 12px;
                font-size: 12px;
                margin-right: 4px;
            }

            .modal-content {
                margin: 5% auto;
                width: 95%;
            }

            .modal-header {
                padding: 20px 24px;
            }

            .modal .registration-form {
                padding: 24px;
            }
        }
    </style>



    <script>
        function editSport(sportId, sportName) {
            document.getElementById('editModal').style.display = 'block';
            document.getElementById('edit_sport_name').value = sportName;
            document.getElementById('editForm').action = `{{ url('/admin/sports') }}/${sportId}`;
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Close modal with escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>

</body>

</html>
