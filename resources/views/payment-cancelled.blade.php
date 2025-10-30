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

                    <!-- Action Button -->
                    <div class="action-buttons">
                        <a href="{{ route('home') }}" class="btn btn-primary">Return to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .cancelled-wrapper {
            max-width: 500px;
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
            background: #dc2626;
            color: white;
        }

        .btn-primary:hover {
            background: #b91c1c;
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
        }
    </style>
</body>

</html>