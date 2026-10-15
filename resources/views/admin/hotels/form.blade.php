@extends('layouts.admin')

@section('title', $hotel->exists ? 'Edit hotel' : 'New hotel')
@section('heading', $hotel->exists ? 'Edit hotel' : 'New hotel')

@php
    $inTime = $hotel->check_in_time ? substr((string) $hotel->check_in_time, 0, 5) : '14:00';
    $outTime = $hotel->check_out_time ? substr((string) $hotel->check_out_time, 0, 5) : '11:00';
@endphp

@section('content')
    <form method="POST"
        action="{{ $hotel->exists ? route('admin.hotels.update', $hotel) : route('admin.hotels.store') }}"
        enctype="multipart/form-data">
        @csrf
        @if ($hotel->exists) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="ts-card mb-4">
                    <div class="ts-card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label fw-semibold">Hotel name <span class="text-danger">*</span></label>
                                <input id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $hotel->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="destination_id" class="form-label fw-semibold">Destination <span class="text-danger">*</span></label>
                                <select id="destination_id" name="destination_id" class="form-select @error('destination_id') is-invalid @enderror" required>
                                    <option value="">Choose…</option>
                                    @foreach ($destinations as $destination)
                                        <option value="{{ $destination->id }}" @selected((int) old('destination_id', $hotel->destination_id) === $destination->id)>{{ $destination->name }}</option>
                                    @endforeach
                                </select>
                                @error('destination_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-8">
                                <label for="address" class="form-label fw-semibold">Address <span class="text-danger">*</span></label>
                                <input id="address" name="address" class="form-control @error('address') is-invalid @enderror"
                                    value="{{ old('address', $hotel->address) }}" required>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="category_id" class="form-label fw-semibold">Category</label>
                                <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                    <option value="">— None —</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected((int) old('category_id', $hotel->category_id) === $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="city" class="form-label fw-semibold">City</label>
                                <input id="city" name="city" class="form-control" value="{{ old('city', $hotel->city) }}">
                            </div>

                            <div class="col-md-4">
                                <label for="state" class="form-label fw-semibold">State</label>
                                <input id="state" name="state" class="form-control" value="{{ old('state', $hotel->state) }}">
                            </div>

                            <div class="col-md-4">
                                <label for="pincode" class="form-label fw-semibold">Pincode</label>
                                <input id="pincode" name="pincode" class="form-control" value="{{ old('pincode', $hotel->pincode) }}">
                            </div>

                            <div class="col-12">
                                <label for="short_description" class="form-label fw-semibold">Short description</label>
                                <input id="short_description" name="short_description" maxlength="500"
                                    class="form-control" value="{{ old('short_description', $hotel->short_description) }}">
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea id="description" name="description" rows="4"
                                    class="form-control">{{ old('description', $hotel->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ts-card mb-4">
                    <div class="ts-card-body">
                        <div class="ts-card-title mb-3">Pricing &amp; stay</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="base_price" class="form-label fw-semibold">Base price / night (₹) <span class="text-danger">*</span></label>
                                <input id="base_price" name="base_price" type="number" step="0.01" min="0"
                                    class="form-control @error('base_price') is-invalid @enderror"
                                    value="{{ old('base_price', $hotel->base_price) }}" required>
                                @error('base_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="discount_percent" class="form-label fw-semibold">Discount %</label>
                                <input id="discount_percent" name="discount_percent" type="number" step="0.01" min="0" max="100"
                                    class="form-control" value="{{ old('discount_percent', $hotel->discount_percent ?? 0) }}">
                            </div>

                            <div class="col-md-4">
                                <label for="star_rating" class="form-label fw-semibold">Star rating</label>
                                <select id="star_rating" name="star_rating" class="form-select">
                                    @for ($i = 0; $i <= 5; $i++)
                                        <option value="{{ $i }}" @selected((int) old('star_rating', $hotel->star_rating ?? 3) === $i)>{{ $i }} star</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="check_in_time" class="form-label fw-semibold">Check-in</label>
                                <input id="check_in_time" name="check_in_time" type="time"
                                    class="form-control @error('check_in_time') is-invalid @enderror"
                                    value="{{ old('check_in_time', $inTime) }}">
                                @error('check_in_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="check_out_time" class="form-label fw-semibold">Check-out</label>
                                <input id="check_out_time" name="check_out_time" type="time"
                                    class="form-control @error('check_out_time') is-invalid @enderror"
                                    value="{{ old('check_out_time', $outTime) }}">
                                @error('check_out_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="contact_phone" class="form-label fw-semibold">Contact phone</label>
                                <input id="contact_phone" name="contact_phone" class="form-control" value="{{ old('contact_phone', $hotel->contact_phone) }}">
                            </div>

                            <div class="col-md-4">
                                <label for="contact_email" class="form-label fw-semibold">Contact email</label>
                                <input id="contact_email" name="contact_email" type="email" class="form-control" value="{{ old('contact_email', $hotel->contact_email) }}">
                            </div>

                            <div class="col-12">
                                <label for="cancellation_policy" class="form-label fw-semibold">Cancellation policy</label>
                                <textarea id="cancellation_policy" name="cancellation_policy" rows="2"
                                    class="form-control">{{ old('cancellation_policy', $hotel->cancellation_policy) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                @include('admin.partials.form-image', ['record' => $hotel, 'label' => 'Cover image'])
                <div class="mt-3">@include('admin.partials.form-status', ['record' => $hotel, 'featured' => true])</div>

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-check-lg"></i> {{ $hotel->exists ? 'Save changes' : 'Create hotel' }}
                    </button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.hotels.index') }}">Cancel</a>
                </div>
            </div>
        </div>
    </form>
@endsection
