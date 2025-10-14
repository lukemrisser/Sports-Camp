<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Successful - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<header class="main-header">
    <div class="header-container">
        <div class="header-content">
            <h1>Falcon Teams</h1>
            <p>Registration completed successfully!</p>
        </div>

        <div class="header-buttons">
            <a href="{{ route('home') }}" class="header-btn login-btn">← Home</a>
        </div>
    </div>
</header>

<body>
    <div class="registration-page">
        <div class="registration-container">
            <div class="success-wrapper">
                <div class="success-content">
                    <!-- Success Icon -->
                    <div class="success-icon">
                        <svg class="checkmark" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" fill="#10B981" stroke="#10B981" stroke-width="2"/>
                            <path d="m9 12 2 2 4-4" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <h2 class="success-title">Payment Successful!</h2>
                    
                    <div class="success-message">
                        <p>Thank you for your payment. Your sports camp registration has been completed successfully.</p>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- What's Next Section -->
                    <div class="next-steps">
                        <h3>What's Next?</h3>
                        <ul class="steps-list">
                            <li>
                                <span class="step-icon">📧</span>
                                <span>You will receive a confirmation email with your registration details</span>
                            </li>
                            <li>
                                <span class="step-icon">📋</span>
                                <span>Check your email for camp information and what to bring</span>
                            </li>
                            <li>
                                <span class="step-icon">📅</span>
                                <span>Mark your calendar - camp details will be in your confirmation email</span>
                            </li>
                            <li>
                                <span class="step-icon">📞</span>
                                <span>Contact us if you have any questions about your registration</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact Information -->
                    <div class="contact-info">
                        <h3>Need Help?</h3>
                        <p>If you have any questions about your registration or the camp, please don't hesitate to contact us:</p>
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

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="{{ route('home') }}" class="btn btn-primary">Return to Home</a>
                        <a href="{{ route('registration.form') }}" class="btn btn-secondary">Register Another Camper</a>
                    </div>

                    <!-- Receipt Information -->
                    <div class="receipt-info">
                        <p class="receipt-text">
                            <svg class="receipt-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                            A receipt has been emailed to you for your records
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>