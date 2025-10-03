<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Dashboard - {{ config('app.name', 'Falcon Camps') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <header class="main-header">
        <div class="header-container">
            <div class="header-content">
                <h1 class="welcome-text">
                    Welcome, {{ Auth::user()->coach->Coach_FirstName }}!
                    @if (Auth::user()->isCoachAdmin())
                        <span class="admin-badge">Admin</span>
                    @endif
                </h1>
            </div>

            <div class="header-buttons">
                <a href="{{ route('home') }}" class="header-btn login-btn">← Home</a>
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="header-btn logout-btn">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="session-status">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif


        <!-- Action Cards Grid -->
        <div class="cards-grid">
            <!-- Camp Registrations Card -->
            <div class="registration-card blue">
                <div class="card-icon">📋</div>
                <h3>Camp Registrations</h3>
                <p>View and manage all registrations for your camps</p>
                <a href="{{ route('camp-registrations') }}" class="card-button">View Registrations</a>
            </div>

            <!-- Upload Spreadsheet Card -->
            <div class="registration-card green">
                <div class="card-icon">📊</div>
                <h3>Upload Spreadsheet</h3>
                <p>Upload a spreadsheet with player information to organize into teams</p>
                <form action="{{ route('upload-spreadsheet') }}" method="POST" enctype="multipart/form-data"
                    class="card-form">
                    @csrf
                    <div class="form-group">
                        <input type="file" name="spreadsheet" accept=".xlsx,.xls,.csv" required class="form-input">
                    </div>
                    <button type="submit" class="card-button">Upload File</button>
                </form>
            </div>

            <!-- Organize Teams Card -->
            <div class="registration-card orange">
                <div class="card-icon">👥</div>
                <h3>Organize Teams</h3>
                <p>Create balanced teams from your uploaded player data</p>
                <a href="{{ route('organize-teams') }}" class="card-button">Organize Teams</a>
            </div>

            <!-- Admin Functions (if admin) -->
            @if (Auth::user()->isCoachAdmin())
                <div class="registration-card purple">
                    <div class="card-icon">⚙️</div>
                    <h3>Admin Functions</h3>
                    <p>Access administrative tools and settings</p>
                    <a href="#" class="card-button">Admin Panel</a>
                </div>
            @endif
        </div>

        <div class="navigation">
            <a href="{{ url('/') }}">← Back to Home</a>
        </div>
    </div>

</body>

</html>
