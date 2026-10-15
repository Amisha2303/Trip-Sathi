@extends('layouts.admin')

@section('title', 'Adventures')
@section('heading', 'Adventures')

@section('content')
    <div class="ts-toolbar">
        <form class="d-flex flex-wrap align-items-center gap-2 flex-grow-1" method="get" action="{{ route('admin.adventures.index') }}">
            <input class="form-control form-control-sm" style="max-width: 200px;" type="search" name="q"
                value="{{ request('q') }}" placeholder="Search name or activity type…">
            <select class="form-select form-select-sm" style="max-width: 180px;" name="destination">
                <option value="">All destinations</option>
                @foreach ($destinations as $destination)
                    <option value="{{ $destination->id }}" @selected((int) request('destination') === $destination->id)>{{ $destination->name }}</option>
                @endforeach
            </select>
            <select class="form-select form-select-sm" style="max-width: 140px;" name="status">
                <option value="">Any status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
            <button class="btn btn-sm btn-outline-secondary" type="submit">Filter</button>
        </form>

        <p class="ts-result-count mb-0">{{ $adventures->total() }} adventures</p>

        <a href="{{ route('admin.adventures.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> New adventure
        </a>
    </div>

    <div class="ts-table-card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Adventure</th>
                        <th>Destination</th>
                        <th>Difficulty</th>
                        <th class="text-end">Price</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($adventures as $adventure)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $adventure->image_url }}" alt="" class="rounded" style="width: 46px; height: 34px; object-fit: cover;">
                                    <div>
                                        <strong class="text-dark">{{ $adventure->name }}</strong>
                                        <div class="small text-muted">{{ $adventure->activity_type ?: '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $adventure->destination?->name ?? '—' }}</td>
                            <td><span class="ts-badge ts-badge-plain ts-badge-draft">{{ $adventure->difficulty_label }}</span></td>
                            <td class="text-end">{{ \App\Support\Money::decimal($adventure->effective_price) }}</td>
                            <td>
                                <span class="ts-badge ts-badge-{{ $adventure->is_active ? 'confirmed' : 'cancelled' }}">
                                    {{ $adventure->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @if ($adventure->is_featured)
                                    <span class="ts-badge ts-badge-paid">Featured</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.adventures.edit', $adventure) }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.adventures.destroy', $adventure) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this adventure?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No adventures yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $adventures->links() }}</div>
@endsection
