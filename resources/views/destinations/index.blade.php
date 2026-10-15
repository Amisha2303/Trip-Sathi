@extends('layouts.app')

@section('title', 'Destinations — Trip Sathi')

@section('content')
    @include('partials.page-hero', [
        'image' => asset('photos/banners/banner-destinations.jpg'),
        'eyebrow' => 'Explore India',
        'title' => 'Destinations',
        'lede' => 'From temple towns on the Gujarat coast to lake cities in Rajasthan — handpicked places, honest notes on when to go, and everything you can book once you land.',
        'crumbs' => [['label' => 'Home', 'url' => route('home')], ['label' => 'Destinations']],
        'stats' => [
            [$total . '+', 'Places to explore'],
            ['4.8', 'Average traveller rating'],
            ['24×7', 'Planning support'],
        ],
    ])

    <section class="ts-section">
        <div class="container">
            <div class="ts-layout">
                <aside class="ts-layout-side">
                    <form class="ts-filter" method="get" action="{{ route('destinations.index') }}">
                        <h2 class="ts-filter-title"><i class="bi bi-funnel"></i> Filter destinations</h2>

                        <div class="ts-filter-group">
                            <label for="dest-q">Search</label>
                            <input class="form-control" id="dest-q" name="q" value="{{ $filters['q'] }}"
                                placeholder="City, state or place…">
                        </div>

                        <div class="ts-filter-group">
                            <label for="dest-state">State</label>
                            <select class="form-select" id="dest-state" name="state">
                                <option value="">All states</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state }}" @selected($filters['state'] === $state)>{{ $state }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="ts-filter-group">
                            <label for="dest-sort">Sort by</label>
                            <select class="form-select" id="dest-sort" name="sort">
                                <option value="popular" @selected($filters['sort'] === 'popular')">Most popular</option>
                                <option value="name" @selected($filters['sort'] === 'name')">Name (A–Z)</option>
                                <option value="newest" @selected($filters['sort'] === 'newest')">Recently added</option>
                            </select>
                        </div>

                        <div class="ts-filter-actions">
                            <button class="btn btn-primary btn-sm" type="submit">Apply</button>
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('destinations.index') }}">Reset</a>
                        </div>
                    </form>
                </aside>

                <div class="ts-layout-main">
                    <div class="ts-toolbar">
                        <p class="ts-result-count">
                            Showing <strong>{{ $destinations->count() }}</strong> of
                            <strong>{{ $destinations->total() }}</strong> destinations
                        </p>
                    </div>

                    <div class="row g-4">
                        @php
                            $wished = auth()->check()
                                ? auth()->user()->wishlistItems()->where('type', 'destination')->pluck('wishlistable_id')
                                : collect();
                        @endphp

                        @forelse ($destinations as $destination)
                            <div class="col-md-6 col-lg-4">
                                @include('partials.item-card', [
                                    'image' => $destination->image_url,
                                    'title' => $destination->name,
                                    'subtitle' => $destination->location,
                                    'text' => $destination->short_description ?? \Illuminate\Support\Str::limit((string) $destination->description, 110),
                                    'badge' => $destination->is_featured ? 'Featured' : null,
                                    'meta' => [],
                                    'wishType' => 'destination',
                                    'wishId' => $destination->id,
                                    'wished' => $wished->contains($destination->id),
                                    'cta' => 'View details',
                                    'ctaUrl' => route('destinations.show', ['slug' => $destination->slug]),
                                ])
                            </div>
                        @empty
                            <div class="col-12">
                                @include('partials.empty-state', [
                                    'icon' => 'bi-geo-alt',
                                    'title' => 'No destinations match those filters',
                                    'text' => 'Try clearing the search or picking another state.',
                                    'actionUrl' => route('destinations.index'),
                                    'actionLabel' => 'Reset filters',
                                ])
                            </div>
                        @endforelse
                    </div>

                    @if ($destinations->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $destinations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
