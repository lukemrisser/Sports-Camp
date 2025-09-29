<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        @vite(['resources/css/coach-dashboard.css'])
        <title>Coach Dashboard - Falcon Camps</title>
    </head>

    <body>
        <header>
            <h1>Coach Dashboard</h1>
            <a href="#" class="btn-organize">Organize Teams</a>
        </header>

        <main>
            {{-- Camps Drop-down Menu --}}
            <form action="{{ route('coach-dashboard') }}" method="get">
                <label for="camp">Select Camp:</label>
                <select name="camp_id" id="camp" onchange="this.form.submit()">
                    <option value="">-- Choose a camp --</option>
                    @foreach($camps as $camp)
                        <option value="{{ $camp->Camp_ID }}" {{ $selectedCampId == $camp->Camp_ID ? 'selected' : '' }}>
                            {{ $camp->Camp_Name }}
                        </option>
                    @endforeach
                </select>
            </form>

            {{-- Players Table --}}
            @if($players->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th>Division Name</th>
                            <th>Player First Name</th>
                            <th>Player Last Name</th>
                            <th>Player Gender</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($players as $player)
                            <tr>
                                <td>{{ $player->Division_Name }}</td>
                                <td>{{ $player->Camper_FirstName }}</td>
                                <td>{{ $player->Camper_LastName }}</td>
                                <td>{{ $player->Gender }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @elseif($selectedCampId)
                <p>No players registered for this camp yet.</p>
            @endif
        </main>
    </body>
</html>