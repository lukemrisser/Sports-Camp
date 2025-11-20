<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Dashboard - {{ config('app.name', 'Falcon Camps') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    @include('partials.header', [
        'title' => 'Welcome, ' . Auth::user()->coach->Coach_FirstName . '!',
        'title_class' => 'welcome-text',
    ])

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

            <!-- Organize Teams Card -->
            <div class="registration-card blue">
                <div class="card-icon">👥</div>
                <h3>Organize Teams</h3>
                <p>Create balanced teams from your uploaded player data</p>
                <a href="{{ route('organize-teams') }}" class="card-button">Organize Teams</a>
            </div>

            <!-- Create Camp Card -->
            <div class="registration-card blue">
                <div class="card-icon">🏕️</div>
                <h3>Create New Camp</h3>
                <p>Set up a new camp session for players to register</p>
                <a href="{{ route('create-camp') }}" class="card-button">Create Camp</a>
            </div>

            <!-- Edit Camp Card -->
            <div class="registration-card blue">
                <div class="card-icon">✏️</div>
                <h3>Edit Existing Camps</h3>
                <p>Modify details of your existing camp sessions</p>
                <a href="{{ route('edit-camp') }}" class="card-button">Edit Camps</a>
            </div>
        </div>
    </div>
    @include('partials.footer')
</body>

</html>
