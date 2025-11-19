<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sport->Sport_Name }} Camps - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

@include('partials.header', [
    'title' => $sport->Sport_Name . ' Camps',
    'subtitle' => 'Choose from our available ' . strtolower($sport->Sport_Name) . ' camps below',
])

<div class="container">
    <!-- Navigation -->
    <div class="sport-navigation">
        <a href="{{ route('sport.show', $sport->Sport_ID) }}" class="nav-link">{{ $sport->Sport_Name }} Home</a>
        <a href="{{ route('sport.camps', $sport->Sport_ID) }}" class="nav-link active">Register for Camp!</a>
        <a href="{{ route('sport.about', $sport->Sport_ID) }}" class="nav-link">About Us</a>
        <a href="{{ route('sport.faqs', $sport->Sport_ID) }}" class="nav-link">FAQs</a>
    </div>

    @if (count($campCards) > 0)
        <div class="camps-section">
            <h2>Available {{ $sport->Sport_Name }} Camps</h2>
        </div>
        <div class="cards-grid">
            @foreach ($campCards as $camp)
                <div class="registration-card blue">
                    <div class="card-icon">🏕️</div>
                    <h3>{{ $camp['title'] }}</h3>
                    <div class="camp-details">
                        <p class="camp-description">
                            <strong>Details:</strong> {{ $camp['description'] }}
                        </p>
                        <p class="camp-dates">
                            <strong>Date:</strong> {{ $camp['start_date'] }} - {{ $camp['end_date'] }}
                        </p>
                        <p class="camp-location">
                            <strong>Location:</strong> {{ $camp['location_name'] }}<br>
                            <span class="location-address">{{ $camp['street_address'] }}, {{ $camp['city'] }},
                                {{ $camp['state'] }} {{ $camp['zip_code'] }}</span>
                        </p>
                        @if ($camp['has_discount'])
                            <p class="discount-info">
                                <strong>Early Bird Discount! Save ${{ number_format($camp['discount_amount'], 2) }}
                                    if you register by {{ $camp['discount_expires'] }}</strong>
                            </p>
                            <p class="camp-price original-price">
                                <strong>Original Price:</strong> <span
                                    class="strikethrough">${{ number_format($camp['price'], 2) }}</span>
                            </p>
                            <p class="camp-price discounted-price">
                                <strong>Discounted Price:</strong> <span
                                    class="discount-highlight">${{ number_format($camp['discounted_price'], 2) }}</span>
                            </p>
                        @else
                            <p class="camp-price">
                                <strong>Price:</strong> ${{ number_format($camp['price'], 2) }}
                            </p>
                        @endif
                        <p class="registration-due">
                            <strong>Register By:</strong> {{ $camp['registration_due'] }}
                        </p>
                    </div>
                    <a href="{{ route($camp['route'], ['camp' => $camp['id']]) }}" class="card-button">
                        Register Now
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="no-camps-message">
            <h3>No camps currently available</h3>
            <p>There are no {{ strtolower($sport->Sport_Name) }} camps accepting registrations at this time.</p>
            <a href="{{ route('home') }}" class="card-button">← Back to Home</a>
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

    /* Camps Section */
    .camps-section {
        text-align: center;
        margin-bottom: 30px;
    }

    .camps-section h2 {
        color: #1f2937;
        font-size: 2rem;
        font-weight: 600;
        margin: 0;
    }

    .camp-details {
        text-align: left;
        margin: 15px 0;
    }

    .camp-description {
        margin-bottom: 10px;
        color: #666;
    }

    .camp-dates,
    .camp-price,
    .registration-due,
    .camp-location,
    .camp-capacity {
        margin: 5px 0;
        font-size: 0.9em;
    }

    .camp-location {
        color: #4b5563;
    }

    .location-address {
        font-size: 0.85em;
        color: #6b7280;
        font-style: italic;
    }

    .camp-capacity {
        color: #7c3aed;
        font-weight: 500;
    }

    .original-price .strikethrough {
        text-decoration: line-through;
        color: #999;
    }

    .discounted-price .discount-highlight {
        color: #16a34a;
        font-weight: bold;
        font-size: 1.1em;
    }

    .discount-info {
        background: #dcfce7;
        color: #15803d;
        padding: 8px;
        border-radius: 6px;
        font-size: 0.85em;
        margin: 8px 0;
        border-left: 3px solid #16a34a;
    }

    .registration-due {
        color: #dc2626;
        font-weight: 500;
    }

    .no-camps-message {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        margin: 0 auto;
    }

    .no-camps-message h3 {
        color: #333;
        margin-bottom: 15px;
    }

    .no-camps-message p {
        color: #666;
        margin-bottom: 25px;
    }

    .no-camps-message .card-button {
        background-color: #3b82f6;
        color: white;
    }

    .no-camps-message .card-button:hover {
        background-color: #2563eb;
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

        .camps-section h2 {
            font-size: 1.5rem;
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
    }
</style>

@include('partials.footer')
</body>

</html>