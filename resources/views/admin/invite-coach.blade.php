<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invite Coach - {{ config('app.name', 'Falcon Teams') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('partials.header', [
        'title' => 'Invite Coach',
        'subtitle' => 'Send invitations and manage coach permissions',
        'title_class' => 'welcome-title',
    ])

    <div class="container">
        <!-- Content will be added here later -->
    </div>
</body>

</html>
