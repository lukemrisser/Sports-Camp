<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Register - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="auth-container">
        <header class="auth-header">
            <h1>Falcon Teams</h1>
            <p>Create your coach account</p>
        </header>

        <div class="auth-card">
            <!-- Update the action to your coach registration route -->
            <form method="POST" action="{{ route('coach-register') }}">
                @csrf

                <!-- First Name -->
                <div class="form-group">
                    <label for="coach_firstname" class="form-label">First Name</label>
                    <input id="coach_firstname" class="form-input" type="text" name="coach_firstname"
                        value="{{ old('coach_firstname') }}" required autofocus autocomplete="given-name" />
                    <x-input-error :messages="$errors->get('coach_firstname')" class="form-error" />
                </div>

                <!-- Last Name -->
                <div class="form-group">
                    <label for="coach_lastname" class="form-label">Last Name</label>
                    <input id="coach_lastname" class="form-input" type="text" name="coach_lastname"
                        value="{{ old('coach_lastname') }}" required autocomplete="family-name" />
                    <x-input-error :messages="$errors->get('coach_lastname')" class="form-error" />
                </div>

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}"
                        required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="form-error" />
                </div>

                <!-- Sport Selection -->
                <div class="form-group">
                    <label for="sport" class="form-label">Sport/Camp</label>
                    <select id="sport" class="form-input form-select" name="sport" required>
                        <option value="" disabled {{ old('sport') ? '' : 'selected' }}>Select your sport</option>
                        @foreach ($sports as $sport)
                            <option value="{{ $sport->Sport_ID }}"
                                {{ old('sport') == $sport->Sport_ID ? 'selected' : '' }}>
                                {{ $sport->Sport_Name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('sport')" class="form-error" />
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
                        Register as Coach
                    </button>
                </div>

                <div class="auth-footer">
                    <p>By registering as a coach, you agree to our terms and conditions</p>
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
            /* Different gradient for coach registration to distinguish it */
            background: var(--primary-blue);
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

        .form-select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 20px;
            padding-right: 40px;
        }

        .form-select option[disabled] {
            color: #9ca3af;
        }

        .form-select optgroup {
            font-weight: 600;
            color: #374151;
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

        .checkbox-container {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .form-checkbox {
            width: 20px;
            height: 20px;
            margin-top: 2px;
            border: 2px solid #e5e7eb;
            border-radius: 4px;
            cursor: pointer;
            flex-shrink: 0;
            accent-color: #0a3f94;
        }

        .form-checkbox:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(10, 63, 148, 0.1);
        }

        .checkbox-label {
            font-weight: 600;
            color: #374151;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .checkbox-description {
            font-weight: 400;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.4;
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
