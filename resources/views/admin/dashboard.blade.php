<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - {{ config('app.name', 'Falcon Teams') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <header class="main-header">
        <div class="header-container">
            <div class="header-content">
                <h1 class="welcome-title">Admin Dashboard</h1>
                <p class="welcome-subtitle">Welcome back, {{ Auth::user()->name }}!</p>
            </div>

            <div class="header-buttons">
                <a href="{{ route('coach-dashboard') }}" class="header-btn dashboard-btn">Coach Dashboard</a>
                <a href="{{ route('dashboard') }}" class="header-btn login-btn">Account</a>
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="header-btn logout-btn">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Action Cards Grid -->
        <div class="cards-grid">
            <!-- Admin Finances Card -->
            <div class="registration-card green">
                <div class="card-icon">💰</div>
                <h3>Admin Finances</h3>
                <p>View financial reports and manage camp revenue</p>
                <a href="{{ route('admin.finances') }}" class="card-button">View Finances</a>
            </div>

            <!-- Invite Coach Card -->
            <div class="registration-card blue">
                <div class="card-icon">👨‍🏫</div>
                <h3>Invite Coach</h3>
                <p>Send invitations to new coaches and manage permissions</p>
                <a href="{{ route('admin.invite-coach') }}" class="card-button">Invite Coach</a>
            </div>

            <!-- Manage Coaches Card -->
            <div class="registration-card orange">
                <div class="card-icon">👥</div>
                <h3>Manage Coaches</h3>
                <p>View, edit, and manage existing coach accounts</p>
                <a href="{{ route('admin.manage-coaches') }}" class="card-button">Manage Coaches</a>
            </div>
        </div>
    </div>
</body>

</html>