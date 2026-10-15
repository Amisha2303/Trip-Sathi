@extends('layouts.admin')

@section('title', $pooja->exists ? 'Edit pooja' : 'New pooja')
@section('heading', $pooja->exists ? 'Edit pooja' : 'New pooja')

@php($includesText = old('includes', is_array($pooja->includes ?? null) ? implode("\n", $pooja->includes) : ($pooja->includes ?? '')))

@section('content')
    <form method="POST"
        action="{{ $pooja->exists ? route('admin.pooja.update', $pooja) : route('admin.pooja.store') }}"
        enctype="multipart/form-data">
        @csrf
        @if ($pooja->exists) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="ts-card mb-4">
                    <div class="ts-card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label fw-semibold">Pooja name <span class="text-danger">*</span></label>
                                <input id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $pooja->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="destination_id" class="form-label fw-semibold">Destination <span class="text-danger">*</span></label>
                                <select id="destination_id" name="destination_id" class="form-select @error('destination_id') is-invalid @enderror" required>
                                    <option value="">Choose…</option>
                                    @foreach ($destinations as $destination)
                                        <option value="{{ $destination->id }}" @selected((int) old('destination_id', $pooja->destination_id) === $destination->id)>{{ $destination->name }}</option>
                                    @endforeach
                                </select>
                                @error('destination_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="temple_name" class="form-label fw-semibold">Temple name <span class="text-danger">*</span></label>
                                <input id="temple_name" name="temple_name" class="form-control @error('temple_name') is-invalid @enderror"
                                    value="{{ old('temple_name', $pooja->temple_name) }}" required>
                                @error('temple_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3">
                                <label for="deity" class="form-label fw-semibold">Deity</label>
                                <input id="deity" name="deity" class="form-control" value="{{ old('deity', $pooja->deity) }}">
                            </div>

                            <div class="col-md-3">
                                <label for="category_id" class="form-label fw-semibold">Category</label>
                                <select id="category_id" name="category_id" class="form-select">
                                    <option value="">— None —</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected((int) old('category_id', $pooja->category_id) === $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="short_description" class="form-label fw-semibold">Short description</label>
                                <input id="short_description" name="short_description" maxlength="500"
                                    class="form-control" value="{{ old('short_description', $pooja->short_description) }}">
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea id="description" name="description" rows="4"
                                    class="form-control">{{ old('description', $pooja->description) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label for="benefits" class="form-label fw-semibold">Benefits</label>
                                <textarea id="benefits" name="benefits" rows="2"
                                    class="form-control">{{ old('benefits', $pooja->benefits) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label for="process" class="form-label fw-semibold">Process</label>
                                <textarea id="process" name="process" rows="3"
                                    class="form-control">{{ old('process', $pooja->process) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="includes" class="form-label fw-semibold">Includes</label>
                                <textarea id="includes" name="includes" rows="3" placeholder="One item per line"
                                    class="form-control">{{ $includesText }}</textarea>
                                <div class="form-text">One item per line (e.g. Samagri kit).</div>
                            </div>

                            <div class="col-md-6">
                                <label for="requirements" class="form-label fw-semibold">Requirements</label>
                                <textarea id="requirements" name="requirements" rows="3"
                                    class="form-control">{{ old('requirements', $pooja->requirements) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label for="dress_code" class="form-label fw-semibold">Dress code</label>
                                <input id="dress_code" name="dress_code" class="form-control" value="{{ old('dress_code', $pooja->dress_code) }}">
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
                                    value="{{ old('price', $pooja->price) }}" required>
                                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="discount_percent" class="form-label fw-semibold">Discount %</label>
                                <input id="discount_percent" name="discount_percent" type="number" step="0.01" min="0" max="100"
                                    class="form-control" value="{{ old('discount_percent', $pooja->discount_percent ?? 0) }}">
                            </div>

                            <div class="col-md-4">
                                <label for="duration_minutes" class="form-label fw-semibold">Duration (minutes)</label>
                                <input id="duration_minutes" name="duration_minutes" type="number" min="5"
                                    class="form-control" value="{{ old('duration_minutes', $pooja->duration_minutes ?? 45) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="min_participants" class="form-label fw-semibold">Min participants</label>
                                <input id="min_participants" name="min_participants" type="number" min="1"
                                    class="form-control" value="{{ old('min_participants', $pooja->min_participants ?? 1) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="max_participants" class="form-label fw-semibold">Max participants</label>
                                <input id="max_participants" name="max_participants" type="number" min="1"
                                    class="form-control" value="{{ old('max_participants', $pooja->max_participants ?? 50) }}">
                            </div>

                            <div class="col-12">
                                <label for="cancellation_policy" class="form-label fw-semibold">Cancellation policy</label>
                                <textarea id="cancellation_policy" name="cancellation_policy" rows="2"
                                    class="form-control">{{ old('cancellation_policy', $pooja->cancellation_policy) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                @include('admin.partials.form-image', ['record' => $pooja, 'label' => 'Cover image'])
                <div class="mt-3">@include('admin.partials.form-status', ['record' => $pooja, 'featured' => true])</div>

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-check-lg"></i> {{ $pooja->exists ? 'Save changes' : 'Create pooja' }}
                    </button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.pooja.index') }}">Cancel</a>
                </div>
            </div>
        </div>
    </form>
@endsection
