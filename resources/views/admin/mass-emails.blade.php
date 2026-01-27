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

                <form method="POST" action="{{ route('send-mass-email') }}" class="registration-form">
                    @csrf

                    <div class="form-group">
                        <label for="camp_id" class="form-label">Select Camp</label>
                        <select id="camp_id" name="camp_id" class="form-select" required>
                            <option value="">-- Choose a Camp --</option>
                            @foreach ($camps as $camp)
                                <option value="{{ $camp->Camp_ID }}">{{ $camp->Camp_Name }}</option>
                            @endforeach
                        </select>
                        @error('camp_id')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

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

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Send Email</button>
                        <a href="{{ route('home') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('partials.footer')

</body>

</html>

</html>
