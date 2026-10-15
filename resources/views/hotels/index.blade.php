@extends('layouts.app')

@section('title', 'Hotels — Trip Sathi')

@section('content')
    @include('partials.page-hero', [
        'image' => asset('photos/banners/banner-hotels.jpg'),
        'eyebrow' => 'Stay well',
        'title' => 'Hotels & Stays',
        'lede' => 'Handpicked hotels, heritage havelis and beachside resorts with transparent pricing, real guest ratings and free cancellation windows.',
        'crumbs' => [['label' => 'Home', 'url' => route('home')], ['label' => 'Hotels']],
        'stats' => [
            [$total . '+', 'Verified stays'],
            ['Free', 'Cancellation on most stays'],
            ['Best', 'Price guarantee'],
        ],
    ])

    <section class="ts-section">
        <div class="container">
            <div class="ts-layout">
                <aside class="ts-layout-side">
                    <form class="ts-filter" method="get" action="{{ route('hotels.index') }}">
                        <h2 class="ts-filter-title"><i class="bi bi-funnel"></i> Filter stays</h2>

                        <div class="ts-filter-group">
                            <label for="hotel-q">Search</label>
                            <input class="form-control" id="hotel-q" name="q" value="{{ $filters['q'] }}"
                                placeholder="Hotel, area or landmark…">
                        </div>

                        <div class="ts-filter-group">
                            <label for="hotel-destination">Destination</label>
                            <select class="form-select" id="hotel-destination" name="destination">
                                <option value="">Anywhere</option>
                                @foreach ($destinations as $destination)
                                    <option value="{{ $destination->slug }}"
                                        @selected($filters['destination'] === $destination->slug)>{{ $destination->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="ts-filter-group">
                            <span class="ts-filter-label">Star rating</span>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach ([3, 4, 5] as $stars)
                                    <label class="btn btn-sm {{ $filters['stars'] === $stars ? 'btn-primary' : 'btn-outline-secondary' }}">
                                        <input class="d-none" type="radio" name="stars" value="{{ $stars }}"
                                            @checked($filters['stars'] === $stars)>
                                        {{ $stars }}★
                                    </label>
                                @endforeach
                                <label class="btn btn-sm {{ $filters['stars'] === 0 ? 'btn-primary' : 'btn-outline-secondary' }}">
                                    <input class="d-none" type="radio" name="stars" value="0" @checked($filters['stars'] === 0)>
                                    Any
                                </label>
                            </div>
                        </div>

                        <div class="ts-filter-group">
                            <span class="ts-filter-label">Price per night (₹)</span>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input class="form-control" type="number" min="0" name="min_price"
                                        value="{{ $filters['min_price'] }}" placeholder="Min">
                                </div>
                                <div class="col-6">
                                    <input class="form-control" type="number" min="0" name="max_price"
                                        value="{{ $filters['max_price'] }}" placeholder="Max">
                                </div>
                            </div>
                        </div>

                        <div class="ts-filter-group">
                            <label for="hotel-sort">Sort by</label>
                            <select class="form-select" id="hotel-sort" name="sort">
                                <option value="recommended" @selected($filters['sort'] === 'recommended')">Recommended</option>
                                <option value="price_asc" @selected($filters['sort'] === 'price_asc')">Price: low to high</option>
                                <option value="price_desc" @selected($filters['sort'] === 'price_desc')">Price: high to low</option>
                                <option value="rating" @selected($filters['sort'] === 'rating')">Guest rating</option>
                                <option value="stars" @selected($filters['sort'] === 'stars')">Star rating</option>
                            </select>
                        </div>

                        <div class="ts-filter-actions">
                            <button class="btn btn-primary btn-sm" type="submit">Apply</button>
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('hotels.index') }}">Reset</a>
                        </div>
                    </form>
                </aside>

                <div class="ts-layout-main">
                    <div class="ts-toolbar">
                        <p class="ts-result-count">
                            Showing <strong>{{ $hotels->count() }}</strong> of
                            <strong>{{ $hotels->total() }}</strong> stays
                        </p>
                    </div>

                    <div class="row g-4">
                        @php
                            $wished = auth()->check()
                                ? auth()->user()->wishlistItems()->where('type', 'hotel')->pluck('wishlistable_id')
                                : collect();
                        @endphp

                        @forelse ($hotels as $hotel)
                            <div class="col-md-6 col-xl-4">
                                @include('partials.item-card', [
                                    'image' => $hotel->image_url,
                                    'title' => $hotel->name,
                                    'subtitle' => $hotel->destination->name ?? 'Multiple locations',
                                    'text' => $hotel->short_description ?? \Illuminate\Support\Str::limit((string) $hotel->description, 110),
                                    'badge' => $hotel->is_featured ? 'Featured' : ($hotel->star_rating >= 4 ? $hotel->star_rating . ' star' : null),
                                    'stars' => (int) $hotel->star_rating,
                                    'rating' => (float) $hotel->rating,
                                    'ratingNote' => (int) $hotel->review_count > 0 ? $hotel->review_count . ' reviews' : 'New listing',
                                    'price' => (float) $hotel->effective_price,
                                    'priceNote' => 'per night',
                                    'wishType' => 'hotel',
                                    'wishId' => $hotel->id,
                                    'wished' => $wished->contains($hotel->id),
                                    'cta' => 'Enquire',
                                    'ctaUrl' => route('contact', ['subject' => 'Hotel: ' . $hotel->name]),
                                ])
                            </div>
                        @empty
                            <div class="col-12">
                                @include('partials.empty-state', [
                                    'icon' => 'bi-building',
                                    'title' => 'No hotels match those filters',
                                    'text' => 'Widen the price range or clear the star filter to see more stays.',
                                    'actionUrl' => route('hotels.index'),
                                    'actionLabel' => 'Reset filters',
                                ])
                            </div>
                        @endforelse
                    </div>

                    @if ($hotels->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $hotels->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
