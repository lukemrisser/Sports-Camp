<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Camp - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <header class="main-header">
        <div class=header-container>
            <div class="header-content">
                <h1>Falcon Teams</h1>
                <p>Upload a spreadsheet or select a camp to generate teams</p>
            </div>

            <div class="header-buttons">
                @if (Auth::user()->isCoachAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="header-btn dashboard-btn">Admin Dashboard</a>
                @endif
                <a href="{{ route('coach-dashboard') }}" class="header-btn dashboard-btn">Coach Dashboard</a>
                <a href="{{ route('dashboard') }}" class="header-btn login-btn">Account</a>
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="header-btn logout-btn">Logout</button>
                </form>
            </div>
        </div>
    </header>

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

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
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

                <form method="POST" action="{{ route('store-camp') }}" class="registration-form">
                    @csrf

                    <div class="form-section">
                        <div class="form-group">
                            <label for="name" class="form-label">
                                Camp Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   required 
                                   class="form-input"
                                   value="{{ old('name') }}">
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="start_date" class="form-label">
                                    Start Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       name="start_date" 
                                       id="start_date" 
                                       required 
                                       class="form-input"
                                       value="{{ old('start_date') }}">
                            </div>

                            <div class="form-group">
                                <label for="end_date" class="form-label">
                                    End Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       name="end_date" 
                                       id="end_date" 
                                       required 
                                       class="form-input"
                                       value="{{ old('end_date') }}">
                            </div>
                        </div>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="registration_open" class="form-label">
                                    Registration Open Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       name="registration_open" 
                                       id="registration_open" 
                                       required 
                                       class="form-input"
                                       value="{{ old('registration_open') }}">
                            </div>

                            <div class="form-group">
                                <label for="registration_close" class="form-label">
                                    Registration Close Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       name="registration_close" 
                                       id="registration_close" 
                                       required 
                                       class="form-input"
                                       value="{{ old('registration_close') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="price" class="form-label">
                                Normal Price <span class="text-red-500">*</span>
                            </label>
                            <div style="position: relative;">
                                <span class="currency-symbol">$</span>
                                <input type="number"
                                    name="price"
                                    id="price"
                                    required 
                                    class="form-input"
                                    min=".01" step=".01"
                                    value="{{ old('price') }}"
                                    style="padding-left: 25px;">
                            </div>
                        </div>

                        <div class="form-grid-3">
                            <div class="form-group">
                                <label for="gender" class="form-label">
                                    Camp Gender <span class="text-red-500">*</span>
                                </label>
                                <select name="gender"
                                        id="gender"
                                        required
                                        class="form-input">
                                    <option value="" disabled selected>Select gender</option>
                                    <option value="girls">Girls</option>
                                    <option value="boys">Boys</option>
                                    <option value="mixed">Co-ed</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="min_age" class="form-label">
                                    Minimum Age <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       name="min_age"
                                       id="min_age"
                                       required
                                       class="form-input"
                                       min="1"
                                       max="100"
                                       value="{{ old('min_age') }}">
                            </div>

                            <div class="form-group">
                                <label for="max_age" class="form-label">
                                    Maximum Age <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       name="max_age"
                                       id="max_age"
                                       required
                                       class="form-input"
                                       min="1"
                                       max="100"
                                       value="{{ old('max_age') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" 
                                     id="description" 
                                     rows="4" 
                                     required 
                                     class="form-input form-textarea">{{ old('description') }}</textarea>
                        </div>

                        <h5 class="section-title">Early Registration Discounts</h5>
                        <p class="text-sm text-gray-600 mb-3">Optionally add early bird discounts.</p>


                        <div id="discount-section">
                            <div class="form-grid-2 discount-group">
                                <div class="form-group">
                                    <label class="form-label">Early Discount Amount</label>
                                    <div style="position: relative;">
                                        <span class="currency-symbol">$</span>
                                        <input type="number"
                                            id="discount_amount"
                                            name="discount_amount[]"
                                            class="form-input"
                                            min="0.01" step="0.01"
                                            value="{{ old('discount_amount.0') }}"
                                            style="padding-left: 25px;">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Early Discount Deadline</label>
                                    <input type="date"
                                        id="discount_date"
                                        name="discount_date[]"
                                        class="form-input"
                                        value="{{ old('discount_date.0') }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-grid-2">
                            <div class="mt-1">
                                <button type="button" id="add-discount" class="submit-button" style="width: auto;">Add
                                    another discount</button>
                            </div>
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
                    class="remove-discount absolute right-0 top-8 px-3 text-red-500 hover:text-red-700"
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
    </script>
</body>
</html>
