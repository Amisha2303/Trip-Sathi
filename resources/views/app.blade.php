<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" data-accent="teal">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Complete Favicon & PWA App Icons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icons/icon-192x192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon-180x180.png') }}">
    <link rel="apple-touch-icon-precomposed" href="{{ asset('apple-touch-icon-precomposed.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- PWA & Mobile Web App Meta -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Trip Sathi">
    <meta name="application-name" content="Trip Sathi">
    <meta name="theme-color" content="#0d9488">
    <meta name="msapplication-navbutton-color" content="#0d9488">
    <meta name="msapplication-TileColor" content="#0d9488">

    <!-- Anti-FOUC Theme Pre-loader -->
    <script>
        (function() {
            try {
                var storedTheme = localStorage.getItem('ts-theme-mode');
                var storedAccent = localStorage.getItem('ts-theme-accent');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var mode = storedTheme || (prefersDark ? 'dark' : 'light');
                var accent = storedAccent || 'teal';
                document.documentElement.setAttribute('data-theme', mode);
                document.documentElement.setAttribute('data-accent', accent);
            } catch (e) {}
        })();
    </script>

    <!-- Premium Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <title inertia>{{ config('app.name', 'Trip Sathi') }}</title>

    @viteReactRefresh
    @vite(['resources/scss/app.scss', 'resources/js/app.jsx'])
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
