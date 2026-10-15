<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ActivityAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Immutable audit row written by models, services and middleware.
 *
 * Secrets are stripped before persistence: `SENSITIVE_KEYS` is matched against
 * both top-level property names and dotted paths, so `payload.api_key` and
 * `password` are both dropped.
 */
class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    /** Property names never written to the log. */
    public const SENSITIVE_KEYS = [
        'password', 'password_confirmation', 'current_password', 'new_password',
        'remember_token', 'token', 'api_key', 'secret', 'client_secret',
        'webhook_secret', 'two_factor_secret', 'two_factor_recovery_codes',
        'authorization', 'cookie', 'card', 'cvv', 'account_number',
    ];

    protected $fillable = [
        'user_id', 'action', 'module', 'description', 'subject_type', 'subject_id',
        'method', 'route', 'ip_address', 'user_agent', 'properties',
    ];

    protected function casts(): array
    {
        return ['properties' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /*
    |--------------------------------------------------------------------------
    | Writing
    |--------------------------------------------------------------------------
    */

    /**
     * @param  array<string, mixed>  $properties
     */
    public static function record(
        ActivityAction|string $action,
        ?string $module = null,
        ?string $description = null,
        ?Model $subject = null,
        array $properties = [],
        ?string $ip = null,
    ): self {
        $action = $action instanceof ActivityAction ? $action : ActivityAction::tryFrom($action) ?? ActivityAction::Viewed;

        return static::create([
            'user_id' => Auth::id(),
            'action' => $action->value,
            'module' => $module,
            'description' => $description,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'method' => Request::method(),
            'route' => mb_substr((string) Request::route()?->getName(), 0, 255),
            'ip_address' => $ip ?? Request::ip(),
            'user_agent' => mb_substr((string) Request::userAgent(), 0, 1000),
            'properties' => static::redact($properties),
        ]);
    }

    /**
     * Strip sensitive keys recursively.
     *
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>
     */
    public static function redact(array $data): array
    {
        $output = [];

        foreach ($data as $key => $value) {
            $normalised = strtolower((string) $key);

            if (in_array($normalised, self::SENSITIVE_KEYS, true)) {
                $output[$key] = '[redacted]';
                continue;
            }

            $output[$key] = is_array($value) ? static::redact($value) : $value;
        }

        return $output;
    }

    /*
    |--------------------------------------------------------------------------
    | Reading
    |--------------------------------------------------------------------------
    */

    public function getActionLabelAttribute(): string
    {
        return ActivityAction::tryFrom((string) $this->action)?->label() ?? ucfirst((string) $this->action);
    }

    public function getBadgeAttribute(): string
    {
        return ActivityAction::badgeMap()[$this->action] ?? 'secondary';
    }

    public function getSubjectLabelAttribute(): string
    {
        if ($this->subject === null) {
            return '—';
        }

        if (method_exists($this->subject, 'activitySubjectLabel')) {
            return $this->subject->activitySubjectLabel();
        }

        return $this->subject->name ?? $this->subject->title ?? ($this->module . ' #' . $this->subject_id);
    }

    public function scopeForModule(Builder $query, ?string $module): Builder
    {
        return $query->when($module, fn (Builder $q) => $q->where('module', $module));
    }

    public function scopeForAction(Builder $query, ?string $action): Builder
    {
        return $query->when($action, fn (Builder $q) => $q->where('action', $action));
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc('id');
    }
}
