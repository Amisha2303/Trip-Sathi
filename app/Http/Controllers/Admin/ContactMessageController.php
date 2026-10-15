<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContactReplyRequest;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $messages = ContactMessage::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . addcslashes($request->string('q')->toString(), '%_\\') . '%';
                $q->where(fn ($inner) => $inner
                    ->where('name', 'LIKE', $term)
                    ->orWhere('email', 'LIKE', $term)
                    ->orWhere('subject', 'LIKE', $term)
                    ->orWhere('message', 'LIKE', $term));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->boolean('unread'), fn ($q) => $q->where('is_read', false))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'unread' => ContactMessage::query()->where('is_read', false)->count(),
            'new' => ContactMessage::query()->where('status', 'new')->count(),
            'replied' => ContactMessage::query()->where('status', 'replied')->count(),
            'closed' => ContactMessage::query()->where('status', 'closed')->count(),
        ];

        return view('admin.contact.index', [
            'messages' => $messages,
            'counts' => $counts,
            'statuses' => ['new' => 'New', 'read' => 'Read', 'replied' => 'Replied', 'closed' => 'Closed', 'spam' => 'Spam'],
        ]);
    }

    public function show(ContactMessage $message): View
    {
        // Opening the message marks it read — same as an email client.
        $message->markRead();

        $message->load(['user:id,name,email', 'repliedBy:id,name']);

        return view('admin.contact.show', [
            'message' => $message,
            'statuses' => ['new' => 'New', 'read' => 'Read', 'replied' => 'Replied', 'closed' => 'Closed', 'spam' => 'Spam'],
        ]);
    }

    public function update(Request $request, ContactMessage $message): RedirectResponse
    {
        $status = $request->validate([
            'status' => ['required', 'string', 'in:new,read,replied,closed,spam'],
        ])['status'];

        $message->forceFill([
            'status' => $status,
            'is_read' => $status === 'new' ? $message->is_read : true,
        ])->save();

        return back()->with('success', 'Message marked as ' . $status . '.');
    }

    public function reply(ContactReplyRequest $request, ContactMessage $message): RedirectResponse
    {
        $message->replyTo($request->string('reply')->toString(), $request->user()?->id);

        return redirect()->route('admin.contact-messages.show', $message)->with('success', 'Reply recorded.');
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return redirect()->route('admin.contact-messages.index')->with('success', 'Message deleted.');
    }
}
