<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generated Teams - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('partials.header', [
        'title' => 'Generated Teams',
        'subtitle' => 'Teams organized with teammate requests considered',
    ])

    <div class="container">
        <div class="action-buttons-container">
            <a href="{{ route('organize-teams') }}" class="btn btn-back">← Back to Organize Teams</a>
            <a href="{{ route('download-teams-excel') }}" class="btn btn-download">Download Excel</a>
        </div>

        <div class="teams-table-container">
            <table class="teams-table">
                <thead>
                    <tr>
                        <th>Team</th>
                        <th>Player Name</th>
                        <th>Age</th>
                        <th>Teammate Requests</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($teamsData as $row)
                        <tr>
                            <td class="team-name">{{ $row['Team'] }}</td>
                            <td class="player-name">{{ $row['Player Name'] }}</td>
                            <td class="age">{{ $row['Age'] ?: 'N/A' }}</td>
                            <td class="teammate-requests">{{ $row['Teammate Requests'] ?: 'None' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #6e84e7 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .main-header {
            background: #0a3f94;
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-content h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .header-content p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .header-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .logout-form {
            display: inline;
        }

        .action-buttons-container {
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .btn-back,
        .btn-download {
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .btn-back:hover,
        .btn-download:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .btn-download {
            background: #28a745;
        }

        .btn-download:hover {
            background: #218838;
        }

        .teams-table-container {
            background: white;
            border-radius: 12px;
            padding: 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .teams-table {
            width: 100%;
            border-collapse: collapse;
        }

        .teams-table th {
            background: #0a3f94;
            color: white;
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 16px;
        }

        .teams-table td {
            padding: 16px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
        }

        .teams-table tbody tr:hover {
            background: #f8f9fa;
        }

        .teams-table tbody tr:last-child td {
            border-bottom: none;
        }

        .team-name {
            font-weight: 600;
            color: #0a3f94;
            width: 15%;
        }

        .player-name {
            font-weight: 500;
            color: #333;
            width: 30%;
        }

        .age {
            font-weight: 500;
            color: #333;
            width: 10%;
            text-align: center;
        }

        .teammate-requests {
            color: #666;
            width: 45%;
            font-size: 14px;
        }

        /* Alternate row colors for teams */
        .teams-table tbody tr:nth-child(odd) .team-name {
            background: #f8f9fa;
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .teams-table-container {
                overflow-x: auto;
            }

            .teams-table {
                min-width: 600px;
            }

            .teams-table th,
            .teams-table td {
                padding: 12px 8px;
                font-size: 14px;
            }

            .header-content h1 {
                font-size: 2rem;
            }

            .action-buttons-container {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-back,
            .btn-download {
                text-align: center;
                margin-bottom: 10px;
            }
        }
    </style>
    @include('partials.footer')
</body>

</html>
