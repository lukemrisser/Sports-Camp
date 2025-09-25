<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sports Camp Registration</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1a365d;
            text-align: center;
            margin-bottom: 2rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding-bottom: 1rem;
            border-bottom: 4px solid #4F46E5;
            display: inline-block;
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .form-input-field {
            margin-top: 0.25rem;
            display: block;
            width: 100%;
            border-radius: 0.375rem;
            border: 1px solid #D1D5DB;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            padding: 0.5rem 0.75rem;
            height: 2.5rem;
            appearance: none;
        }

        select.form-input-field {
            padding-top: 0;
            padding-bottom: 0;
            line-height: 2.5rem;
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }

        .form-input-field:focus {
            outline: none;
            border-color: #6366F1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.25rem;
        }

        .form-section {
            margin-bottom: 1.5rem;
        }

        .form-grid-2 {
            display: grid;
            gap: 1rem;
            grid-template-columns: 1fr;
        }

        @media (min-width: 768px) {
            .form-grid-2 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .form-grid-3 {
            display: grid;
            gap: 1rem;
            grid-template-columns: 1fr;
        }

        @media (min-width: 768px) {
            .form-grid-3 {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .radio-group {
            margin-top: 0.5rem;
            display: flex;
            gap: 1rem;
        }

        .radio-label {
            display: inline-flex;
            align-items: center;
        }

        .radio-text {
            margin-left: 0.5rem;
        }

        .submit-button {
            width: 100%;
            display: flex;
            justify-content: center;
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 0.375rem;
            background-color: #4F46E5;
            color: white;
            font-size: 0.875rem;
            font-weight: 500;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .submit-button:hover {
            background-color: #4338CA;
        }

        .submit-button:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.5);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-6 flex flex-col justify-center sm:py-12">
        <div class="relative py-3 sm:max-w-xl md:max-w-4xl mx-auto">
            <div class="relative px-4 py-10 bg-white mx-8 md:mx-0 shadow rounded-3xl sm:p-10">
                <div class="max-w-md mx-auto">
                    <div class="divide-y divide-gray-200">
                        <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                            <div class="text-center">
                                <h2 class="page-title">Falcon Teams</h2>
                            </div>

                            @if(session('success'))
                                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                                    {{ session('error') }}
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <form method="POST" action="{{ route('players.store') }}" class="space-y-6">
                                @csrf
                                <!-- Camp Selection -->
                                <div class="form-section">
                                    <h3 class="section-title">Camp Selection</h3>
                                    <div>
                                        <label class="form-label">Select Camp</label>
                                        <select name="Division_Name" class="form-input-field" required>
                                            <option value="">Select Camp</option>
                                            <!-- TODO: Get options dynamically from the database -->
                                             <option value="Aroma">Aroma All Sports Camp</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Parent Information -->
                                <div class="form-section">
                                    <h3 class="section-title">Parent/Guardian Information</h3>
                                    <div class="form-grid-2">
                                        <div>
                                            <label class="form-label">Parent First Name</label>
                                            <input type="text" name="Parent_FirstName" class="form-input-field" required>
                                        </div>
                                        <div>
                                            <label class="form-label">Parent Last Name</label>
                                            <input type="text" name="Parent_LastName" class="form-input-field" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Camper Information -->
                                <div class="form-section">
                                    <h3 class="section-title">Camper Information</h3>
                                    <div class="form-grid-2">
                                        <div>
                                            <label class="form-label">Camper First Name</label>
                                            <input type="text" name="Camper_FirstName" class="form-input-field" required>
                                        </div>
                                        <div>
                                            <label class="form-label">Camper Last Name</label>
                                            <input type="text" name="Camper_LastName" class="form-input-field" required>
                                        </div>
                                        <div>
                                            <label class="form-label">Gender</label>
                                            <select name="Gender" class="form-input-field" required>
                                                <option value="">Select Gender</option>
                                                <option value="M">Male</option>
                                                <option value="F">Female</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label">Birth Date</label>
                                            <input type="date" name="Birth_Date" class="form-input-field" required max="{{ date('Y-m-d') }}" min="{{ date('Y-m-d', strtotime('-100 years')) }}">
                                        </div>
                                        <div>
                                            <label class="form-label">Age</label>
                                            <input type="number" name="Age" class="form-input-field" required>
                                        </div>
                                        <div>
                                            <label class="form-label">Shirt Size</label>
                                            <select name="Shirt_Size" class="form-input-field" required>
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
                                    <div>
                                        <label class="form-label">Address</label>
                                        <input type="text" name="Address" class="form-input-field" required>
                                    </div>
                                    <div class="form-grid-3">
                                        <div>
                                            <label class="form-label">City</label>
                                            <input type="text" name="City" class="form-input-field" required>
                                        </div>
                                        <div>
                                            <label class="form-label">State</label>
                                            <input type="text" name="State" class="form-input-field" required>
                                        </div>
                                        <div>
                                            <label class="form-label">ZIP Code</label>
                                            <input type="text" name="Postal_Code" class="form-input-field" required>
                                        </div>
                                    </div>
                                    <div class="form-grid-2">
                                        <div>
                                            <label class="form-label">Email</label>
                                            <input type="email" name="Email" class="form-input-field" required>
                                        </div>
                                        <div>
                                            <label class="form-label">Phone</label>
                                            <input type="tel" 
                                                class="form-input-field" 
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
                                    <div class="form-section">
                                        <div>
                                            <label class="form-label">Allergies</label>
                                            <textarea name="Allergies" class="form-input-field" rows="3"></textarea>
                                        </div>
                                        <div>
                                            <label class="form-label">Does the camper have asthma?</label>
                                            <div class="radio-group">
                                                <label class="radio-label">
                                                    <input type="radio" name="Asthma" value="1" class="form-radio" required>
                                                    <span class="radio-text">Yes</span>
                                                </label>
                                                <label class="radio-label">
                                                    <input type="radio" name="Asthma" value="0" class="form-radio" required>
                                                    <span class="radio-text">No</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="form-label">Is the camper on any medications?</label>
                                            <div class="radio-group">
                                                <label class="radio-label">
                                                    <input type="radio" name="Medication_Status" value="1" class="form-radio" required>
                                                    <span class="radio-text">Yes</span>
                                                </label>
                                                <label class="radio-label">
                                                    <input type="radio" name="Medication_Status" value="0" class="form-radio" required>
                                                    <span class="radio-text">No</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="form-label">Recent Injuries or Health Concerns</label>
                                            <textarea name="Injuries" class="form-input-field" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Church Information -->
                                <div class="form-section">
                                    <h3 class="section-title">Church Information</h3>
                                    <div class="form-grid-2">
                                        <div>
                                            <label class="form-label">Church Name</label>
                                            <input type="text" name="Church_Name" class="form-input-field">
                                        </div>
                                        <div>
                                            <label class="form-label">Church Attendance</label>
                                            <select name="Church_Attendance" class="form-input-field">
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
                                <div class="pt-5">
                                    <button type="submit" class="submit-button">
                                        Submit Registration
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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
