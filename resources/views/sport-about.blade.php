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
        <a href="{{ route('sport.show', $sport->Sport_ID) }}" class="nav-link">Home</a>
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

<style>
    /* Sport Navigation */
    .sport-navigation {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 40px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        flex-wrap: wrap;
    }

    .nav-link {
        padding: 12px 24px;
        text-decoration: none;
        color: #6b7280;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .nav-link:hover {
        color: #3b82f6;
        background: #f8fafc;
        border-color: #e5e7eb;
    }

    .nav-link.active {
        color: white;
        background: #3b82f6;
        border-color: #3b82f6;
    }

    /* About Section */
    .about-section {
        background: white;
        border-radius: 12px;
        padding: 60px 40px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
        max-width: 900px;
        margin: 0 auto;
    }

    .about-section h2 {
        color: #1f2937;
        margin-bottom: 30px;
        font-size: 2.2rem;
        font-weight: 600;
    }

    .about-section .sport-description {
        color: #4b5563;
        font-size: 1.2rem;
        line-height: 1.7;
        margin: 0 auto;
        max-width: 800px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .sport-navigation {
            gap: 10px;
            padding: 15px;
        }

        .nav-link {
            padding: 10px 16px;
            font-size: 0.9rem;
        }

        .about-section {
            padding: 40px 20px;
            margin: 0 10px;
        }

        .about-section h2 {
            font-size: 1.8rem;
        }

        .about-section .sport-description {
            font-size: 1.1rem;
        }
    }

    @media (max-width: 480px) {
        .sport-navigation {
            flex-direction: column;
            align-items: center;
        }

        .nav-link {
            width: 100%;
            max-width: 200px;
            text-align: center;
        }

        .about-section {
            padding: 30px 15px;
        }

        .about-section h2 {
            font-size: 1.6rem;
        }

        .about-section .sport-description {
            font-size: 1rem;
        }
    }
</style>

@include('partials.footer')
</body>

</html>