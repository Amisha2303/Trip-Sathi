@extends('layouts.admin')

@section('title', 'Contact inbox')
@section('heading', 'Contact inbox')

@section('content')
    <div class="ts-toolbar">
        <form class="d-flex flex-wrap align-items-center gap-2 flex-grow-1" method="get" action="{{ route('admin.contact-messages.index') }}">
            <input class="form-control form-control-sm" style="max-width: 220px;" type="search" name="q"
                value="{{ request('q') }}" placeholder="Search name, email or message…">
            <select class="form-select form-select-sm" style="max-width: 150px;" name="status">
                <option value="">Any status</option>
                @foreach ($statuses as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="form-check m-0">
                <input class="form-check-input" type="checkbox" name="unread" value="1" id="unread" @checked(request('unread'))>
                <label class="form-check-label small" for="unread">Unread only</label>
            </div>
            <button class="btn btn-sm btn-outline-secondary" type="submit">Filter</button>
        </form>

        <p class="ts-result-count mb-0">{{ $messages->total() }} messages</p>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-3">
        <span class="ts-badge ts-badge-pending">{{ $counts['unread'] }} unread</span>
        <span class="ts-badge ts-badge-plain ts-badge-draft">{{ $counts['new'] }} new</span>
        <span class="ts-badge ts-badge-plain ts-badge-confirmed">{{ $counts['replied'] }} replied</span>
        <span class="ts-badge ts-badge-plain ts-badge-cancelled">{{ $counts['closed'] }} closed</span>
    </div>

    <div class="ts-table-card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Received</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($messages as $message)
                        <tr class="{{ $message->is_read ? '' : 'table-light' }}">
                            <td>
                                <strong class="text-dark">{{ $message->name }}</strong>
                                <div class="small text-muted">{{ $message->email }}</div>
                            </td>
                            <td>
                                @if (! $message->is_read)
                                    <i class="bi bi-envelope-exclamation text-warning me-1"></i>
                                @endif
                                {{ $message->subject ?: 'General enquiry' }}
                            </td>
                            <td><span class="text-muted">{{ \Illuminate\Support\Str::limit($message->message, 60) }}</span></td>
                            <td><span class="ts-badge ts-badge-{{ $message->status === 'replied' ? 'confirmed' : ($message->status === 'closed' ? 'cancelled' : 'pending') }}">{{ ucfirst($message->status) }}</span></td>
                            <td>{{ $message->created_at->diffForHumans() }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.contact-messages.show', $message) }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this message?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No messages.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $messages->links() }}</div>
@endsection
