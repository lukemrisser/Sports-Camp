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
            <div class="sort-controls">
                <label>Sort by:</label>
                <div class="sort-buttons">
                    <button class="sort-btn active" data-sort="registration">Registration Deadline</button>
                    <button class="sort-btn" data-sort="price">Price</button>
                    <button class="sort-btn" data-sort="discount">Discount Amount</button>
                    <button class="sort-btn" data-sort="discount-expires">Discount Expiration</button>
                </div>
                <button class="sort-direction" data-direction="asc" title="Toggle sort direction">
                    <span class="arrow">↑</span>
                </button>
            </div>
        </div>
        <div class="accordion-list">
            @foreach ($campCards as $camp)
                @php
                    $final_price_calc = $camp['has_discount'] ? $camp['price'] - $camp['discount_amount'] : $camp['price'];
                    $discount_amount = $camp['has_discount'] ? $camp['discount_amount'] : 0;
                    $discount_expires = $camp['has_discount'] ? $camp['discount_expires'] : '';
                @endphp
                <div class="accordion-item" 
                     data-price="{{ $final_price_calc }}" 
                     data-reg-date="{{ $camp['registration_due'] }}"
                     data-discount-amount="{{ $discount_amount }}"
                     data-discount-expires="{{ $discount_expires }}">
                    <div class="accordion-item-wrapper">
                        <button type="button" class="accordion-header" aria-expanded="false">
                            <span class="accordion-title">{{ $camp['title'] }}</span>
                            <span class="accordion-meta">
                                ${{ number_format($camp['price'], 2) }} · {{ $camp['start_date'] }} – {{ $camp['end_date'] }}
                            </span>
                            @if ($camp['has_discount'])
                                <span class="accordion-discount">Save ${{ number_format($camp['discount_amount'], 2) }} - Register by {{ $camp['discount_expires'] }}</span>
                            @endif
                            <span class="accordion-icon">▾</span>
                        </button>
                        <a href="{{ route('registration.form', ['camp' => $camp['id']]) }}" class="register-btn-header" onclick="event.stopPropagation()">
                            Register
                        </a>
                    </div>
                    <div class="accordion-body is-collapsed">
                        <div class="accordion-details">
                            <p class="camp-dates">
                                <strong>Who:</strong> {{ $camp['gender'] }} · Ages {{ $camp['age_range'] }}
                            </p>
                            <p class="camp-description">
                                <strong>What:</strong> {{ $camp['description'] }}
                            </p>
                            <p class="camp-dates">
                                <strong>When:</strong> {{ $camp['start_date'] }} - {{ $camp['end_date'] }}
                            </p>
                            <p class="camp-location">
                                <strong>Where:</strong> {{ $camp['location_name'] }}<br>
                                <span class="location-address">{{ $camp['street_address'] }}, {{ $camp['city'] }},
                                    {{ $camp['state'] }} {{ $camp['zip_code'] }}</span>
                            </p>
                            @if ($camp['has_discount'])
                                <p class="discount-info">
                                    <strong>Early Bird Discount:</strong> Save ${{ number_format($camp['discount_amount'], 2) }}
                                    if you register by {{ $camp['discount_expires'] }}
                                </p>
                                <p class="camp-price original-price">
                                    <strong>Original Price:</strong> <span
                                        class="strikethrough">${{ number_format($camp['price'], 2) }}</span>
                                </p>
                                @php
                                    $final_price = $camp['price'] - $camp['discount_amount'];
                                @endphp
                                <p class="camp-price discounted-price">
                                    <strong>Discounted Price:</strong> <span
                                        class="discount-highlight">${{ number_format($final_price, 2) }}</span>
                                </p>
                            @else
                                <p class="camp-price">
                                    <strong>How much:</strong> ${{ number_format($camp['price'], 2) }}
                                </p>
                            @endif
                            <p class="registration-due">
                                <strong>Register By:</strong> {{ $camp['registration_due'] }}
                            </p>
                        </div>
                        <a href="{{ route('registration.form', ['camp' => $camp['id']]) }}" class="card-button">
                            Register Now
                        </a>
                    </div>
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
        padding: 8px 16px;
        text-decoration: none;
        color: #6b7280;
        font-weight: 500;
        font-size: 0.85rem;
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
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0 0 20px 0;
    }

    .sort-controls {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin-top: 15px;
        flex-wrap: wrap;
    }

    .sort-controls label {
        color: #4b5563;
        font-weight: 500;
        font-size: 0.8rem;
    }

    .sort-buttons {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .sort-btn {
        padding: 6px 12px;
        border: 2px solid #d1d5db;
        border-radius: 6px;
        background: white;
        color: #4b5563;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .sort-btn:hover {
        border-color: #3b82f6;
        color: #3b82f6;
    }

    .sort-btn.active {
        background: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }

    .sort-direction {
        padding: 6px 10px;
        border: 2px solid #3b82f6;
        border-radius: 6px;
        background: white;
        color: #3b82f6;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        min-width: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sort-direction:hover {
        background: #eff6ff;
    }

    .sort-direction .arrow {
        display: inline-block;
        transition: transform 0.2s ease;
    }

    .sort-direction[data-direction="desc"] .arrow {
        transform: rotate(180deg);
    }

    .accordion-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .accordion-item {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    .accordion-item-wrapper {
        display: flex;
        align-items: center;
        background: #f8fafc;
    }

    .accordion-header {
        flex: 1;
        background: transparent;
        border: none;
        padding: 12px 16px;
        display: grid;
        grid-template-columns: 1fr auto;
        grid-template-areas:
            "title icon"
            "meta icon"
            "discount icon";
        gap: 4px 10px;
        text-align: left;
        cursor: pointer;
        font-weight: 600;
        color: #1f2937;
    }

    .register-btn-header {
        padding: 8px 16px;
        background: #3b82f6;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-right: 12px;
        transition: all 0.2s ease;
        white-space: nowrap;
        align-self: center;
    }

    .register-btn-header:hover {
        background: #2563eb;
        transform: translateY(-1px);
    }

    .accordion-title {
        grid-area: title;
        font-size: 0.95rem;
    }

    .accordion-meta {
        grid-area: meta;
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 500;
    }

    .accordion-discount {
        grid-area: discount;
        font-size: 0.8rem;
        color: #15803d;
        font-weight: 600;
    }

    .accordion-icon {
        grid-area: icon;
        align-self: center;
        justify-self: end;
        transition: transform 0.2s ease;
    }

    .accordion-header[aria-expanded="true"] .accordion-icon {
        transform: rotate(180deg);
    }

    .accordion-body {
        padding: 12px 16px 16px 16px;
        border-top: 1px solid #e5e7eb;
    }

    .accordion-body.is-collapsed {
        display: none;
    }

    .accordion-details {
        text-align: left;
        margin: 6px 0 12px 0;
        color: #4b5563;
        font-size: 0.8rem;
    }

    .accordion-details .camp-description,
    .accordion-details .camp-dates,
    .accordion-details .camp-location,
    .accordion-details .camp-price {
        color: #4b5563;
        font-size: 0.8rem;
    }

    .accordion-details .discounted-price .discount-highlight {
        color: inherit;
        font-weight: 600;
        font-size: 0.8rem;
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
        font-size: 0.8rem;
    }

    .camp-location {
        color: #4b5563;
    }

    .location-address {
        font-size: 0.75rem;
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
        font-size: 1em;
    }

    .discount-info {
        color: #15803d;
        font-size: 0.8rem;
        margin: 8px 0;
    }

    .registration-due {
        color: #dc2626;
        font-weight: normal;
        font-size: 0.9rem;
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Accordion functionality
        document.querySelectorAll('.accordion-header').forEach((button) => {
            button.addEventListener('click', (e) => {
                const item = button.closest('.accordion-item');
                const body = item ? item.querySelector('.accordion-body') : null;
                if (!body) return;
                const isCollapsed = body.classList.toggle('is-collapsed');
                button.setAttribute('aria-expanded', String(!isCollapsed));
            });
        });

        // Sorting functionality
        const sortButtons = document.querySelectorAll('.sort-btn');
        const sortDirection = document.querySelector('.sort-direction');
        const accordionList = document.querySelector('.accordion-list');
        let currentSort = 'registration';
        let currentDirection = 'asc';

        const performSort = () => {
            if (!accordionList) return;
            
            const items = Array.from(accordionList.querySelectorAll('.accordion-item'));
            
            items.sort((a, b) => {
                let aValue, bValue;
                
                switch(currentSort) {
                    case 'price':
                        aValue = parseFloat(a.dataset.price) || 0;
                        bValue = parseFloat(b.dataset.price) || 0;
                        break;
                    case 'registration':
                        aValue = new Date(a.dataset.regDate);
                        bValue = new Date(b.dataset.regDate);
                        break;
                    case 'discount':
                        aValue = parseFloat(a.dataset.discountAmount) || 0;
                        bValue = parseFloat(b.dataset.discountAmount) || 0;
                        break;
                    case 'discount-expires':
                        aValue = a.dataset.discountExpires ? new Date(a.dataset.discountExpires) : new Date('9999-12-31');
                        bValue = b.dataset.discountExpires ? new Date(b.dataset.discountExpires) : new Date('9999-12-31');
                        break;
                    default:
                        return 0;
                }
                
                if (currentDirection === 'asc') {
                    return aValue > bValue ? 1 : aValue < bValue ? -1 : 0;
                } else {
                    return aValue < bValue ? 1 : aValue > bValue ? -1 : 0;
                }
            });
            
            items.forEach(item => accordionList.appendChild(item));
        };

        // Category button clicks
        sortButtons.forEach(button => {
            button.addEventListener('click', () => {
                sortButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                currentSort = button.dataset.sort;
                performSort();
            });
        });

        // Direction toggle
        if (sortDirection) {
            sortDirection.addEventListener('click', () => {
                currentDirection = currentDirection === 'asc' ? 'desc' : 'asc';
                sortDirection.dataset.direction = currentDirection;
                performSort();
            });
        }

        // Initial sort
        performSort();
    });
</script>

@include('partials.footer')
</body>

</html>