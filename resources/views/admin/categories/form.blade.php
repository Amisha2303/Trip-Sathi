@extends('layouts.admin')

@section('title', $category->exists ? 'Edit category' : 'New category')
@section('heading', $category->exists ? 'Edit category' : 'New category')

@section('content')
    <form method="POST"
        action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
        @csrf
        @if ($category->exists) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="ts-card mb-4">
                    <div class="ts-card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                                <input id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $category->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="type" class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                                <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    @foreach ($types as $key => $label)
                                        <option value="{{ $key }}" @selected(old('type', $category->type) === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="icon" class="form-label fw-semibold">Icon class</label>
                                <input id="icon" name="icon" class="form-control @error('icon') is-invalid @enderror"
                                    value="{{ old('icon', $category->icon) }}" placeholder="e.g. bi-star">
                                <div class="form-text">A Bootstrap Icons class name.</div>
                                @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="sort_order" class="form-label fw-semibold">Sort order</label>
                                <input id="sort_order" name="sort_order" type="number" min="0" class="form-control @error('sort_order') is-invalid @enderror"
                                    value="{{ old('sort_order', $category->sort_order ?? 0) }}">
                                @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea id="description" name="description" rows="3"
                                    class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                @include('admin.partials.form-status', ['record' => $category, 'featured' => true])

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-check-lg"></i> {{ $category->exists ? 'Save changes' : 'Create category' }}
                    </button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.categories.index') }}">Cancel</a>
                </div>
            </div>
        </div>
    </form>
@endsection
