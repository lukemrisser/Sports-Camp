<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sports - {{ config('app.name', 'Falcon Teams') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@php
    use Illuminate\Support\Str;
@endphp

<body>

    @include('partials.header', [
        'title' => 'Manage Sports',
        'subtitle' => 'Add, edit, and delete sports available for camps',
    ])



    <div class="registration-page">
        <div class="registration-container">
            <div class="registration-form-wrapper">
                <div class="registration-header">
                    <h2 class="registration-title">Manage Sports</h2>
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

                <!-- Sports List -->
                <div class="form-section">
                    <h3 class="section-title">Current Sports</h3>

                    @if ($sports->count() > 0)
                        <div class="sports-table-wrapper">
                            <table class="sports-table">
                                <thead>
                                    <tr>
                                        <th>Sport Name</th>
                                        <th>Description</th>
                                        <th>FAQs</th>
                                        <th>Sponsors</th>
                                        <th>Usage</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sports as $sport)
                                        <tr>
                                            <td class="sport-name">{{ $sport->Sport_Name }}</td>
                                            <td class="sport-description">
                                                {{ $sport->Sport_Description ? Str::limit($sport->Sport_Description, 50) : 'No description' }}
                                            </td>
                                            <td class="sport-count">{{ $sport->faqs->count() }}</td>
                                            <td class="sport-count">{{ $sport->sponsors->count() }}</td>
                                            <td class="sport-usage">
                                                {{ $sport->camps->count() }} camps<br>
                                                {{ $sport->coaches->count() }} coaches
                                            </td>
                                            <td class="sport-actions">
                                                <button class="action-btn edit-btn"
                                                    onclick="editSport({{ $sport->Sport_ID }})">
                                                    Edit
                                                </button>
                                                <form method="POST"
                                                    action="{{ route('admin.sports.destroy', $sport->Sport_ID) }}"
                                                    style="display: inline;"
                                                    onsubmit="return confirm('Delete {{ addslashes($sport->Sport_Name) }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn delete-btn">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <p>No sports added yet. Add your first sport below!</p>
                        </div>
                    @endif
                </div>

                <!-- Add Sport Section -->
                <div class="form-section">
                    <div class="collapsible-header" onclick="toggleAddSportSection()">
                        <h3 class="section-title">Add New Sport</h3>
                        <span class="toggle-icon" id="addSportToggle">▼</span>
                    </div>
                    <div class="collapsible-content" id="addSportContent">
                        <form method="POST" action="{{ route('admin.sports.store') }}" class="registration-form" enctype="multipart/form-data">
                        
                        <!-- Basic Information -->
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="sport_name" class="form-label">Sport Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="sport_name" id="sport_name" class="form-input"
                                    placeholder="e.g., Soccer, Basketball, etc." required
                                    value="{{ old('sport_name') }}">
                            </div>
                            <div class="form-group">
                                <label for="sport_description" class="form-label">Description</label>
                                <textarea name="sport_description" id="sport_description" class="form-input"
                                    placeholder="Description for the Sport's About Us page..." rows="3">{{ old('sport_description') }}</textarea>
                            </div>
                        </div>

                        <!-- FAQs Section -->
                        <div class="subsection">
                            <h4 class="subsection-title">Frequently Asked Questions</h4>
                            <div id="faqs-container">
                                @if(old('faqs'))
                                    @foreach(old('faqs') as $index => $faq)
                                        <div class="faq-item">
                                            <div class="form-group">
                                                <label class="form-label">Question</label>
                                                <input type="text" name="faqs[{{ $index }}][question]" class="form-input"
                                                    placeholder="Enter FAQ question..." value="{{ $faq['question'] ?? '' }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Answer</label>
                                                <textarea name="faqs[{{ $index }}][answer]" class="form-input"
                                                    placeholder="Enter FAQ answer..." rows="2">{{ $faq['answer'] ?? '' }}</textarea>
                                            </div>
                                            <button type="button" class="remove-btn" onclick="removeFaq(this)">Remove FAQ</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="add-btn" onclick="addFaq()">Add FAQ</button>
                        </div>

                        <!-- Sponsors Section -->
                        <div class="subsection">
                            <h4 class="subsection-title">Sponsors</h4>
                            <div id="sponsors-container">
                                @if(old('sponsors'))
                                    @foreach(old('sponsors') as $index => $sponsor)
                                        <div class="sponsor-item">
                                            <div class="form-grid-3">
                                                <div class="form-group">
                                                    <label class="form-label">Sponsor Name</label>
                                                    <input type="text" name="sponsors[{{ $index }}][name]" class="form-input"
                                                        placeholder="Sponsor name..." value="{{ $sponsor['name'] ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Website Link</label>
                                                    <input type="url" name="sponsors[{{ $index }}][link]" class="form-input"
                                                        placeholder="https://..." value="{{ $sponsor['link'] ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Logo Path</label>
                                                    <input type="text" name="sponsors[{{ $index }}][image_path]" class="form-input"
                                                        placeholder="path/to/logo.png" value="{{ $sponsor['image_path'] ?? '' }}">
                                                </div>
                                            </div>
                                            <button type="button" class="remove-btn" onclick="removeSponsor(this)">Remove Sponsor</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="add-btn" onclick="addSponsor()">Add Sponsor</button>
                        </div>

                            <div class="submit-section">
                                <button type="submit" class="submit-button">Add Sport</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content-large">
            <div class="modal-header">
                <h3>Edit Sport</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="editForm" method="POST" class="registration-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="edit_sport_name" class="form-label">Sport Name <span class="text-red-500">*</span></label>
                        <input type="text" name="sport_name" id="edit_sport_name" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_sport_description" class="form-label">Description</label>
                        <textarea name="sport_description" id="edit_sport_description" class="form-input"
                            placeholder="Brief description of the sport..." rows="3"></textarea>
                    </div>
                </div>

                <!-- FAQs Section -->
                <div class="subsection">
                    <h4 class="subsection-title">Frequently Asked Questions</h4>
                    <div id="edit-faqs-container">
                        <!-- FAQs will be populated by JavaScript -->
                    </div>
                    <button type="button" class="add-btn" onclick="addEditFaq()">Add FAQ</button>
                </div>

                <!-- Sponsors Section -->
                <div class="subsection">
                    <h4 class="subsection-title">Sponsors</h4>
                    <div id="edit-sponsors-container">
                        <!-- Sponsors will be populated by JavaScript -->
                    </div>
                    <button type="button" class="add-btn" onclick="addEditSponsor()">Add Sponsor</button>
                </div>

                <div class="submit-section">
                    <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="submit-button">Update Sport</button>
                </div>
            </form>
        </div>
    </div>



    <style>
        /* Sports Table Styles */
        .sports-table-wrapper {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            margin-top: 20px;
        }

        .sports-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sports-table th {
            background: #f8fafc;
            padding: 16px 24px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        .sports-table td {
            padding: 16px 24px;
            border-bottom: 1px solid #f3f4f6;
        }

        .sports-table tr:hover {
            background: #f9fafb;
        }

        .sport-name {
            font-weight: 600;
            color: #1f2937;
        }

        .sport-description {
            color: #6b7280;
            font-size: 14px;
            max-width: 200px;
        }

        .sport-count {
            color: #6b7280;
            font-size: 14px;
            text-align: center;
        }

        .sport-usage {
            color: #6b7280;
            font-size: 14px;
            white-space: nowrap;
        }

        .sport-actions {
            white-space: nowrap;
        }

        /* Action Buttons */
        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            margin-right: 8px;
            transition: all 0.2s ease;
        }

        .edit-btn {
            background: #3b82f6;
            color: white;
        }

        .edit-btn:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .delete-btn {
            background: #ef4444;
            color: white;
        }

        .delete-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            margin-top: 20px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .modal-content-large {
            background-color: white;
            margin: 5% auto;
            border-radius: 16px;
            width: 95%;
            max-width: 800px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 32px;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
        }

        .close {
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            color: #9ca3af;
            transition: color 0.2s ease;
        }

        .close:hover {
            color: #6b7280;
        }

        .modal .registration-form {
            padding: 32px;
        }

        .submit-section {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }

        .cancel-btn {
            background: #6b7280;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .cancel-btn:hover {
            background: #4b5563;
            transform: translateY(-1px);
        }

        /* Red asterisk for required fields */
        .text-red-500 {
            color: #ef4444;
        }

        /* Subsection Styles */
        .subsection {
            margin-top: 30px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .subsection-title {
            margin: 0 0 15px 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #374151;
        }

        /* FAQ and Sponsor Item Styles */
        .faq-item, .sponsor-item {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            position: relative;
        }

        .faq-item:last-child, .sponsor-item:last-child {
            margin-bottom: 0;
        }

        /* Form Grid Styles */
        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }

        /* Button Styles */
        .add-btn {
            background: #10b981;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .add-btn:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        .remove-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
            transition: all 0.2s ease;
        }

        .remove-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        /* File Input Styles */
        input[type="file"].form-input {
            padding: 8px;
            border: 2px dashed #d1d5db;
            background: #f9fafb;
        }

        input[type="file"].form-input:hover {
            border-color: #9ca3af;
        }

        input[type="file"].form-input:focus {
            border-color: #3b82f6;
            background: white;
        }

        .form-help {
            display: block;
            margin-top: 4px;
            font-size: 12px;
            color: #6b7280;
        }

        .current-image {
            margin-top: 8px;
            padding: 8px;
            background: #f0f9ff;
            border-radius: 4px;
            border: 1px solid #bae6fd;
        }

        .current-image a {
            color: #0ea5e9;
            text-decoration: none;
        }

        .current-image a:hover {
            text-decoration: underline;
        }

        /* Collapsible Section Styles */
        .collapsible-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 20px;
            transition: all 0.2s ease;
        }

        .collapsible-header:hover {
            background-color: #f9fafb;
            padding: 10px 15px;
            margin: 0 -15px 20px -15px;
            border-radius: 8px;
        }

        .toggle-icon {
            font-size: 1.2rem;
            color: #6b7280;
            transition: transform 0.3s ease;
        }

        .toggle-icon.rotated {
            transform: rotate(180deg);
        }

        .collapsible-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .collapsible-content.expanded {
            max-height: 2000px;
            transition: max-height 0.5s ease-in;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sports-table-wrapper {
                overflow-x: auto;
            }

            .sports-table th,
            .sports-table td {
                padding: 12px 8px;
                font-size: 12px;
            }

            .sport-description {
                max-width: 120px;
            }

            .action-btn {
                padding: 6px 12px;
                font-size: 12px;
                margin-right: 4px;
            }

            .modal-content, .modal-content-large {
                margin: 5% auto;
                width: 95%;
                max-height: 85vh;
            }

            .modal-header {
                padding: 20px 24px;
            }

            .modal .registration-form {
                padding: 24px;
            }

            .form-grid-2, .form-grid-3 {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .faq-item, .sponsor-item {
                padding: 15px;
            }

            .subsection {
                padding: 15px;
            }
        }
    </style>



    <script>
        let faqCounter = {{ old('faqs') ? count(old('faqs')) : 0 }};
        let sponsorCounter = {{ old('sponsors') ? count(old('sponsors')) : 0 }};
        let editFaqCounter = 0;
        let editSponsorCounter = 0;

        // Add FAQ functionality
        function addFaq() {
            const container = document.getElementById('faqs-container');
            const faqDiv = document.createElement('div');
            faqDiv.className = 'faq-item';
            faqDiv.innerHTML = `
                <div class="form-group">
                    <label class="form-label">Question</label>
                    <input type="text" name="faqs[${faqCounter}][question]" class="form-input"
                        placeholder="Enter FAQ question...">
                </div>
                <div class="form-group">
                    <label class="form-label">Answer</label>
                    <textarea name="faqs[${faqCounter}][answer]" class="form-input"
                        placeholder="Enter FAQ answer..." rows="2"></textarea>
                </div>
                <button type="button" class="remove-btn" onclick="removeFaq(this)">Remove FAQ</button>
            `;
            container.appendChild(faqDiv);
            faqCounter++;
        }

        function removeFaq(button) {
            button.parentElement.remove();
        }

        // Add Sponsor functionality
        function addSponsor() {
            const container = document.getElementById('sponsors-container');
            const sponsorDiv = document.createElement('div');
            sponsorDiv.className = 'sponsor-item';
            sponsorDiv.innerHTML = `
                <div class="form-grid-3">
                    <div class="form-group">
                        <label class="form-label">Sponsor Name</label>
                        <input type="text" name="sponsors[${sponsorCounter}][name]" class="form-input"
                            placeholder="Sponsor name...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Website Link</label>
                        <input type="url" name="sponsors[${sponsorCounter}][link]" class="form-input"
                            placeholder="https://...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Logo Image</label>
                        <input type="file" name="sponsors[${sponsorCounter}][image]" class="form-input"
                            accept="image/*">
                    </div>
                </div>
                <button type="button" class="remove-btn" onclick="removeSponsor(this)">Remove Sponsor</button>
            `;
            container.appendChild(sponsorDiv);
            sponsorCounter++;
        }

        function removeSponsor(button) {
            button.parentElement.remove();
        }

        // Edit modal functions
        function addEditFaq() {
            const container = document.getElementById('edit-faqs-container');
            const faqDiv = document.createElement('div');
            faqDiv.className = 'faq-item';
            faqDiv.innerHTML = `
                <div class="form-group">
                    <label class="form-label">Question</label>
                    <input type="text" name="faqs[${editFaqCounter}][question]" class="form-input"
                        placeholder="Enter FAQ question...">
                </div>
                <div class="form-group">
                    <label class="form-label">Answer</label>
                    <textarea name="faqs[${editFaqCounter}][answer]" class="form-input"
                        placeholder="Enter FAQ answer..." rows="2"></textarea>
                </div>
                <button type="button" class="remove-btn" onclick="removeFaq(this)">Remove FAQ</button>
            `;
            container.appendChild(faqDiv);
            editFaqCounter++;
        }

        function addEditSponsor() {
            const container = document.getElementById('edit-sponsors-container');
            const sponsorDiv = document.createElement('div');
            sponsorDiv.className = 'sponsor-item';
            sponsorDiv.innerHTML = `
                <div class="form-grid-3">
                    <div class="form-group">
                        <label class="form-label">Sponsor Name</label>
                        <input type="text" name="sponsors[${editSponsorCounter}][name]" class="form-input"
                            placeholder="Sponsor name...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Website Link</label>
                        <input type="url" name="sponsors[${editSponsorCounter}][link]" class="form-input"
                            placeholder="https://...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Logo Image</label>
                        <input type="file" name="sponsors[${editSponsorCounter}][image]" class="form-input"
                            accept="image/*">
                        <small class="form-help">Leave empty to keep current image</small>
                    </div>
                </div>
                <button type="button" class="remove-btn" onclick="removeSponsor(this)">Remove Sponsor</button>
            `;
            container.appendChild(sponsorDiv);
            editSponsorCounter++;
        }

        // Edit sport functionality
        async function editSport(sportId) {
            try {
                // Fetch sport data
                const response = await fetch(`/admin/sports/${sportId}/data`);
                if (!response.ok) {
                    throw new Error('Failed to fetch sport data');
                }
                const sport = await response.json();
                
                // Populate basic fields
                document.getElementById('edit_sport_name').value = sport.Sport_Name;
                document.getElementById('edit_sport_description').value = sport.Sport_Description || '';
                
                // Clear existing FAQs and sponsors
                document.getElementById('edit-faqs-container').innerHTML = '';
                document.getElementById('edit-sponsors-container').innerHTML = '';
                editFaqCounter = 0;
                editSponsorCounter = 0;
                
                // Populate FAQs
                if (sport.faqs && sport.faqs.length > 0) {
                    sport.faqs.forEach(faq => {
                        const container = document.getElementById('edit-faqs-container');
                        const faqDiv = document.createElement('div');
                        faqDiv.className = 'faq-item';
                        faqDiv.innerHTML = `
                            <div class="form-group">
                                <label class="form-label">Question</label>
                                <input type="text" name="faqs[${editFaqCounter}][question]" class="form-input"
                                    value="${faq.Question}" placeholder="Enter FAQ question...">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Answer</label>
                                <textarea name="faqs[${editFaqCounter}][answer]" class="form-input"
                                    placeholder="Enter FAQ answer..." rows="2">${faq.Answer}</textarea>
                            </div>
                            <button type="button" class="remove-btn" onclick="removeFaq(this)">Remove FAQ</button>
                        `;
                        container.appendChild(faqDiv);
                        editFaqCounter++;
                    });
                }
                
                // Populate sponsors
                if (sport.sponsors && sport.sponsors.length > 0) {
                    sport.sponsors.forEach(sponsor => {
                        const container = document.getElementById('edit-sponsors-container');
                        const sponsorDiv = document.createElement('div');
                        sponsorDiv.className = 'sponsor-item';
                        sponsorDiv.innerHTML = `
                            <div class="form-grid-3">
                                <div class="form-group">
                                    <label class="form-label">Sponsor Name</label>
                                    <input type="text" name="sponsors[${editSponsorCounter}][name]" class="form-input"
                                        value="${(sponsor.Sponsor_Name || '').replace(/"/g, '&quot;')}" placeholder="Sponsor name...">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Website Link</label>
                                    <input type="url" name="sponsors[${editSponsorCounter}][link]" class="form-input"
                                        value="${(sponsor.Sponsor_Link || '').replace(/"/g, '&quot;')}" placeholder="https://...">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Logo Image</label>
                                    <input type="file" name="sponsors[${editSponsorCounter}][image]" class="form-input"
                                        accept="image/*">
                                    ${sponsor.Image_Path ? 
                                        `<div class="current-image">
                                            <small>Current: <a href="/storage/${sponsor.Image_Path}" target="_blank">View Image</a></small>
                                            <input type="hidden" name="sponsors[${editSponsorCounter}][current_image]" value="${sponsor.Image_Path}">
                                        </div>` : 
                                        '<small class="form-help">No image currently uploaded</small>'
                                    }
                                </div>
                            </div>
                            <button type="button" class="remove-btn" onclick="removeSponsor(this)">Remove Sponsor</button>
                        `;
                        container.appendChild(sponsorDiv);
                        editSponsorCounter++;
                    });
                }
                
                // Set form action and show modal
                document.getElementById('editForm').action = `{{ url('/admin/sports') }}/${sportId}`;
                document.getElementById('editModal').style.display = 'block';
                
            } catch (error) {
                console.error('Error fetching sport data:', error);
                alert('Error loading sport data. Please try again.');
            }
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Close modal with escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        // Toggle Add Sport Section
        function toggleAddSportSection() {
            const content = document.getElementById('addSportContent');
            const icon = document.getElementById('addSportToggle');
            
            if (content.classList.contains('expanded')) {
                content.classList.remove('expanded');
                icon.classList.remove('rotated');
            } else {
                content.classList.add('expanded');
                icon.classList.add('rotated');
            }
        }

        // Auto-expand add sport section if there are validation errors
        document.addEventListener('DOMContentLoaded', function() {
            @if($errors->any() || old('sport_name'))
                toggleAddSportSection();
            @endif
        });
    </script>

    @include('partials.footer')
</body>

</html>
