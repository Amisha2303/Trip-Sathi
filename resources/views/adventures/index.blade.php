@extends('layouts.app')

@section('title', 'Adventure Activities — Trip Sathi')

@section('content')
    @include('partials.page-hero', [
        'image' => asset('photos/banners/banner-adventures.jpg'),
        'eyebrow' => 'Get your pulse up',
        'title' => 'Adventure Activities',
        'lede' => 'Treks, rafting runs, dives and sky-high flights — with difficulty levels, duration, safety notes and age limits spelled out before you book.',
        'crumbs' => [['label' => 'Home', 'url' => route('home')], ['label' => 'Adventures']],
        'stats' => [
            [$total . '+', 'Activities'],
            ['Certified', 'Operators & gear'],
            ['Clear', 'Difficulty ratings'],
        ],
    ])

    <section class="ts-section">
        <div class="container">
            <div class="ts-layout">
                <aside class="ts-layout-side">
                    <form class="ts-filter" method="get" action="{{ route('adventures.index') }}">
                        <h2 class="ts-filter-title"><i class="bi bi-funnel"></i> Filter activities</h2>

                        <div class="ts-filter-group">
                            <label for="adv-q">Search</label>
                            <input class="form-control" id="adv-q" name="q" value="{{ $filters['q'] }}"
                                placeholder="Activity or experience…">
                        </div>

                        <div class="ts-filter-group">
                            <label for="adv-destination">Destination</label>
                            <select class="form-select" id="adv-destination" name="destination">
                                <option value="">Anywhere</option>
                                @foreach ($destinations as $destination)
                                    <option value="{{ $destination->slug }}"
                                        @selected($filters['destination'] === $destination->slug)>{{ $destination->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="ts-filter-group">
                            <label for="adv-type">Activity type</label>
                            <select class="form-select" id="adv-type" name="type">
                                <option value="">All types</option>
                                @foreach ($types as $value => $label)
                                    <option value="{{ $value }}" @selected($filters['type'] === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="ts-filter-group">
                            <label for="adv-difficulty">Difficulty</label>
                            <select class="form-select" id="adv-difficulty" name="difficulty">
                                <option value="">Any level</option>
                                @foreach ($difficulties as $value => $label)
                                    <option value="{{ $value }}" @selected($filters['difficulty'] === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="ts-filter-group">
                            <label for="adv-sort">Sort by</label>
                            <select class="form-select" id="adv-sort" name="sort">
                                <option value="popular" @selected($filters['sort'] === 'popular')">Most popular</option>
                                <option value="price_asc" @selected($filters['sort'] === 'price_asc')">Price: low to high</option>
                                <option value="price_desc" @selected($filters['sort'] === 'price_desc')">Price: high to low</option>
                                <option value="duration" @selected($filters['sort'] === 'duration')">Shortest first</option>
                            </select>
                        </div>

                        <div class="ts-filter-actions">
                            <button class="btn btn-primary btn-sm" type="submit">Apply</button>
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('adventures.index') }}">Reset</a>
                        </div>
                    </form>
                </aside>

                <div class="ts-layout-main">
                    <div class="ts-toolbar">
                        <p class="ts-result-count">
                            Showing <strong>{{ $adventures->count() }}</strong> of
                            <strong>{{ $adventures->total() }}</strong> activities
                        </p>
                    </div>

                    <div class="row g-4">
                        @php
                            $wished = auth()->check()
                                ? auth()->user()->wishlistItems()->where('type', 'adventure')->pluck('wishlistable_id')
                                : collect();
                        @endphp

                        @forelse ($adventures as $adventure)
                            <div class="col-md-6 col-lg-4">
                                @include('partials.item-card', [
                                    'image' => $adventure->image_url,
                                    'title' => $adventure->name,
                                    'subtitle' => $adventure->destination->name ?? 'Multiple locations',
                                    'text' => $adventure->short_description ?? \Illuminate\Support\Str::limit((string) $adventure->description, 110),
                                    'meta' => [
                                        'bi-clock' => $adventure->duration_label,
                                        'bi-speedometer2' => $adventure->difficulty_label,
                                    ],
                                    'badge' => $adventure->is_featured ? 'Popular' : null,
                                    'price' => (float) $adventure->effective_price,
                                    'priceNote' => 'per person',
                                    'wishType' => 'adventure',
                                    'wishId' => $adventure->id,
                                    'wished' => $wished->contains($adventure->id),
                                    'cta' => 'View details',
                                    'ctaUrl' => route('adventures.show', ['slug' => $adventure->slug]),
                                ])
                            </div>
                        @empty
                            <div class="col-12">
                                @include('partials.empty-state', [
                                    'icon' => 'bi-signpost-split',
                                    'title' => 'No activities match those filters',
                                    'text' => 'Try another destination or clear the difficulty filter.',
                                    'actionUrl' => route('adventures.index'),
                                    'actionLabel' => 'Reset filters',
                                ])
                            </div>
                        @endforelse
                    </div>

                    @if ($adventures->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $adventures->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
