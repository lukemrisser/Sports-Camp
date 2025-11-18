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
        <a href="{{ route('sport.show', $sport->Sport_ID) }}" class="nav-link">Home</a>
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

    /* FAQ Section */
    .faq-section {
        background: white;
        border-radius: 12px;
        padding: 40px;
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

    .no-faqs-message {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        margin: 0 auto;
    }

    .no-faqs-message h3 {
        color: #333;
        margin-bottom: 15px;
    }

    .no-faqs-message p {
        color: #666;
        margin-bottom: 15px;
    }

    .no-faqs-message .card-button {
        background-color: #3b82f6;
        color: white;
        text-decoration: none;
        padding: 12px 24px;
        border-radius: 8px;
        display: inline-block;
        margin-top: 10px;
        transition: background-color 0.3s ease;
    }

    .no-faqs-message .card-button:hover {
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

        .faq-section {
            padding: 30px 20px;
            margin: 0 10px;
        }

        .faq-section h2 {
            font-size: 1.5rem;
        }

        .faq-question h3 {
            font-size: 1rem;
        }

        .faq-answer p {
            font-size: 0.9rem;
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

        .faq-section {
            padding: 30px 15px;
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
</script>

@include('partials.footer')
</body>

</html>