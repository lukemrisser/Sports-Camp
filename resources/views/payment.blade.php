<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        .payment-summary {
            margin: 0 0 1.5rem 0;
        }
        
        .summary-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            border-left: 4px solid #2563eb;
        }

        .summary-card h3 {
            margin: 0 0 0.75rem 0;
            font-size: 1rem;
            font-weight: 600;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.4rem 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.95rem;
        }

        .summary-item:last-of-type {
            border-bottom: none;
        }

        .summary-item.total {
            padding: 0.6rem 0;
            border-top: 2px solid #2563eb;
            border-bottom: none;
            font-weight: 600;
            font-size: 1.05rem;
            margin-top: 0.5rem;
        }

        .summary-item .label {
            font-weight: 500;
            color: #374151;
        }

        .summary-item .value {
            color: #1f2937;
            font-weight: 500;
        }

        .summary-item.discount {
            padding: 0.5rem 0;
            background: #f0fdf4;
        }

        .summary-item.discount .value {
            color: #15803d;
        }

        .add-on-line {
            padding-left: 1rem;
            font-size: 0.9rem;
            color: #6b7280;
        }
    </style>
</head>

<body>
    @include('partials.header', [
        'title' => 'Checkout',
    ])
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
                                <span class="value">{{ $campName ?? 'N/A' }}</span>
                            </div>

                            @php
                                $camp = \App\Models\Camp::find($campId);
                                $campPrice = $camp->Price ?? 0;
                                
                                // Get the current active discount from the database
                                $bestDiscount = $camp ? $camp->getBestDiscount() : null;
                                $discountAmt = $bestDiscount ? (float)$bestDiscount->Discount_Amount : 0;
                            @endphp

                            <div class="summary-item">
                                <span class="label">Camp Price:</span>
                                <span class="value">${{ number_format($campPrice, 2) }}</span>
                            </div>

                            @if ($addOnsTotal > 0)
                                @foreach ($selectedAddOns as $addOn)
                                    <div class="summary-item add-on-line">
                                        <span class="label">• {{ $addOn->Fee_Name }}</span>
                                        <span class="value">${{ number_format($addOn->Fee_Amount, 2) }}</span>
                                    </div>
                                @endforeach
                                <div class="summary-item">
                                    <span class="label" style="padding-left: 1rem;">Add Ons Subtotal:</span>
                                    <span class="value">${{ number_format($addOnsTotal, 2) }}</span>
                                </div>
                            @endif

                            @if ($discountAmt > 0)
                                <div class="summary-item discount">
                                    <span class="label">Discount:</span>
                                    <span class="value">-${{ number_format($discountAmt, 2) }}</span>
                                </div>
                            @endif

                            <div class="summary-item total">
                                <span class="label">Total Due:</span>
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

                <form id="payment-form" method="POST" action="{{ route('payment.process') }}"
                    class="registration-form">
                    @csrf
                    <input type="hidden" name="player_id" value="{{ $playerId }}">
                    <input type="hidden" name="camp_id" value="{{ $campId }}">
                    <input type="hidden" name="amount" value="{{ $amount }}">
                    <input type="hidden" name="selected_add_ons" value="{{ $addOnsString ?? '' }}">

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
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd"></path>
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
        const stripe = Stripe("{{ config('services.stripe.key') }}");
        const elements = stripe.elements();

        // Create card element with simplified styling
        const cardElement = elements.create('card', {
            hidePostalCode: true,
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            }
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

            // Clear any previous errors
            clearErrors();

            // Validate form before processing
            if (!validateForm()) {
                return;
            }

            // Disable submit button and show loading
            setLoadingState(true);

            // Get cardholder name and billing details
            const cardholderName = document.querySelector('input[name="cardholder_name"]').value.trim();
            const billingDetails = {
                name: cardholderName,
                email: document.querySelector('input[name="receipt_email"]').value.trim(),
                address: {
                    line1: document.querySelector('input[name="billing_address"]').value.trim(),
                    city: document.querySelector('input[name="billing_city"]').value.trim(),
                    state: document.querySelector('input[name="billing_state"]').value.trim(),
                    postal_code: document.querySelector('input[name="billing_zip"]').value.trim(),
                    country: 'US'
                }
            };

            try {
                // Create payment method
                const {
                    error,
                    paymentMethod
                } = await stripe.createPaymentMethod({
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'Accept': 'application/json',
                    }
                });

                const result = await response.json();

                if (result.success) {
                    // Payment successful
                    window.location.href = result.redirect_url || "{{ route('payment.success') }}";
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
            const {
                error,
                paymentIntent: confirmedPaymentIntent
            } = await stripe.confirmCardPayment(
                paymentIntent.client_secret
            );

            if (error) {
                showError(error.message);
                resetButton();
            } else {
                // Payment succeeded
                window.location.href = "{{ route('payment.success') }}";
            }
        }

        function showError(message) {
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = message;
            errorElement.style.display = 'block';

            // Scroll to error
            errorElement.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        // Helper functions
        function validateForm() {
            const requiredFields = [{
                    name: 'cardholder_name',
                    label: 'Cardholder Name'
                },
                {
                    name: 'receipt_email',
                    label: 'Email'
                },
                {
                    name: 'billing_address',
                    label: 'Billing Address'
                },
                {
                    name: 'billing_city',
                    label: 'City'
                },
                {
                    name: 'billing_state',
                    label: 'State'
                },
                {
                    name: 'billing_zip',
                    label: 'ZIP Code'
                }
            ];

            let isValid = true;
            for (const field of requiredFields) {
                const input = document.querySelector(`input[name="${field.name}"]`);
                if (!input.value.trim()) {
                    showFieldError(input, `${field.label} is required`);
                    isValid = false;
                }
            }

            // Validate email format
            const emailInput = document.querySelector('input[name="receipt_email"]');
            if (emailInput.value && !isValidEmail(emailInput.value)) {
                showFieldError(emailInput, 'Please enter a valid email address');
                isValid = false;
            }

            return isValid;
        }

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        function showFieldError(input, message) {
            input.classList.add('error');
            let errorDiv = input.parentNode.querySelector('.field-error');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'field-error';
                input.parentNode.appendChild(errorDiv);
            }
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }

        function clearErrors() {
            // Clear card errors
            const cardErrors = document.getElementById('card-errors');
            cardErrors.textContent = '';
            cardErrors.style.display = 'none';
            document.getElementById('card-element').classList.remove('has-error');

            // Clear field errors
            document.querySelectorAll('.error').forEach(input => {
                input.classList.remove('error');
            });
            document.querySelectorAll('.field-error').forEach(error => {
                error.style.display = 'none';
            });
        }

        function setLoadingState(loading) {
            submitButton.disabled = loading;
            if (loading) {
                buttonText.textContent = 'Processing...';
                spinner.style.display = 'inline-block';
                submitButton.classList.add('loading');
            } else {
                buttonText.textContent = `Pay ${{ number_format($amount / 100, 2) }}`;
                spinner.style.display = 'none';
                submitButton.classList.remove('loading');
            }
        }

        function resetButton() {
            setLoadingState(false);
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

        }

        .summary-item.total {
            font-weight: 600;
            font-size: 1.1rem;
            margin-top: 0.5rem;
            padding-top: 1rem;
            border-top: 2px solid #dee2e6;
        }

        .stripe-element {
            padding: 15px;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            background-color: #ffffff;
            min-height: 50px;
            font-size: 16px;
        }

        .stripe-element:focus-within {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .stripe-element:focus-within {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .stripe-element.has-error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
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
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
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

        /* Form input error states */
        .form-input.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .field-error {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        /* Submit button states */
        .submit-button {
            transition: all 0.2s ease;
            position: relative;
        }

        .submit-button.loading {
            opacity: 0.8;
            cursor: not-allowed;
        }

        .submit-button.card-complete {
            background-color: #059669;
            border-color: #059669;
        }

        .submit-button.card-complete:hover {
            background-color: #047857;
            border-color: #047857;
        }

        /* Form input focus states */
        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        /* Loading spinner improvements */
        .spinner {
            margin-left: 8px;
        }

        /* Responsive improvements */
        @media (max-width: 640px) {
            .form-grid-3 {
                grid-template-columns: 1fr;
            }

            .summary-item {
                font-size: 0.875rem;
            }

            .submit-button {
                padding: 12px 20px;
                font-size: 16px;
                /* Prevents zoom on iOS */
            }
        }
    </style>

    @include('partials.footer')
</body>

</html>
