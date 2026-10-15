@extends('layouts.admin')

@section('title', 'Destinations')
@section('heading', 'Destinations')

@section('content')
    <div class="ts-toolbar">
        <form class="d-flex flex-wrap align-items-center gap-2 flex-grow-1" method="get" action="{{ route('admin.destinations.index') }}">
            <input class="form-control form-control-sm" style="max-width: 220px;" type="search" name="q"
                value="{{ request('q') }}" placeholder="Search name, city or state…">
            <select class="form-select form-select-sm" style="max-width: 140px;" name="status">
                <option value="">Any status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
            <div class="form-check m-0">
                <input class="form-check-input" type="checkbox" name="featured" value="1" id="feat" @checked(request('featured'))>
                <label class="form-check-label small" for="feat">Featured</label>
            </div>
            <button class="btn btn-sm btn-outline-secondary" type="submit">Filter</button>
        </form>

        <p class="ts-result-count mb-0">{{ $destinations->total() }} destinations</p>

        <a href="{{ route('admin.destinations.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> New destination
        </a>
    </div>

    <div class="ts-table-card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Destination</th>
                        <th>Location</th>
                        <th class="text-end">Popularity</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($destinations as $destination)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $destination->image_url }}" alt="" class="rounded" style="width: 46px; height: 34px; object-fit: cover;">
                                    <div>
                                        <strong class="text-dark">{{ $destination->name }}</strong>
                                        @if ($destination->category)
                                            <div class="small text-muted">{{ $destination->category->name }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $destination->city ?: '—' }}
                                @if ($destination->state)<div class="small text-muted">{{ $destination->state }}, {{ $destination->country }}</div>@endif
                            </td>
                            <td class="text-end">{{ $destination->popularity }} pts</td>
                            <td>
                                <span class="ts-badge ts-badge-{{ $destination->is_active ? 'confirmed' : 'cancelled' }}">
                                    {{ $destination->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @if ($destination->is_featured)
                                    <span class="ts-badge ts-badge-paid">Featured</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.destinations.edit', $destination) }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.destinations.destroy', $destination) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this destination? Hotels, events and bookings linked to it stay but lose their parent.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No destinations yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $destinations->links() }}</div>
@endsection
