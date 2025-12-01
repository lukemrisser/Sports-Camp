<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Coaches - {{ config('app.name', 'Falcon Teams') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('partials.header', [
        'title' => 'Manage Coaches',
        'title_class' => 'welcome-title',
    ])

    <div class="container">

        <div class="coaches-table-container">
            <table class="coaches-table">
                <thead>
                    <tr>
                        <th>Coach Name</th>
                        <th>Email</th>
                        <th>Sport</th>
                        <th>Actions</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($coaches as $coach)
                        <tr>
                            <td>{{ $coach->Coach_FirstName }} {{ $coach->Coach_LastName }}</td>
                            <td>{{ $coach->user->email ?? 'N/A' }}</td>
                            <td>{{ $coach->sport_name ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.edit-coach', $coach->Coach_ID) }}" class="btn btn-edit">Edit</a>
                                <form action="{{ route('admin.delete-coach', $coach->Coach_ID) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-delete"
                                        onclick="openDeleteCoachModal(this)">Delete</button>
                                </form>
                            </td>
                            <td>{{ $coach->is_admin ? 'Admin' : 'Coach' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px;">No coaches found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <div id="delete-coach-modal" class="modal-overlay" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h2>Confirm Delete</h2>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this coach?</p>
            </div>

            <div class="modal-footer">
                <button type="submit" class="modal-btn modal-btn-confirm">Delete</button>
                <button type="button" class="modal-btn modal-btn-cancel"
                    onclick="closeDeleteCoachModal()">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        // Opens the delete confirmation modal and stores the form to submit
        function openDeleteCoachModal(button) {
            const modal = document.getElementById('delete-coach-modal');
            if (!modal) return;
            const form = button.closest('form');
            modal.style.display = 'flex';
            // store form reference on the modal element
            modal._targetForm = form;
        }

        function closeDeleteCoachModal() {
            const modal = document.getElementById('delete-coach-modal');
            if (!modal) return;
            modal.style.display = 'none';
            modal._targetForm = null;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const confirmBtn = document.getElementById('confirm-delete-btn');
            if (!confirmBtn) return;
            confirmBtn.addEventListener('click', function() {
                const modal = document.getElementById('delete-coach-modal');
                if (modal && modal._targetForm) {
                    modal._targetForm.submit();
                }
            });

            // Close modal when clicking outside the container
            const modal = document.getElementById('delete-coach-modal');
            modal && modal.addEventListener('click', function(e) {
                if (e.target === modal) closeDeleteCoachModal();
            });
        });
    </script>

    @include('partials.footer')
</body>

</html>
