<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title', 'Admin') — {{ $appSettings['site_name'] ?? config('app.name', 'Trip Sathi') }}</title>

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('head')
</head>

<body>
    <div class="ts-admin-shell">
        <aside class="ts-admin-side">
            <div class="ts-admin-brand">
                <span class="admin-brand-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Trip Sathi">
                </span>
                <span>Admin</span>
            </div>

            <div class="ts-admin-group">Overview</div>
            <nav class="ts-admin-nav">
                <a href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </nav>

            <div class="ts-admin-group">Management</div>
            <nav class="ts-admin-nav">
                <a href="{{ route('admin.users.index') }}"
                    class="{{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
                    <i class="bi bi-people"></i> Users
                </a>
                <a href="{{ route('admin.destinations.index') }}"
                    class="{{ request()->routeIs('admin.destinations.*') ? 'is-active' : '' }}">
                    <i class="bi bi-geo-alt"></i> Destinations
                </a>
                <a href="{{ route('admin.hotels.index') }}"
                    class="{{ request()->routeIs('admin.hotels.*') ? 'is-active' : '' }}">
                    <i class="bi bi-building"></i> Hotels
                </a>
                <a href="{{ route('admin.events.index') }}"
                    class="{{ request()->routeIs('admin.events.*') ? 'is-active' : '' }}">
                    <i class="bi bi-calendar-event"></i> Events
                </a>
                <a href="{{ route('admin.adventures.index') }}"
                    class="{{ request()->routeIs('admin.adventures.*') ? 'is-active' : '' }}">
                    <i class="bi bi-signpost-split"></i> Adventures
                </a>
                <a href="{{ route('admin.darshan.index') }}"
                    class="{{ request()->routeIs('admin.darshan.*') ? 'is-active' : '' }}">
                    <i class="bi bi-gem"></i> VIP Darshan
                </a>
                <a href="{{ route('admin.pooja.index') }}"
                    class="{{ request()->routeIs('admin.pooja.*') ? 'is-active' : '' }}">
                    <i class="bi bi-flower1"></i> Pooja Vidhi
                </a>
                <a href="{{ route('admin.categories.index') }}"
                    class="{{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}">
                    <i class="bi bi-tags"></i> Categories
                </a>
            </nav>

            <div class="ts-admin-group">Bookings</div>
            <nav class="ts-admin-nav">
                <a href="{{ route('admin.bookings.index') }}"
                    class="{{ request()->routeIs('admin.bookings.*') ? 'is-active' : '' }}">
                    <i class="bi bi-ticket-perforated"></i> All bookings
                </a>
                <a href="{{ route('admin.payments.index') }}"
                    class="{{ request()->routeIs('admin.payments.*') ? 'is-active' : '' }}">
                    <i class="bi bi-credit-card"></i> Payments
                </a>
                <a href="{{ route('admin.contact-messages.index') }}"
                    class="{{ request()->routeIs('admin.contact-messages.*') ? 'is-active' : '' }}">
                    <i class="bi bi-inbox"></i> Contact inbox
                </a>
            </nav>

            <div class="ts-admin-group">Storefront</div>
            <nav class="ts-admin-nav">
                <a href="{{ route('home') }}"><i class="bi bi-box-arrow-up-right"></i> View website</a>
                <a href="{{ route('dashboard') }}"><i class="bi bi-person"></i> My account</a>
            </nav>
        </aside>

        <div class="ts-admin-main">
            <div class="ts-admin-bar">
                <div class="d-flex align-items-center gap-2">
                    <strong class="text-dark">@yield('heading', 'Dashboard')</strong>
                    <span class="badge text-bg-light d-none d-sm-inline-block">{{ now()->format('D, d M Y') }}</span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted d-none d-md-inline">
                        {{ $currentUser?->name }}
                    </span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-secondary" type="submit">
                            <i class="bi bi-box-arrow-right"></i> Sign out
                        </button>
                    </form>
                </div>
            </div>

            <div class="ts-admin-body">
                @if (session('success'))
                    <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
