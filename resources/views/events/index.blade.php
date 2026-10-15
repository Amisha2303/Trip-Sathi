@extends('layouts.app')

@section('title', 'Events — Trip Sathi')

@section('content')
    @include('partials.page-hero', [
        'image' => asset('photos/banners/banner-events.jpg'),
        'eyebrow' => 'What\'s on',
        'title' => 'Events & Festivals',
        'lede' => 'Concerts, fairs, cultural nights and sporting weekends — filter by city, grab tickets before they sell out, and keep the plan in your trip itinerary.',
        'crumbs' => [['label' => 'Home', 'url' => route('home')], ['label' => 'Events']],
        'stats' => [
            [$total . '+', 'Events listed'],
            ['Instant', 'Ticket confirmation'],
            ['Secure', 'Payments'],
        ],
    ])

    <section class="ts-section">
        <div class="container">
            <div class="ts-layout">
                <aside class="ts-layout-side">
                    <form class="ts-filter" method="get" action="{{ route('events.index') }}">
                        <h2 class="ts-filter-title"><i class="bi bi-funnel"></i> Filter events</h2>

                        <div class="ts-filter-group">
                            <label for="event-q">Search</label>
                            <input class="form-control" id="event-q" name="q" value="{{ $filters['q'] }}"
                                placeholder="Event, venue or organiser…">
                        </div>

                        <div class="ts-filter-group">
                            <label for="event-destination">City</label>
                            <select class="form-select" id="event-destination" name="destination">
                                <option value="">Anywhere</option>
                                @foreach ($destinations as $destination)
                                    <option value="{{ $destination->slug }}"
                                        @selected($filters['destination'] === $destination->slug)>{{ $destination->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="ts-filter-group">
                            <span class="ts-filter-label">When</span>
                            <select class="form-select" name="when">
                                <option value="upcoming" @selected($filters['when'] === 'upcoming')">Upcoming</option>
                                <option value="past" @selected($filters['when'] === 'past')">Past events</option>
                                <option value="all" @selected($filters['when'] === 'all')">All events</option>
                            </select>
                        </div>

                        <div class="ts-filter-group">
                            <label for="event-sort">Sort by</label>
                            <select class="form-select" id="event-sort" name="sort">
                                <option value="soonest" @selected($filters['sort'] === 'soonest')">Soonest first</option>
                                <option value="latest" @selected($filters['sort'] === 'latest')">Latest first</option>
                                <option value="popular" @selected($filters['sort'] === 'popular')">Selling fastest</option>
                                <option value="price_asc" @selected($filters['sort'] === 'price_asc')">Price: low to high</option>
                                <option value="price_desc" @selected($filters['sort'] === 'price_desc')">Price: high to low</option>
                            </select>
                        </div>

                        <div class="ts-filter-actions">
                            <button class="btn btn-primary btn-sm" type="submit">Apply</button>
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('events.index') }}">Reset</a>
                        </div>
                    </form>
                </aside>

                <div class="ts-layout-main">
                    <div class="ts-toolbar">
                        <p class="ts-result-count">
                            Showing <strong>{{ $events->count() }}</strong> of
                            <strong>{{ $events->total() }}</strong> events
                        </p>
                    </div>

                    <div class="row g-4">
                        @php
                            $wished = auth()->check()
                                ? auth()->user()->wishlistItems()->where('type', 'event')->pluck('wishlistable_id')
                                : collect();
                        @endphp

                        @forelse ($events as $event)
                            @php
                                $seatsLeft = max(0, (int) $event->capacity - (int) $event->sold_count - (int) $event->reserved_count);
                            @endphp

                            <div class="col-md-6 col-lg-4">
                                @include('partials.item-card', [
                                    'image' => $event->image_url,
                                    'title' => $event->name,
                                    'subtitle' => $event->venue . ', ' . ($event->destination->name ?? $event->city),
                                    'text' => $event->short_description ?? \Illuminate\Support\Str::limit((string) $event->description, 110),
                                    'meta' => ['bi-calendar-event' => $event->event_date->format('D, d M Y')],
                                    'badge' => $seatsLeft > 0 ? ($seatsLeft < 20 ? 'Only ' . $seatsLeft . ' left' : 'On sale') : 'Sold out',
                                    'price' => (float) $event->effective_price,
                                    'priceNote' => 'per ticket',
                                    'wishType' => 'event',
                                    'wishId' => $event->id,
                                    'wished' => $wished->contains($event->id),
                                    'cta' => 'Enquire',
                                    'ctaUrl' => route('contact', ['subject' => 'Event: ' . $event->name]),
                                ])
                            </div>
                        @empty
                            <div class="col-12">
                                @include('partials.empty-state', [
                                    'icon' => 'bi-calendar-event',
                                    'title' => 'No events found',
                                    'text' => 'Nothing scheduled for that filter yet — check upcoming events in other cities.',
                                    'actionUrl' => route('events.index'),
                                    'actionLabel' => 'Show upcoming events',
                                ])
                            </div>
                        @endforelse
                    </div>

                    @if ($events->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $events->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
