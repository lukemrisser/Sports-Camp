<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Camp - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    @include('partials.header', [
        'title' => 'Falcon Teams',
    ])

    <style>
        .currency-symbol {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #555;
        }
    </style>

    <div class="registration-page">
        <div class="registration-container">
            <div class="registration-form-wrapper">
                <div class="registration-header">
                    <h2 class="registration-title">Create New Camp</h2>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
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

                <form method="POST" action="{{ route('store-camp') }}" class="registration-form">
                    @csrf

                    <div class="form-section">
                        <div class="form-group">
                            <label for="name" class="form-label">
                                Camp Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" required class="form-input"
                                value="{{ old('name') }}">
                        </div>

                        <div class="form-group">
                            <label for="sport_id" class="form-label">
                                Sport <span class="text-red-500">*</span>
                            </label>
                            <select name="sport_id" id="sport_id" required class="form-input">
                                <option value="" disabled
                                    {{ old('sport_id', $defaultSportId) ? '' : 'selected' }}>Select a sport</option>
                                @foreach ($sports as $sport)
                                    <option value="{{ $sport->Sport_ID }}"
                                        {{ old('sport_id', $defaultSportId) == $sport->Sport_ID ? 'selected' : '' }}>
                                        {{ $sport->Sport_Name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="start_date" class="form-label">
                                    Start Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="start_date" id="start_date" required class="form-input"
                                    value="{{ old('start_date') }}">
                            </div>

                            <div class="form-group">
                                <label for="end_date" class="form-label">
                                    End Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="end_date" id="end_date" required class="form-input"
                                    value="{{ old('end_date') }}">
                            </div>
                        </div>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="registration_open" class="form-label">
                                    Registration Open Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="registration_open" id="registration_open" required
                                    class="form-input" value="{{ old('registration_open') }}">
                            </div>

                            <div class="form-group">
                                <label for="registration_close" class="form-label">
                                    Registration Close Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="registration_close" id="registration_close" required
                                    class="form-input" value="{{ old('registration_close') }}">
                            </div>
                        </div>

                        <div class="form-grid-3">
                            <div class="form-group">
                                <label for="gender" class="form-label">
                                    Camp Gender <span class="text-red-500">*</span>
                                </label>
                                <select name="gender" id="gender" required class="form-input">
                                    <option value="" disabled selected>Select gender</option>
                                    <option value="girls">Girls</option>
                                    <option value="boys">Boys</option>
                                    <option value="coed">Co-ed</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="min_age" class="form-label">
                                    Minimum Age <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="min_age" id="min_age" required class="form-input"
                                    min="1" max="100" value="{{ old('min_age') }}">
                            </div>

                            <div class="form-group">
                                <label for="max_age" class="form-label">
                                    Maximum Age <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="max_age" id="max_age" required class="form-input"
                                    min="1" max="100" value="{{ old('max_age') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" id="description" rows="4" required class="form-input form-textarea">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="max_capacity" class="form-label">
                                Maximum Capacity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="max_capacity" id="max_capacity" required class="form-input"
                                min="1" max="1000" value="{{ old('max_capacity') }}"
                                placeholder="Enter maximum number of participants">
                        </div>

                        <h5 class="section-title">Location Information</h5>
                        <p class="text-sm text-gray-600 mb-3">Enter the camp location details.</p>

                        <div class="form-group">
                            <label for="location_name" class="form-label">
                                Location Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="location_name" id="location_name" required
                                class="form-input" value="{{ old('location_name') }}"
                                placeholder="e.g., University Sports Complex, Community Center">
                        </div>

                        <div class="form-group">
                            <label for="street_address" class="form-label">
                                Street Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="street_address" id="street_address" required
                                class="form-input" value="{{ old('street_address') }}"
                                placeholder="123 Main Street">
                        </div>

                        <div class="form-grid-3">
                            <div class="form-group">
                                <label for="zip_code" class="form-label">
                                    ZIP Code <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="zip_code" id="zip_code" required class="form-input"
                                    value="{{ old('zip_code') }}" placeholder="12345" pattern="[0-9]{5}(-[0-9]{4})?"
                                    title="Please enter a valid ZIP code (e.g., 12345 or 12345-6789)">
                            </div>
                            <div class="form-group">
                                <label for="city" class="form-label">
                                    City <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="city" id="city" required class="form-input"
                                    value="{{ old('city') }}" placeholder="City">
                            </div>

                            <div class="form-group">
                                <label for="state" class="form-label">
                                    State <span class="text-red-500">*</span>
                                </label>
                                <select name="state" id="state" required class="form-input">
                                    <option value="" disabled {{ old('state') ? '' : 'selected' }}>Select State
                                    </option>
                                    <option value="AL" {{ old('state') == 'AL' ? 'selected' : '' }}>Alabama
                                    </option>
                                    <option value="AK" {{ old('state') == 'AK' ? 'selected' : '' }}>Alaska
                                    </option>
                                    <option value="AZ" {{ old('state') == 'AZ' ? 'selected' : '' }}>Arizona
                                    </option>
                                    <option value="AR" {{ old('state') == 'AR' ? 'selected' : '' }}>Arkansas
                                    </option>
                                    <option value="CA" {{ old('state') == 'CA' ? 'selected' : '' }}>California
                                    </option>
                                    <option value="CO" {{ old('state') == 'CO' ? 'selected' : '' }}>Colorado
                                    </option>
                                    <option value="CT" {{ old('state') == 'CT' ? 'selected' : '' }}>Connecticut
                                    </option>
                                    <option value="DE" {{ old('state') == 'DE' ? 'selected' : '' }}>Delaware
                                    </option>
                                    <option value="FL" {{ old('state') == 'FL' ? 'selected' : '' }}>Florida
                                    </option>
                                    <option value="GA" {{ old('state') == 'GA' ? 'selected' : '' }}>Georgia
                                    </option>
                                    <option value="HI" {{ old('state') == 'HI' ? 'selected' : '' }}>Hawaii
                                    </option>
                                    <option value="ID" {{ old('state') == 'ID' ? 'selected' : '' }}>Idaho</option>
                                    <option value="IL" {{ old('state') == 'IL' ? 'selected' : '' }}>Illinois
                                    </option>
                                    <option value="IN" {{ old('state') == 'IN' ? 'selected' : '' }}>Indiana
                                    </option>
                                    <option value="IA" {{ old('state') == 'IA' ? 'selected' : '' }}>Iowa</option>
                                    <option value="KS" {{ old('state') == 'KS' ? 'selected' : '' }}>Kansas
                                    </option>
                                    <option value="KY" {{ old('state') == 'KY' ? 'selected' : '' }}>Kentucky
                                    </option>
                                    <option value="LA" {{ old('state') == 'LA' ? 'selected' : '' }}>Louisiana
                                    </option>
                                    <option value="ME" {{ old('state') == 'ME' ? 'selected' : '' }}>Maine</option>
                                    <option value="MD" {{ old('state') == 'MD' ? 'selected' : '' }}>Maryland
                                    </option>
                                    <option value="MA" {{ old('state') == 'MA' ? 'selected' : '' }}>Massachusetts
                                    </option>
                                    <option value="MI" {{ old('state') == 'MI' ? 'selected' : '' }}>Michigan
                                    </option>
                                    <option value="MN" {{ old('state') == 'MN' ? 'selected' : '' }}>Minnesota
                                    </option>
                                    <option value="MS" {{ old('state') == 'MS' ? 'selected' : '' }}>Mississippi
                                    </option>
                                    <option value="MO" {{ old('state') == 'MO' ? 'selected' : '' }}>Missouri
                                    </option>
                                    <option value="MT" {{ old('state') == 'MT' ? 'selected' : '' }}>Montana
                                    </option>
                                    <option value="NE" {{ old('state') == 'NE' ? 'selected' : '' }}>Nebraska
                                    </option>
                                    <option value="NV" {{ old('state') == 'NV' ? 'selected' : '' }}>Nevada
                                    </option>
                                    <option value="NH" {{ old('state') == 'NH' ? 'selected' : '' }}>New Hampshire
                                    </option>
                                    <option value="NJ" {{ old('state') == 'NJ' ? 'selected' : '' }}>New Jersey
                                    </option>
                                    <option value="NM" {{ old('state') == 'NM' ? 'selected' : '' }}>New Mexico
                                    </option>
                                    <option value="NY" {{ old('state') == 'NY' ? 'selected' : '' }}>New York
                                    </option>
                                    <option value="NC" {{ old('state') == 'NC' ? 'selected' : '' }}>North Carolina
                                    </option>
                                    <option value="ND" {{ old('state') == 'ND' ? 'selected' : '' }}>North Dakota
                                    </option>
                                    <option value="OH" {{ old('state') == 'OH' ? 'selected' : '' }}>Ohio</option>
                                    <option value="OK" {{ old('state') == 'OK' ? 'selected' : '' }}>Oklahoma
                                    </option>
                                    <option value="OR" {{ old('state') == 'OR' ? 'selected' : '' }}>Oregon
                                    </option>
                                    <option value="PA" {{ old('state') == 'PA' ? 'selected' : '' }}>Pennsylvania
                                    </option>
                                    <option value="RI" {{ old('state') == 'RI' ? 'selected' : '' }}>Rhode Island
                                    </option>
                                    <option value="SC" {{ old('state') == 'SC' ? 'selected' : '' }}>South Carolina
                                    </option>
                                    <option value="SD" {{ old('state') == 'SD' ? 'selected' : '' }}>South Dakota
                                    </option>
                                    <option value="TN" {{ old('state') == 'TN' ? 'selected' : '' }}>Tennessee
                                    </option>
                                    <option value="TX" {{ old('state') == 'TX' ? 'selected' : '' }}>Texas</option>
                                    <option value="UT" {{ old('state') == 'UT' ? 'selected' : '' }}>Utah</option>
                                    <option value="VT" {{ old('state') == 'VT' ? 'selected' : '' }}>Vermont
                                    </option>
                                    <option value="VA" {{ old('state') == 'VA' ? 'selected' : '' }}>Virginia
                                    </option>
                                    <option value="WA" {{ old('state') == 'WA' ? 'selected' : '' }}>Washington
                                    </option>
                                    <option value="WV" {{ old('state') == 'WV' ? 'selected' : '' }}>West Virginia
                                    </option>
                                    <option value="WI" {{ old('state') == 'WI' ? 'selected' : '' }}>Wisconsin
                                    </option>
                                    <option value="WY" {{ old('state') == 'WY' ? 'selected' : '' }}>Wyoming
                                    </option>
                                </select>
                            </div>


                        </div>

                        <h5 class="section-title">Financials</h5>
                        <div class="form-group">
                            <label for="price" class="form-label">
                                Normal Price <span class="text-red-500">*</span>
                            </label>
                            <div style="position: relative;">
                                <span class="currency-symbol">$</span>
                                <input type="number" name="price" id="price" required class="form-input"
                                    min=".01" step=".01" value="{{ old('price') }}"
                                    style="padding-left: 25px;">
                            </div>
                        </div>

                        <h5 class="section-title">Early Registration Discounts</h5>
                        <p class="text-sm text-gray-600 mb-3">Optionally add early bird discounts.</p>
                        <div id="discount-section">
                            <div class="form-grid-2 discount-group">
                                <div class="form-group">
                                    <label class="form-label">Early Discount Amount</label>
                                    <div style="position: relative;">
                                        <span class="currency-symbol">$</span>
                                        <input type="number" id="discount_amount" name="discount_amount[]"
                                            class="form-input" min="0.01" step="0.01"
                                            value="{{ old('discount_amount.0') }}" style="padding-left: 25px;">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Early Discount Deadline</label>
                                    <input type="date" id="discount_date" name="discount_date[]"
                                        class="form-input" value="{{ old('discount_date.0') }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-grid-2">
                            <div class="mt-1">
                                <button type="button" id="add-discount" class="submit-button"
                                    style="width: auto;">Add
                                    another discount</button>
                            </div>
                        </div>
                    </div>

                        <h5 class="section-title">Promo Codes</h5>
                        <p class="text-sm text-gray-600 mb-3">Optionally add promo codes that parents can use for discounts.</p>
                        <div id="promo-section">
                            <div class="form-grid-3 promo-group">
                                <div class="form-group">
                                    <label class="form-label">Promo Code</label>
                                    <input type="text"
                                        name="promo_code[]"
                                        class="form-input"
                                        placeholder="e.g., SUMMER2026"
                                        value="{{ old('promo_code.0') }}">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Promo Discount Amount</label>
                                    <div style="position: relative;">
                                        <span class="currency-symbol">$</span>
                                        <input type="number"
                                            name="promo_amount[]"
                                            class="form-input"
                                            min="0.01" step="0.01"
                                            value="{{ old('promo_amount.0') }}"
                                            style="padding-left: 25px;">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Promo End Date (optional)</label>
                                    <input type="date"
                                        name="promo_date[]"
                                        class="form-input"
                                        value="{{ old('promo_date.0') }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-grid-2">
                            <div class="mt-1">
                                <button type="button" id="add-promo" class="submit-button" style="width: auto;">Add
                                    another promo code</button>
                            </div>
                        </div>

                        <h5 class="section-title">Extra Fees</h5>
                        <p class="text-sm text-gray-600 mb-3">Optional add-ons like lunch, shirts, or rentals.</p>
                        <div id="extra-fee-section">
                            <div class="form-grid-3 extra-fee-group">
                                <div class="form-group">
                                    <label class="form-label">Fee Name</label>
                                    <input type="text" name="extra_fee_name[]" class="form-input"
                                        placeholder="e.g., Lunch" value="{{ old('extra_fee_name.0') }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Fee Amount</label>
                                    <div style="position: relative;">
                                        <span class="currency-symbol">$</span>
                                        <input type="number" name="extra_fee_amount[]" class="form-input"
                                            min="0" step="0.01" value="{{ old('extra_fee_amount.0') }}"
                                            style="padding-left: 25px;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Fee Description (optional)</label>
                                    <input type="text" name="extra_fee_description[]" class="form-input"
                                        placeholder="Short description" value="{{ old('extra_fee_description.0') }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-grid-2">
                            <div class="mt-1">
                                <button type="button" id="add-extra-fee" class="submit-button" style="width: auto;">Add
                                    another fee</button>
                            </div>
                        </div>

                    <div class="submit-section">
                        <button type="submit" class="submit-button">
                            Create Camp
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Dynamically add/remove discount fields
        document.getElementById('add-discount').addEventListener('click', function() {
            const container = document.getElementById('discount-section');
            const newRequest = document.createElement('div');
            newRequest.classList.add('discount-section', 'form-grid-2');
            newRequest.style.position = 'relative';
            newRequest.innerHTML = `
                <div class="form-group">
                    <label class="form-label">Early Discount Amount</label>
                    <div style="position: relative;">
                        <span class="currency-symbol">$</span>
                        <input type="number"
                            name="discount_amount[]"  class="form-input"
                            min="0.01" step="0.01"
                            style="padding-left: 25px;">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Early Discount Deadline</label>
                    <input type="date"
                        name="discount_date[]"  class="form-input">
                </div>
                <button type="button"
                    class="remove-discount"
                    style="position: absolute; right: -40px; top: 0; background: none; border: none; color: #dc2626; font-size: 32px; cursor: pointer; padding: 0; line-height: 1;"
                    title="Remove discount">&times;</button>
            `;
            container.appendChild(newRequest);

            const removeButton = newRequest.querySelector('.remove-discount');
            removeButton.addEventListener('click', function() {
                newRequest.remove();
            });
        });

        document.querySelectorAll('.remove-discount').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.discount-section').remove();
            });
        });

        // Location auto-fill functionality
        const zipCodeInput = document.getElementById('zip_code');
        const cityInput = document.getElementById('city');
        const stateSelect = document.getElementById('state');

        zipCodeInput.addEventListener('blur', function() {
            const zipCode = this.value.replace(/\D/g, '').substring(0, 5);

            if (zipCode.length === 5) {
                // Use a free ZIP code API to auto-fill city and state
                fetch(`https://api.zippopotam.us/us/${zipCode}`)
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        throw new Error('ZIP code not found');
                    })
                    .then(data => {
                        if (data.places && data.places.length > 0) {
                            const place = data.places[0];

                            // Auto-fill city if empty
                            if (!cityInput.value) {
                                cityInput.value = place['place name'];
                            }

                            // Auto-fill state if empty
                            if (!stateSelect.value) {
                                stateSelect.value = place['state abbreviation'];
                            }
                        }
                    })
                    .catch(error => {
                        // Silently fail - user can still enter manually
                        console.log('Could not auto-fill location:', error.message);
                    });
            }
        });

        // Format ZIP code as user types
        zipCodeInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 9);
            }
            this.value = value;
        });

        // State search functionality
        const stateOptions = Array.from(stateSelect.options);
        stateSelect.addEventListener('keydown', function(e) {
            if (e.key.length === 1 && e.key.match(/[a-zA-Z]/)) {
                const searchChar = e.key.toLowerCase();
                const currentIndex = this.selectedIndex;

                // Find next option starting with the typed character
                for (let i = currentIndex + 1; i < stateOptions.length; i++) {
                    if (stateOptions[i].text.toLowerCase().startsWith(searchChar)) {
                        this.selectedIndex = i;
                        e.preventDefault();
                        return;
                    }
                }

                // If not found after current, search from beginning
                for (let i = 1; i < currentIndex; i++) {
                    if (stateOptions[i].text.toLowerCase().startsWith(searchChar)) {
                        this.selectedIndex = i;
                        e.preventDefault();
                        return;
                    }
                }
            }
        });
     // Dynamically add/remove promo code fields
    document.getElementById('add-promo').addEventListener('click', function() {
            const container = document.getElementById('promo-section');
            const newPromo = document.createElement('div');
            newPromo.classList.add('promo-section', 'form-grid-3');
            newPromo.style.position = 'relative';
            newPromo.innerHTML = `
                <div class="form-group">
                    <label class="form-label">Promo Code</label>
                    <input type="text"
                        name="promo_code[]"
                        class="form-input"
                        placeholder="e.g., SUMMER2026">
                </div>

                <div class="form-group">
                    <label class="form-label">Promo Discount Amount</label>
                    <div style="position: relative;">
                        <span class="currency-symbol">$</span>
                        <input type="number"
                            name="promo_amount[]"  class="form-input"
                            min="0.01" step="0.01"
                            style="padding-left: 25px;">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Promo End Date (optional)</label>
                    <input type="date"
                        name="promo_date[]"  class="form-input">
                </div>

                <button type="button"
                    class="remove-promo"
                    style="position: absolute; right: -40px; top: 0; background: none; border: none; color: #dc2626; font-size: 32px; cursor: pointer; padding: 0; line-height: 1;"
                    title="Remove promo">&times;</button>
            `;
            container.appendChild(newPromo);

            const removeButton = newPromo.querySelector('.remove-promo');
            removeButton.addEventListener('click', function() {
                newPromo.remove();
            });
        });

        document.querySelectorAll('.remove-promo').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.promo-section').remove();
            });
        });

        // Dynamically add/remove extra fee fields
        document.getElementById('add-extra-fee').addEventListener('click', function() {
            const container = document.getElementById('extra-fee-section');
            const newFee = document.createElement('div');
            newFee.classList.add('extra-fee-group', 'form-grid-3');
            newFee.style.position = 'relative';
            newFee.innerHTML = `
                <div class="form-group">
                    <label class="form-label">Fee Name</label>
                    <input type="text" name="extra_fee_name[]" class="form-input" placeholder="e.g., Lunch">
                </div>
                <div class="form-group">
                    <label class="form-label">Fee Amount</label>
                    <div style="position: relative;">
                        <span class="currency-symbol">$</span>
                        <input type="number" name="extra_fee_amount[]" class="form-input" min="0" step="0.01" style="padding-left: 25px;">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Fee Description (optional)</label>
                    <input type="text" name="extra_fee_description[]" class="form-input" placeholder="Short description">
                </div>
                <button type="button"
                    class="remove-extra-fee"
                    style="position: absolute; right: -40px; top: 0; background: none; border: none; color: #dc2626; font-size: 32px; cursor: pointer; padding: 0; line-height: 1;"
                    title="Remove fee">&times;</button>
            `;
            container.appendChild(newFee);

            const removeButton = newFee.querySelector('.remove-extra-fee');
            removeButton.addEventListener('click', function() {
                newFee.remove();
            });
        });
</script>

    @include('partials.footer')
</body>

</html>
