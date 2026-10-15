@extends('layouts.account')

@section('title', 'Wishlist — Trip Sathi')

@section('account-content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h1 class="ts-title" style="font-size: 1.4rem;">Wishlist</h1>
            <p class="ts-subtitle">{{ $total }} saved item{{ $total === 1 ? '' : 's' }} — tap the heart anywhere to add or remove.</p>
        </div>
        <a class="btn btn-accent btn-sm" href="{{ route('destinations.index') }}">
            <i class="bi bi-compass"></i> Find more
        </a>
    </div>

    <div class="ts-tabs">
        <a href="{{ route('wishlist.index') }}" class="{{ $type === null ? 'is-active' : '' }}">
            All <span class="badge text-bg-light ms-1">{{ $total }}</span>
        </a>
        @foreach ($types as $case)
            @if ($counts[$case->value] > 0 || $type === $case->value)
                <a href="{{ route('wishlist.index', ['type' => $case->value]) }}"
                    class="{{ $type === $case->value ? 'is-active' : '' }}">
                    {{ $case->label() }} <span class="badge text-bg-light ms-1">{{ $counts[$case->value] }}</span>
                </a>
            @endif
        @endforeach
    </div>

    @if ($items->isEmpty())
        @include('partials.empty-state', [
            'icon' => 'bi-heart',
            'title' => $type ? 'Nothing saved in this category yet' : 'Your wishlist is empty',
            'text' => 'Tap the heart on any destination, hotel, event, adventure, darshan or pooja to keep it here.',
            'actionUrl' => route('destinations.index'),
            'actionLabel' => 'Start exploring',
        ])
    @else
        <div class="row g-4">
            @php
                $listingRoutes = [
                    'destination' => fn ($item) => route('hotels.index', ['destination' => $item->slug]),
                    'hotel' => fn ($item) => route('hotels.index', ['q' => $item->name]),
                    'event' => fn ($item) => route('events.index', ['q' => $item->name]),
                    'adventure' => fn ($item) => route('adventures.index', ['q' => $item->name]),
                    'darshan' => fn ($item) => route('darshan.index', ['q' => $item->name]),
                    'pooja' => fn ($item) => route('pooja.index', ['q' => $item->name]),
                    'restaurant' => fn ($item) => route('contact', ['subject' => 'Restaurant: ' . $item->name]),
                ];
            @endphp

            @foreach ($items as $entry)
                @php
                    $item = $entry->wishlistable;
                    $url = $item ? ($listingRoutes[$entry->type->value] ?? route('contact'))($item) : route('contact');
                @endphp

                <div class="col-md-6 col-xl-4">
                    <article class="ts-card h-100">
                        <div class="ts-card-media">
                            <img src="{{ $entry->image_url }}" alt="{{ $entry->label }}" loading="lazy"
                                 onerror="this.onerror=null;this.src='{{ asset('images/' . $entry->type->value . '-placeholder.svg') }}';">
                            <span class="ts-card-media-tag">{{ $entry->type->label() }}</span>

                            <button type="button" class="ts-wish is-active" data-ts-wish
                                data-type="{{ $entry->type->value }}" data-id="{{ $entry->wishlistable_id }}"
                                aria-label="Remove from wishlist" aria-pressed="true">
                                <i class="bi bi-heart-fill"></i>
                            </button>
                        </div>

                        <div class="ts-card-body">
                            <h3 class="ts-card-title">{{ $entry->label }}</h3>
                            <p class="ts-card-text">
                                @if ($item)
                                    {{ \Illuminate\Support\Str::limit((string) ($item->short_description ?? $item->description ?? ''), 110) }}
                                @else
                                    This listing is no longer available.
                                @endif
                            </p>

                            <div class="ts-card-foot">
                                <span class="ts-card-meta">Saved {{ $entry->created_at->diffForHumans() }}</span>
                                <div class="ts-card-actions">
                                    <a class="btn btn-sm btn-soft" href="{{ $url }}">View</a>
                                    <form method="post" action="{{ route('wishlist.destroy', $entry) }}"
                                        onsubmit="return confirm('Remove this item from your wishlist?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-secondary" type="submit">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>

        @if ($items->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $items->links() }}
            </div>
        @endif
    @endif
@endsection
