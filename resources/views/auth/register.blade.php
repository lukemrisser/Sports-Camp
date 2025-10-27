<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="auth-container">
        <header class="auth-header">
            <h1>Falcon Teams</h1>
            <p>Create your account</p>
        </header>

        <div class="auth-card">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="form-group">
                    <label for="name" class="form-label">Name</label>
                    <input id="name" class="form-input" type="text" name="name" value="{{ old('name') }}"
                        required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="form-error" />
                </div>

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}"
                        required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="form-error" />
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" class="form-input" type="password" name="password" required
                        autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="form-error" />
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input id="password_confirmation" class="form-input" type="password" name="password_confirmation"
                        required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="form-error" />
                </div>

                <div class="form-actions">
                    <a class="login-link" href="{{ route('login') }}">
                        Already registered?
                    </a>

                    <button type="submit" class="register-button">
                        Register
                    </button>
                </div>

                <div class="auth-footer">
                    <p>By registering, you agree to our terms and conditions</p>
                </div>
            </form>
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

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }

        .login-link {
            color: #0a3f94;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        .register-button {
            background: #fbbf24;
            color: #0a3f94;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
        }

        .register-button:hover {
            background: #f59e0b;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(251, 191, 36, 0.4);
        }

        .auth-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .auth-footer p {
            color: #6b7280;
            font-size: 12px;
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

            .form-actions {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }

            .register-button {
                width: 100%;
                justify-self: stretch;
            }
        }
    </style>
</body>

</html>
