<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Dashboard - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <header class="main-header">
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
                    @if (Auth::user()->isCoach())
                        <a href="{{ route('coach-dashboard') }}" class="header-btn dashboard-btn">Coach Dashboard</a>
                    @endif
                    <a href="{{ route('dashboard') }}" class="header-btn login-btn">Account</a>
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="header-btn logout-btn">Logout</button>
                    </form>
                @endguest
            </div>
        </div>
    </header>

    <div class="container">
        <div class="cards-grid">
            @foreach ($registrationCards as $card)
                <div class="registration-card {{ $card['color'] }}">
                    <div class="card-icon">{{ $card['icon'] }}</div>
                    <h3>{{ $card['title'] }}</h3>
                    <p>{{ $card['description'] }}</p>
                    <a href="{{ route($card['route'], ['camp' => $card['id']]) }}" class="card-button">
                        Register Now
                    </a>
                </div>
            @endforeach
        </div>
    </div>

</body>

</html>
