@extends('layouts.admin')

@section('title', 'Users')
@section('heading', 'Users')

@section('content')
    <div class="ts-toolbar">
        <form class="d-flex flex-wrap align-items-center gap-2 flex-grow-1" method="get" action="{{ route('admin.users.index') }}">
            <input class="form-control form-control-sm" style="max-width: 220px;" type="search" name="q"
                value="{{ request('q') }}" placeholder="Search name, email or phone…">
            <select class="form-select form-select-sm" style="max-width: 140px;" name="role">
                <option value="">All roles</option>
                <option value="admin" @selected(request('role') === 'admin')>Admins</option>
                <option value="traveller" @selected(request('role') === 'traveller')>Travellers</option>
            </select>
            <select class="form-select form-select-sm" style="max-width: 140px;" name="status">
                <option value="">Any status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
            <button class="btn btn-sm btn-outline-secondary" type="submit">Filter</button>
        </form>

        <p class="ts-result-count mb-0">{{ $users->total() }} users</p>
    </div>

    <div class="ts-table-card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Phone</th>
                        <th class="text-end">Bookings</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="ts-list-thumb d-inline-flex align-items-center justify-content-center fw-bold text-primary">
                                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <strong class="text-dark">{{ $user->name }}</strong>
                                        <div class="small text-muted">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->phone ?: '—' }}</td>
                            <td class="text-end">{{ $user->bookings_count }}</td>
                            <td>
                                <span class="ts-badge ts-badge-{{ $user->is_admin ? 'confirmed' : 'draft' }}">
                                    {{ $user->is_admin ? 'Admin' : 'Traveller' }}
                                </span>
                            </td>
                            <td>
                                <span class="ts-badge ts-badge-{{ $user->is_active ? 'confirmed' : 'cancelled' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.users.show', $user) }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this account?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>
@endsection
