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
    ])

    <div class="container">
        <!-- Navigation -->
        <div class="sport-navigation">
            <a href="{{ route('sport.show', $sport->Sport_ID) }}" class="nav-link active">{{ $sport->Sport_Name }}
                Home</a>
            <a href="{{ route('sport.camps', $sport->Sport_ID) }}" class="nav-link">Register for Camp!</a>
            <a href="{{ route('sport.about', $sport->Sport_ID) }}" class="nav-link">About Us</a>
            <a href="{{ route('sport.faqs', $sport->Sport_ID) }}" class="nav-link">FAQs</a>
        </div>

        <!-- Gallery Section -->
        @if ($sport->galleryImages && $sport->galleryImages->count() > 0)
            <div class="gallery-section">
                <div class="gallery-container">
                    <div class="gallery-slider">
                        @foreach ($sport->galleryImages as $index => $image)
                            <div class="gallery-slide {{ $index === 0 ? 'active' : '' }}"
                                data-slide="{{ $index }}">
                                <div class="gallery-image-wrapper">
                                    <img src="{{ $image->image_url ?? '' }}" alt="{{ $image->Image_Title }}"
                                        class="gallery-image">
                                    <div class="gallery-overlay">
                                        <div class="gallery-overlay-content">
                                            <div class="gallery-text">
                                                <h3>{{ $image->Image_Title }}</h3>
                                                @if ($image->Image_Text)
                                                    <p>{{ $image->Image_Text }}</p>
                                                @endif
                                            </div>
                                            <div class="gallery-button">
                                                <a href="{{ route('sport.camps', $sport->Sport_ID) }}"
                                                    class="camp-register-btn">
                                                    Register Now
                                                </a>
                                            </div>
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
                                    <span class="gallery-dot {{ $index === 0 ? 'active' : '' }}"
                                        onclick="goToGallerySlide({{ $index }})"></span>
                                @endforeach
                            </div>
                            <button class="gallery-nav-btn next-btn" onclick="changeGallerySlide(1)">&gt;</button>
                        </div>
                    @endif
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
                                    <a href="{{ $sponsor->Sponsor_Link }}" target="_blank" rel="noopener noreferrer"
                                        class="sponsor-link">
                                @endif
                                @if ($sponsor->image_url)
                                    <img src="{{ $sponsor->image_url }}"
                                        alt="{{ $sponsor->Sponsor_Name }}" class="sponsor-logo">
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
                                    <span class="dot {{ $i === 0 ? 'active' : '' }}"
                                        onclick="goToSlide({{ $i }})"></span>
                                @endfor
                            </div>
                            <button class="nav-btn next-btn" onclick="rotateSponsor(1)">&gt;</button>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <script>
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
