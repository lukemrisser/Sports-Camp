<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://js.stripe.com/v3/"></script>
</head>

<header class="main-header">
    <div class="header-container">
        <div class="header-content">
            <h1>Falcon Teams</h1>
            <p>Complete your payment to finalize registration</p>
        </div>

        <div class="header-buttons">
            <a href="{{ route('home') }}" class="header-btn login-btn">← Home</a>
        </div>
    </div>
</header>

<body>
    <div class="registration-page">
        <div class="registration-container">
            <div class="registration-form-wrapper">
                <div class="registration-header">
                    <h2 class="registration-title">Complete Payment</h2>
                    <div class="payment-summary">
                        <div class="summary-card">
                            <h3>Registration Summary</h3>
                            <div class="summary-item">
                                <span class="label">Camper:</span>
                                <span class="value">{{ $registration['camper_name'] ?? 'N/A' }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="label">Camp:</span>
                                <span class="value">{{ $registration['division_name'] ?? 'N/A' }}</span>
                            </div>
                            <div class="summary-item total">
                                <span class="label">Total Amount:</span>
                                <span class="value">${{ number_format($amount / 100, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if (session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <ul class="error-list">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="payment-form" method="POST" action="{{ route('payment.process') }}" class="registration-form">
                    @csrf
                    <input type="hidden" name="player_id" value="{{ $playerId }}">
                    <input type="hidden" name="amount" value="{{ $amount }}">

                    <!-- Payment Information -->
                    <div class="form-section">
                        <h3 class="section-title">Payment Information</h3>
                        
                        <div class="form-group">
                            <label class="form-label">Cardholder Name</label>
                            <input type="text" name="cardholder_name" class="form-input" 
                                   value="{{ $registration['parent_name'] ?? '' }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Card Details</label>
                            <div id="card-element" class="form-input stripe-element">
                                <!-- Stripe Elements will create form elements here -->
                            </div>
                            <div id="card-errors" class="error-message" role="alert"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Receipt</label>
                            <input type="email" name="receipt_email" class="form-input" 
                                   value="{{ $registration['email'] ?? '' }}" required>
                        </div>
                    </div>

                    <!-- Billing Address -->
                    <div class="form-section">
                        <h3 class="section-title">Billing Address</h3>
                        <div class="form-group">
                            <label class="form-label">Address</label>
                            <input type="text" name="billing_address" class="form-input" 
                                   value="{{ $registration['address'] ?? '' }}" required>
                        </div>
                        <div class="form-grid-3">
                            <div class="form-group">
                                <label class="form-label">City</label>
                                <input type="text" name="billing_city" class="form-input" 
                                       value="{{ $registration['city'] ?? '' }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">State</label>
                                <input type="text" name="billing_state" class="form-input" 
                                       value="{{ $registration['state'] ?? '' }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">ZIP Code</label>
                                <input type="text" name="billing_zip" class="form-input" 
                                       value="{{ $registration['postal_code'] ?? '' }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="submit-section">
                        <button type="submit" id="submit-button" class="submit-button">
                            <span id="button-text">Pay ${{ number_format($amount / 100, 2) }}</span>
                            <span id="spinner" class="spinner hidden"></span>
                        </button>
                        <p class="payment-security">
                            <svg class="security-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            Your payment information is secure and encrypted
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Initialize Stripe
        const stripe = Stripe('{{ config("services.stripe.key") }}');
        const elements = stripe.elements();

        // Custom styling for Stripe Elements
        const style = {
            base: {
                fontSize: '16px',
                color: '#424770',
                '::placeholder': {
                    color: '#aab7c4',
                },
                padding: '12px',
            },
            invalid: {
                color: '#9e2146',
            },
        };

        // Create card element
        const cardElement = elements.create('card', {
            style: style,
            hidePostalCode: true
        });

        // Mount card element
        cardElement.mount('#card-element');

        // Handle real-time validation errors from the card Element
        cardElement.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
                displayError.style.display = 'block';
            } else {
                displayError.textContent = '';
                displayError.style.display = 'none';
            }
        });

        // Handle form submission
        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const buttonText = document.getElementById('button-text');
        const spinner = document.getElementById('spinner');

        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            // Disable submit button and show loading
            submitButton.disabled = true;
            buttonText.style.display = 'none';
            spinner.style.display = 'inline-block';

            // Get cardholder name
            const cardholderName = document.querySelector('input[name="cardholder_name"]').value;
            const billingDetails = {
                name: cardholderName,
                address: {
                    line1: document.querySelector('input[name="billing_address"]').value,
                    city: document.querySelector('input[name="billing_city"]').value,
                    state: document.querySelector('input[name="billing_state"]').value,
                    postal_code: document.querySelector('input[name="billing_zip"]').value,
                }
            };

            try {
                // Create payment method
                const {error, paymentMethod} = await stripe.createPaymentMethod({
                    type: 'card',
                    card: cardElement,
                    billing_details: billingDetails,
                });

                if (error) {
                    // Show error to customer
                    showError(error.message);
                    resetButton();
                } else {
                    // Send paymentMethod to your server
                    handlePaymentMethod(paymentMethod);
                }
            } catch (error) {
                showError('An unexpected error occurred. Please try again.');
                resetButton();
            }
        });

        async function handlePaymentMethod(paymentMethod) {
            // Add payment method ID to form
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'payment_method_id';
            hiddenInput.value = paymentMethod.id;
            form.appendChild(hiddenInput);

            // Submit the form
            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                });

                const result = await response.json();

                if (result.success) {
                    // Payment successful
                    window.location.href = result.redirect_url || '{{ route("payment.success") }}';
                } else if (result.requires_action) {
                    // Handle 3D Secure authentication
                    handleAction(result.payment_intent);
                } else {
                    // Payment failed
                    showError(result.error || 'Payment failed. Please try again.');
                    resetButton();
                }
            } catch (error) {
                showError('Network error. Please check your connection and try again.');
                resetButton();
            }
        }

        async function handleAction(paymentIntent) {
            const {error, paymentIntent: confirmedPaymentIntent} = await stripe.confirmCardPayment(
                paymentIntent.client_secret
            );

            if (error) {
                showError(error.message);
                resetButton();
            } else {
                // Payment succeeded
                window.location.href = '{{ route("payment.success") }}';
            }
        }

        function showError(message) {
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = message;
            errorElement.style.display = 'block';
            
            // Scroll to error
            errorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function resetButton() {
            submitButton.disabled = false;
            buttonText.style.display = 'inline';
            spinner.style.display = 'none';
        }

        // Auto-format ZIP code
        document.querySelector('input[name="billing_zip"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 9);
            }
            e.target.value = value;
        });
    </script>

    <style>
        .payment-summary {
            margin-bottom: 2rem;
        }

        .summary-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
        }

        .summary-card h3 {
            margin: 0 0 1rem 0;
            color: #495057;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-item.total {
            font-weight: 600;
            font-size: 1.1rem;
            margin-top: 0.5rem;
            padding-top: 1rem;
            border-top: 2px solid #dee2e6;
        }

        .stripe-element {
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: white;
        }

        .stripe-element:focus-within {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: none;
        }

        .payment-security {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .security-icon {
            width: 16px;
            height: 16px;
        }

        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .hidden {
            display: none;
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

        .error-list {
            margin: 0;
            padding-left: 1.5rem;
        }
    </style>
</body>

</html>
