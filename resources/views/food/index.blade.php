@extends('layouts.app')

@section('title', 'Food & Dining — Trip Sathi')

@section('content')
    @include('partials.page-hero', [
        'image' => asset('photos/banners/banner-hotels.jpg'),
        'eyebrow' => 'Eat well, everywhere',
        'title' => 'Food & Dining',
        'lede' => 'From thali joints and coastal seafood to hosted tasting walks — book a table or a full food experience with verified kitchens and clear pricing.',
        'crumbs' => [['label' => 'Home', 'url' => route('home')], ['label' => 'Food & Dining']],
        'stats' => [
            [$total . '+', 'Places to eat'],
            ['4.7', 'Average diner rating'],
            ['24h', 'Reservation support'],
        ],
    ])

    {{-- Hosted food experiences ------------------------------------------- --}}
    @if ($experiences->isNotEmpty())
        <section class="ts-section ts-section-tight">
            <div class="container">
                <div class="ts-section-head">
                    <div>
                        <span class="ts-eyebrow">Hosted experiences</span>
                        <h2 class="ts-title mb-0">Eat like a local, guided</h2>
                    </div>
                </div>
                <div class="row g-4">
                    @foreach (\App\Support\Catalogue::cards($experiences, 'food') as $card)
                        <div class="col-md-6 col-lg-3">
                            @include('partials.item-card', $card)
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Restaurants ------------------------------------------------------- --}}
    <section class="ts-section">
        <div class="container">
            <div class="ts-layout">
                <aside class="ts-layout-side">
                    <form class="ts-filter" method="get" action="{{ route('food.index') }}">
                        <h2 class="ts-filter-title"><i class="bi bi-funnel"></i> Filter restaurants</h2>

                        <div class="ts-filter-group">
                            <label for="food-q">Search</label>
                            <input class="form-control" id="food-q" name="q" value="{{ $filters['q'] }}"
                                placeholder="Name, cuisine or city…">
                        </div>

                        <div class="ts-filter-group">
                            <label for="food-destination">Destination</label>
                            <select class="form-select" id="food-destination" name="destination">
                                <option value="">All destinations</option>
                                @foreach ($destinations as $slug => $name)
                                    <option value="{{ $slug }}" @selected($filters['destination'] === $slug)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="ts-filter-group">
                            <label for="food-cuisine">Cuisine</label>
                            <select class="form-select" id="food-cuisine" name="cuisine">
                                <option value="">Any cuisine</option>
                                @foreach ($cuisines as $cuisine)
                                    <option value="{{ $cuisine }}" @selected($filters['cuisine'] === $cuisine)>{{ $cuisine }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="ts-filter-group">
                            <label for="food-sort">Sort by</label>
                            <select class="form-select" id="food-sort" name="sort">
                                <option value="popular" @selected($filters['sort'] === 'popular')">Top rated</option>
                                <option value="name" @selected($filters['sort'] === 'name')">Name (A–Z)</option>
                                <option value="cost_low" @selected($filters['sort'] === 'cost_low')">Cost: low to high</option>
                                <option value="cost_high" @selected($filters['sort'] === 'cost_high')">Cost: high to low</option>
                            </select>
                        </div>

                        <div class="ts-filter-actions">
                            <button class="btn btn-primary btn-sm" type="submit">Apply</button>
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('food.index') }}">Reset</a>
                        </div>
                    </form>
                </aside>

                <div class="ts-layout-main">
                    <div class="ts-toolbar">
                        <p class="ts-result-count">
                            Showing <strong>{{ $restaurants->count() }}</strong> of
                            <strong>{{ $restaurants->total() }}</strong> places to eat
                        </p>
                    </div>

                    <div class="row g-4">
                        @forelse ($restaurants as $restaurant)
                            <div class="col-md-6 col-lg-4">
                                @include('partials.item-card', \App\Support\Catalogue::card($restaurant, 'restaurant'))
                            </div>
                        @empty
                            <div class="col-12">
                                @include('partials.empty-state', [
                                    'icon' => 'bi-cup-hot',
                                    'title' => 'No restaurants match those filters',
                                    'text' => 'Try a different destination or clear the cuisine filter.',
                                    'actionUrl' => route('food.index'),
                                    'actionLabel' => 'Reset filters',
                                ])
                            </div>
                        @endforelse
                    </div>

                    @if ($restaurants->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $restaurants->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
