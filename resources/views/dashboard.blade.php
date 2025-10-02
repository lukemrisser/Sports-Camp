<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ config('app.name', 'Falcon Teams') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-wrapper">
            <!-- Welcome Card -->
            <div class="welcome-card">
                <div class="welcome-content">
                    <h1 class="welcome-title">Welcome back, {{ Auth::user()->name }}!</h1>
                    <p class="welcome-subtitle">Here's your account information</p>
                </div>
            </div>

            <!-- User Information Card -->
            <div class="info-card">
                <h3 class="card-title">Personal Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label class="info-label">Full Name</label>
                        <p class="info-value">{{ Auth::user()->name }}</p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Email Address</label>
                        <p class="info-value">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Account Type</label>
                        <p class="info-value">
                            @if(Auth::user()->isCoach())
                                <span class="badge badge-coach">Coach</span>
                                @if(Auth::user()->isCoachAdmin())
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
            </div>

            <!-- Coach-specific Information -->
            @if(Auth::user()->isCoach())
            <div class="info-card">
                <h3 class="card-title">Coach Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label class="info-label">Sport/Camp</label>
                        <p class="info-value">{{ ucfirst(str_replace('_', ' ', Auth::user()->coach->sport)) }}</p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Coach Name</label>
                        <p class="info-value">{{ Auth::user()->coach->Coach_FirstName }} {{ Auth::user()->coach->Coach_LastName }}</p>
                    </div>
                    @if(Auth::user()->isCoachAdmin())
                    <div class="info-item">
                        <label class="info-label">Administrative Access</label>
                        <p class="info-value">Full Access Granted</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="info-card">
                <h3 class="card-title">Quick Actions</h3>
                <div class="action-buttons">
                    @if(Auth::user()->isCoach())
                        <a href="{{ route('coach-dashboard') }}" class="action-btn btn-primary">
                            Go to Coach Dashboard
                        </a>
                        <a href="{{ route('organize-teams') }}" class="action-btn btn-secondary">
                            Organize Teams
                        </a>
                    @else
                        <a href="{{ route('registration') }}" class="action-btn btn-primary">
                            Register for Camp
                        </a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="action-btn btn-secondary">
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
