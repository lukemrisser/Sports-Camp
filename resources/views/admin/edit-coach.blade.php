<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Coach - {{ config('app.name', 'Falcon Teams') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('partials.header', [
        'title' => 'Edit Coach',
        'subtitle' => 'Update coach information',
        'title_class' => 'welcome-title',
    ])

    <div class="container">
        <div class="registration-page">
            <div class="registration-container">
                <div class="registration-form-wrapper">
                    <div class="registration-header">
                        <h2 class="registration-title">Edit Coach</h2>
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

                    <form method="POST" action="{{ route('admin.update-coach', $coach->Coach_ID) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-section">
                            <div class="form-row">
                                <div class="form-field">
                                    <label for="coach_firstname">First Name</label>
                                    <input type="text" id="coach_firstname" name="coach_firstname"
                                        value="{{ old('coach_firstname', $coach->Coach_FirstName) }}" required>
                                </div>

                                <div class="form-field">
                                    <label for="coach_lastname">Last Name</label>
                                    <input type="text" id="coach_lastname" name="coach_lastname"
                                        value="{{ old('coach_lastname', $coach->Coach_LastName) }}" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-field">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email"
                                        value="{{ old('email', $coach->user->email) }}" required>
                                </div>

                                <div class="form-field">
                                    <label for="sport">Sport</label>
                                    <select id="sport" name="sport" required>
                                        <option value="">Select a sport</option>
                                        @foreach ($sports as $sport)
                                            <option value="{{ $sport->Sport_ID }}"
                                                {{ $coach->Sport_ID == $sport->Sport_ID ? 'selected' : '' }}>
                                                {{ $sport->Sport_Name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-field full-width">
                                    <label class="checkbox-label">
                                        <input type="checkbox" id="admin" name="admin" value="1"
                                            {{ old('admin', $coach->admin) ? 'checked' : '' }}>
                                        Administrator
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('admin.manage-coaches') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer')
</body>

</html>
