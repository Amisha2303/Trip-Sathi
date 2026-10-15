<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ActivityAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A message submitted through the public contact form.
 */
class ContactMessage extends BaseModel
{
    public static bool $usesSlug = false;

    protected $fillable = [
        'user_id', 'name', 'email', 'phone', 'subject', 'message',
        'status', 'reply', 'replied_at', 'replied_by', 'ip_address', 'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'replied_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function markRead(): void
    {
        if (! $this->is_read) {
            $this->forceFill(['is_read' => true])->saveQuietly();
        }
    }

    /** Reply and close in one audited step. */
    public function replyTo(string $reply, ?int $adminId = null): void
    {
        $this->forceFill([
            'reply' => $reply,
            'replied_at' => now(),
            'replied_by' => $adminId ?? auth()->id(),
            'status' => 'replied',
            'is_read' => true,
        ])->save();

        $this->logActivity(ActivityAction::Updated, 'Replied to contact message');
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false)->where('status', 'new');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', ['new', 'read']);
    }

    public function activityModule(): string
    {
        return 'contact_messages';
    }

    public function activitySubjectLabel(): string
    {
        return ($this->subject ?? 'No subject') . ' · ' . $this->email;
    }
}
