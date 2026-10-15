@extends('layouts.app')

@section('title', 'Pooja Vidhi — Trip Sathi')

@section('content')
    @include('partials.page-hero', [
        'image' => asset('photos/banners/banner-pooja.jpg'),
        'eyebrow' => 'Rituals, done right',
        'title' => 'Pooja Vidhi',
        'lede' => 'Book qualified pandits, complete samagri and fixed time slots for griha pravesh, satyanarayan, karna vidhi and more — at the temple or at your home.',
        'crumbs' => [['label' => 'Home', 'url' => route('home')], ['label' => 'Pooja Vidhi']],
        'stats' => [
            [$total . '+', 'Pooja packages'],
            ['Samagri', 'Included'],
            ['Verified', 'Pandits'],
        ],
    ])

    <section class="ts-section">
        <div class="container">
            <div class="ts-layout">
                <aside class="ts-layout-side">
                    <form class="ts-filter" method="get" action="{{ route('pooja.index') }}">
                        <h2 class="ts-filter-title"><i class="bi bi-funnel"></i> Filter pooja</h2>

                        <div class="ts-filter-group">
                            <label for="pooja-q">Search</label>
                            <input class="form-control" id="pooja-q" name="q" value="{{ $filters['q'] }}"
                                placeholder="Pooja, temple or deity…">
                        </div>

                        <div class="ts-filter-group">
                            <label for="pooja-destination">Place</label>
                            <select class="form-select" id="pooja-destination" name="destination">
                                <option value="">Anywhere</option>
                                @foreach ($destinations as $destination)
                                    <option value="{{ $destination->slug }}"
                                        @selected($filters['destination'] === $destination->slug)>{{ $destination->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="ts-filter-group">
                            <label for="pooja-sort">Sort by</label>
                            <select class="form-select" id="pooja-sort" name="sort">
                                <option value="popular" @selected($filters['sort'] === 'popular')">Most booked</option>
                                <option value="price_asc" @selected($filters['sort'] === 'price_asc')">Price: low to high</option>
                                <option value="price_desc" @selected($filters['sort'] === 'price_desc')">Price: high to low</option>
                                <option value="duration" @selected($filters['sort'] === 'duration')">Shortest first</option>
                            </select>
                        </div>

                        <div class="ts-filter-actions">
                            <button class="btn btn-primary btn-sm" type="submit">Apply</button>
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('pooja.index') }}">Reset</a>
                        </div>
                    </form>
                </aside>

                <div class="ts-layout-main">
                    <div class="ts-toolbar">
                        <p class="ts-result-count">
                            Showing <strong>{{ $poojas->count() }}</strong> of
                            <strong>{{ $poojas->total() }}</strong> pooja vidhi
                        </p>
                    </div>

                    <div class="row g-4">
                        @php
                            $wished = auth()->check()
                                ? auth()->user()->wishlistItems()->where('type', 'pooja')->pluck('wishlistable_id')
                                : collect();
                        @endphp

                        @forelse ($poojas as $pooja)
                            <div class="col-md-6 col-lg-4">
                                @include('partials.item-card', [
                                    'image' => $pooja->image_url,
                                    'title' => $pooja->name,
                                    'subtitle' => $pooja->temple_name . ($pooja->destination?->name ? ', ' . $pooja->destination->name : ''),
                                    'text' => $pooja->short_description ?? \Illuminate\Support\Str::limit((string) $pooja->benefits, 110),
                                    'meta' => [
                                        'bi-clock' => $pooja->duration_minutes . ' min',
                                        'bi-people' => 'Up to ' . $pooja->max_participants . ' participants',
                                    ],
                                    'badge' => $pooja->is_featured ? 'Recommended' : null,
                                    'price' => (float) $pooja->effective_price,
                                    'priceNote' => 'per pooja',
                                    'wishType' => 'pooja',
                                    'wishId' => $pooja->id,
                                    'wished' => $wished->contains($pooja->id),
                                    'cta' => 'Enquire',
                                    'ctaUrl' => route('contact', ['subject' => 'Pooja: ' . $pooja->name]),
                                ])
                            </div>
                        @empty
                            <div class="col-12">
                                @include('partials.empty-state', [
                                    'icon' => 'bi-flower1',
                                    'title' => 'No pooja packages found',
                                    'text' => 'Try a different place, or clear the search to browse everything.',
                                    'actionUrl' => route('pooja.index'),
                                    'actionLabel' => 'Reset filters',
                                ])
                            </div>
                        @endforelse
                    </div>

                    @if ($poojas->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $poojas->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
