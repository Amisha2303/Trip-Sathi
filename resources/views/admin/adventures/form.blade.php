@extends('layouts.admin')

@section('title', $adventure->exists ? 'Edit adventure' : 'New adventure')
@section('heading', $adventure->exists ? 'Edit adventure' : 'New adventure')

@section('content')
    <form method="POST"
        action="{{ $adventure->exists ? route('admin.adventures.update', $adventure) : route('admin.adventures.store') }}"
        enctype="multipart/form-data">
        @csrf
        @if ($adventure->exists) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="ts-card mb-4">
                    <div class="ts-card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label fw-semibold">Adventure name <span class="text-danger">*</span></label>
                                <input id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $adventure->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="destination_id" class="form-label fw-semibold">Destination <span class="text-danger">*</span></label>
                                <select id="destination_id" name="destination_id" class="form-select @error('destination_id') is-invalid @enderror" required>
                                    <option value="">Choose…</option>
                                    @foreach ($destinations as $destination)
                                        <option value="{{ $destination->id }}" @selected((int) old('destination_id', $adventure->destination_id) === $destination->id)>{{ $destination->name }}</option>
                                    @endforeach
                                </select>
                                @error('destination_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="activity_type" class="form-label fw-semibold">Activity type</label>
                                <input id="activity_type" name="activity_type" class="form-control" list="activity-types"
                                    value="{{ old('activity_type', $adventure->activity_type) }}" placeholder="e.g. Trekking">
                                <datalist id="activity-types">
                                    @foreach (\App\Models\Adventure::activityTypes() as $type)
                                        <option value="{{ $type }}">
                                    @endforeach
                                </datalist>
                            </div>

                            <div class="col-md-3">
                                <label for="difficulty" class="form-label fw-semibold">Difficulty</label>
                                <select id="difficulty" name="difficulty" class="form-select">
                                    @foreach ($difficulties as $key => $label)
                                        <option value="{{ $key }}" @selected(old('difficulty', $adventure->difficulty?->value ?? 'moderate') === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="category_id" class="form-label fw-semibold">Category</label>
                                <select id="category_id" name="category_id" class="form-select">
                                    <option value="">— None —</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected((int) old('category_id', $adventure->category_id) === $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="short_description" class="form-label fw-semibold">Short description</label>
                                <input id="short_description" name="short_description" maxlength="500"
                                    class="form-control" value="{{ old('short_description', $adventure->short_description) }}">
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea id="description" name="description" rows="4"
                                    class="form-control">{{ old('description', $adventure->description) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label for="meeting_point" class="form-label fw-semibold">Meeting point</label>
                                <input id="meeting_point" name="meeting_point" class="form-control"
                                    value="{{ old('meeting_point', $adventure->meeting_point) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="inclusions" class="form-label fw-semibold">Inclusions</label>
                                <textarea id="inclusions" name="inclusions" rows="2"
                                    class="form-control">{{ old('inclusions', $adventure->inclusions) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="safety_instructions" class="form-label fw-semibold">Safety instructions</label>
                                <textarea id="safety_instructions" name="safety_instructions" rows="2"
                                    class="form-control">{{ old('safety_instructions', $adventure->safety_instructions) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ts-card mb-4">
                    <div class="ts-card-body">
                        <div class="ts-card-title mb-3">Pricing &amp; limits</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="price" class="form-label fw-semibold">Price (₹) <span class="text-danger">*</span></label>
                                <input id="price" name="price" type="number" step="0.01" min="0"
                                    class="form-control @error('price') is-invalid @enderror"
                                    value="{{ old('price', $adventure->price) }}" required>
                                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="discount_percent" class="form-label fw-semibold">Discount %</label>
                                <input id="discount_percent" name="discount_percent" type="number" step="0.01" min="0" max="100"
                                    class="form-control" value="{{ old('discount_percent', $adventure->discount_percent ?? 0) }}">
                            </div>

                            <div class="col-md-4">
                                <label for="duration_minutes" class="form-label fw-semibold">Duration (minutes)</label>
                                <input id="duration_minutes" name="duration_minutes" type="number" min="5"
                                    class="form-control" value="{{ old('duration_minutes', $adventure->duration_minutes ?? 180) }}">
                            </div>

                            <div class="col-md-3">
                                <label for="min_age" class="form-label fw-semibold">Min age</label>
                                <input id="min_age" name="min_age" type="number" min="0" class="form-control" value="{{ old('min_age', $adventure->min_age ?? 8) }}">
                            </div>

                            <div class="col-md-3">
                                <label for="max_age" class="form-label fw-semibold">Max age</label>
                                <input id="max_age" name="max_age" type="number" min="0" class="form-control" value="{{ old('max_age', $adventure->max_age) }}">
                            </div>

                            <div class="col-md-3">
                                <label for="min_participants" class="form-label fw-semibold">Min participants</label>
                                <input id="min_participants" name="min_participants" type="number" min="1" class="form-control" value="{{ old('min_participants', $adventure->min_participants ?? 1) }}">
                            </div>

                            <div class="col-md-3">
                                <label for="max_participants" class="form-label fw-semibold">Max participants</label>
                                <input id="max_participants" name="max_participants" type="number" min="1" class="form-control" value="{{ old('max_participants', $adventure->max_participants ?? 20) }}">
                            </div>

                            <div class="col-12">
                                <label for="cancellation_policy" class="form-label fw-semibold">Cancellation policy</label>
                                <textarea id="cancellation_policy" name="cancellation_policy" rows="2"
                                    class="form-control">{{ old('cancellation_policy', $adventure->cancellation_policy) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                @include('admin.partials.form-image', ['record' => $adventure, 'label' => 'Cover image'])
                <div class="mt-3">@include('admin.partials.form-status', ['record' => $adventure, 'featured' => true])</div>

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-check-lg"></i> {{ $adventure->exists ? 'Save changes' : 'Create adventure' }}
                    </button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.adventures.index') }}">Cancel</a>
                </div>
            </div>
        </div>
    </form>
@endsection
