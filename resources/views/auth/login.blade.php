<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="auth-container">
        <header class="auth-header">
            <h1>Falcon Teams</h1>
            <p>Login to your account</p>
        </header>

        <div class="auth-card">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Add Warning Message for Unverified Users -->
            @if (session('warning'))
                <div class="warning-message">
                    {{ session('warning') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}"
                        required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="form-error" />
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" class="form-input" type="password" name="password" required
                        autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="form-error" />
                </div>

                <!-- Remember Me -->
                <div class="form-group checkbox-group">
                    <label for="remember_me" class="checkbox-label">
                        <input id="remember_me" type="checkbox" class="checkbox-input" name="remember">
                        <span class="checkbox-text">Remember me</span>
                    </label>
                </div>

                <!-- After the password field and before the form actions -->

                <!-- Only show verification link if user has pending verification -->
                @if (session('pending_verification'))
                    <div class="verification-section">
                        <p class="verification-text">
                            Haven't verified your email?
                            <a href="{{ route('verification.resend.form') }}?email={{ session('pending_email') }}"
                                class="verification-link">
                                Click here to resend verification email
                            </a>
                        </p>
                    </div>
                @endif

                <div class="form-actions">
                    @if (Route::has('password.request'))
                        <a class="forgot-password-link" href="{{ route('password.request') }}">
                            Forgot your password?
                        </a>
                    @endif

                    <button type="submit" class="login-button">
                        Log in
                    </button>
                </div>

                <div class="auth-footer">
                    <p>Don't have an account?
                        <a href="{{ route('register') }}" class="auth-link">Sign up here</a>
                    </p>
                </div>
            </form>
        </div>

        <div class="back-link">
            <a href="{{ url('/') }}">← Back to Home</a>
        </div>
    </div>

    <style>
        /* Keep all your existing styles and add these new ones */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #6e84e7 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 450px;
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .form-input:focus {
            outline: none;
            border-color: #0a3f94;
            background: white;
            box-shadow: 0 0 0 3px rgba(10, 63, 148, 0.1);
        }

        .form-error {
            color: #ef4444;
            font-size: 14px;
            margin-top: 6px;
        }

        .checkbox-group {
            margin: 24px 0;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .checkbox-input {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            border: 2px solid #d1d5db;
            margin-right: 8px;
            cursor: pointer;
        }

        .checkbox-input:checked {
            background-color: #0a3f94;
            border-color: #0a3f94;
        }

        .checkbox-text {
            font-size: 14px;
            color: #6b7280;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }

        .forgot-password-link {
            color: #0a3f94;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .forgot-password-link:hover {
            text-decoration: underline;
        }

        .login-button {
            background: #0a3f94;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(10, 63, 148, 0.3);
        }

        .login-button:hover {
            background: #083570;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(10, 63, 148, 0.4);
        }

        /* NEW STYLES FOR VERIFICATION SECTION */
        .warning-message {
            margin-bottom: 20px;
            padding: 12px 16px;
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            color: #92400e;
            font-size: 14px;
        }

        .verification-section {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .verification-section p {
            color: #6b7280;
            font-size: 14px;
        }

        .verification-link {
            color: #7c3aed;
            text-decoration: none;
            font-weight: 600;
        }

        .verification-link:hover {
            text-decoration: underline;
            color: #6d28d9;
        }

        .auth-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .auth-footer p {
            color: #6b7280;
            font-size: 14px;
        }

        .auth-link {
            color: #0a3f94;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-link:hover {
            text-decoration: underline;
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

        /* Session status styling */
        .mb-4 {
            margin-bottom: 1rem;
            padding: 12px;
            background: #d1fae5;
            border: 1px solid #10b981;
            border-radius: 8px;
            color: #065f46;
            font-size: 14px;
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 30px 20px;
            }

            .auth-header h1 {
                font-size: 2rem;
            }

            .form-actions {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }

            .login-button {
                width: 100%;
                justify-self: stretch;
            }
        }
    </style>
</body>

</html>
