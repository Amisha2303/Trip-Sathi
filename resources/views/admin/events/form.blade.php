@extends('layouts.admin')

@section('title', $event->exists ? 'Edit event' : 'New event')
@section('heading', $event->exists ? 'Edit event' : 'New event')

@php
    $startsAt = $event->exists && $event->starts_at ? $event->starts_at->format('Y-m-d\TH:i') : '';
    $endsAt = $event->exists && $event->ends_at ? $event->ends_at->format('Y-m-d\TH:i') : '';
    $eventDate = $event->exists && $event->event_date ? $event->event_date->format('Y-m-d') : '';
@endphp

@section('content')
    <form method="POST"
        action="{{ $event->exists ? route('admin.events.update', $event) : route('admin.events.store') }}"
        enctype="multipart/form-data">
        @csrf
        @if ($event->exists) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="ts-card mb-4">
                    <div class="ts-card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label fw-semibold">Event name <span class="text-danger">*</span></label>
                                <input id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $event->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="destination_id" class="form-label fw-semibold">Destination <span class="text-danger">*</span></label>
                                <select id="destination_id" name="destination_id" class="form-select @error('destination_id') is-invalid @enderror" required>
                                    <option value="">Choose…</option>
                                    @foreach ($destinations as $destination)
                                        <option value="{{ $destination->id }}" @selected((int) old('destination_id', $event->destination_id) === $destination->id)>{{ $destination->name }}</option>
                                    @endforeach
                                </select>
                                @error('destination_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="venue" class="form-label fw-semibold">Venue <span class="text-danger">*</span></label>
                                <input id="venue" name="venue" class="form-control @error('venue') is-invalid @enderror"
                                    value="{{ old('venue', $event->venue) }}" required>
                                @error('venue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="organiser" class="form-label fw-semibold">Organiser</label>
                                <input id="organiser" name="organiser" class="form-control" value="{{ old('organiser', $event->organiser) }}">
                            </div>

                            <div class="col-md-4">
                                <label for="event_date" class="form-label fw-semibold">Event date <span class="text-danger">*</span></label>
                                <input id="event_date" name="event_date" type="date"
                                    class="form-control @error('event_date') is-invalid @enderror"
                                    value="{{ old('event_date', $eventDate) }}" required>
                                @error('event_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="starts_at" class="form-label fw-semibold">Starts at <span class="text-danger">*</span></label>
                                <input id="starts_at" name="starts_at" type="datetime-local"
                                    class="form-control @error('starts_at') is-invalid @enderror"
                                    value="{{ old('starts_at', $startsAt) }}" required>
                                @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="ends_at" class="form-label fw-semibold">Ends at <span class="text-danger">*</span></label>
                                <input id="ends_at" name="ends_at" type="datetime-local"
                                    class="form-control @error('ends_at') is-invalid @enderror"
                                    value="{{ old('ends_at', $endsAt) }}" required>
                                @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">Address</label>
                                <input id="address" name="address" class="form-control" value="{{ old('address', $event->address) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="city" class="form-label fw-semibold">City</label>
                                <input id="city" name="city" class="form-control" value="{{ old('city', $event->city) }}">
                            </div>

                            <div class="col-md-3">
                                <label for="capacity" class="form-label fw-semibold">Capacity</label>
                                <input id="capacity" name="capacity" type="number" min="0" class="form-control"
                                    value="{{ old('capacity', $event->capacity ?? 100) }}">
                            </div>

                            <div class="col-md-3">
                                <label for="category_id" class="form-label fw-semibold">Category</label>
                                <select id="category_id" name="category_id" class="form-select">
                                    <option value="">— None —</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected((int) old('category_id', $event->category_id) === $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="short_description" class="form-label fw-semibold">Short description</label>
                                <input id="short_description" name="short_description" maxlength="500"
                                    class="form-control" value="{{ old('short_description', $event->short_description) }}">
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea id="description" name="description" rows="4"
                                    class="form-control">{{ old('description', $event->description) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label for="terms" class="form-label fw-semibold">Terms</label>
                                <textarea id="terms" name="terms" rows="2"
                                    class="form-control">{{ old('terms', $event->terms) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ts-card mb-4">
                    <div class="ts-card-body">
                        <div class="ts-card-title mb-3">Pricing</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="base_price" class="form-label fw-semibold">Base price (₹) <span class="text-danger">*</span></label>
                                <input id="base_price" name="base_price" type="number" step="0.01" min="0"
                                    class="form-control @error('base_price') is-invalid @enderror"
                                    value="{{ old('base_price', $event->base_price) }}" required>
                                @error('base_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="discount_percent" class="form-label fw-semibold">Discount %</label>
                                <input id="discount_percent" name="discount_percent" type="number" step="0.01" min="0" max="100"
                                    class="form-control" value="{{ old('discount_percent', $event->discount_percent ?? 0) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                @include('admin.partials.form-image', ['record' => $event, 'label' => 'Cover image'])
                <div class="mt-3">@include('admin.partials.form-status', ['record' => $event, 'featured' => true])</div>

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-check-lg"></i> {{ $event->exists ? 'Save changes' : 'Create event' }}
                    </button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.events.index') }}">Cancel</a>
                </div>
            </div>
        </div>
    </form>
@endsection
