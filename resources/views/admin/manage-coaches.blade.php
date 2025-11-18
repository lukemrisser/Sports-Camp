<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Coaches - {{ config('app.name', 'Falcon Teams') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('partials.header', [
        'title' => 'Manage Coaches',
        'subtitle' => 'View, edit, and manage existing coach accounts',
        'title_class' => 'welcome-title',
    ])

    <div class="container">

        <div class="coaches-table-container">
            <table class="coaches-table">
                <thead>
                    <tr>
                        <th>Coach Name</th>
                        <th>Email</th>
                        <th>Sport</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($coaches as $coach)
                        <tr>
                            <td>{{ $coach->Coach_FirstName }} {{ $coach->Coach_LastName }}</td>
                            <td>{{ $coach->user->email ?? 'N/A' }}</td>
                            <td>{{ $coach->sport->Sport_Name ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.edit-coach', $coach->Coach_ID) }}" class="btn btn-edit">Edit</a>
                                <form action="{{ route('admin.delete-coach', $coach->Coach_ID) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-delete"
                                        onclick="return confirm('Are you sure you want to delete this coach?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px;">No coaches found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    @include('partials.footer')
</body>

</html>
