@extends('layouts.account')

@section('title', 'Dashboard — Trip Sathi')

@section('account-head')
    @php
        $initials = collect(explode(' ', trim((string) $currentUser->name)))
            ->filter()
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->take(2)
            ->implode('');
    @endphp

    <div class="ts-account-head">
        <span class="ts-avatar">{{ $initials ?: 'TS' }}</span>
        <div>
            <h1>Welcome back, {{ \Illuminate\Support\Str::before($currentUser->name, ' ') }}</h1>
            <p>{{ $currentUser->email }} · {{ $isAdmin ? 'Administrator' : 'Traveller' }}</p>
        </div>
        <div class="ts-account-actions">
            <a class="btn btn-sm btn-accent" href="{{ route('trips.index', ['scope' => 'mine']) }}">
                <i class="bi bi-plus-lg"></i> Plan a trip
            </a>
            <a class="btn btn-sm btn-light" href="{{ route('profile.edit') }}">
                <i class="bi bi-person-gear"></i> Edit profile
            </a>
        </div>
    </div>
@endsection

@section('account-content')
    <div class="row g-3 mb-4">
        @foreach ([
            ['label' => 'Total bookings', 'value' => $stats['total'], 'icon' => 'bi-ticket-perforated'],
            ['label' => 'Upcoming', 'value' => $stats['upcoming'], 'icon' => 'bi-calendar-check'],
            ['label' => 'Completed', 'value' => $stats['completed'], 'icon' => 'bi-check2-circle'],
            ['label' => 'Cancelled', 'value' => $stats['cancelled'], 'icon' => 'bi-x-circle'],
            ['label' => 'Saved trips', 'value' => $stats['trips'], 'icon' => 'bi-map'],
            ['label' => 'Wishlist', 'value' => $stats['wishlist'], 'icon' => 'bi-heart'],
        ] as $stat)
            <div class="col-6 col-md-4 col-xl-2">
                <div class="ts-stat">
                    <div class="ts-stat-label"><i class="bi {{ $stat['icon'] }} me-1"></i>{{ $stat['label'] }}</div>
                    <div class="ts-stat-value">{{ $stat['value'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="ts-card mb-4">
                <div class="ts-card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="ts-title" style="font-size: 1.15rem;">Upcoming trips & bookings</h2>
                        <a class="btn btn-sm btn-soft" href="{{ route('bookings.index') }}">All bookings</a>
                    </div>

                    @if ($upcomingBookings->isEmpty())
                        @include('partials.empty-state', [
                            'icon' => 'bi-calendar-plus',
                            'title' => 'No upcoming bookings',
                            'text' => 'When you book a stay, event or darshan it will show up here with its dates.',
                            'actionUrl' => route('destinations.index'),
                            'actionLabel' => 'Explore destinations',
                        ])
                    @else
                        <ul class="ts-list">
                            @foreach ($upcomingBookings as $booking)
                                <li>
                                    <img class="ts-list-thumb" src="{{ $booking->destination?->image_url ?? asset('images/placeholder.svg') }}"
                                        alt="">
                                    <div class="flex-grow-1">
                                        <p class="ts-list-title">{{ $booking->title }}</p>
                                        <p class="ts-list-meta">
                                            <i class="bi bi-calendar3 me-1"></i>{{ $booking->booking_date->format('D, d M Y') }}
                                            @if ($booking->destination)
                                                · <i class="bi bi-geo-alt me-1"></i>{{ $booking->destination->name }}
                                            @endif
                                        </p>
                                    </div>
                                    <span class="ts-badge ts-badge-{{ $booking->status->value }}">{{ $booking->status_label }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="ts-card">
                <div class="ts-card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="ts-title" style="font-size: 1.15rem;">Recent bookings</h2>
                        <a class="btn btn-sm btn-soft" href="{{ route('bookings.index') }}">History</a>
                    </div>

                    @if ($recentBookings->isEmpty())
                        <p class="text-muted mb-0">Nothing booked yet — your history will appear here.</p>
                    @else
                        <div class="ts-table-card">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Booking</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentBookings as $booking)
                                            <tr>
                                                <td>
                                                    <strong class="text-dark">{{ $booking->booking_number }}</strong>
                                                    <div class="small text-muted">{{ $booking->type_label }}</div>
                                                </td>
                                                <td>{{ $booking->booking_date->format('d M Y') }}</td>
                                                <td>{{ $booking->final_amount_rupees > 0 ? \App\Support\Money::decimal($booking->final_amount_rupees) : '—' }}</td>
                                                <td>
                                                    <span class="ts-badge ts-badge-{{ $booking->status->value }}">{{ $booking->status_label }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="ts-card mb-4">
                <div class="ts-card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="ts-title" style="font-size: 1.15rem;">Recommended for you</h2>
                        <a class="btn btn-sm btn-soft" href="{{ route('destinations.index') }}">See all</a>
                    </div>

                    @if ($recommendations->isEmpty())
                        <p class="text-muted mb-0">Popular destinations will appear here.</p>
                    @else
                        <ul class="ts-list">
                            @foreach ($recommendations as $destination)
                                <li>
                                    <img class="ts-list-thumb" src="{{ $destination->image_url }}" alt="{{ $destination->name }}">
                                    <div class="flex-grow-1">
                                        <p class="ts-list-title">{{ $destination->name }}</p>
                                        <p class="ts-list-meta">
                                            {{ $destination->location }} · {{ $destination->hotels_count }} stay{{ $destination->hotels_count === 1 ? '' : 's' }}
                                        </p>
                                    </div>
                                    <a class="btn btn-sm btn-soft"
                                        href="{{ route('hotels.index', ['destination' => $destination->slug]) }}">View</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="ts-card">
                <div class="ts-card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="ts-title" style="font-size: 1.15rem;">Notifications</h2>
                    </div>

                    @if ($notifications->isEmpty())
                        <p class="text-muted mb-0">You are all caught up — no notifications yet.</p>
                    @else
                        <ul class="ts-list">
                            @foreach ($notifications as $notification)
                                <li>
                                    <i class="bi bi-bell text-primary"></i>
                                    <div>
                                        <p class="ts-list-title">{{ $notification->data['title'] ?? class_basename($notification->type) }}</p>
                                        <p class="ts-list-meta">
                                            {{ $notification->data['body'] ?? '' }}
                                            · {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
