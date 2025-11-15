<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camp Registrations - {{ config('app.name', 'Falcon Camps') }}</title>
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

        <!-- Camp Selection Section -->
        <div class="camp-selection-card">
            <h2>Select Camp to View Players</h2>
            <form action="{{ route('camp-registrations') }}" method="get" class="camp-form">
                <label for="camp" class="form-label">Choose a camp:</label>
                <select name="camp_id" id="camp" onchange="this.form.submit()" class="form-select">
                    <option value="">-- Choose a camp --</option>
                    @if (isset($camps) && count($camps) > 0)
                        @foreach ($camps as $camp)
                            <option value="{{ $camp->Camp_ID }}"
                                {{ isset($selectedCampId) && $selectedCampId == $camp->Camp_ID ? 'selected' : '' }}>
                                {{ $camp->Camp_Name }}
                            </option>
                        @endforeach
                    @else
                        <option value="" disabled>No camps available</option>
                    @endif
                </select>
            </form>
        </div>

        <!-- Players Table (shown when camp is selected) -->
        @if (isset($players) && $players->isNotEmpty())
            <div class="players-section">
                <h3 class="players-title">Total Players: {{ $players->count() }}</h3>
                <div class="table-container">
                    <table class="players-table">
                        <thead>
                            <tr>
                                <th>Division Name</th>
                                <th>Player First Name</th>
                                <th>Player Last Name</th>
                                <th>Player Gender</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($players as $player)
                                <tr>
                                    <td>{{ $player->Division_Name }}</td>
                                    <td>{{ $player->Camper_FirstName }}</td>
                                    <td>{{ $player->Camper_LastName }}</td>
                                    <td>{{ $player->Gender }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif(isset($selectedCampId) && $selectedCampId)
            <div class="no-players-message">
                <p>No players registered for this camp yet.</p>
            </div>
        @endif

    </div>

    @include('partials.footer')

</body>

</html>
