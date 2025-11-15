<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - {{ config('app.name', 'Falcon Teams') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="auth-container">
        <header class="auth-header">
            <h1>Falcon Teams</h1>
            <p>Email Verification Required</p>
        </header>

        <div class="auth-card">
            <div class="verification-message">
                @if (session('pending_email'))
                    <p>Thanks for signing up! We've sent a verification email to
                        <strong>{{ session('pending_email') }}</strong>.
                    </p>
                    <p>Please click the link in the email to complete your registration.</p>
                @else
                    <p>Thanks for signing up! Please check your email for a verification link to complete your
                        registration.</p>
                @endif
            </div>

            <div class="info-box">
                <h3 class="info-box-title">📋 Important Information</h3>
                <ul class="info-list">
                    <li>Your account will be created after you verify your email</li>
                    <li>The verification link expires in 48 hours</li>
                    <li>Check your spam/junk folder if you don't see the email</li>
                </ul>
            </div>

            <div class="help-section">
                <p class="help-title"><strong>Didn't receive the email?</strong></p>
                <p>Since your account isn't created yet, you'll need to register again if the link expires.</p>
            </div>

            <div class="action-buttons">
                <a href="{{ route('register') }}" class="btn-secondary">
                    Register Again
                </a>
                <a href="{{ route('login') }}" class="btn-primary">
                    Go to Login
                </a>
            </div>
        </div>

        <div class="back-link">
            <a href="{{ url('/') }}">← Back to Home</a>
        </div>
    </div>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--primary-blue);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 500px;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 30px;
            color: white;
        }

        .auth-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .auth-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .auth-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .verification-message {
            margin-bottom: 25px;
            color: #374151;
            font-size: 15px;
            line-height: 1.6;
        }

        .verification-message strong {
            color: #0a3f94;
            font-weight: 600;
        }

        .verification-message p {
            margin-bottom: 10px;
        }

        .info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .info-box-title {
            color: #1e40af;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .info-list {
            list-style: none;
            padding-left: 0;
        }

        .info-list li {
            color: #3730a3;
            font-size: 14px;
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .info-list li:before {
            content: '•';
            position: absolute;
            left: 8px;
            color: #60a5fa;
        }

        .help-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .help-title {
            color: #111827;
            font-size: 15px;
            margin-bottom: 8px;
        }

        .help-section p {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.5;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: space-between;
        }

        .btn-primary,
        .btn-secondary {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            transition: all 0.3s ease;
            text-align: center;
            flex: 1;
        }

        .btn-primary {
            background: #0a3f94;
            color: white;
            box-shadow: 0 4px 12px rgba(10, 63, 148, 0.3);
        }

        .btn-primary:hover {
            background: #083570;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(10, 63, 148, 0.4);
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
            transform: translateY(-2px);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .back-link a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 30px 20px;
            }

            .auth-header h1 {
                font-size: 2rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-primary,
            .btn-secondary {
                width: 100%;
            }
        }
    </style>
</body>

</html>
