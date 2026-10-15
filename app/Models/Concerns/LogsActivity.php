<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Enums\ActivityAction;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Opt-in model auditing.
 *
 * Controllers and services call `->logActivity()` after a write; the model
 * supplies the module name, subject and a redacted property diff so the audit
 * trail stays consistent without repeating boilerplate at 60 call sites.
 *
 * Secrets never reach the log: `ActivityLogger` strips anything matching the
 * sensitive key list before persisting.
 */
trait LogsActivity
{
    /** Module name used in the admin activity log; override per model. */
    public function activityModule(): string
    {
        return class_basename(static::class);
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    public function logActivity(ActivityAction $action, ?string $description = null, array $properties = []): ?ActivityLog
    {
        // The audit trail is best-effort: a logging failure must never abort a
        // booking. Fire-and-forget with a guard for unauthenticated contexts.
        try {
            return ActivityLog::record(
                $action,
                $this->activityModule(),
                $description,
                $this,
                $properties,
            );
        } catch (\Throwable) {
            return null;
        }
    }

    /** Attribute values before the current (uncommitted) change. */
    public function activityDiff(): array
    {
        return $this->getDirty();
    }

    /** Never persist a whole model, only its identifier. */
    public function activitySubjectId(): int|string|null
    {
        return $this->getKey();
    }

    /** Resolve the relation used to label the log row. */
    public function activitySubjectLabel(): string
    {
        $title = $this->getAttribute('name')
            ?? $this->getAttribute('title')
            ?? $this->getAttribute('code')
            ?? $this->getAttribute('email');

        return $title === null
            ? sprintf('%s #%s', $this->activityModule(), $this->getKey())
            : (string) $title;
    }

    /** @return array<int,string> Columns that must never appear in an audit diff. */
    public static function activitySecretColumns(): array
    {
        return [
            'password',
            'password_confirmation',
            'remember_token',
            'two_factor_secret',
            'two_factor_recovery_codes',
            'secret',
            'api_key',
            'token',
        ];
    }

    /** Convenience for polymorphic relation wiring in other models. */
    public function morphKey(): Model
    {
        return $this;
    }
}
