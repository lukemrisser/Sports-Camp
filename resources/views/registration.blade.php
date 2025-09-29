<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sports Camp Registration - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="registration-page">
        <div class="registration-container">
            <div class="registration-form-wrapper">
                <div class="registration-header">
                    <h2 class="registration-title">Falcon Teams Registration</h2>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <ul class="error-list">
                            @foreach($errors->all() as $error)
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
                        <div class="form-group">
                            <label class="form-label">Select Camp</label>
                            <select name="Division_Name" class="form-input" required>
                                <option value="">Select Camp</option>
                                <option value="Aroma">Aroma All Sports Camp</option>
                            </select>
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
                                <input type="date" name="Birth_Date" class="form-input" required max="{{ date('Y-m-d') }}" min="{{ date('Y-m-d', strtotime('-100 years')) }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Age</label>
                                <input type="number" name="Age" class="form-input" required>
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

                    <!-- Contact Information -->
                    <div class="form-section">
                        <h3 class="section-title">Contact Information</h3>
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
                                <input type="tel"
                                    class="form-input"
                                    id="phone"
                                    name="Phone"
                                    placeholder="(123) 456-7890"
                                    maxlength="14"
                                    required>
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
                                    <input type="radio" name="Asthma" value="1" class="radio-input" required>
                                    <span class="radio-text">Yes</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="Asthma" value="0" class="radio-input" required>
                                    <span class="radio-text">No</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Is the camper on any medications?</label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="Medication_Status" value="1" class="radio-input" required>
                                    <span class="radio-text">Yes</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="Medication_Status" value="0" class="radio-input" required>
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
                            Submit Registration
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
    </script>
</body>
</html>
