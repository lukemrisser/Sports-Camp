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
        <!-- Content will be added here later -->
    </div>

    @include('partials.footer')
</body>

</html>
