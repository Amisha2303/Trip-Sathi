@extends('layouts.app')

@section('title', 'Trip Planner — Trip Sathi')

@push('head')
    <style>
        .trip-cover {
            aspect-ratio: 16 / 10;
            object-fit: cover;
            width: 100%;
            display: block;
        }

        .trip-meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem 1.1rem;
            color: var(--bs-muted, #73838c);
            font-size: 0.85rem;
        }

        .trip-author {
            width: 1.9rem;
            height: 1.9rem;
            border-radius: 50%;
            background: var(--bs-primary, #0b6e75);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endpush

@section('content')
    @include('partials.page-hero', [
        'image' => asset('photos/banners/banner-trips.jpg'),
        'eyebrow' => 'Plan day by day',
        'title' => 'Trip Planner',
        'lede' => 'Sketch your itinerary day by day, keep bookings attached to the right date, and borrow ideas from trips other travellers have made public.',
        'crumbs' => [['label' => 'Home', 'url' => route('home')], ['label' => 'Trip Planner']],
        'stats' => [
            [$total . '+', 'Public itineraries'],
            ['Day-by-day', 'Planning'],
            ['Synced', 'With your bookings'],
        ],
    ])

    <section class="ts-section">
        <div class="container">
            <div class="ts-toolbar">
                <div class="ts-tabs mb-0">
                    <a href="{{ route('trips.index', ['scope' => 'community']) }}"
                        class="{{ $scope === 'community' ? 'is-active' : '' }}">
                        <i class="bi bi-people me-1"></i>Community trips
                    </a>
                    @auth
                        <a href="{{ route('trips.index', ['scope' => 'mine']) }}"
                            class="{{ $scope === 'mine' ? 'is-active' : '' }}">
                            <i class="bi bi-map me-1"></i>My trips
                        </a>
                    @endauth
                </div>

                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <form class="d-flex gap-2" method="get" action="{{ route('trips.index') }}">
                        <input type="hidden" name="scope" value="{{ $scope }}">
                        <input class="form-control" type="search" name="q" value="{{ $filters['q'] }}"
                            placeholder="Search trips…" aria-label="Search trips">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>

                    @auth
                        <a class="btn btn-accent" href="#new-trip" data-ts-open-trip-modal>
                            <i class="bi bi-plus-lg"></i> New trip
                        </a>
                    @else
                        <a class="btn btn-accent" href="{{ route('login') }}">
                            <i class="bi bi-plus-lg"></i> Sign in to plan
                        </a>
                    @endauth
                </div>
            </div>

            <div class="row g-4">
                @forelse ($trips as $trip)
                    @php
                        $initials = collect(explode(' ', trim((string) $trip->user?->name)))
                            ->filter()
                            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                            ->take(2)
                            ->implode('');
                    @endphp

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

                                @if ($trip->description)
                                    <p class="ts-card-text">{{ \Illuminate\Support\Str::limit($trip->description, 110) }}</p>
                                @endif

                                <div class="trip-meta-row mb-2">
                                    <span><i class="bi bi-person me-1"></i>{{ $trip->travelers }}
                                        traveller{{ $trip->travelers > 1 ? 's' : '' }}</span>
                                    @if ($trip->budget)
                                        <span><i class="bi bi-wallet2 me-1"></i>Budget
                                            {{ \App\Support\Money::decimal((float) $trip->budget) }}</span>
                                    @endif
                                    @if ($trip->is_public)
                                        <span><i class="bi bi-globe2 me-1"></i>Public</span>
                                    @endif
                                </div>

                                <div class="ts-card-foot">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="trip-author">{{ $initials ?: 'TS' }}</span>
                                        <span class="small text-muted">{{ $trip->user?->name ?? 'Trip Sathi' }}</span>
                                    </div>

                                    <button type="button" class="btn btn-sm btn-soft" data-bs-toggle="modal"
                                        data-bs-target="#tripModal"
                                        data-trip-name="{{ $trip->name }}"
                                        data-trip-destination="{{ $trip->destination?->name ?? 'Flexible route' }}"
                                        data-trip-dates="{{ $trip->date_range_label }}"
                                        data-trip-days="{{ $trip->duration_label }}"
                                        data-trip-travelers="{{ $trip->travelers }}"
                                        data-trip-budget="{{ $trip->budget ? \App\Support\Money::decimal((float) $trip->budget) : '—' }}"
                                        data-trip-author="{{ $trip->user?->name ?? 'Trip Sathi' }}"
                                        data-trip-description="{{ (string) $trip->description }}">
                                        View plan
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        @include('partials.empty-state', [
                            'icon' => 'bi-map',
                            'title' => $scope === 'mine' ? 'You have not planned a trip yet' : 'No public trips yet',
                            'text' => $scope === 'mine'
                                ? 'Start with your dates and travellers — you can shape the itinerary day by day.'
                                : 'Be the first to share an itinerary the community can borrow ideas from.',
                            'actionUrl' => '#new-trip',
                            'actionLabel' => auth()->check() ? 'Create a trip' : 'Sign in to plan',
                        ])
                    </div>
                @endforelse
            </div>

            @if ($trips->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $trips->links() }}
                </div>
            @endif
        </div>
    </section>

    {{-- Trip summary modal --}}
    <div class="modal fade" id="tripModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" data-trip-field="name">Trip</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted" data-trip-field="description"></p>
                    <ul class="ts-list">
                        <li>
                            <i class="bi bi-geo-alt text-primary"></i>
                            <div>
                                <p class="ts-list-title" data-trip-field="destination"></p>
                                <p class="ts-list-meta">Destination</p>
                            </div>
                        </li>
                        <li>
                            <i class="bi bi-calendar3 text-primary"></i>
                            <div>
                                <p class="ts-list-title" data-trip-field="dates"></p>
                                <p class="ts-list-meta">Travel dates</p>
                            </div>
                        </li>
                        <li>
                            <i class="bi bi-person text-primary"></i>
                            <div>
                                <p class="ts-list-title"><span data-trip-field="travelers"></span> travellers ·
                                    <span data-trip-field="days"></span></p>
                                <p class="ts-list-meta">Group & length</p>
                            </div>
                        </li>
                        <li>
                            <i class="bi bi-wallet2 text-primary"></i>
                            <div>
                                <p class="ts-list-title" data-trip-field="budget"></p>
                                <p class="ts-list-meta">Budget by <span data-trip-field="author"></span></p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    @auth
                        <a class="btn btn-accent" href="#new-trip" data-bs-dismiss="modal" data-ts-open-trip-modal>
                            Plan my own trip
                        </a>
                    @else
                        <a class="btn btn-accent" href="{{ route('login') }}">Sign in to plan yours</a>
                    @endauth
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @auth
        {{-- Create trip modal --}}
        <div class="modal fade" id="newTripModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form class="modal-content" method="post" action="{{ route('trips.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Start a new trip</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="trip-name">Trip name</label>
                            <input class="form-control @error('name') is-invalid @enderror" id="trip-name" name="name"
                                value="{{ old('name') }}" placeholder="Gujarat temple trail" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="trip-destination">Destination</label>
                            <select class="form-select" id="trip-destination" name="destination_id">
                                <option value="">Not decided yet</option>
                                @foreach ($destinations as $destination)
                                    <option value="{{ $destination->id }}"
                                        @selected((string) old('destination_id') === (string) $destination->id)>
                                        {{ $destination->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="trip-start">Start date</label>
                                <input class="form-control @error('start_date') is-invalid @enderror" type="date"
                                    id="trip-start" name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="trip-end">End date</label>
                                <input class="form-control @error('end_date') is-invalid @enderror" type="date"
                                    id="trip-end" name="end_date" value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="trip-travelers">Travellers</label>
                                <input class="form-control @error('travelers') is-invalid @enderror" type="number"
                                    id="trip-travelers" name="travelers" min="1" max="50"
                                    value="{{ old('travelers', 2) }}" required>
                                @error('travelers')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="trip-budget">Budget (₹, optional)</label>
                                <input class="form-control" type="number" id="trip-budget" name="budget" min="0"
                                    value="{{ old('budget') }}" placeholder="45000">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="trip-public" name="is_public"
                                        value="1" @checked(old('is_public'))>
                                    <label class="form-check-label" for="trip-public">Share this trip publicly</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label" for="trip-description">Notes</label>
                            <textarea class="form-control" id="trip-description" name="description" rows="3"
                                placeholder="What is this trip about?">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-accent">Create trip</button>
                    </div>
                </form>
            </div>
        </div>
    @endauth
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tripModalEl = document.getElementById('tripModal');
            if (tripModalEl) {
                tripModalEl.addEventListener('show.bs.modal', (event) => {
                    const trigger = event.relatedTarget;
                    if (!trigger) return;

                    tripModalEl.querySelectorAll('[data-trip-field]').forEach((node) => {
                        const value = trigger.dataset['trip' + node.dataset.tripField.charAt(0).toUpperCase() + node.dataset.tripField.slice(1)];
                        if (value === undefined) return;
                        node.textContent = value.trim() === '' ? '—' : value;
                    });
                });
            }

            const newTripModalEl = document.getElementById('newTripModal');
            const openers = document.querySelectorAll('[data-ts-open-trip-modal]');
            openers.forEach((opener) => {
                opener.addEventListener('click', (event) => {
                    if (!newTripModalEl) return;
                    event.preventDefault();
                    new bootstrap.Modal(newTripModalEl).show();
                });
            });

            if (newTripModalEl && window.location.hash === '#new-trip') {
                new bootstrap.Modal(newTripModalEl).show();
            }
        });
    </script>
@endpush
