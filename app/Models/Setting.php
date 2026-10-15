<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Runtime configuration edited from the admin settings screen.
 *
 * Values live in the database so non-technical staff can change the tax rate or
 * support email without a deploy; anything sensitive (SMTP passwords, gateway
 * keys) stays in .env and is never stored here.
 */
class Setting extends BaseModel
{
    public static bool $usesSlug = false;

    public $timestamps = true;

    protected $fillable = ['group', 'key', 'value', 'type', 'label', 'hint', 'is_public', 'sort_order'];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /** @return array<string, mixed> */
    public function typedValue(): mixed
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOL),
            'integer' => (int) $this->value,
            'json' => json_decode((string) $this->value, true) ?? [],
            default => $this->value,
        };
    }

    /** Read a single setting. */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();

        return $setting?->typedValue() ?? $default;
    }

    /** Write a single setting (upsert). */
    public static function put(string $key, mixed $value, string $group = 'general'): void
    {
        $serialised = is_array($value) ? json_encode($value) : (string) $value;

        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $serialised, 'group' => $group],
        );

        \Illuminate\Support\Facades\Cache::forget('settings.all');
    }

    /**
     * Every setting as a key => typed value map. Cached because the layout reads
     * it on every request.
     *
     * @return array<string, mixed>
     */
    public static function allCached(): array
    {
        return \Illuminate\Support\Facades\Cache::remember(
            'settings.all',
            (int) config('tripsathi.cache.settings', 3600),
            fn (): array => static::query()->orderBy('sort_order')->get()
                ->mapWithKeys(fn (self $s) => [$s->key => $s->typedValue()])
                ->all(),
        );
    }

    public static function flushCache(): void
    {
        \Illuminate\Support\Facades\Cache::forget('settings.all');
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function activityModule(): string
    {
        return 'settings';
    }
}
