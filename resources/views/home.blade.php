<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Dashboard - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    @include('partials.header', [
        'title' => 'Falcon Teams',
        'subtitle' => 'Choose a registration option below to get started',
    ])

    <div class="container">
        <div class="cards-grid">
            @foreach ($registrationCards as $card)
                <div class="registration-card {{ $card['color'] }}">
                    <div class="card-icon">{{ $card['icon'] }}</div>
                    <h3>{{ $card['title'] }}</h3>
                    <a href="{{ route($card['route'], ['sport' => $card['id']]) }}" class="card-button">
                        Learn More
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    @include('partials.footer')

</body>





</html>
