<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - {{ config('app.name', 'Falcon Teams') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('partials.header', [
        'title' => 'Admin Dashboard',
        'subtitle' => 'Welcome back, ' . Auth::user()->name . '!',
        'title_class' => 'welcome-title',
    ])

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

            <!-- Manage Sports Card -->
            <div class="registration-card purple">
                <div class="card-icon">⚽</div>
                <h3>Manage Sports</h3>
                <p>Add, edit, and delete sports available for camps</p>
                <a href="{{ route('admin.manage-sports') }}" class="card-button">Manage Sports</a>
            </div>
        </div>
    </div>

    @include('partials.footer')
</body>

</html>
