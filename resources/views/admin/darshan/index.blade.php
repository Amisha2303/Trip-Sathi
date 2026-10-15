@extends('layouts.admin')

@section('title', 'VIP Darshan')
@section('heading', 'VIP Darshan')

@section('content')
    <div class="ts-toolbar">
        <form class="d-flex flex-wrap align-items-center gap-2 flex-grow-1" method="get" action="{{ route('admin.darshan.index') }}">
            <input class="form-control form-control-sm" style="max-width: 200px;" type="search" name="q"
                value="{{ request('q') }}" placeholder="Search name, temple or deity…">
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

        <p class="ts-result-count mb-0">{{ $darshans->total() }} listings</p>

        <a href="{{ route('admin.darshan.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> New darshan
        </a>
    </div>

    <div class="ts-table-card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Darshan</th>
                        <th>Temple</th>
                        <th>Destination</th>
                        <th>Type</th>
                        <th class="text-end">Price</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($darshans as $darshan)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $darshan->image_url }}" alt="" class="rounded" style="width: 46px; height: 34px; object-fit: cover;">
                                    <div>
                                        <strong class="text-dark">{{ $darshan->name }}</strong>
                                        <div class="small text-muted">{{ $darshan->deity ?: '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $darshan->temple_name }}</td>
                            <td>{{ $darshan->destination?->name ?? '—' }}</td>
                            <td><span class="ts-badge ts-badge-plain ts-badge-draft">{{ ucfirst($darshan->darshan_type) }}</span></td>
                            <td class="text-end">{{ \App\Support\Money::decimal($darshan->effective_price) }}</td>
                            <td>
                                <span class="ts-badge ts-badge-{{ $darshan->is_active ? 'confirmed' : 'cancelled' }}">
                                    {{ $darshan->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @if ($darshan->is_featured)
                                    <span class="ts-badge ts-badge-paid">Featured</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.darshan.edit', $darshan) }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.darshan.destroy', $darshan) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this darshan?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No darshan listings yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $darshans->links() }}</div>
@endsection
