<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - {{ config('app.name', 'Falcon Teams') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('partials.header', [
        'title' => 'Mass Emails',
        'title_class' => 'welcome-title',
    ])

    <div class="registration-page">
        <div class="registration-container">
            <div class="registration-form-wrapper">
                <div class="registration-header">
                    <h2 class="registration-title">Send Mass Email to Parents</h2>
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
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('send-mass-email') }}">
                    @csrf

                    <div class="form-group">
                        <label for="camp_status" class="form-label">Camp Status</label>
                        <select id="camp_status" name="camp_status" class="form-select" required>
                            <option value="">-- Choose Camp Status --</option>
                            @foreach ($campStatusOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('camp_status')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" id="camp_selection_group" style="display: none;">
                        <label for="camp_id" class="form-label">Select Camp</label>
                        <select id="camp_id" name="camp_id" class="form-select" required>
                            <option value="">-- Choose a Camp --</option>
                        </select>
                        @error('camp_id')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div id="email_fields_group" style="display: none;">
                        <div class="form-group">
                            <label for="subject" class="form-label">Email Subject</label>
                            <input type="text" id="subject" name="subject" class="form-input" required>
                            @error('subject')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="message" class="form-label">Email Message</label>
                            <textarea id="message" name="message" class="form-textarea" rows="8" required></textarea>
                            @error('message')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="submit_btn" disabled>Send Email</button>
                        <a href="{{ route('home') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>

                <script>
                    // Camp data organized by status
                    const campData = {
                        past: [
                            @foreach ($pastCamps as $camp)
                                {
                                    id: {{ $camp->Camp_ID }},
                                    name: '{{ $camp->Camp_Name }}'
                                },
                            @endforeach
                        ],
                        current: [
                            @foreach ($currentCamps as $camp)
                                {
                                    id: {{ $camp->Camp_ID }},
                                    name: '{{ $camp->Camp_Name }}'
                                },
                            @endforeach
                        ],
                        upcoming: [
                            @foreach ($upcomingCamps as $camp)
                                {
                                    id: {{ $camp->Camp_ID }},
                                    name: '{{ $camp->Camp_Name }}'
                                },
                            @endforeach
                        ]
                    };

                    const campStatusSelect = document.getElementById('camp_status');
                    const campIdSelect = document.getElementById('camp_id');
                    const campSelectionGroup = document.getElementById('camp_selection_group');
                    const emailFieldsGroup = document.getElementById('email_fields_group');
                    const submitBtn = document.getElementById('submit_btn');

                    function updateFormVisibility() {
                        const statusSelected = campStatusSelect.value !== '';
                        const campSelected = campIdSelect.value !== '';

                        // Show email fields only when both status and camp are selected
                        if (statusSelected && campSelected) {
                            emailFieldsGroup.style.display = 'block';
                            submitBtn.disabled = false;
                        } else {
                            emailFieldsGroup.style.display = 'none';
                            submitBtn.disabled = true;
                        }
                    }

                    campStatusSelect.addEventListener('change', function() {
                        const selectedStatus = this.value;

                        // Clear previous options and camp selection
                        campIdSelect.innerHTML = '<option value="">-- Choose a Camp --</option>';
                        campIdSelect.value = '';

                        if (selectedStatus && campData[selectedStatus]) {
                            // Populate camps for selected status
                            campData[selectedStatus].forEach(camp => {
                                const option = document.createElement('option');
                                option.value = camp.id;
                                option.textContent = camp.name + " - " + "(Date: " + camp.start_date + " - " + camp
                                    .end_date + ")";
                                campIdSelect.appendChild(option);
                            });

                            // Show camp selection dropdown
                            campSelectionGroup.style.display = 'block';
                        } else {
                            // Hide camp selection dropdown
                            campSelectionGroup.style.display = 'none';
                        }

                        // Update overall form visibility
                        updateFormVisibility();
                    });

                    campIdSelect.addEventListener('change', function() {
                        // Update overall form visibility
                        updateFormVisibility();
                    });
                </script>
            </div>
        </div>
    </div>

    @include('partials.footer')

</body>

</html>

</html>
