<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sports Camp Registration - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<header class="main-header">
    <div class=header-container>
        <div class="header-content">
            <h1>Falcon Teams</h1>
            <p>Complete the registration form to sign up</p>
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

                <form method="POST" action="{{ route('players.store') }}" class="registration-form">
                    @csrf

                    <!-- Camp Selection -->
                    <div class="form-section">
                        <h3 class="section-title">Camp Selection</h3>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label class="form-label">Select Camp</label>
                                @php
                                    $availableCamps = \App\Models\Camp::getAvailableForRegistration();
                                @endphp
                                
                                @if($availableCamps->count() > 0)
                                    <select name="Camp_ID" class="form-input" required>
                                        <option value="">Select Camp</option>
                                        @foreach($availableCamps as $camp)
                                            <option value="{{ $camp->Camp_ID }}">{{ $camp->Camp_Name }}</option>
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
                            <div class="form-group">
                                <label class="form-label">Division Name</label>
                                <input type="text" name="Division_Name" class="form-input" required 
                                       placeholder="e.g., Boys 10-12, Girls 8-10">
                            </div>
                        </div>
                    </div>

                    <!-- Parent Information -->
                    <div class="form-section">
                        <h3 class="section-title">Parent/Guardian Information</h3>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label class="form-label">Parent First Name</label>
                                <input type="text" name="Parent_FirstName" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Parent Last Name</label>
                                <input type="text" name="Parent_LastName" class="form-input" required>
                            </div>
                        </div>
                    </div>

                    <!-- Camper Information -->
                    <div class="form-section">
                        <h3 class="section-title">Camper Information</h3>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label class="form-label">Camper First Name</label>
                                <input type="text" name="Camper_FirstName" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Camper Last Name</label>
                                <input type="text" name="Camper_LastName" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Gender</label>
                                <select name="Gender" class="form-input" required>
                                    <option value="">Select Gender</option>
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Birth Date</label>
                                <input type="date" name="Birth_Date" class="form-input" required
                                    max="{{ date('Y-m-d') }}" min="{{ date('Y-m-d', strtotime('-100 years')) }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Shirt Size</label>
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
                                <button type="button"
                                    class="remove-teammate absolute right-0 top-8 px-3 text-red-500 hover:text-red-700"
                                    title="Remove teammate request">&times;</button>
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
                            <label class="form-label">Address</label>
                            <input type="text" name="Address" class="form-input" required>
                        </div>
                        <div class="form-grid-3">
                            <div class="form-group">
                                <label class="form-label">City</label>
                                <input type="text" name="City" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">State</label>
                                <input type="text" name="State" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">ZIP Code</label>
                                <input type="text" name="Postal_Code" class="form-input" required>
                            </div>
                        </div>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="Email" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-input" id="phone" name="Phone"
                                    placeholder="(123) 456-7890" maxlength="14" required>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Information -->
                    <div class="form-section">
                        <h3 class="section-title">Medical Information</h3>
                        <div class="form-group">
                            <label class="form-label">Allergies</label>
                            <textarea name="Allergies" class="form-input form-textarea" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Does the camper have asthma?</label>
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
                            <label class="form-label">Is the camper on any medications?</label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="Medication_Status" value="1"
                                        class="radio-input" required>
                                    <span class="radio-text">Yes</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="Medication_Status" value="0"
                                        class="radio-input" required>
                                    <span class="radio-text">No</span>
                                </label>
                            </div>
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
                                <input type="text" name="Church_Name" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Church Attendance</label>
                                <select name="Church_Attendance" class="form-input">
                                    <option value="">Select Frequency</option>
                                    <option value="Weekly">Weekly</option>
                                    <option value="Monthly">Monthly</option>
                                    <option value="Occasionally">Occasionally</option>
                                    <option value="Rarely">Rarely</option>
                                    <option value="Never">Never</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="submit-section">
                        <button type="submit" class="submit-button">
                            Continue to Payment →
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '').substring(0, 10);
            if (value.length >= 6) {
                value = '(' + value.substring(0, 3) + ') ' + value.substring(3, 6) + '-' + value.substring(6);
            } else if (value.length >= 3) {
                value = '(' + value.substring(0, 3) + ') ' + value.substring(3);
            } else if (value.length > 0) {
                value = '(' + value;
            }
            e.target.value = value;
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
</body>

</html>
