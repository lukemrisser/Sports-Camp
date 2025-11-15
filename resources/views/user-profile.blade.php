<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - {{ config('app.name', 'Falcon Teams') }}</title>

    <!-- Include both app.css, user-profile.css, and app.js -->
    @vite(['resources/css/app.css', 'resources/css/user-profile.css', 'resources/js/app.js'])

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    @include('partials.header', [
        'title' => 'Welcome back, ' . Auth::user()->name . '!',
        'subtitle' => "Here's your account information",
        'title_class' => 'welcome-title',
    ])
    <div class="dashboard-container">
        <div class="dashboard-wrapper">

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
                        @if (Auth::user()->parent)
                            <button onclick="openAddPlayerModal()" class="action-btn btn-secondary">
                                Add New Player
                            </button>
                            @if (Auth::user()->parent->players->count() > 0)
                                <button onclick="scrollToPlayers()" class="action-btn btn-secondary">
                                    My Players ({{ Auth::user()->parent->players->count() }})
                                </button>
                            @endif
                        @endif
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
            @if (Auth::user()->parent)
                <div class="info-card">
                    <h3 class="card-title">Contact Information</h3>

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
                            <p class="info-value" id="display-city">
                                {{ Auth::user()->parent->City ?: 'Not provided' }}
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
                </div>
            @elseif (Auth::user()->parent && !Auth::user()->isCoach())
                <!-- Form for new parent records -->
                <div class="info-card">
                    <h3 class="card-title">Contact Information</h3>
                    <form method="POST" action="{{ route('parent.store') }}" class="contact-form">
                        @csrf
                        <div class="info-grid">
                            <div class="info-item">
                                <label class="info-label" for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="Phone" class="info-input"
                                    placeholder="(555) 123-4567" value="{{ old('Phone') }}">
                                @error('Phone')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="info-item">
                                <label class="info-label" for="address">Home Address</label>
                                <input type="text" id="address" name="Address" class="info-input"
                                    placeholder="123 Main St" value="{{ old('Address') }}">
                                @error('Address')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="info-item">
                                <label class="info-label" for="city">City</label>
                                <input type="text" id="city" name="City" class="info-input"
                                    placeholder="Springfield" value="{{ old('City') }}">
                                @error('City')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="info-item">
                                <label class="info-label" for="state">State</label>
                                <input type="text" id="state" name="State" class="info-input"
                                    placeholder="PA" maxlength="2" value="{{ old('State') }}">
                                @error('State')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="info-item">
                                <label class="info-label" for="postal_code">ZIP Code</label>
                                <input type="text" id="postal_code" name="Postal_Code" class="info-input"
                                    placeholder="12345" value="{{ old('Postal_Code') }}">
                                @error('Postal_Code')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="info-item">
                                <label class="info-label" for="church_name">Church Affiliation (Optional)</label>
                                <input type="text" id="church_name" name="Church_Name" class="info-input"
                                    placeholder="Church Name" value="{{ old('Church_Name') }}">
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Save Contact Information</button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Children/Players Information -->
            @if (Auth::user()->parent && Auth::user()->parent->players->count() > 0)
                <div class="info-card collapsible-card" id="players-section">
                    <div class="card-header-collapsible" onclick="togglePlayersSection()">
                        <h3 class="card-title">My Players</h3>
                        <div class="card-meta">
                            <span class="player-count">{{ Auth::user()->parent->players->count() }}
                                {{ Auth::user()->parent->players->count() == 1 ? 'Player' : 'Players' }}</span>
                            <span class="collapse-icon" id="players-collapse-icon">▶</span>
                        </div>
                    </div>
                    <div class="players-content smooth-transition" id="players-content">
                        <div class="players-grid">
                            @foreach (Auth::user()->parent->players as $player)
                                <div class="player-card">
                                    <div class="player-info">
                                        <!-- Display Mode -->
                                        <div class="player-display-mode"
                                            id="player-display-{{ $player->Player_ID }}">
                                            <div class="player-name">
                                                <strong>
                                                    <span
                                                        id="display-player-fname-{{ $player->Player_ID }}">{{ $player->Camper_FirstName }}</span>
                                                    <span
                                                        id="display-player-lname-{{ $player->Player_ID }}">{{ $player->Camper_LastName }}</span>
                                                </strong>
                                            </div>

                                            <div class="player-details">
                                                <span class="detail-item">
                                                    <label>Birthdate:</label>
                                                    <span id="display-player-birthdate-{{ $player->Player_ID }}">
                                                        {{ $player->Birth_Date ? \Carbon\Carbon::parse($player->Birth_Date)->format('M d, Y') : 'Not specified' }}
                                                    </span>
                                                </span>
                                                <span class="detail-item">
                                                    <label>Gender:</label>
                                                    <span id="display-player-gender-{{ $player->Player_ID }}">
                                                        {{ $player->Gender == 'M' ? 'Male' : ($player->Gender == 'F' ? 'Female' : 'Not specified') }}
                                                    </span>
                                                </span>
                                                <span class="detail-item">
                                                    <label>Shirt Size:</label>
                                                    <span
                                                        id="display-player-shirt-{{ $player->Player_ID }}">{{ $player->Shirt_Size ?: 'Not specified' }}</span>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Edit Mode -->
                                        <div class="player-edit-mode" id="player-edit-{{ $player->Player_ID }}"
                                            style="display: none;">
                                            <div class="player-edit-form">
                                                <div class="edit-row">
                                                    <div class="edit-field">
                                                        <label>First Name:</label>
                                                        <input type="text"
                                                            id="edit-player-fname-{{ $player->Player_ID }}"
                                                            value="{{ $player->Camper_FirstName }}"
                                                            class="player-input">
                                                    </div>
                                                    <div class="edit-field">
                                                        <label>Last Name:</label>
                                                        <input type="text"
                                                            id="edit-player-lname-{{ $player->Player_ID }}"
                                                            value="{{ $player->Camper_LastName }}"
                                                            class="player-input">
                                                    </div>
                                                </div>

                                                <div class="edit-row">
                                                    <div class="edit-field">
                                                        <label>Birthdate:</label>
                                                        <input type="date"
                                                            id="edit-player-birthdate-{{ $player->Player_ID }}"
                                                            value="{{ $player->Birth_Date }}" class="player-input">
                                                    </div>
                                                    <div class="edit-field">
                                                        <label>Gender:</label>
                                                        <select id="edit-player-gender-{{ $player->Player_ID }}"
                                                            class="player-input">
                                                            <option value="M"
                                                                {{ $player->Gender == 'M' ? 'selected' : '' }}>Male
                                                            </option>
                                                            <option value="F"
                                                                {{ $player->Gender == 'F' ? 'selected' : '' }}>Female
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="edit-field">
                                                        <label>Shirt Size:</label>
                                                        <select id="edit-player-shirt-{{ $player->Player_ID }}"
                                                            class="player-input">
                                                            <option value="">Select Size</option>
                                                            <option value="YXS"
                                                                {{ $player->Shirt_Size == 'YXS' ? 'selected' : '' }}>
                                                                Youth
                                                                XS</option>
                                                            <option value="YS"
                                                                {{ $player->Shirt_Size == 'YS' ? 'selected' : '' }}>
                                                                Youth S
                                                            </option>
                                                            <option value="YM"
                                                                {{ $player->Shirt_Size == 'YM' ? 'selected' : '' }}>
                                                                Youth M
                                                            </option>
                                                            <option value="YL"
                                                                {{ $player->Shirt_Size == 'YL' ? 'selected' : '' }}>
                                                                Youth L
                                                            </option>
                                                            <option value="YXL"
                                                                {{ $player->Shirt_Size == 'YXL' ? 'selected' : '' }}>
                                                                Youth
                                                                XL</option>
                                                            <option value="AS"
                                                                {{ $player->Shirt_Size == 'AS' ? 'selected' : '' }}>
                                                                Adult S
                                                            </option>
                                                            <option value="AM"
                                                                {{ $player->Shirt_Size == 'AM' ? 'selected' : '' }}>
                                                                Adult M
                                                            </option>
                                                            <option value="AL"
                                                                {{ $player->Shirt_Size == 'AL' ? 'selected' : '' }}>
                                                                Adult L
                                                            </option>
                                                            <option value="AXL"
                                                                {{ $player->Shirt_Size == 'AXL' ? 'selected' : '' }}>
                                                                Adult
                                                                XL</option>
                                                            <option value="AXXL"
                                                                {{ $player->Shirt_Size == 'AXXL' ? 'selected' : '' }}>
                                                                Adult
                                                                XXL</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="edit-row">
                                                    <div class="edit-field full-width">
                                                        <label>Medications:</label>
                                                        <input type="text"
                                                            id="edit-player-medications-{{ $player->Player_ID }}"
                                                            value="{{ $player->Medications }}" class="player-input"
                                                            placeholder="None or list medications">
                                                    </div>
                                                </div>

                                                <div class="edit-row">
                                                    <div class="edit-field full-width">
                                                        <label>Allergies:</label>
                                                        <input type="text"
                                                            id="edit-player-allergies-{{ $player->Player_ID }}"
                                                            value="{{ $player->Allergies }}" class="player-input"
                                                            placeholder="None or list allergies">
                                                    </div>
                                                </div>

                                                <div class="edit-row">
                                                    <div class="edit-field full-width">
                                                        <label>Injuries:</label>
                                                        <input type="text"
                                                            id="edit-player-injuries-{{ $player->Player_ID }}"
                                                            value="{{ $player->Injuries }}" class="player-input"
                                                            placeholder="None or list injuries">
                                                    </div>
                                                </div>

                                                <div class="edit-row">
                                                    <div class="edit-field">
                                                        <label class="checkbox-label">
                                                            <input type="checkbox"
                                                                id="edit-player-asthma-{{ $player->Player_ID }}"
                                                                {{ $player->Asthma ? 'checked' : '' }}>
                                                            Has Asthma
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Health Information Section (Collapsible) -->
                                        <div class="health-info-section"
                                            onclick="toggleHealthInfo({{ $player->Player_ID }})"
                                            style="cursor: pointer;">
                                            <div class="health-header">
                                                <span class="health-icon">🏥</span>
                                                <label style="cursor: pointer;">Health Information</label>
                                                @php
                                                    $healthCount = 0;
                                                    if ($player->Asthma == 1) {
                                                        $healthCount++;
                                                    }
                                                    if (
                                                        $player->Medications &&
                                                        $player->Medications !== 'None' &&
                                                        $player->Medications !== ''
                                                    ) {
                                                        $healthCount++;
                                                    }
                                                    if (
                                                        $player->Allergies &&
                                                        $player->Allergies !== 'None' &&
                                                        $player->Allergies !== ''
                                                    ) {
                                                        $healthCount++;
                                                    }
                                                    if (
                                                        $player->Injuries &&
                                                        $player->Injuries !== 'None' &&
                                                        $player->Injuries !== ''
                                                    ) {
                                                        $healthCount++;
                                                    }
                                                @endphp

                                                @if ($healthCount > 0)
                                                    <span class="health-badge">{{ $healthCount }}
                                                        item{{ $healthCount > 1 ? 's' : '' }}</span>
                                                @else
                                                    <span class="health-badge-clear">Clear</span>
                                                @endif
                                                <span class="expand-icon"
                                                    id="expand-{{ $player->Player_ID }}">▼</span>
                                            </div>

                                            <div class="health-details" id="health-{{ $player->Player_ID }}"
                                                style="display: none;">
                                                @if ($healthCount > 0)
                                                    @if ($player->Asthma == 1)
                                                        <div class="health-item">
                                                            <span class="health-label">Asthma:</span>
                                                            <span class="health-value">Yes - requires monitoring</span>
                                                        </div>
                                                    @endif

                                                    @if ($player->Medications && $player->Medications !== 'None' && $player->Medications !== '')
                                                        <div class="health-item">
                                                            <span class="health-label">Medications:</span>
                                                            <span
                                                                class="health-value">{{ $player->Medications }}</span>
                                                        </div>
                                                    @endif

                                                    @if ($player->Allergies && $player->Allergies !== 'None' && $player->Allergies !== '')
                                                        <div class="health-item">
                                                            <span class="health-label">Allergies:</span>
                                                            <span class="health-value">{{ $player->Allergies }}</span>
                                                        </div>
                                                    @endif

                                                    @if ($player->Injuries && $player->Injuries !== 'None' && $player->Injuries !== '')
                                                        <div class="health-item">
                                                            <span class="health-label">Injuries:</span>
                                                            <span class="health-value">{{ $player->Injuries }}</span>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="health-item no-issues">
                                                        <span class="health-value">✓ No health concerns reported</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        @if ($player->camps && $player->camps->count() > 0)
                                            <div class="player-camps">
                                                <label>📅 Registered Camps:</label>
                                                <ul class="camps-list">
                                                    @foreach ($player->camps as $camp)
                                                        <li class="camp-item">
                                                            <div class="camp-name">{{ $camp->Camp_Name }}</div>
                                                            @if ($camp->Start_Date && $camp->End_Date)
                                                                <div class="camp-dates">
                                                                    {{ \Carbon\Carbon::parse($camp->Start_Date)->format('M d') }}
                                                                    -
                                                                    {{ \Carbon\Carbon::parse($camp->End_Date)->format('M d, Y') }}
                                                                </div>
                                                            @endif
                                                            @if ($camp->Location)
                                                                <div class="camp-location">📍 {{ $camp->Location }}
                                                                </div>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @else
                                            <div class="player-camps">
                                                <label>📅 Registered Camps:</label>
                                                <p class="no-camps">Not registered for any camps yet</p>
                                            </div>
                                        @endif

                                        <!-- Action Buttons -->
                                        <div class="player-actions">
                                            <button id="edit-player-btn-{{ $player->Player_ID }}"
                                                class="btn-edit-player"
                                                onclick="togglePlayerEdit({{ $player->Player_ID }})">
                                                Edit Player
                                            </button>
                                            <button id="delete-player-btn-{{ $player->Player_ID }}"
                                                class="btn-delete-player"
                                                onclick="confirmDeletePlayer({{ $player->Player_ID }}, '{{ $player->Camper_FirstName }} {{ $player->Camper_LastName }}')">
                                                Delete Player
                                            </button>
                                            <button id="save-player-btn-{{ $player->Player_ID }}"
                                                class="btn-save-player" style="display: none;"
                                                onclick="savePlayer({{ $player->Player_ID }})">
                                                Save Changes
                                            </button>
                                            <button id="cancel-player-btn-{{ $player->Player_ID }}"
                                                class="btn-cancel-player" style="display: none;"
                                                onclick="cancelPlayerEdit({{ $player->Player_ID }})">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @elseif (Auth::user()->parent && !Auth::user()->isCoach())
                <div class="info-card">
                    <h3 class="card-title">My Children</h3>
                    <div class="no-players-message">
                        <p>No children registered yet.</p>
                        <a href="{{ route('registration.form') }}" class="btn-primary">Register a Child for
                            Camp</a>
                    </div>
                </div>
            @endif




        </div>
    </div>



    <script>
        let originalData = {};
        // Player editing functionality
        let originalPlayerData = {};

        let playerToDelete = null;

        // Toast Notification System
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;

            const icon = type === 'success' ? '✓' : '⚠';

            toast.innerHTML = `
        <span class="toast-icon">${icon}</span>
        <span class="toast-message">${message}</span>
        <button class="toast-close" onclick="closeToast(this)">×</button>
    `;

            container.appendChild(toast);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('fade-out');
                setTimeout(() => {
                    if (container.contains(toast)) {
                        container.removeChild(toast);
                    }
                }, 300);
            }, 5000);
        }

        function closeToast(button) {
            const toast = button.closest('.toast');
            toast.classList.add('fade-out');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }


        function confirmDeletePlayer(playerId, playerName) {
            playerToDelete = playerId;
            document.getElementById('playerNameToDelete').textContent = playerName;
            document.getElementById('deleteModal').classList.add('active');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
            playerToDelete = null;
        }

        async function deletePlayer() {
            if (!playerToDelete) return;

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('player_id', playerToDelete);

            try {
                const response = await fetch('/player/delete-ajax', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Close modal
                    closeDeleteModal();

                    // Store success message for after reload
                    sessionStorage.setItem('pendingToast', JSON.stringify({
                        message: 'Player has been removed from your account',
                        type: 'success'
                    }));

                    // Reload immediately
                    location.reload();
                } else {
                    alert('Error removing player: ' + (data.message || 'Please try again.'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Network error. Please try again.');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Check for pending toast messages
            const pendingToast = sessionStorage.getItem('pendingToast');
            if (pendingToast) {
                const {
                    message,
                    type
                } = JSON.parse(pendingToast);
                showToast(message, type);
                sessionStorage.removeItem('pendingToast');
            }

            // Modal click handlers
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                deleteModal.addEventListener('click', function(e) {
                    if (e.target === deleteModal) {
                        closeDeleteModal();
                    }
                });
            }

            const addModal = document.getElementById('addPlayerModal');
            if (addModal) {
                addModal.addEventListener('click', function(e) {
                    if (e.target === addModal) {
                        closeAddPlayerModal();
                    }
                });
            }

            // Players section - ALWAYS start closed (no session persistence)
            const content = document.getElementById('players-content');
            const icon = document.getElementById('players-collapse-icon');

            if (content) {
                // Clear any stored state from previous implementation
                localStorage.removeItem('playersExpanded');
                localStorage.removeItem('playersOpenedByButton');

                // Set initial closed state with inline styles
                content.style.maxHeight = '0px';
                content.style.opacity = '0';
                content.style.overflow = 'hidden';
                content.style.paddingTop = '0';
                content.style.transition = 'none'; // No transition on initial load

                // Ensure icon starts in closed position
                if (icon) {
                    icon.style.transform = 'rotate(0deg)';
                    icon.style.transition = 'transform 0.3s ease';
                }

                // Remove any expanded class that might be lingering
                content.classList.remove('expanded');
                content.classList.add('smooth-transition');

                // Set up transition for future animations after page loads
                setTimeout(() => {
                    content.style.transition =
                        'max-height 0.6s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease-out, padding 0.4s ease-out';
                }, 100);
            }
        });



        function togglePlayerEdit(playerId) {
            // Store original values
            originalPlayerData[playerId] = {
                fname: document.getElementById('edit-player-fname-' + playerId).value,
                lname: document.getElementById('edit-player-lname-' + playerId).value,
                birthdate: document.getElementById('edit-player-birthdate-' + playerId).value,
                gender: document.getElementById('edit-player-gender-' + playerId).value,
                shirt: document.getElementById('edit-player-shirt-' + playerId).value,
                medications: document.getElementById('edit-player-medications-' + playerId).value,
                allergies: document.getElementById('edit-player-allergies-' + playerId).value,
                injuries: document.getElementById('edit-player-injuries-' + playerId).value,
                asthma: document.getElementById('edit-player-asthma-' + playerId).checked
            };

            // Toggle display/edit modes
            document.getElementById('player-display-' + playerId).style.display = 'none';
            document.getElementById('player-edit-' + playerId).style.display = 'block';

            // Hide health info section during edit
            const healthSection = document.getElementById('health-' + playerId).parentElement;
            if (healthSection) {
                healthSection.style.display = 'none';
            }

            // Toggle buttons
            document.getElementById('edit-player-btn-' + playerId).style.display = 'none';
            document.getElementById('delete-player-btn-' + playerId).style.display = 'none'; // Hide delete button
            document.getElementById('save-player-btn-' + playerId).style.display = 'inline-block';
            document.getElementById('cancel-player-btn-' + playerId).style.display = 'inline-block';
        }

        function cancelPlayerEdit(playerId) {
            // Restore original values
            const data = originalPlayerData[playerId];
            if (data) {
                document.getElementById('edit-player-fname-' + playerId).value = data.fname;
                document.getElementById('edit-player-lname-' + playerId).value = data.lname;
                document.getElementById('edit-player-birthdate-' + playerId).value = data.birthdate;
                document.getElementById('edit-player-gender-' + playerId).value = data.gender;
                document.getElementById('edit-player-shirt-' + playerId).value = data.shirt;
                document.getElementById('edit-player-medications-' + playerId).value = data.medications;
                document.getElementById('edit-player-allergies-' + playerId).value = data.allergies;
                document.getElementById('edit-player-injuries-' + playerId).value = data.injuries;
                document.getElementById('edit-player-asthma-' + playerId).checked = data.asthma;
            }

            // Toggle back to display mode
            document.getElementById('player-display-' + playerId).style.display = 'block';
            document.getElementById('player-edit-' + playerId).style.display = 'none';

            // Show health info section again
            const healthSection = document.getElementById('health-' + playerId).parentElement;
            if (healthSection) {
                healthSection.style.display = 'block';
            }

            // Toggle buttons
            document.getElementById('edit-player-btn-' + playerId).style.display = 'inline-block';
            document.getElementById('delete-player-btn-' + playerId).style.display = 'inline-block'; // Show delete button
            document.getElementById('save-player-btn-' + playerId).style.display = 'none';
            document.getElementById('cancel-player-btn-' + playerId).style.display = 'none';
        }

        async function savePlayer(playerId) {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('player_id', playerId);
            formData.append('Camper_FirstName', document.getElementById('edit-player-fname-' + playerId).value);
            formData.append('Camper_LastName', document.getElementById('edit-player-lname-' + playerId).value);
            formData.append('Birth_Date', document.getElementById('edit-player-birthdate-' + playerId).value);
            formData.append('Gender', document.getElementById('edit-player-gender-' + playerId).value);
            formData.append('Shirt_Size', document.getElementById('edit-player-shirt-' + playerId).value);
            formData.append('Medications', document.getElementById('edit-player-medications-' + playerId).value ||
                'None');
            formData.append('Allergies', document.getElementById('edit-player-allergies-' + playerId).value || 'None');
            formData.append('Injuries', document.getElementById('edit-player-injuries-' + playerId).value || 'None');
            formData.append('Asthma', document.getElementById('edit-player-asthma-' + playerId).checked ? 1 : 0);

            try {
                const response = await fetch('/player/update-ajax', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Store success message for after reload
                    sessionStorage.setItem('pendingToast', JSON.stringify({
                        message: 'Player information updated successfully',
                        type: 'success'
                    }));

                    // Reload immediately
                    location.reload();
                } else {
                    alert('Error updating player: ' + (data.message || 'Please try again.'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Network error. Please try again.');
            }
        }

        // Health info toggle (existing function)
        function toggleHealthInfo(playerId) {
            const details = document.getElementById('health-' + playerId);
            const expandIcon = document.getElementById('expand-' + playerId);

            if (details.style.display === 'none') {
                details.style.display = 'block';
                if (expandIcon) expandIcon.classList.add('rotated');
            } else {
                details.style.display = 'none';
                if (expandIcon) expandIcon.classList.remove('rotated');
            }
        }

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

                    // Show success toast
                    showToast('Profile updated successfully');
                } else {
                    alert('Error updating profile. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error updating profile. Please try again.');
            }
        }

        function openAddPlayerModal() {
            // Clear the form
            document.getElementById('addPlayerForm').reset();
            // Show the modal
            document.getElementById('addPlayerModal').classList.add('active');
        }

        function closeAddPlayerModal() {
            document.getElementById('addPlayerModal').classList.remove('active');
        }

        async function addNewPlayer() {
            // Get form values
            const fname = document.getElementById('new-player-fname').value.trim();
            const lname = document.getElementById('new-player-lname').value.trim();
            const birthdate = document.getElementById('new-player-birthdate').value;
            const gender = document.getElementById('new-player-gender').value;
            const shirtSize = document.getElementById('new-player-shirt').value;

            const medications = document.getElementById('new-player-medications').value.trim() || 'None';
            const allergies = document.getElementById('new-player-allergies').value.trim() || 'None';
            const injuries = document.getElementById('new-player-injuries').value.trim() || 'None';
            const asthma = document.getElementById('new-player-asthma').checked ? 1 : 0;

            // Validate required fields
            if (!fname || !lname || !birthdate || !gender || !shirtSize) {
                alert('Please fill in all required fields marked with *');
                return;
            }

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('Camper_FirstName', fname);
            formData.append('Camper_LastName', lname);
            formData.append('Birth_Date', birthdate);
            formData.append('Gender', gender);
            formData.append('Shirt_Size', shirtSize);

            formData.append('Medications', medications);
            formData.append('Allergies', allergies);
            formData.append('Injuries', injuries);
            formData.append('Asthma', asthma);

            try {
                const response = await fetch('/player/add-ajax', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Close modal
                    closeAddPlayerModal();

                    // Store success message for after reload
                    sessionStorage.setItem('pendingToast', JSON.stringify({
                        message: `${fname} ${lname} has been added successfully`,
                        type: 'success'
                    }));

                    // Reload immediately
                    location.reload();
                } else {
                    alert('Error adding player: ' + (data.message || 'Please try again.'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Network error. Please try again.');
            }
        }

        // Close modals when clicking outside
        document.addEventListener('DOMContentLoaded', function() {
            const addModal = document.getElementById('addPlayerModal');
            if (addModal) {
                addModal.addEventListener('click', function(e) {
                    if (e.target === addModal) {
                        closeAddPlayerModal();
                    }
                });
            }
        });

        function togglePlayersSection() {
            const content = document.getElementById('players-content');
            const icon = document.getElementById('players-collapse-icon');

            // Check if currently expanded
            const isExpanded = content.style.opacity === '1' ||
                (content.style.opacity === '' && window.getComputedStyle(content).opacity === '1');

            // Set smooth transition with better easing
            content.style.transition =
                'max-height 0.6s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease-out, padding 0.4s ease-out';

            if (isExpanded) {
                // Closing animation
                content.style.maxHeight = content.scrollHeight + 'px'; // Set explicit height first
                content.style.overflow = 'hidden';

                // Force browser reflow
                content.offsetHeight;

                // Now animate to closed
                content.style.maxHeight = '0px';
                content.style.opacity = '0';
                content.style.paddingTop = '0';
                icon.style.transform = 'rotate(0deg)';

                // Clean up classes
                content.classList.remove('expanded');
                localStorage.removeItem('playersExpanded');
                localStorage.removeItem('playersOpenedByButton');
            } else {
                // Opening animation
                content.style.display = 'block'; // Ensure it's visible
                content.style.paddingTop = '24px';

                // Calculate the full height
                const targetHeight = content.scrollHeight;

                // Animate to open
                content.style.maxHeight = targetHeight + 'px';
                content.style.opacity = '1';
                icon.style.transform = 'rotate(90deg)';

                // After animation, set to auto height
                setTimeout(() => {
                    content.style.overflow = 'visible';
                    content.style.maxHeight = 'none';
                }, 600);

                // Update state
                content.classList.add('expanded');
                localStorage.setItem('playersExpanded', 'true');
            }
        }

        // Custom smooth scroll function for better control
        function smoothScrollTo(targetPosition, duration) {
            const startPosition = window.pageYOffset;
            const distance = targetPosition - startPosition;
            let startTime = null;

            function animation(currentTime) {
                if (startTime === null) startTime = currentTime;
                const timeElapsed = currentTime - startTime;
                const progress = Math.min(timeElapsed / duration, 1);

                // Easing function for smoother animation (ease-in-out-cubic)
                const easeInOutCubic = progress < 0.5 ?
                    4 * progress * progress * progress :
                    1 - Math.pow(-2 * progress + 2, 3) / 2;

                window.scrollTo(0, startPosition + (distance * easeInOutCubic));

                if (timeElapsed < duration) {
                    requestAnimationFrame(animation);
                }
            }

            requestAnimationFrame(animation);
        }




        function scrollToPlayers() {
            const playersSection = document.getElementById('players-section');

            if (playersSection) {
                const content = document.getElementById('players-content');
                const icon = document.getElementById('players-collapse-icon');

                // Check if already open
                const isExpanded = content.style.opacity === '1';

                if (!isExpanded && content) {
                    // Open with smooth animation
                    content.style.transition =
                        'max-height 0.6s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease-out, padding 0.4s ease-out';
                    content.style.display = 'block';
                    content.style.paddingTop = '24px';
                    content.style.maxHeight = content.scrollHeight + 'px';
                    content.style.opacity = '1';
                    icon.style.transform = 'rotate(90deg)';

                    setTimeout(() => {
                        content.style.overflow = 'visible';
                        content.style.maxHeight = 'none';
                    }, 600);

                    content.classList.add('expanded');
                    localStorage.setItem('playersExpanded', 'true');
                    localStorage.setItem('playersOpenedByButton', 'true');
                }

                // Smooth scroll after a delay
                setTimeout(() => {
                    const rect = playersSection.getBoundingClientRect();
                    const absoluteTop = window.pageYOffset + rect.top;
                    const targetPosition = absoluteTop - 80;
                    smoothScrollTo(targetPosition, 1000); // Slightly slower for smoothness

                    setTimeout(() => {
                        playersSection.classList.add('highlight-section');
                        setTimeout(() => {
                            playersSection.classList.remove('highlight-section');
                        }, 2000);
                    }, 1050);
                }, isExpanded ? 100 : 400); // Wait longer if we just opened it
            }
        }
    </script>

    <!-- Delete Player Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">Confirm Player Removal</div>
            <div class="modal-body">
                <p>Are you sure you want to remove <strong id="playerNameToDelete"></strong> from your account?</p>
                <p class="warning">⚠️ Note: This action cannot be undone. The player's registration will be removed
                    from your account.</p>
            </div>
            <div class="modal-actions">
                <button class="modal-btn modal-btn-cancel" onclick="closeDeleteModal()">Cancel</button>
                <button class="modal-btn modal-btn-confirm" onclick="deletePlayer()">Remove Player</button>
            </div>
        </div>
    </div>


    <!-- Add Player Modal -->
    <div id="addPlayerModal" class="modal-overlay">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h2>Add New Player</h2>
                <button class="modal-close" onclick="closeAddPlayerModal()">×</button>
            </div>
            <div class="modal-body">
                <form id="addPlayerForm">
                    <div class="form-section">
                        <h3>Player Information</h3>
                        <div class="form-row">
                            <div class="form-field">
                                <label>First Name <span class="required">*</span></label>
                                <input type="text" id="new-player-fname" required>
                            </div>
                            <div class="form-field">
                                <label>Last Name <span class="required">*</span></label>
                                <input type="text" id="new-player-lname" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-field">
                                <label>Birthdate <span class="required">*</span></label>
                                <input type="date" id="new-player-birthdate" required>
                            </div>
                            <div class="form-field">
                                <label>Gender <span class="required">*</span></label>
                                <select id="new-player-gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                </select>
                            </div>
                            <div class="form-field">
                                <label>Shirt Size <span class="required">*</span></label>
                                <select id="new-player-shirt" required>
                                    <option value="">Select Size</option>
                                    <optgroup label="Youth Sizes">
                                        <option value="YXS">Youth XS</option>
                                        <option value="YS">Youth S</option>
                                        <option value="YM">Youth M</option>
                                        <option value="YL">Youth L</option>
                                        <option value="YXL">Youth XL</option>
                                    </optgroup>
                                    <optgroup label="Adult Sizes">
                                        <option value="AS">Adult S</option>
                                        <option value="AM">Adult M</option>
                                        <option value="AL">Adult L</option>
                                        <option value="AXL">Adult XL</option>
                                        <option value="AXXL">Adult XXL</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="form-section">
                        <h3>Health Information</h3>
                        <div class="form-row">
                            <div class="form-field full-width">
                                <label>Medications</label>
                                <input type="text" id="new-player-medications"
                                    placeholder="None or list medications">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-field full-width">
                                <label>Allergies</label>
                                <input type="text" id="new-player-allergies" placeholder="None or list allergies">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-field full-width">
                                <label>Injuries/Medical Conditions</label>
                                <input type="text" id="new-player-injuries"
                                    placeholder="None or list injuries/conditions">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-field">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="new-player-asthma">
                                    Has Asthma
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-actions">
                <button class="modal-btn modal-btn-cancel" onclick="closeAddPlayerModal()">Cancel</button>
                <button class="modal-btn modal-btn-primary" onclick="addNewPlayer()">Add Player</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container"></div>


    @include('partials.footer')


</body>



</html>
