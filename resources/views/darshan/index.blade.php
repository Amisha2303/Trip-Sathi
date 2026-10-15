@extends('layouts.app')

@section('title', 'VIP Darshan — Trip Sathi')

@section('content')
    @include('partials.page-hero', [
        'image' => asset('photos/banners/banner-darshan.jpg'),
        'eyebrow' => 'Skip the queue',
        'title' => 'VIP Darshan',
        'lede' => 'Special-entry darshan slots at India\'s most visited temples — fixed timings, documented ID rules and confirmed slots instead of hours in a queue.',
        'crumbs' => [['label' => 'Home', 'url' => route('home')], ['label' => 'VIP Darshan']],
        'stats' => [
            [$total . '+', 'Darshan slots'],
            ['Timed', 'Entry windows'],
            ['Verified', 'ID & dress guidance'],
        ],
    ])

    <section class="ts-section">
        <div class="container">
            <div class="ts-layout">
                <aside class="ts-layout-side">
                    <form class="ts-filter" method="get" action="{{ route('darshan.index') }}">
                        <h2 class="ts-filter-title"><i class="bi bi-funnel"></i> Filter darshan</h2>

                        <div class="ts-filter-group">
                            <label for="darshan-q">Search</label>
                            <input class="form-control" id="darshan-q" name="q" value="{{ $filters['q'] }}"
                                placeholder="Temple, deity or city…">
                        </div>

                        <div class="ts-filter-group">
                            <label for="darshan-destination">Pilgrimage place</label>
                            <select class="form-select" id="darshan-destination" name="destination">
                                <option value="">Anywhere</option>
                                @foreach ($destinations as $destination)
                                    <option value="{{ $destination->slug }}"
                                        @selected($filters['destination'] === $destination->slug)>{{ $destination->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="ts-filter-group">
                            <label for="darshan-type">Darshan type</label>
                            <select class="form-select" id="darshan-type" name="type">
                                <option value="">All types</option>
                                @foreach ($types as $value => $label)
                                    <option value="{{ $value }}" @selected($filters['type'] === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="ts-filter-group">
                            <label for="darshan-sort">Sort by</label>
                            <select class="form-select" id="darshan-sort" name="sort">
                                <option value="popular" @selected($filters['sort'] === 'popular')">Most booked</option>
                                <option value="price_asc" @selected($filters['sort'] === 'price_asc')">Price: low to high</option>
                                <option value="price_desc" @selected($filters['sort'] === 'price_desc')">Price: high to low</option>
                                <option value="duration" @selected($filters['sort'] === 'duration')">Shortest first</option>
                            </select>
                        </div>

                        <div class="ts-filter-actions">
                            <button class="btn btn-primary btn-sm" type="submit">Apply</button>
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('darshan.index') }}">Reset</a>
                        </div>
                    </form>
                </aside>

                <div class="ts-layout-main">
                    <div class="ts-toolbar">
                        <p class="ts-result-count">
                            Showing <strong>{{ $darshans->count() }}</strong> of
                            <strong>{{ $darshans->total() }}</strong> darshan options
                        </p>
                    </div>

                    <div class="row g-4">
                        @php
                            $wished = auth()->check()
                                ? auth()->user()->wishlistItems()->where('type', 'darshan')->pluck('wishlistable_id')
                                : collect();
                        @endphp

                        @forelse ($darshans as $darshan)
                            <div class="col-md-6 col-lg-4">
                                @include('partials.item-card', [
                                    'image' => $darshan->image_url,
                                    'title' => $darshan->name,
                                    'subtitle' => $darshan->temple_name . ($darshan->destination?->name ? ', ' . $darshan->destination->name : ''),
                                    'text' => $darshan->short_description ?? \Illuminate\Support\Str::limit((string) $darshan->description, 110),
                                    'meta' => [
                                        'bi-clock' => $darshan->duration_minutes . ' min',
                                        'bi-people' => 'Max ' . $darshan->max_visitors_per_booking . ' per booking',
                                    ],
                                    'badge' => strtoupper($darshan->darshan_type) . ' darshan',
                                    'price' => (float) $darshan->effective_price,
                                    'priceNote' => 'per person',
                                    'wishType' => 'darshan',
                                    'wishId' => $darshan->id,
                                    'wished' => $wished->contains($darshan->id),
                                    'cta' => 'View details',
                                    'ctaUrl' => route('darshan.show', ['slug' => $darshan->slug]),
                                ])
                            </div>
                        @empty
                            <div class="col-12">
                                @include('partials.empty-state', [
                                    'icon' => 'bi-gem',
                                    'title' => 'No darshan slots found',
                                    'text' => 'Try another temple town or clear the darshan type filter.',
                                    'actionUrl' => route('darshan.index'),
                                    'actionLabel' => 'Reset filters',
                                ])
                            </div>
                        @endforelse
                    </div>

                    @if ($darshans->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $darshans->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
