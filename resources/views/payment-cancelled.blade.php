<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Cancelled - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<header class="main-header">
    <div class="header-container">
        <div class="header-content">
            <h1>Falcon Teams</h1>
            <p>Payment was cancelled</p>
        </div>

        <div class="header-buttons">
            <a href="{{ route('home') }}" class="header-btn login-btn">← Home</a>
        </div>
    </div>
</header>

<body>
    <div class="registration-page">
        <div class="registration-container">
            <div class="cancelled-wrapper">
                <div class="cancelled-content">
                    <!-- Cancelled Icon -->
                    <div class="cancelled-icon">
                        <svg class="x-mark" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" fill="#ef4444" stroke="#ef4444" stroke-width="2"/>
                            <path d="m15 9-6 6m0-6 6 6" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <h2 class="cancelled-title">Payment Cancelled</h2>
                    
                    <div class="cancelled-message">
                        <p>Your payment was cancelled and no charges were made to your card.</p>
                        <p>Your registration has not been completed.</p>
                    </div>

                    @if (session('error'))
                        <div class="alert alert-error">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- What Happened Section -->
                    <div class="info-section">
                        <h3>What Happened?</h3>
                        <ul class="info-list">
                            <li>
                                <span class="info-icon">❌</span>
                                <span>The payment process was cancelled before completion</span>
                            </li>
                            <li>
                                <span class="info-icon">💳</span>
                                <span>No charges were made to your payment method</span>
                            </li>
                            <li>
                                <span class="info-icon">📝</span>
                                <span>Your registration was not completed</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Next Steps Section -->
                    <div class="next-steps">
                        <h3>What Would You Like to Do?</h3>
                        <div class="options-grid">
                            <div class="option-card">
                                <h4>Try Payment Again</h4>
                                <p>Complete your registration by trying the payment process again</p>
                                <a href="{{ url()->previous() }}" class="btn btn-primary">Try Again</a>
                            </div>
                            <div class="option-card">
                                <h4>Start Over</h4>
                                <p>Return to the registration form and start the process over</p>
                                <a href="{{ route('registration.form') }}" class="btn btn-secondary">New Registration</a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="contact-info">
                        <h3>Need Help?</h3>
                        <p>If you're experiencing issues with payment or have questions about registration:</p>
                        <div class="contact-details">
                            <div class="contact-item">
                                <span class="contact-icon">📧</span>
                                <span>Email: info@falconteams.com</span>
                            </div>
                            <div class="contact-item">
                                <span class="contact-icon">📞</span>
                                <span>Phone: (555) 123-4567</span>
                            </div>
                        </div>
                    </div>

                    <!-- Alternative Payment Methods -->
                    <div class="alternatives">
                        <h3>Alternative Payment Options</h3>
                        <p>Having trouble with online payment? We also accept:</p>
                        <ul class="payment-methods">
                            <li>Cash payments at our office</li>
                            <li>Check payments (made out to Falcon Teams)</li>
                            <li>Money orders</li>
                            <li>Bank transfers (contact us for details)</li>
                        </ul>
                        <p class="note">Contact us to arrange alternative payment methods.</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="{{ route('home') }}" class="btn btn-outline">Return to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .cancelled-wrapper {
            max-width: 700px;
            margin: 0 auto;
            padding: 2rem;
            text-align: center;
        }

        .cancelled-content {
            background: white;
            border-radius: 12px;
            padding: 3rem 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .cancelled-icon {
            margin-bottom: 2rem;
        }

        .x-mark {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            display: block;
        }

        .cancelled-title {
            font-size: 2.5rem;
            color: #dc2626;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .cancelled-message {
            font-size: 1.125rem;
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .cancelled-message p {
            margin-bottom: 0.5rem;
        }

        .info-section,
        .next-steps,
        .contact-info,
        .alternatives {
            text-align: left;
            margin: 2rem 0;
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 8px;
        }

        .info-section h3,
        .next-steps h3,
        .contact-info h3,
        .alternatives h3 {
            color: #374151;
            margin-bottom: 1rem;
            font-size: 1.25rem;
            font-weight: 600;
            text-align: center;
        }

        .info-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .info-list li {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
            font-size: 1rem;
            color: #4b5563;
        }

        .info-icon {
            font-size: 1.25rem;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }

        .options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .option-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .option-card h4 {
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .option-card p {
            color: #6b7280;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .contact-info {
            background: #eff6ff;
        }

        .contact-info h3 {
            color: #1e40af;
        }

        .contact-info p {
            color: #4b5563;
            margin-bottom: 1rem;
            text-align: center;
        }

        .contact-details {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            align-items: center;
        }

        .contact-item {
            display: flex;
            align-items: center;
            color: #374151;
            font-weight: 500;
        }

        .contact-icon {
            margin-right: 0.75rem;
            font-size: 1.125rem;
        }

        .alternatives {
            background: #fef3c7;
        }

        .alternatives h3 {
            color: #92400e;
        }

        .alternatives p {
            color: #4b5563;
            margin-bottom: 1rem;
            text-align: center;
        }

        .payment-methods {
            list-style: disc;
            padding-left: 2rem;
            color: #4b5563;
            margin-bottom: 1rem;
        }

        .payment-methods li {
            margin-bottom: 0.5rem;
        }

        .note {
            font-style: italic;
            color: #92400e;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 2rem 0;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.75rem 2rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-block;
            text-align: center;
            min-width: 140px;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #059669;
            color: white;
        }

        .btn-primary:hover {
            background: #047857;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #3b82f6;
            color: white;
        }

        .btn-secondary:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            color: #374151;
            border: 2px solid #d1d5db;
        }

        .btn-outline:hover {
            background: #f9fafb;
            border-color: #9ca3af;
            transform: translateY(-1px);
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .alert-error {
            background-color: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        @media (max-width: 640px) {
            .cancelled-wrapper {
                padding: 1rem;
            }

            .cancelled-content {
                padding: 2rem 1rem;
            }

            .cancelled-title {
                font-size: 2rem;
            }

            .options-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                min-width: auto;
            }

            .contact-details {
                align-items: flex-start;
            }
        }
    </style>
</body>

</html>