@extends('layouts.admin')

@section('title', $destination->exists ? 'Edit destination' : 'New destination')
@section('heading', $destination->exists ? 'Edit destination' : 'New destination')

@section('content')
    <form method="POST"
        action="{{ $destination->exists ? route('admin.destinations.update', $destination) : route('admin.destinations.store') }}"
        enctype="multipart/form-data">
        @csrf
        @if ($destination->exists) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="ts-card mb-4">
                    <div class="ts-card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                                <input id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $destination->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="category_id" class="form-label fw-semibold">Category</label>
                                <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                    <option value="">— None —</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected((int) old('category_id', $destination->category_id) === $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="country" class="form-label fw-semibold">Country <span class="text-danger">*</span></label>
                                <input id="country" name="country" class="form-control @error('country') is-invalid @enderror"
                                    value="{{ old('country', $destination->country ?? 'India') }}" required>
                                @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="state" class="form-label fw-semibold">State</label>
                                <input id="state" name="state" class="form-control @error('state') is-invalid @enderror"
                                    value="{{ old('state', $destination->state) }}">
                                @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="city" class="form-label fw-semibold">City</label>
                                <input id="city" name="city" class="form-control @error('city') is-invalid @enderror"
                                    value="{{ old('city', $destination->city) }}">
                                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="short_description" class="form-label fw-semibold">Short description</label>
                                <input id="short_description" name="short_description" maxlength="500"
                                    class="form-control @error('short_description') is-invalid @enderror"
                                    value="{{ old('short_description', $destination->short_description) }}">
                                @error('short_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea id="description" name="description" rows="5"
                                    class="form-control @error('description') is-invalid @enderror">{{ old('description', $destination->description) }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="best_time_to_visit" class="form-label fw-semibold">Best time to visit</label>
                                <textarea id="best_time_to_visit" name="best_time_to_visit" rows="2"
                                    class="form-control @error('best_time_to_visit') is-invalid @enderror">{{ old('best_time_to_visit', $destination->best_time_to_visit) }}</textarea>
                                @error('best_time_to_visit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="latitude" class="form-label fw-semibold">Latitude</label>
                                <input id="latitude" name="latitude" step="any" class="form-control @error('latitude') is-invalid @enderror"
                                    value="{{ old('latitude', $destination->latitude) }}">
                                @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="longitude" class="form-label fw-semibold">Longitude</label>
                                <input id="longitude" name="longitude" step="any" class="form-control @error('longitude') is-invalid @enderror"
                                    value="{{ old('longitude', $destination->longitude) }}">
                                @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="language" class="form-label fw-semibold">Language</label>
                                <input id="language" name="language" class="form-control @error('language') is-invalid @enderror"
                                    value="{{ old('language', $destination->language) }}">
                                @error('language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                @include('admin.partials.form-image', ['record' => $destination, 'label' => 'Cover image'])
                <div class="mt-3">@include('admin.partials.form-status', ['record' => $destination, 'featured' => true])</div>

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-check-lg"></i> {{ $destination->exists ? 'Save changes' : 'Create destination' }}
                    </button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.destinations.index') }}">Cancel</a>
                </div>
            </div>
        </div>
    </form>
@endsection
