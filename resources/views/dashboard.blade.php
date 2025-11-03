<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ config('app.name', 'Falcon Teams') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<header class="main-header">
    <div class="header-container">
        <div class="header-content">
            <h1 class="welcome-title">Welcome back, <span id="welcome-name">{{ Auth::user()->name }}</span>!</h1>
            <p class="welcome-subtitle">Here's your account information</p>
        </div>

        <div class="header-buttons">
            @if (Auth::user()->isCoachAdmin())
                <a href="{{ route('admin.dashboard') }}" class="header-btn dashboard-btn">Admin Dashboard</a>
                <a href="{{ route('coach-dashboard') }}" class="header-btn dashboard-btn">Coach Dashboard</a>
            @elseif (Auth::user()->isCoach())
                <a href="{{ route('coach-dashboard') }}" class="header-btn dashboard-btn">Coach Dashboard</a>
            @else
                <a href="{{ route('home') }}" class="header-btn login-btn">← Home</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="header-btn logout-btn">Logout</button>
            </form>
        </div>
    </div>
</header>

<body>
    <div class="dashboard-container">
        <div class="dashboard-wrapper">
            <!-- User Information Card -->
            <div class="info-card">
                <h3 class="card-title">Personal Information</h3>

                <!-- Display Mode -->
                <div class="info-grid display-mode" id="personal-display">
                    <div class="info-item">
                        <label class="info-label">Full Name</label>
                        <p class="info-value" id="display-name">{{ Auth::user()->name }}</p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Email Address</label>
                        <p class="info-value">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Account Type</label>
                        <p class="info-value">
                            @if (Auth::user()->isCoach())
                                <span class="badge badge-coach">Coach</span>
                                @if (Auth::user()->isCoachAdmin())
                                    <span class="badge badge-admin">Admin</span>
                                @endif
                            @else
                                <span class="badge badge-parent">Parent</span>
                            @endif
                        </p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Member Since</label>
                        <p class="info-value">{{ Auth::user()->created_at->format('F j, Y') }}</p>
                    </div>
                </div>

                <!-- Edit Mode -->
                <form id="personal-form" class="info-grid edit-mode" style="display: none;">
                    @csrf
                    <div class="info-item">
                        <label class="info-label" for="edit-fname">First Name</label>
                        <input type="text" id="edit-fname" name="fname" class="info-input"
                            value="{{ Auth::user()->fname }}">
                    </div>
                    <div class="info-item">
                        <label class="info-label" for="edit-lname">Last Name</label>
                        <input type="text" id="edit-lname" name="lname" class="info-input"
                            value="{{ Auth::user()->lname }}">
                    </div>
                    <div class="info-item">
                        <label class="info-label">Email Address</label>
                        <p class="info-value">{{ Auth::user()->email }} <span class="text-muted">(cannot be
                                changed)</span></p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Account Type</label>
                        <p class="info-value">
                            @if (Auth::user()->isCoach())
                                <span class="badge badge-coach">Coach</span>
                                @if (Auth::user()->isCoachAdmin())
                                    <span class="badge badge-admin">Admin</span>
                                @endif
                            @else
                                <span class="badge badge-parent">Parent</span>
                            @endif
                        </p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Member Since</label>
                        <p class="info-value">{{ Auth::user()->created_at->format('F j, Y') }}</p>
                    </div>
                </form>
            </div>

            <!-- Parent Contact Information -->
            <div class="info-card">
                <h3 class="card-title">Contact Information</h3>

                @if (Auth::user()->parent)
                    <!-- Display Mode -->
                    <div class="info-grid display-mode" id="contact-display">
                        <div class="info-item">
                            <label class="info-label">Phone Number</label>
                            <p class="info-value" id="display-phone">
                                {{ Auth::user()->parent->Phone ?: 'Not provided' }}</p>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Home Address</label>
                            <p class="info-value" id="display-address">
                                {{ Auth::user()->parent->Address ?: 'Not provided' }}</p>
                        </div>
                        <div class="info-item">
                            <label class="info-label">City</label>
                            <p class="info-value" id="display-city">{{ Auth::user()->parent->City ?: 'Not provided' }}
                            </p>
                        </div>
                        <div class="info-item">
                            <label class="info-label">State</label>
                            <p class="info-value" id="display-state">
                                {{ Auth::user()->parent->State ?: 'Not provided' }}</p>
                        </div>
                        <div class="info-item">
                            <label class="info-label">ZIP Code</label>
                            <p class="info-value" id="display-postal">
                                {{ Auth::user()->parent->Postal_Code ?: 'Not provided' }}</p>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Church Affiliation</label>
                            <p class="info-value" id="display-church">
                                {{ Auth::user()->parent->Church_Name ?: 'Not provided' }}</p>
                        </div>
                    </div>

                    <!-- Edit Mode -->
                    <form id="contact-form" class="info-grid edit-mode" style="display: none;">
                        @csrf
                        <div class="info-item">
                            <label class="info-label" for="edit-phone">Phone Number</label>
                            <input type="tel" id="edit-phone" name="Phone" class="info-input"
                                value="{{ Auth::user()->parent->Phone }}" placeholder="(555) 123-4567">
                        </div>
                        <div class="info-item">
                            <label class="info-label" for="edit-address">Home Address</label>
                            <input type="text" id="edit-address" name="Address" class="info-input"
                                value="{{ Auth::user()->parent->Address }}" placeholder="123 Main St">
                        </div>
                        <div class="info-item">
                            <label class="info-label" for="edit-city">City</label>
                            <input type="text" id="edit-city" name="City" class="info-input"
                                value="{{ Auth::user()->parent->City }}" placeholder="Springfield">
                        </div>
                        <div class="info-item">
                            <label class="info-label" for="edit-state">State</label>
                            <input type="text" id="edit-state" name="State" class="info-input"
                                value="{{ Auth::user()->parent->State }}" placeholder="PA" maxlength="2">
                        </div>
                        <div class="info-item">
                            <label class="info-label" for="edit-postal">ZIP Code</label>
                            <input type="text" id="edit-postal" name="Postal_Code" class="info-input"
                                value="{{ Auth::user()->parent->Postal_Code }}" placeholder="12345">
                        </div>
                        <div class="info-item">
                            <label class="info-label" for="edit-church">Church Affiliation</label>
                            <input type="text" id="edit-church" name="Church_Name" class="info-input"
                                value="{{ Auth::user()->parent->Church_Name }}" placeholder="Church Name">
                        </div>
                    </form>
                @else
                    <!-- Original form for new parent records -->
                    <form method="POST" action="{{ route('parent.store') }}" class="contact-form">
                        @csrf
                        <!-- Keep your existing form fields here -->
                        <div class="info-grid">
                            <!-- ... existing form fields ... -->
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Save Contact Information</button>
                        </div>
                    </form>
                @endif
            </div>

            <!-- Coach-specific Information (unchanged) -->
            @if (Auth::user()->isCoach())
                <!-- ... existing coach section ... -->
            @endif

            <!-- Quick Actions -->
            <div class="info-card">
                <h3 class="card-title">Quick Actions</h3>
                <div class="action-buttons">
                    @if (Auth::user()->isCoach())
                        <a href="{{ route('coach-dashboard') }}" class="action-btn btn-primary">
                            Go to Coach Dashboard
                        </a>
                        <a href="{{ route('organize-teams') }}" class="action-btn btn-secondary">
                            Organize Teams
                        </a>
                    @else
                        <a href="{{ route('registration.form') }}" class="action-btn btn-primary">
                            Register for Camp
                        </a>
                    @endif

                    <!-- Edit/Save/Cancel buttons -->
                    <button id="edit-btn" class="action-btn btn-secondary" onclick="toggleEditMode()">
                        Edit Profile
                    </button>
                    <button id="save-btn" class="action-btn btn-primary" style="display: none;"
                        onclick="saveProfile()">
                        Save Changes
                    </button>
                    <button id="cancel-btn" class="action-btn btn-secondary" style="display: none;"
                        onclick="cancelEdit()">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .info-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .info-input:focus {
            outline: none;
            border-color: #0a3f94;
            box-shadow: 0 0 0 3px rgba(10, 63, 148, 0.1);
        }

        .text-muted {
            color: #6b7280;
            font-size: 0.875em;
            font-style: italic;
        }

        .edit-mode {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
    </style>

    <script>
        let originalData = {};

        function toggleEditMode() {
            // Get current values from the display elements, not from PHP
            const displayNameText = document.getElementById('display-name').textContent.trim();
            const nameParts = displayNameText.split(' ');
            const currentFname = nameParts[0] || '{{ Auth::user()->fname }}';
            const currentLname = nameParts.slice(1).join(' ') || '{{ Auth::user()->lname }}';

            // Set the edit fields with current values
            document.getElementById('edit-fname').value = currentFname;
            document.getElementById('edit-lname').value = currentLname;

            // Store original values (what's currently displayed) - with trim()
            originalData = {
                fname: currentFname,
                lname: currentLname,
                @if (Auth::user()->parent)
                    phone: document.getElementById('display-phone').textContent.trim() !== 'Not provided' ?
                        document.getElementById('display-phone').textContent.trim() : '',
                    address: document.getElementById('display-address').textContent.trim() !== 'Not provided' ?
                        document.getElementById('display-address').textContent.trim() : '',
                    city: document.getElementById('display-city').textContent.trim() !== 'Not provided' ?
                        document.getElementById('display-city').textContent.trim() : '',
                    state: document.getElementById('display-state').textContent.trim() !== 'Not provided' ?
                        document.getElementById('display-state').textContent.trim() : '',
                    postal: document.getElementById('display-postal').textContent.trim() !== 'Not provided' ?
                        document.getElementById('display-postal').textContent.trim() : '',
                    church: document.getElementById('display-church').textContent.trim() !== 'Not provided' ?
                        document.getElementById('display-church').textContent.trim() : '',
                @endif
            };

            // Set parent fields if they exist - values are already trimmed
            @if (Auth::user()->parent)
                document.getElementById('edit-phone').value = originalData.phone;
                document.getElementById('edit-address').value = originalData.address;
                document.getElementById('edit-city').value = originalData.city;
                document.getElementById('edit-state').value = originalData.state;
                document.getElementById('edit-postal').value = originalData.postal;
                document.getElementById('edit-church').value = originalData.church;
            @endif

            // Toggle display/edit modes
            document.querySelectorAll('.display-mode').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'grid');

            // Toggle buttons
            document.getElementById('edit-btn').style.display = 'none';
            document.getElementById('save-btn').style.display = 'inline-block';
            document.getElementById('cancel-btn').style.display = 'inline-block';
        }

        function cancelEdit() {
            // Restore original values
            document.getElementById('edit-fname').value = originalData.fname;
            document.getElementById('edit-lname').value = originalData.lname;
            @if (Auth::user()->parent)
                document.getElementById('edit-phone').value = originalData.phone;
                document.getElementById('edit-address').value = originalData.address;
                document.getElementById('edit-city').value = originalData.city;
                document.getElementById('edit-state').value = originalData.state;
                document.getElementById('edit-postal').value = originalData.postal;
                document.getElementById('edit-church').value = originalData.church;
            @endif

            // Toggle back to display mode
            document.querySelectorAll('.display-mode').forEach(el => el.style.display = 'grid');
            document.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'none');

            // Toggle buttons
            document.getElementById('edit-btn').style.display = 'inline-block';
            document.getElementById('save-btn').style.display = 'none';
            document.getElementById('cancel-btn').style.display = 'none';
        }

        async function saveProfile() {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            // Get fname and lname from separate input fields
            formData.append('fname', document.getElementById('edit-fname').value);
            formData.append('lname', document.getElementById('edit-lname').value);
            @if (Auth::user()->parent)
                formData.append('Phone', document.getElementById('edit-phone').value);
                formData.append('Address', document.getElementById('edit-address').value);
                formData.append('City', document.getElementById('edit-city').value);
                formData.append('State', document.getElementById('edit-state').value);
                formData.append('Postal_Code', document.getElementById('edit-postal').value);
                formData.append('Church_Name', document.getElementById('edit-church').value);
            @endif

            try {
                const response = await fetch('{{ route('profile.update.ajax') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    //  Update the full name display (combining fname and lname)
                    const displayName = document.getElementById('display-name');
                    if (displayName) {
                        displayName.textContent = (data.user.fname || '') + ' ' + (data.user.lname || '');
                    }

                    // Update the welcome message
                    const welcomeName = document.getElementById('welcome-name');
                    if (welcomeName) {
                        welcomeName.textContent = (data.user.fname || '') + ' ' + (data.user.lname || '');
                    }


                    @if (Auth::user()->parent)
                        document.getElementById('display-phone').textContent = data.parent.Phone || 'Not provided';
                        document.getElementById('display-address').textContent = data.parent.Address || 'Not provided';
                        document.getElementById('display-city').textContent = data.parent.City || 'Not provided';
                        document.getElementById('display-state').textContent = data.parent.State || 'Not provided';
                        document.getElementById('display-postal').textContent = data.parent.Postal_Code ||
                            'Not provided';
                        document.getElementById('display-church').textContent = data.parent.Church_Name ||
                            'Not provided';
                    @endif

                    // Switch back to display mode
                    cancelEdit();

                    // Show success message
                    alert('Profile updated successfully!');
                } else {
                    alert('Error updating profile. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error updating profile. Please try again.');
            }
        }
    </script>
</body>

</html>
