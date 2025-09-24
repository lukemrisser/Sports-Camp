<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Dashboard - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="gradient-bg">

    <header class="main-header">
        <div class="header-container">
            <div class="header-content">
                <h1>Coach Dashboard</h1>
                <p>Welcome back, {{ $user->name }}!</p>
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
        @if(session('success'))
            <div class="session-status">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="cards-grid">
            <!-- Camp Registrations Card -->
            <div class="registration-card blue">
                <div class="card-icon">📋</div>
                <h3>Camp Registrations</h3>
                <p>View and manage all registrations for your camps</p>
                <a href="#" class="card-button">View Registrations</a>
            </div>

            <!-- Upload Spreadsheet Card -->
            <div class="registration-card green">
                <div class="card-icon">📊</div>
                <h3>Upload Spreadsheet</h3>
                <p>Upload a spreadsheet with player information to organize into teams</p>
                <form action="{{ route('upload-spreadsheet') }}" method="POST" enctype="multipart/form-data" class="card-form">
                    @csrf
                    <div class="form-group">
                        <input type="file" name="spreadsheet" accept=".xlsx,.xls,.csv" required class="form-input">
                    </div>
                    <button type="submit" class="card-button">Upload File</button>
                </form>
            </div>

            <!-- Select Camp Card -->
            <div class="registration-card purple">
                <div class="card-icon">🏕️</div>
                <h3>Select Camp</h3>
                <p>Choose which camp you want to organize teams for</p>
                <form action="{{ route('select-camp') }}" method="POST" class="card-form">
                    @csrf
                    <div class="form-group">
                        <select name="camp_id" required class="form-input">
                            <option value="">Choose a camp...</option>
                            <!-- You'll need to populate this with actual camps -->
                            <option value="1">Summer Camp 2024</option>
                            <option value="2">Spring Training 2024</option>
                        </select>
                    </div>
                    <button type="submit" class="card-button">Select Camp</button>
                </form>
            </div>

            <!-- Organize Teams Card -->
            <div class="registration-card orange">
                <div class="card-icon">👥</div>
                <h3>Organize Teams</h3>
                <p>Create balanced teams from your uploaded player data</p>
                <a href="#" class="card-button">Organize Teams</a>
            </div>
        </div>

        <!-- Coming Soon Section -->
        <div class="coming-soon-card">
            <h2 class="coming-soon-title">Coming Soon Features:</h2>
            <div class="coming-soon-content">
                <ul class="coming-soon-list">
                    <li>Filter registrations by specific camps</li>
                    <li>Export registration data to Excel/CSV</li>
                    <li>Contact information management for parents</li>
                    <li>Team balance analysis and reporting</li>
                    <li>Email communication tools</li>
                </ul>
            </div>
        </div>
    </div>

</body>
</html>
