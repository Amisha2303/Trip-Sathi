@extends('layouts.admin')

@section('title', 'Categories')
@section('heading', 'Categories')

@section('content')
    <div class="ts-toolbar">
        <form class="d-flex flex-wrap align-items-center gap-2 flex-grow-1" method="get" action="{{ route('admin.categories.index') }}">
            <input class="form-control form-control-sm" style="max-width: 220px;" type="search" name="q"
                value="{{ request('q') }}" placeholder="Search name or type…">
            <select class="form-select form-select-sm" style="max-width: 170px;" name="type">
                <option value="">All types</option>
                @foreach ($types as $key => $label)
                    <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select class="form-select form-select-sm" style="max-width: 140px;" name="status">
                <option value="">Any status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
            <button class="btn btn-sm btn-outline-secondary" type="submit">Filter</button>
        </form>

        <p class="ts-result-count mb-0">{{ $categories->total() }} categories</p>

        <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> New category
        </a>
    </div>

    <div class="ts-table-card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Icon</th>
                        <th class="text-end">Sort</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>
                                <strong class="text-dark">{{ $category->name }}</strong>
                                @if ($category->description)
                                    <div class="small text-muted">{{ \Illuminate\Support\Str::limit($category->description, 60) }}</div>
                                @endif
                            </td>
                            <td><span class="ts-badge ts-badge-plain ts-badge-draft">{{ $types[$category->type] ?? $category->type }}</span></td>
                            <td>@if ($category->icon)<i class="bi {{ $category->icon }}"></i>@else<span class="text-muted">—</span>@endif</td>
                            <td class="text-end">{{ $category->sort_order }}</td>
                            <td>
                                <span class="ts-badge ts-badge-{{ $category->is_active ? 'confirmed' : 'cancelled' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @if ($category->is_featured)
                                    <span class="ts-badge ts-badge-paid">Featured</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.categories.edit', $category) }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this category?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No categories yet. Create the first one.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $categories->links() }}</div>
@endsection
