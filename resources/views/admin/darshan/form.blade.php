@extends('layouts.admin')

@section('title', $darshan->exists ? 'Edit darshan' : 'New darshan')
@section('heading', $darshan->exists ? 'Edit darshan' : 'New darshan')

@php($documentsText = old('documents_required', is_array($darshan->documents_required ?? null) ? implode("\n", $darshan->documents_required) : ($darshan->documents_required ?? '')))

@section('content')
    <form method="POST"
        action="{{ $darshan->exists ? route('admin.darshan.update', $darshan) : route('admin.darshan.store') }}"
        enctype="multipart/form-data">
        @csrf
        @if ($darshan->exists) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="ts-card mb-4">
                    <div class="ts-card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label fw-semibold">Listing name <span class="text-danger">*</span></label>
                                <input id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $darshan->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="destination_id" class="form-label fw-semibold">Destination <span class="text-danger">*</span></label>
                                <select id="destination_id" name="destination_id" class="form-select @error('destination_id') is-invalid @enderror" required>
                                    <option value="">Choose…</option>
                                    @foreach ($destinations as $destination)
                                        <option value="{{ $destination->id }}" @selected((int) old('destination_id', $darshan->destination_id) === $destination->id)>{{ $destination->name }}</option>
                                    @endforeach
                                </select>
                                @error('destination_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="temple_name" class="form-label fw-semibold">Temple name <span class="text-danger">*</span></label>
                                <input id="temple_name" name="temple_name" class="form-control @error('temple_name') is-invalid @enderror"
                                    value="{{ old('temple_name', $darshan->temple_name) }}" required>
                                @error('temple_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3">
                                <label for="deity" class="form-label fw-semibold">Deity</label>
                                <input id="deity" name="deity" class="form-control" value="{{ old('deity', $darshan->deity) }}">
                            </div>

                            <div class="col-md-3">
                                <label for="darshan_type" class="form-label fw-semibold">Type</label>
                                <select id="darshan_type" name="darshan_type" class="form-select">
                                    @foreach ($types as $key => $label)
                                        <option value="{{ $key }}" @selected(old('darshan_type', $darshan->darshan_type ?? 'vip') === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="temple_address" class="form-label fw-semibold">Temple address</label>
                                <input id="temple_address" name="temple_address" class="form-control" value="{{ old('temple_address', $darshan->temple_address) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="category_id" class="form-label fw-semibold">Category</label>
                                <select id="category_id" name="category_id" class="form-select">
                                    <option value="">— None —</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected((int) old('category_id', $darshan->category_id) === $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="short_description" class="form-label fw-semibold">Short description</label>
                                <input id="short_description" name="short_description" maxlength="500"
                                    class="form-control" value="{{ old('short_description', $darshan->short_description) }}">
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea id="description" name="description" rows="4"
                                    class="form-control">{{ old('description', $darshan->description) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label for="instructions" class="form-label fw-semibold">Instructions</label>
                                <textarea id="instructions" name="instructions" rows="2"
                                    class="form-control">{{ old('instructions', $darshan->instructions) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="documents_required" class="form-label fw-semibold">Documents required</label>
                                <textarea id="documents_required" name="documents_required" rows="3" placeholder="One document per line"
                                    class="form-control">{{ $documentsText }}</textarea>
                                <div class="form-text">One document per line (e.g. Aadhaar card).</div>
                            </div>

                            <div class="col-md-6">
                                <label for="dress_code" class="form-label fw-semibold">Dress code</label>
                                <textarea id="dress_code" name="dress_code" rows="3"
                                    class="form-control">{{ old('dress_code', $darshan->dress_code) }}</textarea>
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
                                    value="{{ old('price', $darshan->price) }}" required>
                                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="discount_percent" class="form-label fw-semibold">Discount %</label>
                                <input id="discount_percent" name="discount_percent" type="number" step="0.01" min="0" max="100"
                                    class="form-control" value="{{ old('discount_percent', $darshan->discount_percent ?? 0) }}">
                            </div>

                            <div class="col-md-4">
                                <label for="duration_minutes" class="form-label fw-semibold">Duration (minutes)</label>
                                <input id="duration_minutes" name="duration_minutes" type="number" min="5"
                                    class="form-control" value="{{ old('duration_minutes', $darshan->duration_minutes ?? 45) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="max_visitors_per_booking" class="form-label fw-semibold">Max visitors / booking</label>
                                <input id="max_visitors_per_booking" name="max_visitors_per_booking" type="number" min="1"
                                    class="form-control" value="{{ old('max_visitors_per_booking', $darshan->max_visitors_per_booking ?? 10) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="max_visitors_per_day" class="form-label fw-semibold">Max visitors / day</label>
                                <input id="max_visitors_per_day" name="max_visitors_per_day" type="number" min="1"
                                    class="form-control" value="{{ old('max_visitors_per_day', $darshan->max_visitors_per_day) }}">
                            </div>

                            <div class="col-12">
                                <label for="cancellation_policy" class="form-label fw-semibold">Cancellation policy</label>
                                <textarea id="cancellation_policy" name="cancellation_policy" rows="2"
                                    class="form-control">{{ old('cancellation_policy', $darshan->cancellation_policy) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                @include('admin.partials.form-image', ['record' => $darshan, 'label' => 'Cover image'])
                <div class="mt-3">@include('admin.partials.form-status', ['record' => $darshan, 'featured' => true])</div>

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-check-lg"></i> {{ $darshan->exists ? 'Save changes' : 'Create darshan' }}
                    </button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.darshan.index') }}">Cancel</a>
                </div>
            </div>
        </div>
    </form>
@endsection
