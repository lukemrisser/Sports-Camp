<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Dashboard - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <header>
        <div class="header-container">
            <div class="header-content">
                <h1>Falcon Teams</h1>
                <p>Choose a registration option below to get started</p>
            </div>

            <div class="header-buttons">
                @guest
                    <a href="{{ route('login') }}" class="header-btn login-btn">Login</a>
                    <a href="{{ route('register') }}" class="header-btn register-btn">Register</a>
                @else
                    <span class="welcome-text">Welcome, {{ Auth::user()->name }}!</span>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="header-btn logout-btn">Logout</button>
                    </form>
                @endguest
            </div>
        </div>
    </header>

    <div class="container">
        <div class="cards-grid">
            @foreach($registrationCards as $card)
                <div class="registration-card {{ $card['color'] }}">
                    <div class="card-icon">{{ $card['icon'] }}</div>
                    <h3>{{ $card['title'] }}</h3>
                    <p>{{ $card['description'] }}</p>
                    <a href="{{ route($card['route']) }}" class="card-button">
                        Register Now
                    </a>
                </div>
            @endforeach
        </div>

        <div class="navigation">
            <a href="{{ url('/') }}">← Back to Home</a>
            @auth
                <a href="{{ route('coach-dashboard') }}">My Dashboard</a>
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
            background: #0a3f94;
            width: 100%;
            padding: 20px 0;
            box-sizing: border-box;
            color: white;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .header-content {
            text-align: left;
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
            align-items: center;
            gap: 15px;
        }

        .header-btn {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .login-btn {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .login-btn:hover {
            background: white;
            color: #0a3f94;
            transform: translateY(-2px);
        }

        .register-btn {
            background: #fbbf24;
            color: #0a3f94;
        }

        .register-btn:hover {
            background: #f59e0b;
            transform: translateY(-2px);
        }

        .logout-btn {
            background: #ef4444;
            color: white;
        }

        .logout-btn:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        .welcome-text {
            color: white;
            font-weight: 500;
            margin-right: 10px;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .registration-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .registration-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--card-color), var(--card-color-light));
        }

        .registration-card.blue {
            --card-color: #3b82f6;
            --card-color-light: #60a5fa;
        }

        .registration-card.green {
            --card-color: #10b981;
            --card-color-light: #34d399;
        }

        .registration-card.purple {
            --card-color: #8b5cf6;
            --card-color-light: #a78bfa;
        }

        .registration-card.orange {
            --card-color: #f59e0b;
            --card-color-light: #fbbf24;
        }

        .registration-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .card-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .registration-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #1f2937;
            font-weight: 600;
        }

        .registration-card p {
            color: #6b7280;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .card-button {
            display: inline-block;
            background: var(--card-color);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .card-button:hover {
            background: var(--card-color-light);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
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

            .header-container {
                flex-direction: column;
                text-align: center;
                gap: 20px;
                padding: 0 10px;
            }

            .header-content h1 {
                font-size: 2rem;
            }

            .header-buttons {
                justify-content: center;
                flex-wrap: wrap;
            }

            .cards-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .registration-card {
                padding: 20px;
            }

            .navigation a {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</body>
</html>
