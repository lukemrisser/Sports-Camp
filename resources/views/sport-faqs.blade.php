<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sport->Sport_Name }} FAQs - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

@include('partials.header', [
    'title' => $sport->Sport_Name . ' FAQs',
    'subtitle' => 'Frequently asked questions about our ' . strtolower($sport->Sport_Name) . ' programs',
])

<div class="container">
    <!-- Navigation -->
    <div class="sport-navigation">
        <a href="{{ route('sport.show', $sport->Sport_ID) }}" class="nav-link">{{ $sport->Sport_Name }} Home</a>
        <a href="{{ route('sport.camps', $sport->Sport_ID) }}" class="nav-link">Register for Camp!</a>
        <a href="{{ route('sport.about', $sport->Sport_ID) }}" class="nav-link">About Us</a>
        <a href="{{ route('sport.faqs', $sport->Sport_ID) }}" class="nav-link active">FAQs</a>
    </div>

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
    @else
        <div class="no-faqs-message">
            <h3>No FAQs Available</h3>
            <p>We don't have any frequently asked questions for {{ $sport->Sport_Name }} at this time.</p>
            <p>If you have questions, please feel free to contact us!</p>
            <a href="{{ route('sport.show', $sport->Sport_ID) }}" class="card-button">← Back to Gallery</a>
        </div>
    @endif
</div>

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
</script>

@include('partials.footer')
</body>

</html>