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
                        <p>Thank you for your payment. A charge should be added to your account shortly.</p>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Action Button -->
                    <div class="action-buttons">
                        <a href="{{ route('home') }}" class="btn btn-primary">Return to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .success-wrapper {
            max-width: 500px;
            margin: 0 auto;
            padding: 2rem;
            text-align: center;
        }

        .success-content {
            background: white;
            border-radius: 12px;
            padding: 3rem 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .success-icon {
            margin-bottom: 2rem;
        }

        .checkmark {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            display: block;
        }

        .success-title {
            font-size: 2.5rem;
            color: #059669;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .success-message {
            font-size: 1.125rem;
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            margin: 2rem 0;
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
        }

        .btn-primary {
            background: #059669;
            color: white;
        }

        .btn-primary:hover {
            background: #047857;
            transform: translateY(-1px);
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        @media (max-width: 640px) {
            .success-wrapper {
                padding: 1rem;
            }

            .success-content {
                padding: 2rem 1rem;
            }

            .success-title {
                font-size: 2rem;
            }
        }
    </style>
</body>

</html>