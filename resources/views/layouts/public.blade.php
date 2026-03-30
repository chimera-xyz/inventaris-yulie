<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Asset Info | Inventaris IT')</title>

    <!-- Favicon and Touch Icons -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('apple-touch-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('apple-touch-icon-120x120-precomposed.png') }}">
    <link rel="apple-touch-icon-precomposed" href="{{ asset('apple-touch-icon-precomposed.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public-body">
    <div class="public-shell">
        <header class="public-header">
            <a href="{{ config('app.public_url') }}" class="public-header__brand" aria-label="Yulie Sekuritas">
                <img src="{{ asset('brand/yulie-sekuritas-logo.png') }}" alt="Yulie Sekuritas" class="public-header__logo">
                <div>
                    <div class="public-header__eyebrow">Public Asset View</div>
                    <div class="public-header__title">Inventaris IT</div>
                </div>
            </a>
        </header>

        @yield('content')
    </div>
</body>
</html>
