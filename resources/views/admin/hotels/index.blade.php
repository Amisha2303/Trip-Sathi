@extends('layouts.admin')

@section('title', 'Hotels')
@section('heading', 'Hotels')

@section('content')
    <div class="ts-toolbar">
        <form class="d-flex flex-wrap align-items-center gap-2 flex-grow-1" method="get" action="{{ route('admin.hotels.index') }}">
            <input class="form-control form-control-sm" style="max-width: 200px;" type="search" name="q"
                value="{{ request('q') }}" placeholder="Search name, address or city…">
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

        <p class="ts-result-count mb-0">{{ $hotels->total() }} hotels</p>

        <a href="{{ route('admin.hotels.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> New hotel
        </a>
    </div>

    <div class="ts-table-card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Hotel</th>
                        <th>Destination</th>
                        <th>Rating</th>
                        <th class="text-end">From</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hotels as $hotel)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $hotel->image_url }}" alt="" class="rounded" style="width: 46px; height: 34px; object-fit: cover;">
                                    <div>
                                        <strong class="text-dark">{{ $hotel->name }}</strong>
                                        <div class="small text-muted">{{ $hotel->city ?: $hotel->address }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $hotel->destination?->name ?? '—' }}</td>
                            <td>
                                @if ($hotel->star_rating)
                                    <span class="ts-badge ts-badge-plain ts-badge-paid">{{ $hotel->star_rating }}★</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">{{ \App\Support\Money::decimal($hotel->effective_price) }}</td>
                            <td>
                                <span class="ts-badge ts-badge-{{ $hotel->is_active ? 'confirmed' : 'cancelled' }}">
                                    {{ $hotel->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @if ($hotel->is_featured)
                                    <span class="ts-badge ts-badge-paid">Featured</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.hotels.edit', $hotel) }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.hotels.destroy', $hotel) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this hotel?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No hotels yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $hotels->links() }}</div>
@endsection
