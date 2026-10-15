<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Generates and refreshes URL slugs.
 *
 * A slug is only regenerated while the record is unsaved, so renaming a hotel
 * never silently breaks inbound links — use `regenerateSlug()` explicitly for
 * that. Collisions get a numeric suffix, which `uniqueSlug()` guarantees with a
 * database existence check.
 */
trait SlugsItself
{
    /** Set to false on models whose table has no `slug` column. */
    public static bool $usesSlug = true;

    protected static function bootSlugsItself(): void
    {
        static::creating(function (self $model): void {
            if (! static::$usesSlug || blank($model->slugSourceValue())) {
                return;
            }

            if (blank($model->slug)) {
                $model->slug = $model->generateSlug();
            } else {
                $model->slug = Str::slug($model->slug);
            }
        });
    }

    /** Attribute the slug is derived from. */
    protected function slugSource(): string
    {
        return 'name';
    }

    /** Current value of the slug source attribute. */
    public function slugSourceValue(): mixed
    {
        return $this->getAttribute($this->slugSource());
    }

    /** Force a new slug from the current source value. */
    public function regenerateSlug(): self
    {
        $this->slug = $this->generateSlug();

        return $this;
    }

    public function generateSlug(): string
    {
        return $this->uniqueSlug(Str::slug((string) $this->slugSourceValue()));
    }

    /**
     * Append `-2`, `-3`, … until the slug is free. Uses the primary key to
     * exclude the current record when re-slugging an existing row.
     */
    public function uniqueSlug(string $base): string
    {
        $base = $base === '' ? 'item' : $base;
        $slug = $base;
        $suffix = 1;

        while ($this->slugExists($slug)) {
            $suffix++;
            $slug = $base . '-' . $suffix;
        }

        return $slug;
    }

    protected function slugExists(string $slug): bool
    {
        $query = static::query()
            ->where('slug', $slug)
            ->when($this->exists, fn ($q) => $q->whereKeyNot($this->getKey()));

        return $query->exists();
    }
}
