<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Finances - {{ config('app.name', 'Falcon Teams') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        <!-- Content will be added here later -->
    </div>
</body>

</html>