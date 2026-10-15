<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Outbound email record shown on the admin "Email Logs" screen.
 *
 * Written by a queued listener after the mail is dispatched, so a slow SMTP
 * server never blocks a booking and a delivery failure is still visible.
 */
class EmailLog extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'mailable', 'notifiable_type', 'notifiable_id', 'subject',
        'to', 'cc', 'bcc', 'status', 'error', 'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'to' => 'array',
            'cc' => 'array',
            'bcc' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getRecipientAttribute(): string
    {
        return collect($this->to ?? [])
            ->map(fn ($row) => is_array($row) ? ($row['address'] ?? '') : $row)
            ->filter()
            ->implode(', ');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'sent' => 'success',
            'failed' => 'danger',
            default => 'warning',
        };
    }
}
