@extends('layouts.admin')

@section('title', 'Events')
@section('heading', 'Events')

@section('content')
    <div class="ts-toolbar">
        <form class="d-flex flex-wrap align-items-center gap-2 flex-grow-1" method="get" action="{{ route('admin.events.index') }}">
            <input class="form-control form-control-sm" style="max-width: 200px;" type="search" name="q"
                value="{{ request('q') }}" placeholder="Search name, venue or organiser…">
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

        <p class="ts-result-count mb-0">{{ $events->total() }} events</p>

        <a href="{{ route('admin.events.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> New event
        </a>
    </div>

    <div class="ts-table-card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Destination</th>
                        <th>Date</th>
                        <th>Capacity</th>
                        <th class="text-end">Price</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $event->image_url }}" alt="" class="rounded" style="width: 46px; height: 34px; object-fit: cover;">
                                    <div>
                                        <strong class="text-dark">{{ $event->name }}</strong>
                                        <div class="small text-muted">{{ $event->venue }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $event->destination?->name ?? '—' }}</td>
                            <td>{{ $event->event_date?->format('d M Y') }}</td>
                            <td>{{ number_format($event->capacity) }}</td>
                            <td class="text-end">{{ \App\Support\Money::decimal($event->effective_price) }}</td>
                            <td>
                                <span class="ts-badge ts-badge-{{ $event->is_active ? 'confirmed' : 'cancelled' }}">
                                    {{ $event->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @if ($event->is_featured)
                                    <span class="ts-badge ts-badge-paid">Featured</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.events.edit', $event) }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.events.destroy', $event) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this event?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No events yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $events->links() }}</div>
@endsection
