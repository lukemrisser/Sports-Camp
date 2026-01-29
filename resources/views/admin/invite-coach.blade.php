<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invite Coach - {{ config('app.name', 'Falcon Teams') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .coach-entry {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 12px;
            align-items: flex-end;
            padding: 12px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .coach-entry-group {
            display: flex;
            flex-direction: column;
        }

        .coach-entry label {
            font-weight: 600;
            color: #374151;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .coach-entry input {
            padding: 8px 12px;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s ease;
        }

        .coach-entry input:focus {
            outline: none;
            border-color: #0a3f94;
            box-shadow: 0 0 0 3px rgba(10, 63, 148, 0.1);
        }

        .remove-coach-btn {
            padding: 8px 12px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: background 0.2s ease;
        }

        .remove-coach-btn:hover {
            background: #dc2626;
        }

        .add-coach-btn {
            padding: 10px 16px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: background 0.2s ease;
            margin-bottom: 20px;
        }

        .add-coach-btn:hover {
            background: #2563eb;
        }

        .coaches-container {
            margin: 20px 0;
        }

        @media (max-width: 768px) {
            .coach-entry {
                grid-template-columns: 1fr;
            }

            .remove-coach-btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    @include('partials.header', [
        'title' => 'Invite Coach',
        'title_class' => 'welcome-title',
    ])

    <div class="registration-page">
        <div class="registration-container">
            <div class="registration-form-wrapper">
                <div class="registration-header">
                    <h2 class="registration-title">Invite Coaches</h2>
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

                <form method="POST" action="{{ route('admin.send-invite-coach') }}">
                    @csrf

                    <div class="coaches-container" id="coaches_container">
                        <!-- Coach entries will be generated here -->
                    </div>

                    <button type="button" class="add-coach-btn" id="add_coach_btn">+ Add Coach</button>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Send Invites</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>

                @include('partials.footer')
            </div>
        </div>
    </div>

    <script>
        const coachesContainer = document.getElementById('coaches_container');
        const addCoachBtn = document.getElementById('add_coach_btn');
        let coachCount = 0;

        function createCoachEntry(index) {
            const entry = document.createElement('div');
            entry.className = 'coach-entry';
            entry.dataset.index = index;
            entry.innerHTML = `
                <div class="coach-entry-group">
                    <label for="coach_name_${index}">Coach Name</label>
                    <input type="text" id="coach_name_${index}" name="coaches[${index}][name]" placeholder="John Doe" required>
                </div>
                <div class="coach-entry-group">
                    <label for="coach_email_${index}">Email Address</label>
                    <input type="email" id="coach_email_${index}" name="coaches[${index}][email]" placeholder="coach@example.com" required>
                </div>
                <button type="button" class="remove-coach-btn" onclick="removeCoachEntry(${index})">Remove</button>
            `;
            return entry;
        }

        function addCoachEntry() {
            const entry = createCoachEntry(coachCount);
            coachesContainer.appendChild(entry);
            coachCount++;
        }

        function removeCoachEntry(index) {
            const entry = document.querySelector(`[data-index="${index}"]`);
            if (entry) {
                entry.remove();
            }
            // Ensure at least one coach entry remains
            if (coachesContainer.children.length === 0) {
                addCoachEntry();
            }
        }

        // Add initial coach entry
        addCoachEntry();

        // Add event listener to add coach button
        addCoachBtn.addEventListener('click', addCoachEntry);
    </script>
</body>

</html>
