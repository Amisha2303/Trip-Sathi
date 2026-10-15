{{--
    Signed-in account chrome: shared sidebar + optional header card.
    Pages define `account-content` (required) and optionally `account-head`.
--}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="ts-account">
            <aside class="ts-layout-side">
                <nav class="ts-account-nav" aria-label="Account sections">
                    <a href="{{ route('dashboard') }}"
                        class="{{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="{{ route('bookings.index') }}"
                        class="{{ request()->routeIs('bookings.index') ? 'is-active' : '' }}">
                        <i class="bi bi-ticket-perforated"></i> My bookings
                    </a>
                    <a href="{{ route('trips.index', ['scope' => 'mine']) }}"
                        class="{{ request()->routeIs('trips.index') && request('scope') === 'mine' ? 'is-active' : '' }}">
                        <i class="bi bi-map"></i> My trips
                    </a>
                    <a href="{{ route('wishlist.index') }}"
                        class="{{ request()->routeIs('wishlist.index') ? 'is-active' : '' }}">
                        <i class="bi bi-heart"></i> Wishlist
                    </a>
                    <a href="{{ route('profile.edit') }}"
                        class="{{ request()->routeIs('profile.edit') ? 'is-active' : '' }}">
                        <i class="bi bi-person"></i> Profile & security
                    </a>

                    @if ($isAdmin)
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-shield-lock"></i> Admin panel
                        </a>
                    @endif
                </nav>
            </aside>

            <div class="ts-account-main">
                @hasSection('account-head')
                    @yield('account-head')
                @endif

                @yield('account-content')
            </div>
        </div>
    </div>
@endsection
