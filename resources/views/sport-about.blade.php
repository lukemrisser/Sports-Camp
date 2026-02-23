<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About {{ $sport->Sport_Name }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

@include('partials.header', [
    'title' => 'About ' . $sport->Sport_Name,
    'subtitle' => 'Learn more about our ' . strtolower($sport->Sport_Name) . ' programs',
])

<div class="container">
    <!-- Navigation -->
    <div class="sport-navigation">
        <a href="{{ route('sport.show', $sport->Sport_ID) }}" class="nav-link">{{ $sport->Sport_Name }} Home</a>
        <a href="{{ route('sport.camps', $sport->Sport_ID) }}" class="nav-link">Register for Camp!</a>
        <a href="{{ route('sport.about', $sport->Sport_ID) }}" class="nav-link active">About Us</a>
        <a href="{{ route('sport.faqs', $sport->Sport_ID) }}" class="nav-link">FAQs</a>
    </div>

    <!-- About Section -->
    @if ($sport->Sport_Description)
        <div class="about-section">
            <h2>About Our {{ $sport->Sport_Name }} Program</h2>
            <p class="sport-description">{{ $sport->Sport_Description }}</p>
        </div>
    @else
        <div class="about-section">
            <h2>About Our {{ $sport->Sport_Name }} Program</h2>
            <p class="sport-description">Welcome to our {{ $sport->Sport_Name }} program! We're dedicated to providing the best sports experience for young athletes.</p>
        </div>
    @endif
</div>

@include('partials.footer')
</body>

</html>