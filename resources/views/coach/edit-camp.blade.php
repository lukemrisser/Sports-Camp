<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Camp - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    @include('partials.header', [
        'title' => 'Falcon Teams',
        'subtitle' => 'Upload a spreadsheet or select a camp to generate teams',
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
                    <h2 class="registration-title">Edit Camp</h2>
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

                <div class="form-section">
                    <div class="form-group">
                        <label class="form-label">Select Camp to Edit</label>
                        <select id="camp-select" class="form-input">
                            <option value="">-- Select a camp --</option>
                            @foreach ($camps as $camp)
                                <option value="{{ $camp->Camp_ID }}">{{ $camp->Camp_Name }} ({{ $camp->Start_Date }} -
                                    {{ $camp->End_Date }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <form id="edit-camp-form" method="POST" action="" class="registration-form" style="display:none;">
                    @csrf
                    @method('PUT')

                    <div class="form-section">
                        <div class="form-group">
                            <label for="name" class="form-label">Camp Name</label>
                            <input id="name" name="name" type="text" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label for="sport_id" class="form-label">Sport</label>
                            <select id="sport_id" name="sport_id" class="form-input" required>
                                <option value="">Select a sport</option>
                                @foreach ($sports as $sport)
                                    <option value="{{ $sport->Sport_ID }}">{{ $sport->Sport_Name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input id="start_date" name="start_date" type="date" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label for="end_date" class="form-label">End Date</label>
                                <input id="end_date" name="end_date" type="date" class="form-input" required>
                            </div>
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="registration_open" class="form-label">Registration Open</label>
                                <input id="registration_open" name="registration_open" type="date" class="form-input"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="registration_close" class="form-label">Registration Close</label>
                                <input id="registration_close" name="registration_close" type="date"
                                    class="form-input" required>
                            </div>
                        </div>

                        <div class="form-group" style="position:relative;">
                            <label for="price" class="form-label">Normal Price</label>
                            <div style="position: relative;">
                                <span class="currency-symbol">$</span>
                                <input id="price" name="price" type="number" step="0.01" min="0"
                                    class="form-input" style="padding-left:25px;" required>
                            </div>
                        </div>

                        <div class="form-grid-3">
                            <div class="form-group">
                                <label for="gender" class="form-label">Gender</label>
                                <select id="gender" name="gender" class="form-input" required>
                                    <option value="coed">Coed</option>
                                    <option value="boys">Boys</option>
                                    <option value="girls">Girls</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="min_age" class="form-label">Min Age</label>
                                <input id="min_age" name="min_age" type="number" min="0" class="form-input"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="max_age" class="form-label">Max Age</label>
                                <input id="max_age" name="max_age" type="number" min="0"
                                    class="form-input" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" rows="4" class="form-input form-textarea" required></textarea>
                        </div>

                        <h5 class="section-title">Early Registration Discounts</h5>
                        <div id="discount-section"></div>
                        <div class="form-grid-2">
                            <div class="mt-1">
                                <button type="button" id="add-discount" class="submit-button"
                                    style="width:auto;">Add Discount</button>
                            </div>
                        </div>

                    </div>

                    <div class="submit-section">
                        <button type="submit" class="submit-button">Save Changes</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        const select = document.getElementById('camp-select');
        const form = document.getElementById('edit-camp-form');
        const discountSection = document.getElementById('discount-section');

        function clearDiscounts() {
            discountSection.innerHTML = '';
        }

        function addDiscountRow(amount = '', date = '') {
            const wrapper = document.createElement('div');
            wrapper.classList.add('discount-section', 'form-grid-2');
            wrapper.innerHTML = `
                <div class="form-group">
                    <label class="form-label">Early Discount Amount</label>
                    <div style="position: relative;">
                        <span class="currency-symbol">$</span>
                        <input type="number" name="discount_amount[]" class="form-input" min="0.01" step="0.01" style="padding-left:25px;" value="${amount}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Early Discount Deadline</label>
                    <input type="date" name="discount_date[]" class="form-input" value="${date}">
                </div>
                <button type="button" class="remove-discount absolute right-0 top-8 px-3 text-red-500 hover:text-red-700" title="Remove discount">&times;</button>
            `;
            discountSection.appendChild(wrapper);
            const remove = wrapper.querySelector('.remove-discount');
            remove.addEventListener('click', () => wrapper.remove());
        }

        document.getElementById('add-discount').addEventListener('click', () => addDiscountRow());

        select.addEventListener('change', function() {
            const id = this.value;
            if (!id) {
                form.style.display = 'none';
                return;
            }

            fetch(`{{ url('/edit-camp') }}/${id}/data`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(r => {
                    if (!r.ok) throw new Error('Failed to load camp');
                    return r.json();
                })
                .then(data => {
                    // populate form
                    form.style.display = '';
                    form.action = `{{ url('/edit-camp') }}/${id}`;
                    document.getElementById('name').value = data.Camp_Name || '';
                    document.getElementById('sport_id').value = data.Sport_ID || '';
                    document.getElementById('start_date').value = data.Start_Date ? data.Start_Date.split('T')[
                        0] : '';
                    document.getElementById('end_date').value = data.End_Date ? data.End_Date.split('T')[0] :
                        '';
                    document.getElementById('registration_open').value = data.Registration_Open ? data
                        .Registration_Open.split('T')[0] : '';
                    document.getElementById('registration_close').value = data.Registration_Close ? data
                        .Registration_Close.split('T')[0] : '';
                    document.getElementById('price').value = data.Price || '';
                    document.getElementById('gender').value = data.Camp_Gender || 'coed';
                    document.getElementById('min_age').value = data.Age_Min || '';
                    document.getElementById('max_age').value = data.Age_Max || '';
                    document.getElementById('description').value = data.Description || '';

                    clearDiscounts();
                    if (data.discounts && data.discounts.length) {
                        data.discounts.forEach(d => addDiscountRow(d.Discount_Amount, d.Discount_Date ? d
                            .Discount_Date.split('T')[0] : ''));
                    }
                })
                .catch(e => {
                    alert('Unable to load camp data.');
                    console.error(e);
                });
        });
    </script>

    @include('partials.footer')

</body>

</html>
