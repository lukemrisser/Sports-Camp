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
                                    <div class="player-name">
                                        <strong>{{ $player->Camper_FirstName }}
                                            {{ $player->Camper_LastName }}</strong>
                                    </div>


                                    <div class="player-details">
                                        <span class="detail-item">
                                            <label>Birthdate:</label>
                                            @if ($player->Birth_Date)
                                                {{ \Carbon\Carbon::parse($player->Birth_Date)->format('M d, Y') }}
                                            @else
                                                Not specified
                                            @endif
                                        </span>
                                        <span class="detail-item">
                                            <label>Gender:</label>
                                            {{ $player->Gender == 'M' ? 'Male' : ($player->Gender == 'F' ? 'Female' : 'Not specified') }}
                                        </span>
                                        <span class="detail-item">
                                            <label>Shirt Size:</label>
                                            {{ $player->Shirt_Size ?: 'Not specified' }}
                                        </span>
                                        @if ($player->School_Grade)
                                            <span class="detail-item">
                                                <label>Grade:</label> {{ $player->School_Grade }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Health Information Section -->
                                    <div class="health-info-section"
                                        onclick="toggleHealthInfo({{ $player->Player_ID }})" style="cursor: pointer;">
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
                                                        <span class="health-label">Asthma:</span>
                                                        <span class="health-value">Yes - requires monitoring</span>
                                                    </div>
                                                @endif

                                                @if ($player->Medications && $player->Medications !== 'None' && $player->Medications !== '')
                                                    <div class="health-item">
                                                        <span class="health-label">Medications:</span>
                                                        <span class="health-value">{{ $player->Medications }}</span>
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
                                                    <li>{{ $camp->Camp_Name }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
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
    </script>
</body>

</html>
