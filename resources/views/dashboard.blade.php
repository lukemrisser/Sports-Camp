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
            <!-- Children/Players Information -->
            @if (Auth::user()->parent && Auth::user()->parent->players->count() > 0)
                <div class="info-card">
                    <h3 class="card-title">My Players</h3>
                    <div class="players-grid">
                        @foreach (Auth::user()->parent->players as $player)
                            <div class="player-card">
                                <div class="player-info">
                                    <!-- Display Mode -->
                                    <div class="player-display-mode" id="player-display-{{ $player->Player_ID }}">
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
                                                        value="{{ $player->Camper_FirstName }}" class="player-input">
                                                </div>
                                                <div class="edit-field">
                                                    <label>Last Name:</label>
                                                    <input type="text"
                                                        id="edit-player-lname-{{ $player->Player_ID }}"
                                                        value="{{ $player->Camper_LastName }}" class="player-input">
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
                                                            {{ $player->Shirt_Size == 'YXS' ? 'selected' : '' }}>Youth
                                                            XS</option>
                                                        <option value="YS"
                                                            {{ $player->Shirt_Size == 'YS' ? 'selected' : '' }}>Youth S
                                                        </option>
                                                        <option value="YM"
                                                            {{ $player->Shirt_Size == 'YM' ? 'selected' : '' }}>Youth M
                                                        </option>
                                                        <option value="YL"
                                                            {{ $player->Shirt_Size == 'YL' ? 'selected' : '' }}>Youth L
                                                        </option>
                                                        <option value="YXL"
                                                            {{ $player->Shirt_Size == 'YXL' ? 'selected' : '' }}>Youth
                                                            XL</option>
                                                        <option value="AS"
                                                            {{ $player->Shirt_Size == 'AS' ? 'selected' : '' }}>Adult S
                                                        </option>
                                                        <option value="AM"
                                                            {{ $player->Shirt_Size == 'AM' ? 'selected' : '' }}>Adult M
                                                        </option>
                                                        <option value="AL"
                                                            {{ $player->Shirt_Size == 'AL' ? 'selected' : '' }}>Adult L
                                                        </option>
                                                        <option value="AXL"
                                                            {{ $player->Shirt_Size == 'AXL' ? 'selected' : '' }}>Adult
                                                            XL</option>
                                                        <option value="AXXL"
                                                            {{ $player->Shirt_Size == 'AXXL' ? 'selected' : '' }}>Adult
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
                                            <span class="expand-icon" id="expand-{{ $player->Player_ID }}">▼</span>
                                        </div>

                                        <div class="health-details" id="health-{{ $player->Player_ID }}"
                                            style="display: none;">
                                            @if ($healthCount > 0)
                                                @if ($player->Asthma == 1)
                                                    <div class="health-item">
                                                        <span class="health-label">🫁 Asthma:</span>
                                                        <span class="health-value">Yes - requires monitoring</span>
                                                    </div>
                                                @endif

                                                @if ($player->Medications && $player->Medications !== 'None' && $player->Medications !== '')
                                                    <div class="health-item">
                                                        <span class="health-label">💊 Medications:</span>
                                                        <span class="health-value">{{ $player->Medications }}</span>
                                                    </div>
                                                @endif

                                                @if ($player->Allergies && $player->Allergies !== 'None' && $player->Allergies !== '')
                                                    <div class="health-item">
                                                        <span class="health-label">⚠️ Allergies:</span>
                                                        <span class="health-value">{{ $player->Allergies }}</span>
                                                    </div>
                                                @endif

                                                @if ($player->Injuries && $player->Injuries !== 'None' && $player->Injuries !== '')
                                                    <div class="health-item">
                                                        <span class="health-label">🩹 Injuries:</span>
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
                                                            <div class="camp-location">📍 {{ $camp->Location }}</div>
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
                                        <button id="edit-player-btn-{{ $player->Player_ID }}" class="btn-edit-player"
                                            onclick="togglePlayerEdit({{ $player->Player_ID }})">
                                            Edit Player
                                        </button>
                                        <button id="delete-player-btn-{{ $player->Player_ID }}"
                                            class="btn-delete-player"
                                            onclick="confirmDeletePlayer({{ $player->Player_ID }}, '{{ $player->Camper_FirstName }} {{ $player->Camper_LastName }}')">
                                            Delete Player
                                        </button>
                                        <button id="save-player-btn-{{ $player->Player_ID }}" class="btn-save-player"
                                            style="display: none;" onclick="savePlayer({{ $player->Player_ID }})">
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
            @elseif (Auth::user()->parent && !Auth::user()->isCoach())
                <div class="info-card">
                    <h3 class="card-title">My Children</h3>
                    <div class="no-players-message">
                        <p>No children registered yet.</p>
                        <a href="{{ route('registration.form') }}" class="btn-primary">Register a Child for Camp</a>
                    </div>
                </div>
            @endif
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

        .players-grid {
            display: grid;
            gap: 20px;
        }

        .player-card {
            background: #ffffff;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
            transition: all 0.3s ease;
        }

        .player-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: #d1d5db;
        }

        .player-info {
            flex-grow: 1;
        }

        .player-name {
            font-size: 20px;
            margin-bottom: 15px;
            color: #111827;
            border-bottom: 2px solid #0a3f94;
            padding-bottom: 10px;
        }

        .player-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .detail-item {
            font-size: 14px;
            color: #4b5563;
        }

        .detail-item label {
            font-weight: 600;
            color: #374151;
            margin-right: 6px;
        }

        /* Health Information Styles */
        .health-info-section {
            background: #f0f9ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 16px;
            margin: 20px 0;
        }

        .health-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            font-weight: 600;
            color: #1e40af;
        }

        .health-icon {
            font-size: 18px;
        }

        .health-details {
            padding-left: 28px;
        }

        .health-item {
            margin-bottom: 8px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .health-label {
            font-weight: 600;
            color: #3730a3;
            font-size: 13px;
            white-space: nowrap;
        }

        .health-value {
            color: #4b5563;
            font-size: 13px;
            line-height: 1.4;
        }

        .health-item.no-issues {
            color: #059669;
            font-style: italic;
        }

        .player-camps {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }

        .player-camps label {
            font-weight: 600;
            color: #374151;
            display: block;
            margin-bottom: 8px;
        }

        .camps-list {
            margin-left: 28px;
            list-style-type: disc;
            color: #6b7280;
            font-size: 14px;
        }

        .camps-list li {
            margin-bottom: 4px;
        }

        .no-players-message {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }

        .no-players-message p {
            margin-bottom: 20px;
            font-size: 16px;
        }

        @media (max-width: 640px) {
            .player-details {
                grid-template-columns: 1fr;
            }
        }

        /* Compact Health Display */
        .health-info-compact {
            margin: 15px 0;
            position: relative;
        }

        .health-summary {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .health-label-compact {
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .health-pill {
            background: #e0e7ff;
            color: #3730a3;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .health-pill.health-clickable {
            cursor: pointer;
            background: #c7d2fe;
        }

        .health-pill.health-clickable:hover {
            background: #a5b4fc;
        }

        .health-clear {
            color: #059669;
            font-size: 14px;
            font-style: italic;
        }

        .health-dropdown {
            position: absolute;
            top: 100%;
            left: 60px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 10;
            min-width: 250px;
            margin-top: 8px;
        }

        .dropdown-item {
            padding: 6px 0;
            font-size: 13px;
            color: #4b5563;
            border-bottom: 1px solid #f3f4f6;
        }

        .dropdown-item:last-child {
            border-bottom: none;
        }

        /* Collapsible version styling */
        .health-info-section {
            background: #f0f9ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 12px;
            margin: 15px 0;
            transition: background-color 0.2s ease;
        }

        .health-info-section:hover {
            background: #e0f2fe;
        }

        .health-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #1e40af;
            position: relative;
        }

        .health-badge {
            background: #fbbf24;
            color: #92400e;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: auto;
        }

        .health-badge-clear {
            background: #86efac;
            color: #14532d;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: auto;
        }

        .expand-icon {
            margin-left: 8px;
            transition: transform 0.3s ease;
        }

        .expand-icon.rotated {
            transform: rotate(180deg);
        }

        /* Compact Health Display */
        .health-info-compact {
            margin: 15px 0;
            position: relative;
        }

        .health-summary {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .health-label-compact {
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .health-pill {
            background: #e0e7ff;
            color: #3730a3;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .health-pill.health-clickable {
            cursor: pointer;
            background: #c7d2fe;
        }

        .health-pill.health-clickable:hover {
            background: #a5b4fc;
        }

        .health-clear {
            color: #059669;
            font-size: 14px;
            font-style: italic;
        }

        .health-dropdown {
            position: absolute;
            top: 100%;
            left: 60px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 10;
            min-width: 250px;
            margin-top: 8px;
        }

        .dropdown-item {
            padding: 6px 0;
            font-size: 13px;
            color: #4b5563;
            border-bottom: 1px solid #f3f4f6;
        }

        .dropdown-item:last-child {
            border-bottom: none;
        }

        /* Collapsible version styling */
        .health-info-section {
            background: #f0f9ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 12px;
            margin: 15px 0;
        }

        .health-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #1e40af;
            position: relative;
        }

        .health-badge {
            background: #fbbf24;
            color: #92400e;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: auto;
        }

        .health-badge-clear {
            background: #86efac;
            color: #14532d;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: auto;
        }

        .expand-icon {
            margin-left: 8px;
            transition: transform 0.3s ease;
        }

        .expand-icon.rotated {
            transform: rotate(180deg);
        }

        /* Player Edit Mode Styles */
        .player-edit-form {
            padding: 15px 0;
            background: #f9fafb;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }

        .edit-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .edit-field {
            flex: 1;
        }

        .edit-field.full-width {
            flex: 100%;
        }

        .edit-field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }

        .player-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .player-input:focus {
            outline: none;
            border-color: #0a3f94;
            box-shadow: 0 0 0 3px rgba(10, 63, 148, 0.1);
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #374151;
            cursor: pointer;
        }

        .checkbox-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .player-actions {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 10px;
        }

        .btn-edit-player,
        .btn-save-player,
        .btn-cancel-player {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-edit-player {
            background: #0a3f94;
            color: white;
        }

        .btn-edit-player:hover {
            background: #083570;
            transform: translateY(-1px);
        }

        .btn-save-player {
            background: #10b981;
            color: white;
        }

        .btn-save-player:hover {
            background: #059669;
        }

        .btn-cancel-player {
            background: #6b7280;
            color: white;
        }

        .btn-cancel-player:hover {
            background: #4b5563;
        }

        @media (max-width: 640px) {
            .edit-row {
                flex-direction: column;
            }

            .player-actions {
                flex-direction: column;
            }

            .player-actions button {
                width: 100%;
            }
        }

        .player-camps {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }

        .player-camps label {
            font-weight: 600;
            color: #374151;
            display: block;
            margin-bottom: 12px;
            font-size: 15px;
        }

        .camps-list {
            margin-left: 28px;
            list-style: none;
        }

        .camp-item {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 10px;
        }

        .camp-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .camp-dates {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .camp-location {
            font-size: 13px;
            color: #6b7280;
        }

        .no-camps {
            color: #9ca3af;
            font-style: italic;
            font-size: 14px;
            margin-left: 28px;
        }

        .btn-delete-player {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #dc2626;
            color: white;
            display: inline-block;
            /* Ensure it's displayed by default */

        }

        .btn-delete-player:hover {
            background: #b91c1c;
            transform: translateY(-1px);
        }


        /* Delete Confirmation Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            font-size: 20px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 15px;
        }

        .modal-body {
            color: #6b7280;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .modal-body .warning {
            color: #dc2626;
            font-weight: 600;
            margin-top: 10px;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .modal-btn {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal-btn-cancel {
            background: #e5e7eb;
            color: #374151;
        }

        .modal-btn-cancel:hover {
            background: #d1d5db;
        }

        .modal-btn-confirm {
            background: #dc2626;
            color: white;
        }

        .modal-btn-confirm:hover {
            background: #b91c1c;
        }

        /* Add Player Modal Styles */
        .modal-large {
            max-width: 700px;
            width: 90%;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            color: #6b7280;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            color: #111827;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            font-size: 24px;
            font-weight: 600;
            color: #111827;
        }

        .form-section {
            margin-bottom: 25px;
        }

        .form-section h3 {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-field {
            flex: 1;
        }

        .form-field.full-width {
            flex: 100%;
        }

        .form-field label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-field input,
        .form-field select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-field input:focus,
        .form-field select:focus {
            outline: none;
            border-color: #0a3f94;
            box-shadow: 0 0 0 3px rgba(10, 63, 148, 0.1);
        }

        .required {
            color: #dc2626;
        }

        .modal-btn-primary {
            background: #0a3f94;
            color: white;
        }

        .modal-btn-primary:hover {
            background: #083570;
        }

        @media (max-width: 640px) {
            .form-row {
                flex-direction: column;
            }
        }
    </style>

    <script>
        let originalData = {};
        // Player editing functionality
        let originalPlayerData = {};

        let playerToDelete = null;

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

                    // Show success message
                    alert('Player has been removed from your account.');

                    // Reload page to refresh the player list
                    location.reload();
                } else {
                    alert('Error removing player: ' + (data.message || 'Please try again.'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Network error. Please try again.');
            }
        }

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('deleteModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeDeleteModal();
                    }
                });
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
                    // Reload the page to refresh all player data and health information
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

                    // Show success message
                    alert(`${fname} ${lname} has been added successfully!`);

                    // Reload page to show the new player
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

</body>

</html>
