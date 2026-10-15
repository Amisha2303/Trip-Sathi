@extends('layouts.app')

@section('title', 'Trip Sathi — Plan Your Journey. Create Beautiful Memories.')

@push('head')
    <style>
        .ts-home-search .ts-search-field {
            min-width: 0;
        }
    </style>
@endpush

@section('content')
    {{-- Hero + main search ------------------------------------------------ --}}
    <section class="ts-hero ts-hero-photo" style="--ts-hero-img: url('{{ asset('photos/banners/banner-home.jpg') }}');">
        <div class="container">
            <span class="ts-eyebrow">Plan · Book · Remember</span>
            <h1 class="ts-hero-title">Plan Your Journey. Create Beautiful Memories.</h1>
            <p class="ts-hero-lede">Discover destinations, stays, events, adventures and spiritual experiences across
                India — then keep the whole itinerary in one place.</p>

            <div class="ts-hero-stats">
                <div><strong>{{ $counts['destinations'] }}+</strong><span>Destinations</span></div>
                <div><strong>{{ $counts['hotels'] }}+</strong><span>Stays</span></div>
                <div><strong>{{ $counts['events'] }}+</strong><span>Events</span></div>
                <div><strong>{{ $counts['darshan'] }}+</strong><span>Darshan slots</span></div>
            </div>
        </div>
    </section>

    <div class="container">
        <form class="ts-search ts-home-search" method="get" action="{{ route('destinations.index') }}" data-ts-search>
            <div class="row g-2 align-items-center">
                <div class="col-md-3 ts-search-field">
                    <label for="search-destination">Destination</label>
                    <select class="form-select" id="search-destination" name="destination">
                        <option value="">Anywhere in India</option>
                        @foreach ($destinationOptions as $destination)
                            <option value="{{ $destination->slug }}">{{ $destination->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 ts-search-field">
                    <label for="search-date">Travel date</label>
                    <input class="form-control" type="date" id="search-date" name="date">
                </div>

                <div class="col-md-2 ts-search-field">
                    <label for="search-travelers">Travelers</label>
                    <select class="form-select" id="search-travelers" name="travelers">
                        <option value="1">1 traveler</option>
                        <option value="2" selected>2 travelers</option>
                        <option value="3">3 travelers</option>
                        <option value="4">4+ travelers</option>
                    </select>
                </div>

                <div class="col-md-3 ts-search-field">
                    <label for="search-category">Category</label>
                    <select class="form-select" id="search-category" name="category" data-ts-search-target>
                        <option value="{{ route('destinations.index') }}">Destinations</option>
                        <option value="{{ route('hotels.index') }}">Hotels</option>
                        <option value="{{ route('events.index') }}">Events</option>
                        <option value="{{ route('adventures.index') }}">Adventures</option>
                        <option value="{{ route('darshan.index') }}">VIP Darshan</option>
                        <option value="{{ route('pooja.index') }}">Pooja Vidhi</option>
                        <option value="{{ route('trips.index') }}">Trip ideas</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-accent w-100 ts-search-submit" type="submit">Explore Now</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Popular destinations --------------------------------------------- --}}
    <section class="ts-section">
        <div class="container">
            <div class="ts-section-head">
                <div>
                    <span class="ts-eyebrow">Where travellers go</span>
                    <h2 class="ts-title">Popular destinations</h2>
                    <p class="ts-subtitle">Top-rated places for your next trip, with stays and experiences attached.</p>
                </div>
                <a class="btn btn-soft" href="{{ route('destinations.index') }}">View all <i
                        class="bi bi-arrow-right"></i></a>
            </div>

            <div class="row g-4">
                @php
                    $wishedDestinations = auth()->check()
                        ? auth()->user()->wishlistItems()->where('type', 'destination')->pluck('wishlistable_id')
                        : collect();
                @endphp

                @forelse ($destinations as $destination)
                    <div class="col-md-6 col-lg-4">
                        @include('partials.item-card', [
                            'image' => $destination->image_url,
                            'title' => $destination->name,
                            'subtitle' => $destination->location,
                            'text' => $destination->short_description,
                            'badge' => $destination->is_featured ? 'Popular' : null,
                            'wishType' => 'destination',
                            'wishId' => $destination->id,
                            'wished' => $wishedDestinations->contains($destination->id),
                            'cta' => 'View details',
                            'ctaUrl' => route('destinations.show', ['slug' => $destination->slug]),
                        ])
                    </div>
                @empty
                    <div class="col-12">
                        @include('partials.empty-state', [
                            'icon' => 'bi-geo-alt',
                            'title' => 'Destinations are being curated',
                            'text' => 'New places go live every week — check back shortly.',
                            'actionUrl' => route('contact'),
                            'actionLabel' => 'Tell us where to go next',
                        ])
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Featured hotels ---------------------------------------------------- --}}
    <section class="ts-section ts-section-alt">
        <div class="container">
            <div class="ts-section-head">
                <div>
                    <span class="ts-eyebrow">Stay well</span>
                    <h2 class="ts-title">Featured hotels</h2>
                    <p class="ts-subtitle">Handpicked properties with real ratings and clear cancellation terms.</p>
                </div>
                <a class="btn btn-soft" href="{{ route('hotels.index') }}">View all <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="row g-4">
                @php
                    $wishedHotels = auth()->check()
                        ? auth()->user()->wishlistItems()->where('type', 'hotel')->pluck('wishlistable_id')
                        : collect();
                @endphp

                @forelse ($hotels as $hotel)
                    <div class="col-md-6 col-xl-3">
                        @include('partials.item-card', [
                            'image' => $hotel->image_url,
                            'title' => $hotel->name,
                            'subtitle' => $hotel->destination?->name,
                            'stars' => (int) $hotel->star_rating,
                            'rating' => (float) $hotel->rating,
                            'price' => (float) $hotel->effective_price,
                            'priceNote' => 'per night',
                            'badge' => $hotel->is_featured ? 'Featured' : null,
                            'wishType' => 'hotel',
                            'wishId' => $hotel->id,
                            'wished' => $wishedHotels->contains($hotel->id),
                            'cta' => 'View details',
                            'ctaUrl' => route('hotels.show', ['slug' => $hotel->slug]),
                        ])
                    </div>
                @empty
                    <div class="col-12">
                        @include('partials.empty-state', [
                            'icon' => 'bi-building',
                            'title' => 'Hotels are coming online soon',
                            'actionUrl' => route('contact'),
                            'actionLabel' => 'Partner with us',
                        ])
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Adventures --------------------------------------------------------- --}}
    <section class="ts-section">
        <div class="container">
            <div class="ts-section-head">
                <div>
                    <span class="ts-eyebrow">Adrenaline</span>
                    <h2 class="ts-title">Adventure activities</h2>
                    <p class="ts-subtitle">Treks, dives, safaris and rides — with difficulty and duration spelled out.</p>
                </div>
                <a class="btn btn-soft" href="{{ route('adventures.index') }}">View all <i
                        class="bi bi-arrow-right"></i></a>
            </div>

            <div class="row g-4">
                @forelse ($adventures as $adventure)
                    <div class="col-md-6 col-xl-3">
                        @include('partials.item-card', [
                            'image' => $adventure->image_url,
                            'title' => $adventure->name,
                            'subtitle' => $adventure->destination?->name,
                            'meta' => ['bi-clock' => $adventure->duration_label],
                            'price' => (float) $adventure->effective_price,
                            'priceNote' => 'per person',
                            'badge' => $adventure->difficulty_label,
                            'wishType' => 'adventure',
                            'wishId' => $adventure->id,
                            'cta' => 'View details',
                            'ctaUrl' => route('adventures.show', ['slug' => $adventure->slug]),
                        ])
                    </div>
                @empty
                    <div class="col-12">
                        @include('partials.empty-state', [
                            'icon' => 'bi-mountain',
                            'title' => 'Adventures are on their way',
                            'actionUrl' => route('contact'),
                            'actionLabel' => 'Suggest an activity',
                        ])
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Events ------------------------------------------------------------- --}}
    <section class="ts-section ts-section-alt">
        <div class="container">
            <div class="ts-section-head">
                <div>
                    <span class="ts-eyebrow">What's on</span>
                    <h2 class="ts-title">Upcoming events</h2>
                    <p class="ts-subtitle">Festivals, walks and shows you can fold straight into your itinerary.</p>
                </div>
                <a class="btn btn-soft" href="{{ route('events.index') }}">View all <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="row g-4">
                @forelse ($events as $event)
                    <div class="col-md-6 col-xl-3">
                        @include('partials.item-card', [
                            'image' => $event->image_url,
                            'title' => $event->name,
                            'subtitle' => $event->venue,
                            'meta' => ['bi-calendar-event' => $event->event_date->format('d M Y')],
                            'price' => (float) $event->effective_price,
                            'priceNote' => 'per ticket',
                            'badge' => $event->is_featured ? 'Featured' : null,
                            'wishType' => 'event',
                            'wishId' => $event->id,
                            'cta' => 'View details',
                            'ctaUrl' => route('events.show', ['slug' => $event->slug]),
                        ])
                    </div>
                @empty
                    <div class="col-12">
                        @include('partials.empty-state', [
                            'icon' => 'bi-calendar-event',
                            'title' => 'No events scheduled yet',
                            'actionUrl' => route('events.index'),
                            'actionLabel' => 'Browse all events',
                        ])
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Spiritual ---------------------------------------------------------- --}}
    <section class="ts-section">
        <div class="container">
            <div class="ts-section-head">
                <div>
                    <span class="ts-eyebrow">Plan your darshan</span>
                    <h2 class="ts-title">VIP Darshan & Pooja Vidhi</h2>
                    <p class="ts-subtitle">Confirmed slots, documented requirements and verified pandits.</p>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-soft" href="{{ route('darshan.index') }}">Darshan</a>
                    <a class="btn btn-soft" href="{{ route('pooja.index') }}">Pooja</a>
                </div>
            </div>

            <div class="row g-4">
                @php
                    $wishedDarshan = auth()->check()
                        ? auth()->user()->wishlistItems()->where('type', 'darshan')->pluck('wishlistable_id')
                        : collect();
                    $wishedPooja = auth()->check()
                        ? auth()->user()->wishlistItems()->where('type', 'pooja')->pluck('wishlistable_id')
                        : collect();
                @endphp

                @forelse ($darshans as $darshan)
                    <div class="col-md-6 col-xl-3">
                        @include('partials.item-card', [
                            'image' => $darshan->image_url,
                            'title' => $darshan->name,
                            'subtitle' => $darshan->temple_name,
                            'meta' => ['bi-clock' => $darshan->duration_minutes . ' min'],
                            'price' => (float) $darshan->effective_price,
                            'priceNote' => 'per person',
                            'badge' => strtoupper($darshan->darshan_type),
                            'wishType' => 'darshan',
                            'wishId' => $darshan->id,
                            'wished' => $wishedDarshan->contains($darshan->id),
                            'cta' => 'View details',
                            'ctaUrl' => route('darshan.show', ['slug' => $darshan->slug]),
                        ])
                    </div>
                @empty
                    <div class="col-12">
                        @include('partials.empty-state', [
                            'icon' => 'bi-gem',
                            'title' => 'Darshan slots are being added',
                            'actionUrl' => route('darshan.index'),
                            'actionLabel' => 'See all darshan',
                        ])
                    </div>
                @endforelse

                @foreach ($poojas as $pooja)
                    <div class="col-md-6 col-xl-3">
                        @include('partials.item-card', [
                            'image' => $pooja->image_url,
                            'title' => $pooja->name,
                            'subtitle' => $pooja->temple_name,
                            'meta' => ['bi-clock' => $pooja->duration_minutes . ' min'],
                            'price' => (float) $pooja->effective_price,
                            'priceNote' => 'per pooja',
                            'badge' => 'Pooja vidhi',
                            'wishType' => 'pooja',
                            'wishId' => $pooja->id,
                            'wished' => $wishedPooja->contains($pooja->id),
                            'cta' => 'View details',
                            'ctaUrl' => route('pooja.show', ['slug' => $pooja->slug]),
                        ])
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Trips -------------------------------------------------------------- --}}
    <section class="ts-section ts-section-alt">
        <div class="container">
            <div class="ts-section-head">
                <div>
                    <span class="ts-eyebrow">Steal an itinerary</span>
                    <h2 class="ts-title">Popular trip plans</h2>
                    <p class="ts-subtitle">Borrow a route from the community, then reshape it around your dates.</p>
                </div>
                <a class="btn btn-soft" href="{{ route('trips.index') }}">Open planner <i
                        class="bi bi-arrow-right"></i></a>
            </div>

            <div class="row g-4">
                @forelse ($trips as $trip)
                    <div class="col-md-6 col-lg-4">
                        <article class="ts-card h-100">
                            <div class="ts-card-media">
                                <img src="{{ $trip->image_url }}" alt="{{ $trip->name }}" loading="lazy"
                                     onerror="this.onerror=null;this.src='{{ asset('images/trip-placeholder.svg') }}';">
                                <span class="ts-card-media-tag">{{ $trip->duration_label }}</span>
                            </div>
                            <div class="ts-card-body">
                                <div class="ts-card-meta mb-1">
                                    <span><i class="bi bi-geo-alt"></i> {{ $trip->destination?->name ?? 'Flexible route' }}</span>
                                    <span><i class="bi bi-calendar3"></i> {{ $trip->date_range_label }}</span>
                                </div>
                                <h3 class="ts-card-title">{{ $trip->name }}</h3>
                                <p class="ts-card-text">{{ $trip->description ? \Illuminate\Support\Str::limit($trip->description, 110) : '' }}</p>
                                <div class="ts-card-foot">
                                    <span class="ts-card-meta"><i class="bi bi-person"></i> {{ $trip->travelers }}
                                        travellers</span>
                                    <a class="btn btn-sm btn-soft" href="{{ route('trips.index') }}">View plans</a>
                                </div>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        @include('partials.empty-state', [
                            'icon' => 'bi-map',
                            'title' => 'No public trip plans yet',
                            'text' => 'Sign in, plan your first trip and share it with the community.',
                            'actionUrl' => route('trips.index'),
                            'actionLabel' => 'Open the planner',
                        ])
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Why Trip Sathi ----------------------------------------------------- --}}
    <section class="ts-section">
        <div class="container">
            <div class="text-center mb-5">
                <span class="ts-eyebrow">Why Trip Sathi</span>
                <h2 class="ts-title">One sathi for the whole journey</h2>
                <p class="ts-subtitle mx-auto">Everything a trip needs — before, during and after you leave home.</p>
            </div>

            <div class="row g-4">
                @foreach ([
                    ['bi-search-heart', 'Curated catalogue', 'Destinations, stays, events and rituals vetted before they go live.'],
                    ['bi-calendar2-check', 'Real availability', 'Slots, capacities and cut-off windows shown before you pay.'],
                    ['bi-shield-check', 'Secure payments', 'Encrypted checkout with receipts, refunds and status history.'],
                    ['bi-headset', 'Human support', 'A real team on email and phone for booking changes and emergencies.'],
                    ['bi-map', 'Day-by-day planner', 'Attach bookings to days and keep the itinerary shareable.'],
                    ['bi-coin', 'Transparent pricing', 'Taxes, discounts and cancellation terms visible up front.'],
                ] as [$icon, $title, $text])
                    <div class="col-md-6 col-lg-4">
                        <div class="ts-card h-100">
                            <div class="ts-card-body">
                                <span class="ts-kpi-icon mb-3 d-inline-flex"><i class="bi {{ $icon }}"></i></span>
                                <h3 class="ts-card-title">{{ $title }}</h3>
                                <p class="ts-card-text mb-0">{{ $text }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Newsletter --------------------------------------------------------- --}}
    <section class="ts-hero ts-hero-photo" style="--ts-hero-img: url('{{ asset('photos/banners/banner-newsletter.jpg') }}'); padding: 3.5rem 0 4rem;">
        <div class="container text-center">
            <span class="ts-eyebrow">Stay in the loop</span>
            <h2 class="ts-hero-title mx-auto" style="max-width: 22ch;">Get travel updates worth opening</h2>
            <p class="ts-hero-lede mx-auto">Deal drops, festival dates and new destinations — about twice a month, never
                more.</p>

            <form class="d-flex gap-2 justify-content-center mt-4 flex-column flex-sm-row" style="max-width: 560px; margin-inline: auto;"
                method="post" action="{{ route('newsletter.store') }}">
                @csrf
                <input class="form-control form-control-lg" type="email" name="email" required
                    placeholder="Enter your email address" aria-label="Email address">
                <button class="btn btn-accent btn-lg px-4" type="submit">Subscribe</button>
            </form>

            @error('email')
                <p class="mt-2 mb-0 small" style="color:#ffd7a1;">{{ $message }}</p>
            @enderror
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // The search bar posts to whichever catalogue the traveller picked.
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('[data-ts-search]');
            const select = document.querySelector('[data-ts-search-target]');
            if (!form || !select) return;

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                const target = select.value || form.action;
                const params = new URLSearchParams(new FormData(form));
                params.delete('category');
                window.location.href = target + '?' + params.toString();
            });
        });
    </script>
@endpush
