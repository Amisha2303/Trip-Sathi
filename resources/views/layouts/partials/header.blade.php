<header class="ts-header">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img class="brand-logo" src="{{ asset('images/logo.png') }}" width="116" height="52"
                    alt="{{ $appSettings['site_name'] ?? config('app.name', 'Trip Sathi') }}">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('destinations*') ? 'active' : '' }}" href="{{ route('destinations.index') }}">Destinations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('hotels*') ? 'active' : '' }}" href="{{ route('hotels.index') }}">Hotels</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('food*') ? 'active' : '' }}" href="{{ route('food.index') }}">Food &amp; Dining</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('events*') ? 'active' : '' }}" href="{{ route('events.index') }}">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('adventures*') ? 'active' : '' }}" href="{{ route('adventures.index') }}">Adventures</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('darshan*') ? 'active' : '' }}" href="{{ route('darshan.index') }}">VIP Darshan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('pooja*') ? 'active' : '' }}" href="{{ route('pooja.index') }}">Pooja Vidhi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('trips*') ? 'active' : '' }}" href="{{ route('trips.index') }}">Trip Planner</a>
                    </li>
                </ul>

                <div class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <a class="nav-link" href="{{ route('contact') }}">Contact</a>

                    @auth
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                {{ \Illuminate\Support\Str::before($currentUser->name, ' ') }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('bookings.index') }}">
                                        <i class="bi bi-ticket-perforated me-2"></i>My bookings
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('trips.index') }}">
                                        <i class="bi bi-map me-2"></i>My trips
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('wishlist.index') }}">
                                        <i class="bi bi-heart me-2"></i>Wishlist
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="bi bi-person me-2"></i>Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.preferences') }}">
                                        <i class="bi bi-sliders me-2"></i>Preferences
                                    </a>
                                </li>

                                @if ($isAdmin)
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            <i class="bi bi-shield-lock me-2"></i>Admin panel
                                        </a>
                                    </li>
                                @endif

                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="dropdown-item text-danger" type="submit">
                                            <i class="bi bi-box-arrow-right me-2"></i>Sign out
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a class="nav-link" href="{{ route('login') }}">Sign in</a>
                        <a class="btn btn-accent nav-link-cta" href="{{ route('register') }}">Get started</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>
