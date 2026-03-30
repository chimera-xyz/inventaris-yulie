<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login | Inventaris IT')</title>

    <!-- Favicon and Touch Icons -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('apple-touch-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('apple-touch-icon-120x120-precomposed.png') }}">
    <link rel="apple-touch-icon-precomposed" href="{{ asset('apple-touch-icon-precomposed.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="guest-body">
    <main class="guest-shell">
        @yield('content')
    </main>
</body>
</html>
