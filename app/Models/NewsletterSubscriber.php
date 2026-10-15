<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * A newsletter subscriber.
 *
 * `token` powers a double-opt-out unsubscribe link in the footer of every
 * campaign email, so no authenticated session is required.
 */
class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'email', 'name', 'status', 'token', 'source',
        'subscribed_at', 'unsubscribed_at', 'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Guarantee a token so unsubscribe links never break.
        static::creating(function (self $subscriber): void {
            $subscriber->token ??= Str::random(48);
            $subscriber->subscribed_at ??= now();
        });
    }

    public function getUnsubscribeUrlAttribute(): string
    {
        return URL::signedRoute('newsletter.unsubscribe', ['token' => $this->token]);
    }

    public function isSubscribed(): bool
    {
        return $this->status === 'subscribed';
    }

    public function subscribe(): void
    {
        $this->forceFill([
            'status' => 'subscribed',
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
            'token' => $this->token ?: Str::random(48),
        ])->save();
    }

    public function unsubscribe(): void
    {
        $this->forceFill([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ])->save();
    }

    public function scopeSubscribed(Builder $query): Builder
    {
        return $query->where('status', 'subscribed');
    }

    public function activityModule(): string
    {
        return 'newsletter_subscribers';
    }
}
