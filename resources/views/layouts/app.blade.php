<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inventaris IT | Yulie Sekuritas')</title>

    <!-- Favicon and Touch Icons -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('apple-touch-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('apple-touch-icon-120x120-precomposed.png') }}">
    <link rel="apple-touch-icon-precomposed" href="{{ asset('apple-touch-icon-precomposed.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        <div class="app-overlay" data-sidebar-overlay hidden></div>

        <aside class="app-sidebar" data-sidebar data-open="false">
            <div class="sidebar-brand">
                <a href="{{ route('dashboard') }}" class="sidebar-brand__logo-link" aria-label="Yulie Sekuritas">
                    <img src="{{ asset('brand/yulie-sekuritas-logo.png') }}" alt="Yulie Sekuritas" class="sidebar-brand__logo">
                </a>
               
                <p class="hero-panel__title">Sistem inventaris IT</p>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'nav-link--active' : '' }}">
                    <span class="nav-link__icon">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l8-8 8 8M5 10v9h14v-9" />
                        </svg>
                    </span>
                    Dashboard
                </a>

                <a href="{{ route('items.index') }}" class="nav-link {{ request()->routeIs('items.*') ? 'nav-link--active' : '' }}">
                    <span class="nav-link__icon">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7 12 3 4 7m16 0-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                        </svg>
                    </span>
                    Assets
                </a>

                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'nav-link--active' : '' }}">
                    <span class="nav-link__icon">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h7v7H4zM13 7h7v4h-7zM13 13h7v7h-7zM4 16h7v4H4z" />
                        </svg>
                    </span>
                    Categories
                </a>
            </nav>

            <div class="sidebar-account">
                <div class="sidebar-account__meta">
                    <div class="sidebar-account__label">Internal Access</div>
                    <div class="sidebar-account__name">{{ auth()->user()?->name }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn--secondary sidebar-account__button">Keluar</button>
                </form>
            </div>

            <div class="sidebar-footnote">
                <span class="chip chip--muted">Yulie Sekuritas Indonesia</span>
                <p>Prioritas utama: inventaris akurat, QR siap pakai, dan jejak audit yang tetap bersih.</p>
            </div>
        </aside>

        <div class="app-main">
            <header class="app-topbar">
                <div class="app-topbar__inner">
                    <div class="page-intro">
                        <button type="button" class="btn btn--secondary lg:hidden" data-sidebar-toggle>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            Menu
                        </button>

                        <div>
                            <div class="page-eyebrow">@yield('page-eyebrow', 'Operational Control')</div>
                            <h1 class="page-title">@yield('page-title', 'Inventaris IT')</h1>
                            @hasSection('page-subtitle')
                                <p class="page-subtitle">@yield('page-subtitle')</p>
                            @endif
                        </div>
                    </div>

                    <div class="page-actions">
                        @hasSection('page-actions')
                            @yield('page-actions')
                        @endif

                        <div class="topbar-badge">
                            <span class="topbar-badge__dot"></span>
                            {{ now()->translatedFormat('d M Y') }}
                        </div>

                        <div class="topbar-user">
                            <div class="topbar-user__meta">
                                <div class="topbar-user__label">Internal Access</div>
                                <div class="topbar-user__name">{{ auth()->user()?->name }}</div>
                            </div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn--secondary">Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="page-content">
                @foreach (['success' => 'alert--success', 'error' => 'alert--danger'] as $flashKey => $flashClass)
                    @if(session($flashKey))
                        <div class="alert {{ $flashClass }}" data-dismissible>
                            <div>{{ session($flashKey) }}</div>
                            <button type="button" class="alert__close" data-dismiss-parent aria-label="Tutup">x</button>
                        </div>
                    @endif
                @endforeach

                @if($errors->any())
                    <div class="alert alert--danger" data-dismissible>
                        <div>
                            <strong>Validasi belum lolos.</strong>
                            <ul class="mt-2 space-y-1 text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="alert__close" data-dismiss-parent aria-label="Tutup">x</button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
