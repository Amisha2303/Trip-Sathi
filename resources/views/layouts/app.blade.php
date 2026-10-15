<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <meta name="description"
        content="{{ $appSettings['meta_description'] ?? 'Trip Sathi — plan trips, book hotels, events, adventures, VIP Darshan and Pooja Vidhi, all in one place.' }}">

    <title>@yield('title', config('app.name', 'Trip Sathi'))</title>

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('head')
</head>

<body>
    @include('layouts.partials.header')

    {{-- Flash messages surfaced as toasts so they behave the same as AJAX errors. --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090;" data-ts-toasts>
        @foreach (['success' => 'text-success', 'error' => 'text-danger', 'warning' => 'text-warning',
            'info' => 'text-info'] as $type => $tone)
            @if (session($type))
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <span class="me-2 {{ $tone }}"><i
                                class="bi {{ ['success' => 'bi-check-circle-fill', 'error' => 'bi-x-circle-fill',
                                    'warning' => 'bi-exclamation-triangle-fill', 'info' => 'bi-info-circle-fill'][$type] }}"></i></span>
                        <strong class="me-auto">{{ ['success' => 'Success', 'error' => 'Something went wrong',
                            'warning' => 'Heads up', 'info' => 'Note'][$type] }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        {{ session($type) }}
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <main>
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    <script>
        window.TS_WISHLIST = {
            store: @json(route('wishlist.store')),
            loginUrl: @json(route('login')),
        };

        // Toasts rendered above are dismissed automatically; they are created
        // after page load so Vite's module script timing is irrelevant.
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-ts-toasts] .toast').forEach((el) => {
                new bootstrap.Toast(el, { delay: 6000 }).show();
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
