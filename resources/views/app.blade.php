<!DOCTYPE html>
<html lang="ar" dir="rtl" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>{{ config('app.name', 'FCPMS') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
        @routes
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="antialiased body-bg-dark text-slate-800 dark:text-white transition-colors duration-500 min-h-screen">
        @inertia
    </body>
</html>
