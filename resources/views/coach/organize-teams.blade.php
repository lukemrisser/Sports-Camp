<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Teams - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <header>
        <h1>Falcon Teams</h1>
        <p>Upload a spreadsheet or select a camp to generate teams</p>
    </header>

    <div class="container">
        <div class="upload">
            <h2>Upload Spreadsheet</h2>
            <form action="{{ route('coach.uploadSpreadsheet') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="spreadsheet" accept=".xlsx, .xls" required>
                <button type="submit">Upload and Generate</button>
            </form>
        </div>
       
        {{-- <div class="select_camp">
            <h2>Select Camp</h2>
            <form action="{{ route('coach.selectCamp') }}" method="POST">
                @csrf
                <select name="camp_id">
                    @foreach($camps as $camp)
                        <option value="{{ $camp->camp_id }}">
                            {{ $camp->camp_name }} ({{ $camp->start_date }} - {{ $camp->end_date }})
                        </option>
                    @endforeach
                </select>
                <button type="submit">Select and generate teams</button>
            </form>
        </div> --}}

        <div class="navigation">
            <a href="{{ url('/') }}">← Back to Home</a>
            @auth
                <a href="{{ route('dashboard') }}">My Dashboard</a>
            @endauth
        </div>
    </div>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #6e84e7 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        } */


        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            text-align: center;
            margin: 0 0 40px 0;
            color: white;
            background: #0a3f94;
            width: 100%;
            padding: 20px 0;
            box-sizing: border-box;
        }

        header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        /* Upload Spreadsheet Styles */
        .upload {
            background: #f8fafc;
            border-radius: 12px;
            padding: 32px 24px;
            max-width: 420px;
            margin: 40px auto 0 auto;
            box-shadow: 0 4px 24px rgba(10, 63, 148, 0.08);
            text-align: center;
        }

        .upload h2 {
            color: #0a3f94;
            margin-bottom: 18px;
            font-size: 1.4rem;
            font-weight: 600;
        }

        .upload input[type="file"] {
            margin-bottom: 18px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .upload button[type="submit"] {
            background: #0a3f94;
            color: #fff;
            border: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .upload button[type="submit"]:hover {
            background: #1857c1;
        }

        header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .navigation {
            text-align: center;
            margin-top: 40px;
        }

        .navigation a {
            margin: 0 15px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .navigation a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            header h1 {
                font-size: 2rem;
            }

            .navigation a {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</body>
</html>
