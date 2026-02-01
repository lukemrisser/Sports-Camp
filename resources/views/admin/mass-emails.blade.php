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
                        <label class="form-label">Select Camp(s)</label>
                        <div id="camp_checkboxes"
                            style="
                            border: 2px solid #e5e7eb;
                            border-radius: 8px;
                            padding: 12px;
                            background: #f9fafb;
                            max-height: 300px;
                            overflow-y: auto;
                        ">
                            <!-- Checkboxes will be populated by JavaScript -->
                        </div>
                        <small style="color: #6b7280; margin-top: 6px; display: block;">Select one or more camps</small>
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
                            <label for="greeting" class="form-label">Email Greeting</label>
                            <input type="text" id="greeting" name="greeting" class="form-input" optional
                                default="Hello" value="Hello (Parent's Name,)">
                            @error('greeting')
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

                        <div class="form-group">
                            <label for="closing" class="form-label">Email Closing</label>
                            <input type="text" id="closing" name="closing" class="form-input" optional
                                default="Best regards," value="Best regards, (Your Name)">
                            @error('closing')
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
                        past: {!! json_encode($pastCamps) !!},
                        live: {!! json_encode($liveCamps) !!},
                        upcoming: {!! json_encode($upcomingCamps) !!}
                    };

                    const campStatusSelect = document.getElementById('camp_status');
                    const campCheckboxesContainer = document.getElementById('camp_checkboxes');
                    const campSelectionGroup = document.getElementById('camp_selection_group');
                    const emailFieldsGroup = document.getElementById('email_fields_group');
                    const submitBtn = document.getElementById('submit_btn');

                    function updateFormVisibility() {
                        const statusSelected = campStatusSelect.value !== '';
                        const selectedCamps = document.querySelectorAll('input[name="camp_id[]"]:checked').length > 0;

                        // Show email fields only when both status and at least one camp are selected
                        if (statusSelected && selectedCamps) {
                            emailFieldsGroup.style.display = 'block';
                            submitBtn.disabled = false;
                        } else {
                            emailFieldsGroup.style.display = 'none';
                            submitBtn.disabled = true;
                        }
                    }

                    campStatusSelect.addEventListener('change', function() {
                        const selectedStatus = this.value;

                        // Clear previous checkboxes and selections
                        campCheckboxesContainer.innerHTML = '';

                        if (selectedStatus && campData[selectedStatus]) {
                            // Populate checkboxes for selected status
                            campData[selectedStatus].forEach(camp => {
                                const checkboxWrapper = document.createElement('div');
                                checkboxWrapper.style.cssText = `
                                    display: flex;
                                    align-items: center;
                                    padding: 8px;
                                    margin-bottom: 4px;
                                    border-radius: 6px;
                                    transition: background-color 0.2s ease;
                                `;

                                const checkbox = document.createElement('input');
                                checkbox.type = 'checkbox';
                                checkbox.name = 'camp_id[]';
                                checkbox.value = camp.id;
                                checkbox.style.cssText = `
                                    width: 18px;
                                    height: 18px;
                                    cursor: pointer;
                                    margin-right: 12px;
                                    border: 2px solid #d1d5db;
                                    border-radius: 4px;
                                    accent-color: #0a3f94;
                                `;

                                const label = document.createElement('label');
                                label.style.cssText = `
                                    flex: 1;
                                    cursor: pointer;
                                    display: flex;
                                    flex-direction: column;
                                `;
                                label.innerHTML = `
                                    <span style="font-weight: 600; color: #1f2937;">${camp.name}</span>
                                    <span style="font-size: 0.875rem; color: #6b7280;">${camp.start_date} - ${camp.end_date}</span>
                                `;

                                checkbox.addEventListener('change', function() {
                                    if (this.checked) {
                                        checkboxWrapper.style.backgroundColor = '#dbeafe';
                                    } else {
                                        checkboxWrapper.style.backgroundColor = 'transparent';
                                    }
                                    updateFormVisibility();
                                });

                                checkboxWrapper.appendChild(checkbox);
                                checkboxWrapper.appendChild(label);
                                campCheckboxesContainer.appendChild(checkboxWrapper);
                            });

                            // Show camp selection group
                            campSelectionGroup.style.display = 'block';
                        } else {
                            // Hide camp selection group
                            campSelectionGroup.style.display = 'none';
                        }

                        // Update overall form visibility
                        updateFormVisibility();
                    });
                </script>
                </script>
            </div>
        </div>
    </div>

    @include('partials.footer')

</body>

</html>

</html>
