<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Teams - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    @include('partials.header', [
        'title' => 'Falcon Teams',
        'subtitle' => 'Upload a spreadsheet or select a camp to generate teams',
    ])

    <div class="container">
        <div class="cards-container">
            <div class="card">
                <h2>Upload Spreadsheet</h2>
                <form action="{{ route('upload-spreadsheet') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="spreadsheet" accept=".xlsx, .xls" required class="file-input">
                    <input type="number" name="num_teams" min="1" placeholder="Number of teams" required
                        class="form-input" />
                    <button type="submit" class="btn-primary">Upload and Generate Teams</button>
                </form>
            </div>

            <div class="divider">
                <span>OR</span>
            </div>

            <div class="card">
                <h2>Select Camp</h2>
                <form action="{{ route('select-camp') }}" method="POST">
                    @csrf
                    <select name="camp_id" class="form-input">
                        @foreach ($camps as $camp)
                            <option value="{{ $camp->Camp_ID }}">
                                {{ $camp->Camp_Name }} ({{ \Carbon\Carbon::parse($camp->Start_Date)->format('m/d/y') }}
                                -
                                {{ \Carbon\Carbon::parse($camp->End_Date)->format('m/d/y') }})
                            </option>
                        @endforeach
                    </select>
                    <input type="number" name="num_teams" min="1" placeholder="Number of teams" required
                        class="form-input" />
                    <button type="submit" class="btn-primary">Select and Generate Teams</button>
                </form>
            </div>
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

        .cards-container {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 30px;
            align-items: start;
            max-width: 1000px;
            margin: 40px auto 0 auto;
        }

        .card {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 32px 24px;
            box-shadow: 0 4px 24px rgba(10, 63, 148, 0.08);
            text-align: center;
            min-height: 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .card h2 {
            color: #0a3f94;
            margin-bottom: 24px;
            font-size: 1.4rem;
            font-weight: 600;
        }

        .form-input {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 18px;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #0a3f94;
            box-shadow: 0 0 0 2px rgba(10, 63, 148, 0.1);
        }

        .file-input {
            margin-bottom: 18px;
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
        }

        .btn-primary {
            background: #0a3f94;
            color: #fff;
            border: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            width: 100%;
        }

        .btn-primary:hover {
            background: #1857c1;
            transform: translateY(-1px);
        }

        .divider {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            min-height: 200px;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 2px;
            height: 80px;
            background: linear-gradient(to bottom, transparent, rgba(10, 63, 148, 0.3), transparent);
        }

        .divider span {
            background: linear-gradient(135deg, #0a3f94, #1857c1);
            color: white;
            padding: 14px 22px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 1px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            position: relative;
            z-index: 1;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
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

            .cards-container {
                grid-template-columns: 1fr;
                gap: 20px;
                margin: 20px auto 0 auto;
            }

            .divider {
                min-height: auto;
                padding: 15px 0;
            }

            .divider::before {
                width: 60px;
                height: 2px;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: linear-gradient(to right, transparent, rgba(10, 63, 148, 0.3), transparent);
            }

            .divider span {
                padding: 10px 18px;
                font-size: 0.9rem;
            }

            .card {
                padding: 24px 16px;
            }

            .navigation a {
                display: block;
                margin: 10px 0;
            }
        }
    </style>

    @include('partials.footer')
</body>

</html>
