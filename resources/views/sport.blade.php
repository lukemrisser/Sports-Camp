<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sport->Sport_Name }} Camps - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

@php
    use Illuminate\Support\Str;
@endphp

@include('partials.header', [
    'title' => $sport->Sport_Name . ' Camps',
    'subtitle' => 'Choose from our available ' . strtolower($sport->Sport_Name) . ' camps below',
])    <div class="container">
        <!-- About Section -->
        @if ($sport->Sport_Description)
            <div class="about-section">
                <h2>About Us</h2>
                <p class="sport-description">{{ $sport->Sport_Description }}</p>
            </div>
        @endif

        <!-- Gallery Section -->
        @if ($sport->galleryImages && $sport->galleryImages->count() > 0)
            <div class="gallery-section">
                <div class="gallery-container">
                    <div class="gallery-slider">
                        @foreach ($sport->galleryImages as $index => $image)
                            <div class="gallery-slide {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}">
                                <div class="gallery-image-wrapper">
                                    <img src="{{ asset('storage/' . $image->Image_path) }}" 
                                         alt="{{ $image->Image_Title }}" 
                                         class="gallery-image">
                                    <div class="gallery-overlay">
                                        <div class="gallery-overlay-content">
                                            <h3>{{ $image->Image_Title }}</h3>
                                            @if ($image->Image_Text)
                                                <p>{{ $image->Image_Text }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if ($sport->galleryImages->count() > 1)
                        <div class="gallery-navigation">
                            <button class="gallery-nav-btn prev-btn" onclick="changeGallerySlide(-1)">&lt;</button>
                            <div class="gallery-dots">
                                @foreach ($sport->galleryImages as $index => $image)
                                    <span class="gallery-dot {{ $index === 0 ? 'active' : '' }}" onclick="goToGallerySlide({{ $index }})"></span>
                                @endforeach
                            </div>
                            <button class="gallery-nav-btn next-btn" onclick="changeGallerySlide(1)">&gt;</button>
                        </div>
                    @endif
                </div>
            </div>
        @endif

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
                                    <strong>Early Bird Discout! Save ${{ number_format($camp['discount_amount'], 2) }}
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

        <!-- FAQ Section -->
        @if ($sport->faqs && $sport->faqs->count() > 0)
            <div class="faq-section">
                <h2>Frequently Asked Questions</h2>
                <div class="faq-container">
                    @foreach ($sport->faqs as $index => $faq)
                        <div class="faq-item" data-faq="{{ $index }}">
                            <div class="faq-question" onclick="toggleFaq({{ $index }})">
                                <h3>{{ $faq->Question }}</h3>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer" id="faq-{{ $index }}">
                                <p>{{ $faq->Answer }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Sponsors Section -->
        @if ($sport->sponsors && $sport->sponsors->count() > 0)
            <div class="sponsors-section">
                <h2>Our {{ $sport->Sport_Name }} Partners</h2>
                <div class="sponsors-container">
                    <div class="sponsors-grid" id="sponsorsGrid">
                        @foreach ($sport->sponsors as $index => $sponsor)
                            <div class="sponsor-item" data-index="{{ $index }}">
                                @if ($sponsor->Sponsor_Link)
                                    <a href="{{ $sponsor->Sponsor_Link }}" target="_blank" rel="noopener noreferrer" class="sponsor-link">
                                @endif
                                @if ($sponsor->Image_Path)
                                    <img src="{{ asset('storage/' . $sponsor->Image_Path) }}" 
                                         alt="{{ $sponsor->Sponsor_Name }}" 
                                         class="sponsor-logo">
                                @else
                                    <div class="sponsor-placeholder">
                                        {{ $sponsor->Sponsor_Name }}
                                    </div>
                                @endif
                                @if ($sponsor->Sponsor_Link)
                                    </a>
                                @endif
                                <p class="sponsor-name">{{ $sponsor->Sponsor_Name }}</p>
                            </div>
                        @endforeach
                    </div>
                    @if ($sport->sponsors->count() > 5)
                        <div class="sponsor-navigation">
                            <button class="nav-btn prev-btn" onclick="rotateSponsor(-1)">&lt;</button>
                            <div class="sponsor-dots">
                                @for ($i = 0; $i < ceil($sport->sponsors->count() / 5); $i++)
                                    <span class="dot {{ $i === 0 ? 'active' : '' }}" onclick="goToSlide({{ $i }})"></span>
                                @endfor
                            </div>
                            <button class="nav-btn next-btn" onclick="rotateSponsor(1)">&gt;</button>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <style>
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

        .back-btn {
            background-color: #6b7280 !important;
        }

        .back-btn:hover {
            background-color: #4b5563 !important;
        }

        /* About Section */
        .about-section {
            background: white;
            border-radius: 12px;
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
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

        .about-section h2 {
            color: #1f2937;
            margin-bottom: 20px;
            font-size: 2rem;
            font-weight: 600;
        }

        .about-section .sport-description {
            color: #4b5563;
            font-size: 1.1rem;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
        }

        /* FAQ Section */
        .faq-section {
            background: white;
            border-radius: 12px;
            padding: 40px;
            margin-top: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .faq-section h2 {
            color: #1f2937;
            margin-bottom: 30px;
            font-size: 1.8rem;
            font-weight: 600;
            text-align: center;
        }

        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-item {
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 20px;
        }

        .faq-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .faq-question {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            padding: 20px 0;
            transition: all 0.3s ease;
        }

        .faq-question:hover {
            color: #3b82f6;
        }

        .faq-question h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #374151;
            flex: 1;
            padding-right: 20px;
        }

        .faq-question:hover h3 {
            color: #3b82f6;
        }

        .faq-toggle {
            font-size: 1.5rem;
            font-weight: bold;
            color: #6b7280;
            transition: all 0.3s ease;
            transform-origin: center;
        }

        .faq-toggle.open {
            transform: rotate(45deg);
            color: #3b82f6;
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            padding: 0 0 0 0;
        }

        .faq-answer.open {
            max-height: 200px;
            padding: 0 0 20px 0;
            transition: max-height 0.4s ease-in;
        }

        .faq-answer p {
            margin: 0;
            color: #4b5563;
            line-height: 1.6;
            font-size: 1rem;
        }

        /* Gallery Section */
        .gallery-section {
            margin-bottom: 40px;
        }

        .gallery-container {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
        }

        .gallery-slider {
            position: relative;
            width: 100%;
            height: 400px;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .gallery-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .gallery-slide.active {
            opacity: 1;
        }

        .gallery-image-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .gallery-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.4) 70%, transparent 100%);
            display: flex;
            align-items: flex-end;
            padding: 30px;
        }

        .gallery-overlay-content h3 {
            color: white;
            font-size: 1.8rem;
            font-weight: 600;
            margin: 0 0 10px 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .gallery-overlay-content p {
            color: #e5e7eb;
            font-size: 1.1rem;
            margin: 0;
            line-height: 1.5;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        /* Gallery Navigation */
        .gallery-navigation {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            gap: 15px;
        }

        .gallery-nav-btn {
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gallery-nav-btn:hover {
            background: #2563eb;
            transform: scale(1.1);
        }

        .gallery-dots {
            display: flex;
            gap: 10px;
        }

        .gallery-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #d1d5db;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .gallery-dot.active {
            background: #3b82f6;
            transform: scale(1.2);
        }

        .gallery-dot:hover {
            background: #6b7280;
        }

        /* Sponsors Section */
        .sponsors-section {
            background: white;
            border-radius: 12px;
            padding: 40px;
            margin-top: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .sponsors-section h2 {
            color: #1f2937;
            margin-bottom: 30px;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .sponsors-container {
            max-width: 1000px;
            margin: 0 auto;
            position: relative;
        }

        .sponsors-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            margin-top: 20px;
            min-height: 160px;
            align-items: center;
        }

        .sponsor-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.3s ease;
            min-height: 140px;
            width: 100%;
            box-sizing: border-box;
        }

        .sponsor-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        /* Hide sponsors beyond the first 5 initially */
        .sponsor-item[data-index]:nth-child(n+6) {
            display: none;
        }

        .sponsor-link {
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            flex-grow: 1;
        }

        .sponsor-logo {
            max-width: 150px;
            max-height: 80px;
            width: auto;
            height: auto;
            object-fit: contain;
            border-radius: 4px;
            margin: auto;
            display: block;
        }

        .sponsor-placeholder {
            width: 150px;
            height: 80px;
            background: #f3f4f6;
            border: 2px dashed #d1d5db;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            font-weight: 500;
            text-align: center;
            margin-bottom: 15px;
        }

        .sponsor-name {
            color: #374151;
            font-weight: 500;
            margin: 10px 0 0 0;
            text-align: center;
            font-size: 0.9rem;
            line-height: 1.2;
        }

        .sponsor-link:hover .sponsor-name {
            color: #3b82f6;
        }

        /* Navigation Controls */
        .sponsor-navigation {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            gap: 15px;
        }

        .nav-btn {
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .nav-btn:hover {
            background: #2563eb;
            transform: scale(1.1);
        }

        .sponsor-dots {
            display: flex;
            gap: 8px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #d1d5db;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dot.active {
            background: #3b82f6;
        }

        .dot:hover {
            background: #6b7280;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .about-section,
            .sponsors-section,
            .faq-section,
            .gallery-section {
                padding: 30px 20px;
                margin-left: 10px;
                margin-right: 10px;
            }

            .gallery-slider {
                height: 300px;
            }

            .gallery-overlay {
                padding: 20px;
            }

            .gallery-overlay-content h3 {
                font-size: 1.4rem;
            }

            .gallery-overlay-content p {
                font-size: 1rem;
            }

            .gallery-nav-btn {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }

            .about-section h2,
            .sponsors-section h2,
            .faq-section h2 {
                font-size: 1.5rem;
            }

            .faq-question h3 {
                font-size: 1rem;
            }

            .faq-answer p {
                font-size: 0.9rem;
            }

            .about-section .sport-description {
                font-size: 1rem;
            }

            .sponsors-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 15px;
            }

            .sponsor-item {
                min-height: 120px;
                padding: 10px;
            }

            .sponsor-logo {
                max-width: 100px;
                max-height: 50px;
            }

            .sponsor-placeholder {
                width: 100px;
                height: 50px;
                font-size: 0.8rem;
            }

            /* Show only 3 sponsors on mobile */
            .sponsor-item[data-index]:nth-child(n+4) {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .gallery-slider {
                height: 250px;
            }

            .gallery-overlay {
                padding: 15px;
            }

            .gallery-overlay-content h3 {
                font-size: 1.2rem;
            }

            .gallery-overlay-content p {
                font-size: 0.9rem;
            }

            .sponsors-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            /* Show only 2 sponsors on very small screens */
            .sponsor-item[data-index]:nth-child(n+3) {
                display: none;
            }
        }
    </style>

    <script>
        // FAQ Toggle Functionality
        function toggleFaq(index) {
            const answer = document.getElementById(`faq-${index}`);
            const toggle = document.querySelector(`[data-faq="${index}"] .faq-toggle`);
            
            if (answer && toggle) {
                if (answer.classList.contains('open')) {
                    answer.classList.remove('open');
                    toggle.classList.remove('open');
                } else {
                    // Close all other FAQs
                    document.querySelectorAll('.faq-answer').forEach(item => {
                        item.classList.remove('open');
                    });
                    document.querySelectorAll('.faq-toggle').forEach(item => {
                        item.classList.remove('open');
                    });
                    
                    // Open the clicked FAQ
                    answer.classList.add('open');
                    toggle.classList.add('open');
                }
            }
        }

        // Sponsor Rotation Functionality
        let currentSlide = 0;
        const totalSponsors = {{ $sport->sponsors->count() ?? 0 }};
        const sponsorsPerSlide = window.innerWidth <= 480 ? 2 : (window.innerWidth <= 768 ? 3 : 5);
        const totalSlides = Math.ceil(totalSponsors / sponsorsPerSlide);

        function showSponsors(slideIndex) {
            const sponsors = document.querySelectorAll('.sponsor-item');
            const dots = document.querySelectorAll('.dot');
            
            // Hide all sponsors
            sponsors.forEach(sponsor => sponsor.style.display = 'none');
            
            // Show sponsors for current slide
            const startIndex = slideIndex * sponsorsPerSlide;
            const endIndex = Math.min(startIndex + sponsorsPerSlide, totalSponsors);
            
            for (let i = startIndex; i < endIndex; i++) {
                if (sponsors[i]) {
                    sponsors[i].style.display = 'flex';
                }
            }
            
            // Update dots
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === slideIndex);
            });
        }

        function rotateSponsor(direction) {
            currentSlide += direction;
            
            if (currentSlide >= totalSlides) {
                currentSlide = 0;
            } else if (currentSlide < 0) {
                currentSlide = totalSlides - 1;
            }
            
            showSponsors(currentSlide);
        }

        function goToSlide(slideIndex) {
            currentSlide = slideIndex;
            showSponsors(currentSlide);
        }

        // Auto-rotate sponsors every 5 seconds if there are more than the visible amount
        if (totalSponsors > sponsorsPerSlide) {
            setInterval(() => {
                rotateSponsor(1);
            }, 5000);
        }

        // Handle window resize
        window.addEventListener('resize', () => {
            location.reload(); // Simple solution to recalculate layout
        });

        // Gallery Slider Functions
        let currentGallerySlide = 0;
        const totalGallerySlides = {{ $sport->galleryImages->count() ?? 0 }};

        function showGallerySlide(slideIndex) {
            const slides = document.querySelectorAll('.gallery-slide');
            const dots = document.querySelectorAll('.gallery-dot');
            
            // Hide all slides
            slides.forEach(slide => slide.classList.remove('active'));
            
            // Show current slide
            if (slides[slideIndex]) {
                slides[slideIndex].classList.add('active');
            }
            
            // Update dots
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === slideIndex);
            });
        }

        function changeGallerySlide(direction) {
            currentGallerySlide += direction;
            
            if (currentGallerySlide >= totalGallerySlides) {
                currentGallerySlide = 0;
            } else if (currentGallerySlide < 0) {
                currentGallerySlide = totalGallerySlides - 1;
            }
            
            showGallerySlide(currentGallerySlide);
        }

        function goToGallerySlide(slideIndex) {
            currentGallerySlide = slideIndex;
            showGallerySlide(currentGallerySlide);
        }

        // Auto-rotate gallery every 6 seconds if there are multiple images
        if (totalGallerySlides > 1) {
            setInterval(() => {
                changeGallerySlide(1);
            }, 6000);
        }
    </script>

    @include('partials.footer')
</body>

</html>
