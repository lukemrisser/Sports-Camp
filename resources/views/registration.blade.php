<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sports Camp Registration - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('partials.header', [
        'title' => 'Falcon Teams',
    ])
    <div class="registration-page">
        <div class="registration-container">
            <div class="registration-form-wrapper">
                <div class="registration-header">
                    <h2 class="registration-title">Falcon Teams Registration</h2>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

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

                <!-- Age/Gender Validation Error -->
                <div id="validation-error" class="alert alert-error" style="display: none;">
                    <ul class="error-list">
                        <li id="validation-message"></li>
                    </ul>
                </div>

                <form method="POST" action="{{ route('players.store') }}" class="registration-form">
                    @csrf

                    <!-- Camp Selection -->
                    <div class="form-section">
                        <h3 class="section-title">Camp Selection</h3>
                        <div class="form-grid-1">
                            <div class="form-group">
                                <label class="form-label">Select Camp <span class="text-red-500">*</span></label>
                                @if (isset($availableCamps) && $availableCamps->count() > 0)
                                    <select name="Camp_ID" class="form-input" required>
                                        <option value="">Select Camp</option>
                                        @foreach ($availableCamps as $camp)
                                            @php
                                                if ($camp->Camp_Gender == 'boys') {
                                                    $gender = 'Boys ';
                                                } elseif ($camp->Camp_Gender == 'girls') {
                                                    $gender = 'Girls ';
                                                } else {
                                                    $gender = 'Coed ';
                                                }
                                                $ageRange = ": Ages {$camp->Age_Min}-{$camp->Age_Max}";
                                                $fullTitle = $gender . $camp->Camp_Name . $ageRange;
                                            @endphp
                                            <option value="{{ $camp->Camp_ID }}"
                                                @if (isset($selectedCampId) && $camp->Camp_ID == $selectedCampId) selected @endif>
                                                {{ $fullTitle }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <div class="alert alert-error">
                                        <p><strong>No camps are currently accepting registrations.</strong></p>
                                        <p>Please check back later or contact us for more information.</p>
                                    </div>
                                    <input type="hidden" name="Camp_ID" value="">
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Parent Information -->
                    <div class="form-section">
                        <h3 class="section-title">Parent/Guardian Information</h3>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label class="form-label">Parent First Name <span class="text-red-500">*</span></label>
                                <input type="text" name="Parent_FirstName" class="form-input" required
                                    value="{{ old('Parent_FirstName', isset($parent) ? $parent->Parent_FirstName : '') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Parent Last Name <span class="text-red-500">*</span></label>
                                <input type="text" name="Parent_LastName" class="form-input" required
                                    value="{{ old('Parent_LastName', isset($parent) ? $parent->Parent_LastName : '') }}">
                            </div>
                            @if (isset($parent) && $parent)
                                <input type="hidden" name="Parent_ID" value="{{ $parent->Parent_ID }}">
                            @endif
                        </div>
                    </div>

                    <!-- Camper Information -->
                    <div class="form-section">
                        <h3 class="section-title">Camper Information</h3>
                        <!-- Existing player selector (loads data into fields) -->
                        @if (isset($parent) && count($parent->players ?? []) > 0)
                            <div class="form-group">
                                <label for="existing_player">Load Existing Player</label>
                                <select id="existing_player" name="existing_player_id" class="form-control">
                                    <option value="">-- Use New Camper / Select One --</option>
                                    @foreach ($parent->players as $p)
                                        <option value="{{ $p->Player_ID }}">{{ $p->Camper_FirstName }} {{ $p->Camper_LastName }}</option>
                                    @endforeach
                                </select>
                                <p id="existing-player-error" class="text-red-600 mt-1" style="display:none;"></p>
                            </div>

                        @endif
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label class="form-label">Camper First Name <span class="text-red-500">*</span></label>
                                <input type="text" name="Camper_FirstName" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Camper Last Name <span class="text-red-500">*</span></label>
                                <input type="text" name="Camper_LastName" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Gender <span class="text-red-500">*</span></label>
                                <select name="Gender" class="form-input" required>
                                    <option value="">Select Gender</option>
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Birth Date <span class="text-red-500">*</span></label>
                                <input type="date" name="Birth_Date" class="form-input" required
                                    max="{{ date('Y-m-d') }}" min="{{ date('Y-m-d', strtotime('-100 years')) }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Shirt Size <span class="text-red-500">*</span></label>
                                <select name="Shirt_Size" class="form-input" required>
                                    <option value="">Select Size</option>
                                    <option value="YS">Youth Small</option>
                                    <option value="YM">Youth Medium</option>
                                    <option value="YL">Youth Large</option>
                                    <option value="AS">Adult Small</option>
                                    <option value="AM">Adult Medium</option>
                                    <option value="AL">Adult Large</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Teammate Requests -->
                    <div class="form-section">
                        <h3 class="section-title">Teammate Requests (optional)</h3>
                        <p class="text-sm text-gray-600 mb-3">If you'd like to request teammates, enter their names
                            below. You can add multiple requests.</p>
                        <div id="teammate-requests">
                            <div class="teammate-request form-grid-2 relative">
                                <div class="form-group">
                                    <label class="form-label">Teammate First Name</label>
                                    <input type="text" name="teammate_first[]" class="form-input" />
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Teammate Last Name</label>
                                    <input type="text" name="teammate_last[]" class="form-input" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" id="add-teammate" class="submit-button" style="width: auto;">Add
                                another teammate request</button>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="form-section">
                        <h3 class="section-title">Parent Contact Information</h3>
                        <div class="form-group">
                            <label class="form-label">Address <span class="text-red-500">*</span></label>
                            <input type="text" name="Address" class="form-input" required
                                value="{{ old('Address', isset($parent) ? $parent->Address : '') }}">
                        </div>
                        <div class="form-grid-3">
                            <div class="form-group">
                                <label class="form-label">City <span class="text-red-500">*</span></label>
                                <input type="text" name="City" class="form-input" required
                                    value="{{ old('City', isset($parent) ? $parent->City : '') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">State <span class="text-red-500">*</span></label>
                                <input type="text" name="State" class="form-input" required
                                    value="{{ old('State', isset($parent) ? $parent->State : '') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">ZIP Code <span class="text-red-500">*</span></label>
                                <input type="text" name="Postal_Code" class="form-input" required
                                    value="{{ old('Postal_Code', isset($parent) ? $parent->Postal_Code : '') }}">
                            </div>
                        </div>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label class="form-label">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="Email" class="form-input" required
                                    value="{{ old('Email', isset($parent) ? $parent->Email : auth()->user()->email ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone <span class="text-red-500">*</span></label>
                                <input type="tel" class="form-input" id="phone" name="Phone"
                                    placeholder="(123) 456-7890" maxlength="14" required
                                    value="{{ old('Phone', isset($parent) ? $parent->Phone : '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Medical Information -->
                    <div class="form-section">
                        <h3 class="section-title">Medical Information</h3>
                        <div class="form-group">
                            <label class="form-label">Does the camper have asthma? <span
                                    class="text-red-500">*</span></label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="Asthma" value="1" class="radio-input"
                                        required>
                                    <span class="radio-text">Yes</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="Asthma" value="0" class="radio-input"
                                        required>
                                    <span class="radio-text">No</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Is the camper on any medications? <span
                                    class="text-red-500">*</span></label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="medication_status_choice" value="1"
                                        class="radio-input" id="medication-yes" required>
                                    <span class="radio-text">Yes</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="medication_status_choice" value="0"
                                        class="radio-input" id="medication-no" required>
                                    <span class="radio-text">No</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group" id="medication-details-container" style="display: none;">
                            <label class="form-label">Medication Details</label>
                            <textarea id="medication_status" name="Medication_Status" class="form-input form-textarea" rows="3"
                                placeholder="List the medications and dosages"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Allergies</label>
                            <textarea name="Allergies" class="form-input form-textarea" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Recent Injuries or Health Concerns</label>
                            <textarea name="Injuries" class="form-input form-textarea" rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Church Information -->
                    <div class="form-section">
                        <h3 class="section-title">Church Information</h3>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label class="form-label">Church Name</label>
                                <input type="text" name="Church_Name" class="form-input"
                                    value="{{ old('Church_Name', isset($parent) ? $parent->Church_Name : '') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Church Attendance</label>
                                <select name="Church_Attendance" class="form-input">
                                    <option value="">Select Frequency</option>
                                    <option value="Weekly" @if (old('Church_Attendance', isset($parent) ? $parent->Church_Attendance : '') == 'Weekly') selected @endif>Weekly
                                    </option>
                                    <option value="Monthly" @if (old('Church_Attendance', isset($parent) ? $parent->Church_Attendance : '') == 'Monthly') selected @endif>Monthly
                                    </option>
                                    <option value="Occasionally" @if (old('Church_Attendance', isset($parent) ? $parent->Church_Attendance : '') == 'Occasionally') selected @endif>
                                        Occasionally</option>
                                    <option value="Rarely" @if (old('Church_Attendance', isset($parent) ? $parent->Church_Attendance : '') == 'Rarely') selected @endif>Rarely
                                    </option>
                                    <option value="Never" @if (old('Church_Attendance', isset($parent) ? $parent->Church_Attendance : '') == 'Never') selected @endif>Never
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Add Ons / Extra Fees -->
                    <div class="form-section" id="add-ons-section" style="display: none;">
                        <h3 class="section-title">Add Ons</h3>
                        <p class="text-sm text-gray-600 mb-3">Select any optional add-ons you'd like to include.</p>
                        <div id="add-ons-list" class="add-ons-container">
                            <!-- Add-ons will be populated here via JavaScript -->
                        </div>
                        <div id="add-ons-total" class="mt-3" style="display: none;">
                            <strong>Add Ons Total: <span id="add-ons-amount">$0.00</span></strong>
                        </div>
                        <input type="hidden" id="selected_add_ons" name="selected_add_ons" value="">
                    </div>

                    <!-- Submit Button -->
                    <div class="submit-section">
                        <div class="form-section">
                            <h3 class="section-title">Promo Code</h3>
                            <div class="form-group">
                                <label class="form-label">Have a promo code?</label>
                                <input type="text" id="reg_promo_code" name="promo_code" class="form-input" placeholder="Enter promo code">
                                <input type="hidden" id="reg_discount_amount" name="discount_amount" value="0">
                                <div id="reg-promo-message" class="field-error" style="display:none;"></div>
                                <div id="reg-promo-success" class="success-message" style="display:none;">Discount applied: <strong id="reg-discount-amount">$0.00</strong></div>
                            </div>
                        </div>

                        <button type="submit" class="submit-button">
                            Continue to Payment →
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Store camp data globally for validation
        let campData = {};

        // Fetch all camp data on page load
        document.addEventListener('DOMContentLoaded', function() {
            @foreach ($availableCamps as $camp)
                campData[{{ $camp->Camp_ID }}] = {
                    age_min: {{ $camp->Age_Min }},
                    age_max: {{ $camp->Age_Max }},
                    gender: '{{ $camp->Camp_Gender }}',
                    name: '{{ $camp->Camp_Name }}'
                };
            @endforeach

                // Players data for authenticated parent (id => fields + camps)
                const playersData = {!! json_encode($playersForJs ?? []) !!};
                const playersIndex = {};
                playersData.forEach(p => { playersIndex[p.Player_ID] = p; });

                // If an existing player is selected, populate fields and check camp registration
                const existingSelect = document.getElementById('existing_player');
                const existingError = document.getElementById('existing-player-error');
                if (existingSelect) {
                    existingSelect.addEventListener('change', function() {
                        existingError.style.display = 'none';
                        const pid = this.value;

                        // helper to clear camper fields
                        function clearCamperFields() {
                            const fields = ['Camper_FirstName','Camper_LastName','Gender','Birth_Date','Shirt_Size','Allergies','Asthma','Medication_Status','Injuries'];
                            fields.forEach(name => {
                                const el = document.querySelector('[name="' + name + '"]');
                                if (el) {
                                    if (el.tagName === 'SELECT' || el.type === 'text' || el.type === 'date' || el.tagName === 'TEXTAREA' || el.type === 'tel') {
                                        el.value = '';
                                    } else if (el.type === 'radio' || el.type === 'checkbox') {
                                        el.checked = false;
                                    }
                                }
                            });
                        }

                        if (!pid) {
                            clearCamperFields();
                            return;
                        }

                        const player = playersIndex[pid];
                        if (!player) {
                            clearCamperFields();
                            return;
                        }

                        // Check if player already registered for selected camp
                        const campSelect = document.querySelector('select[name="Camp_ID"]');
                        const selectedCampId = campSelect ? campSelect.value : null;
                        if (selectedCampId && player.camps && player.camps.some(c => String(c.Camp_ID) === String(selectedCampId))) {
                            existingError.textContent = 'This player is already registered for the selected camp.';
                            existingError.style.display = 'block';
                            // reset selection
                            this.value = '';
                            return;
                        }

                        // Populate form fields
                        const setIf = (name, value) => { const el = document.querySelector('[name="'+name+'"]'); if(el) el.value = value ?? ''; };
                        setIf('Camper_FirstName', player.Camper_FirstName);
                        setIf('Camper_LastName', player.Camper_LastName);
                        setIf('Gender', player.Gender);
                        setIf('Birth_Date', player.Birth_Date);
                        setIf('Shirt_Size', player.Shirt_Size);
                        setIf('Allergies', player.Allergies);
                        setIf('Asthma', player.Asthma);
                        setIf('Medication_Status', player.Medication_Status);
                        setIf('Injuries', player.Injuries);
                    });

                    // Also re-validate if camp changes while an existing player is selected
                    const campSel = document.querySelector('select[name="Camp_ID"]');
                    if (campSel) {
                        campSel.addEventListener('change', function() {
                            if (!existingSelect.value) return;
                            const pid = existingSelect.value;
                            const player = playersIndex[pid];
                            if (!player) return;
                            const selectedCampId = this.value;
                            if (selectedCampId && player.camps && player.camps.some(c => String(c.Camp_ID) === String(selectedCampId))) {
                                existingError.textContent = 'The selected player is already registered for this camp. Please choose another camper or a different camp.';
                                existingError.style.display = 'block';
                            } else {
                                existingError.style.display = 'none';
                            }
                        });
                    }
                }

            const phoneInput = document.getElementById('phone');
            const addOnsSection = document.getElementById('add-ons-section');
            const campSelect = document.querySelector('select[name="Camp_ID"]');

            // Function to load add-ons for selected camp
            async function loadAddOns(campId) {
                if (!campId) {
                    addOnsSection.style.display = 'none';
                    return;
                }

                try {
                    const response = await fetch(`{{ url('/camps') }}/${campId}/add-ons`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();

                    if (data.add_ons && data.add_ons.length > 0) {
                        const addOnsList = document.getElementById('add-ons-list');
                        addOnsList.innerHTML = '';

                        data.add_ons.forEach(fee => {
                            const checkboxContainer = document.createElement('div');
                            checkboxContainer.className = 'form-group add-on-item';
                            checkboxContainer.innerHTML = `
                                <label class="checkbox-label">
                                    <input type="checkbox" class="checkbox-input add-on-checkbox" 
                                        data-fee-id="${fee.Fee_ID}" 
                                        data-fee-name="${fee.Fee_Name}"
                                        data-fee-amount="${fee.Fee_Amount}"
                                        value="${fee.Fee_ID}">
                                    <span class="checkbox-text">${fee.Fee_Name} - $${parseFloat(fee.Fee_Amount).toFixed(2)}</span>
                                    ${fee.Fee_Description ? `<span class="text-sm text-gray-600" style="margin-left: 8px;">${fee.Fee_Description}</span>` : ''}
                                </label>
                            `;
                            addOnsList.appendChild(checkboxContainer);
                        });

                        addOnsSection.style.display = 'block';
                        updateAddOnsTotal();

                        // Add event listeners to checkboxes
                        document.querySelectorAll('.add-on-checkbox').forEach(checkbox => {
                            checkbox.addEventListener('change', updateAddOnsTotal);
                        });
                    } else {
                        addOnsSection.style.display = 'none';
                    }
                } catch (error) {
                    console.error('Error loading add-ons:', error);
                    addOnsSection.style.display = 'none';
                }
            }

            // Function to update add-ons total and hidden field
            function updateAddOnsTotal() {
                const checkboxes = document.querySelectorAll('.add-on-checkbox:checked');
                let total = 0;
                const selectedIds = [];

                checkboxes.forEach(checkbox => {
                    total += parseFloat(checkbox.dataset.feeAmount);
                    selectedIds.push(checkbox.dataset.feeId);
                });

                document.getElementById('add-ons-amount').textContent = '$' + total.toFixed(2);
                document.getElementById('selected_add_ons').value = selectedIds.join(',');

                if (checkboxes.length > 0) {
                    document.getElementById('add-ons-total').style.display = 'block';
                } else {
                    document.getElementById('add-ons-total').style.display = 'none';
                }
            }

            // Load add-ons when camp selection changes
            if (campSelect) {
                campSelect.addEventListener('change', function() {
                    loadAddOns(this.value);
                });

                // Load add-ons if a camp is already selected
                if (campSelect.value) {
                    loadAddOns(campSelect.value);
                }
            }

            function formatPhone(value) {
                value = value.replace(/\D/g, '').substring(0, 10);
                if (value.length >= 6) {
                    return '(' + value.substring(0, 3) + ') ' + value.substring(3, 6) + '-' + value.substring(6);
                } else if (value.length >= 3) {
                    return '(' + value.substring(0, 3) + ') ' + value.substring(3);
                } else if (value.length > 0) {
                    return '(' + value;
                }
                return value;
            }

            if (phoneInput.value) {
                phoneInput.value = formatPhone(phoneInput.value);
            }

            phoneInput.addEventListener('input', function(e) {
                const input = e.target;
                const oldValue = input.value;
                const digitsBefore = oldValue.slice(0, input.selectionStart).replace(/\D/g, '').length;

                const formatted = formatPhone(oldValue);
                input.value = formatted;

                // Recalculate new cursor position
                let cursor = formatted.length;
                let digitCount = 0;
                for (let i = 0; i < formatted.length; i++) {
                    if (/\d/.test(formatted[i])) {
                        digitCount++;
                    }
                    if (digitCount === digitsBefore) {
                        cursor = i + 1;
                        break;
                    }
                }

                input.setSelectionRange(cursor, cursor);
            });

            // Validation function
            function validateAgeAndGender() {
                const campSelect = document.querySelector('select[name="Camp_ID"]');
                const birthDateInput = document.querySelector('input[name="Birth_Date"]');
                const genderSelect = document.querySelector('select[name="Gender"]');
                const validationError = document.getElementById('validation-error');
                const validationMessage = document.getElementById('validation-message');

                // Clear previous error
                validationError.style.display = 'none';
                validationMessage.textContent = '';

                // If no camp selected, skip validation
                if (!campSelect.value) {
                    return true;
                }

                const campId = parseInt(campSelect.value);
                const camp = campData[campId];
                const birthDate = birthDateInput.value;
                const selectedGender = genderSelect.value;

                if (!camp || !birthDate || !selectedGender) {
                    return true; // Skip if incomplete
                }

                // Calculate age
                const birth = new Date(birthDate);
                const today = new Date();
                let age = today.getFullYear() - birth.getFullYear();
                const monthDiff = today.getMonth() - birth.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                    age--;
                }

                var validation = true
                // Validate age
                if (age < camp.age_min || age > camp.age_max) {
                    validationMessage.textContent =
                        `Camper age (${age}) must be between ${camp.age_min} and ${camp.age_max} for this camp.`;
                    validationError.style.display = 'block';
                    validation = false;
                }

                // Validate gender
                const genderMap = {
                    'M': 'boys',
                    'F': 'girls'
                };
                const camperGender = genderMap[selectedGender];

                // if (camp.gender !== 'coed' && camp.gender !== camperGender) {
                //     const campGenderName = camp.gender === 'boys' ? 'boys' : 'girls';
                //     const camperGenderName = selectedGender === 'M' ? 'Male' : '';
                //     validationMessage.textContent =
                //         `This camp is for ${campGenderName} only, but you selected ${camperGenderName}.`;
                //     validationError.style.display = 'block';
                //     validation = false;
                // }

                return validation;
            }

            // Listen for changes to camp, birth date, and gender
            document.querySelector('select[name="Camp_ID"]').addEventListener('change', validateAgeAndGender);
            document.querySelector('input[name="Birth_Date"]').addEventListener('change', validateAgeAndGender);
            document.querySelector('select[name="Gender"]').addEventListener('change', validateAgeAndGender);

            // Toggle medication details field
            const medicationYes = document.getElementById('medication-yes');
            const medicationNo = document.getElementById('medication-no');
            const medicationDetailsContainer = document.getElementById('medication-details-container');
            const medicationStatusField = document.getElementById('medication_status');

            function toggleMedicationDetails() {
                if (medicationYes.checked) {
                    medicationDetailsContainer.style.display = 'block';
                    medicationStatusField.setAttribute('required', 'required');
                } else {
                    medicationDetailsContainer.style.display = 'none';
                    medicationStatusField.removeAttribute('required');
                    medicationStatusField.value = ''; // Clear the field if "No" is selected
                }
            }

            medicationYes.addEventListener('change', toggleMedicationDetails);
            medicationNo.addEventListener('change', toggleMedicationDetails);
            // Promo code handling on registration
            const regPromoInput = document.getElementById('reg_promo_code');
            const regPromoMessage = document.getElementById('reg-promo-message');
            const regPromoSuccess = document.getElementById('reg-promo-success');
            const regDiscountInput = document.getElementById('reg_discount_amount');
            let regPromoValidated = false;

            async function validateRegPromoCode() {
                regPromoMessage.style.display = 'none';
                regPromoSuccess.style.display = 'none';
                regPromoMessage.textContent = '';
                const code = regPromoInput.value.trim();
                const campSelect = document.querySelector('select[name="Camp_ID"]');
                const campId = campSelect ? campSelect.value : null;
                if (!code) {
                    regDiscountInput.value = '0';
                    regPromoValidated = false;
                    return false;
                }
                if (!campId) {
                    regPromoMessage.textContent = 'Please select a camp before applying a promo code.';
                    regPromoMessage.style.display = 'block';
                    regPromoValidated = false;
                    return false;
                }

                regPromoMessage.textContent = 'Validating promo code...';
                regPromoMessage.style.display = 'block';

                try {
                    const res = await fetch(`{{ url('/validate-promo-code') }}?camp_id=${campId}&code=${encodeURIComponent(code)}`, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    if (data.valid) {
                        regDiscountInput.value = data.discount_amount;
                        document.getElementById('reg-discount-amount').textContent = '$' + parseFloat(data.discount_amount).toFixed(2);
                        regPromoMessage.style.display = 'none';
                        regPromoSuccess.style.display = 'block';
                        regPromoValidated = true;
                        return true;
                    } else {
                        regDiscountInput.value = '0';
                        regPromoMessage.textContent = data.message || 'Invalid or expired promo code';
                        regPromoMessage.style.display = 'block';
                        regPromoValidated = false;
                        return false;
                    }
                } catch (e) {
                    regDiscountInput.value = '0';
                    regPromoMessage.textContent = 'Error validating promo code';
                    regPromoMessage.style.display = 'block';
                    regPromoValidated = false;
                    return false;
                }
            }

            if (regPromoInput) {
                regPromoInput.addEventListener('blur', validateRegPromoCode);
            }

            // Prevent form submission if validation fails
            const form = document.querySelector('.registration-form');
            form.addEventListener('submit', async function(e) {
                if (!validateAgeAndGender()) {
                    e.preventDefault();
                    return false;
                }

                const code = regPromoInput ? regPromoInput.value.trim() : '';
                if (code && !regPromoValidated) {
                    // try to validate now
                    e.preventDefault();
                    const ok = await validateRegPromoCode();
                    if (ok) {
                        // submit after successful validation
                        form.submit();
                    }
                    return false;
                }
            });
        });

        // Handle adding and removing teammate request fields
        document.getElementById('add-teammate').addEventListener('click', function() {
            const container = document.getElementById('teammate-requests');
            const newRequest = document.createElement('div');
            newRequest.classList.add('teammate-request', 'form-grid-2', 'relative');
            newRequest.innerHTML = `
                <div class="form-group">
                    <label class="form-label">Teammate First Name</label>
                    <input type="text" name="teammate_first[]" class="form-input" />
                </div>
                <div class="form-group">
                    <label class="form-label">Teammate Last Name</label>
                    <input type="text" name="teammate_last[]" class="form-input" />
                </div>
                <button type="button" class="remove-teammate absolute right-0 top-8 px-3 text-red-500 hover:text-red-700" title="Remove teammate request">&times;</button>
            `;
            container.appendChild(newRequest);

            const removeButton = newRequest.querySelector('.remove-teammate');
            removeButton.addEventListener('click', function() {
                newRequest.remove();
            });
        });

        document.querySelectorAll('.remove-teammate').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.teammate-request').remove();
            });
        });
    </script>

    @include('partials.footer')
</body>

</html>
