<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sports Camp Registration</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .form-input-field {
            margin-top: 0.25rem;
            display: block;
            width: 100%;
            border-radius: 0.375rem;
            border: 1px solid #D1D5DB;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            padding: 0.5rem 0.75rem;
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

        .form-section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 1rem;
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
                            <h2 class="text-3xl font-bold text-center mb-8">Falcon Teams</h2>
                            
                            <form class="space-y-6">
                                <!-- Parent Information -->
                                <div class="form-section">
                                    <h3 class="form-section-title">Parent/Guardian Information</h3>
                                    <div class="form-grid-2">
                                        <div>
                                            <label class="form-label">Parent First Name</label>
                                            <input type="text" class="form-input-field">
                                        </div>
                                        <div>
                                            <label class="form-label">Parent Last Name</label>
                                            <input type="text" class="form-input-field">
                                        </div>
                                    </div>
                                </div>

                                <!-- Camper Information -->
                                <div class="form-section">
                                    <h3 class="form-section-title">Camper Information</h3>
                                    <div class="form-grid-2">
                                        <div>
                                            <label class="form-label">Camper First Name</label>
                                            <input type="text" class="form-input-field">
                                        </div>
                                        <div>
                                            <label class="form-label">Camper Last Name</label>
                                            <input type="text" class="form-input-field">
                                        </div>
                                        <div>
                                            <label class="form-label">Gender</label>
                                            <select class="form-input-field">
                                                <option value="">Select Gender</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label">Birth Date</label>
                                            <input type="date" class="form-input-field">
                                        </div>
                                        <div>
                                            <label class="form-label">Age</label>
                                            <input type="number" class="form-input-field">
                                        </div>
                                        <div>
                                            <label class="form-label">Shirt Size</label>
                                            <select class="form-input-field">
                                                <option value="">Select Size</option>
                                                <option value="ys">Youth Small</option>
                                                <option value="ym">Youth Medium</option>
                                                <option value="yl">Youth Large</option>
                                                <option value="as">Adult Small</option>
                                                <option value="am">Adult Medium</option>
                                                <option value="al">Adult Large</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="form-section">
                                    <h3 class="form-section-title">Contact Information</h3>
                                    <div>
                                        <label class="form-label">Address</label>
                                        <input type="text" class="form-input-field">
                                    </div>
                                    <div class="form-grid-3">
                                        <div>
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-input-field">
                                        </div>
                                        <div>
                                            <label class="form-label">State</label>
                                            <input type="text" class="form-input-field">
                                        </div>
                                        <div>
                                            <label class="form-label">ZIP Code</label>
                                            <input type="text" class="form-input-field">
                                        </div>
                                    </div>
                                    <div class="form-grid-2">
                                        <div>
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-input-field">
                                        </div>
                                        <div>
                                            <label class="form-label">Phone</label>
                                            <input type="tel" class="form-input-field">
                                        </div>
                                    </div>
                                </div>

                                <!-- Medical Information -->
                                <div class="form-section">
                                    <h3 class="form-section-title">Medical Information</h3>
                                    <div class="form-section">
                                        <div>
                                            <label class="form-label">Allergies</label>
                                            <textarea class="form-input-field" rows="3"></textarea>
                                        </div>
                                        <div>
                                            <label class="form-label">Does the camper have asthma?</label>
                                            <div class="radio-group">
                                                <label class="radio-label">
                                                    <input type="radio" name="asthma" value="yes" class="form-radio">
                                                    <span class="radio-text">Yes</span>
                                                </label>
                                                <label class="radio-label">
                                                    <input type="radio" name="asthma" value="no" class="form-radio">
                                                    <span class="radio-text">No</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="form-label">Is the camper on any medications?</label>
                                            <div class="radio-group">
                                                <label class="radio-label">
                                                    <input type="radio" name="medication" value="yes" class="form-radio">
                                                    <span class="radio-text">Yes</span>
                                                </label>
                                                <label class="radio-label">
                                                    <input type="radio" name="medication" value="no" class="form-radio">
                                                    <span class="radio-text">No</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="form-label">Recent Injuries or Health Concerns</label>
                                            <textarea class="form-input-field" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Church Information -->
                                <div class="form-section">
                                    <h3 class="form-section-title">Church Information</h3>
                                    <div class="form-grid-2">
                                        <div>
                                            <label class="form-label">Church Name</label>
                                            <input type="text" class="form-input-field">
                                        </div>
                                        <div>
                                            <label class="form-label">Church Attendance</label>
                                            <select class="form-input-field">
                                                <option value="">Select Frequency</option>
                                                <option value="weekly">Weekly</option>
                                                <option value="monthly">Monthly</option>
                                                <option value="occasionally">Occasionally</option>
                                                <option value="rarely">Rarely</option>
                                                <option value="never">Never</option>
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
</body>
</html>
