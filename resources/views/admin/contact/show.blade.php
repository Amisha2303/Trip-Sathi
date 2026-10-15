@extends('layouts.admin')

@section('title', 'Message from ' . $message->name)
@section('heading', 'Message · ' . ($message->subject ?: 'General enquiry'))

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ts-card mb-4">
                <div class="ts-card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <h2 class="ts-title mb-1" style="font-size: 1.2rem;">{{ $message->subject ?: 'General enquiry' }}</h2>
                            <p class="text-muted mb-0">
                                From <strong>{{ $message->name }}</strong> &lt;{{ $message->email }}&gt;
                                @if ($message->phone) · {{ $message->phone }} @endif
                            </p>
                        </div>
                        <span class="ts-badge ts-badge-{{ $message->status === 'replied' ? 'confirmed' : ($message->status === 'closed' ? 'cancelled' : 'pending') }}">{{ ucfirst($message->status) }}</span>
                    </div>

                    <div class="p-3 rounded-3 bg-body-secondary mb-3" style="white-space: pre-wrap;">{{ $message->message }}</div>

                    <div class="small text-muted">
                        Received {{ $message->created_at->format('d M Y, h:i A') }}
                        @if ($message->ip_address) · IP {{ $message->ip_address }} @endif
                        @if ($message->user) · Account: <a href="{{ route('admin.users.show', $message->user) }}">{{ $message->user->name }}</a> @endif
                    </div>
                </div>
            </div>

            @if ($message->reply)
                <div class="ts-card">
                    <div class="ts-card-body">
                        <div class="ts-card-title mb-3">Your reply</div>
                        <div class="p-3 rounded-3 border mb-2" style="white-space: pre-wrap;">{{ $message->reply }}</div>
                        <div class="small text-muted">
                            Sent {{ $message->replied_at?->format('d M Y, h:i A') }}
                            @if ($message->repliedBy) by {{ $message->repliedBy->name }} @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <form method="POST" action="{{ route('admin.contact-messages.reply', $message) }}" class="ts-card mb-4">
                @csrf
                <div class="ts-card-body">
                    <div class="ts-card-title mb-3">Reply</div>
                    <div class="mb-3">
                        <label for="reply" class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
                        <textarea id="reply" name="reply" rows="5" required
                            class="form-control @error('reply') is-invalid @enderror"
                            placeholder="Write your reply…">{{ old('reply') }}</textarea>
                        @error('reply')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button class="btn btn-primary w-100" type="submit"><i class="bi bi-send"></i> Save reply</button>
                    <div class="form-text">Records the reply on the message and marks it replied.</div>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.contact-messages.update', $message) }}" class="ts-card mb-4">
                @csrf @method('PUT')
                <div class="ts-card-body">
                    <div class="ts-card-title mb-3">Set status</div>
                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold">Status</label>
                        <select id="status" name="status" class="form-select">
                            @foreach ($statuses as $key => $label)
                                <option value="{{ $key }}" @selected(old('status', $message->status) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-outline-secondary w-100" type="submit"><i class="bi bi-check2"></i> Update status</button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}"
                onsubmit="return confirm('Delete this message?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger w-100" type="submit"><i class="bi bi-trash"></i> Delete message</button>
            </form>
        </div>
    </div>
@endsection
