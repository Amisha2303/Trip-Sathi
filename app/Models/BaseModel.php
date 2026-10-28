<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\SanitisesRichText;
use App\Models\Concerns\SlugsItself;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * Shared behaviour for every Trip Sathi model.
 *
 * Deliberately small: search scopes, a consistent image accessor and a single
 * place to change the soft-delete behaviour if the project ever needs it.
 */
abstract class BaseModel extends Model
{
    use LogsActivity;
    use SanitisesRichText;
    use SlugsItself;
    use SoftDeletes;

    /**
     * Fields that may be mass-assigned. Anything not listed here can never be
     * reached through request input, which is the allow-list half of mass
     * assignment protection.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * Casts applied to every model; child models extend it.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    /**
     * Appended accessors. Kept empty here so child models stay explicit.
     *
     * @var array<int, string>
     */
    protected $appends = [];

    /** Case-insensitive LIKE — the default search operator across the catalogue. */
    public function scopeSearch(Builder $query, ?string $term, array $columns = ['name']): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        // Escape LIKE metacharacters so a user typing "100%" cannot widen the match.
        $escaped = addcslashes($term, '%_\\');
        $like = '%' . $escaped . '%';

        return $query->where(function (Builder $inner) use ($columns, $like): void {
            foreach ($columns as $column) {
                $inner->orWhere($column, 'LIKE', $like);
            }
        });
    }

    /** Inclusive price range. Pass null for either side to leave that bound open. */
    public function scopePriceBetween(Builder $query, float|int|null $min, float|int|null $max): Builder
    {
        return $query
            ->when($min !== null, fn (Builder $q) => $q->where('price', '>=', (float) $min))
            ->when($max !== null, fn (Builder $q) => $q->where('price', '<=', (float) $max));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where($query->getModel()->getTable() . '.is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where($query->getModel()->getTable() . '.is_featured', true);
    }

    public function scopeForDestination(Builder $query, int|Model|null $destination): Builder
    {
        if ($destination instanceof Model) {
            $destination = $destination->getKey();
        }

        if ($destination === null) {
            return $query;
        }

        $table = $query->getModel()->getTable();

        return $query->where(
            $table === 'destinations' ? 'destinations.id' : $table . '.destination_id',
            $destination
        );
    }

    /**
     * Resolve a public image path to a fully qualified URL.
     *
     * Three sources are tried, in order:
     *  1. Absolute http(s) URLs are passed straight through.
     *  2. Bundled static assets shipped in the public web root (photos/…, images/…)
     *     — these deploy with the site and work on Vercel/Netlify.
     *  3. Uploaded files that live on the "public" disk (storage/app/public).
     *
     * When nothing matches we return the bundled placeholder so no card ever
     * renders a broken image.
     */
    public static function imageUrl(?string $path, string $fallback = 'images/placeholder.svg'): string
    {
        if ($path === null || $path === '') {
            return asset($fallback);
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (is_file(public_path($path))) {
            return asset($path);
        }

        $disk = Storage::disk('public');

        return $disk->exists($path) ? $disk->url($path) : asset($fallback);
    }

    /**
     * Resolve route binding by primary key ID or slug.
     * Allows admin routes (/admin/.../1) to resolve by numeric ID while
     * frontend routes (/.../somnath-temple) resolve by slug.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if ($field) {
            return parent::resolveRouteBinding($value, $field);
        }

        if (is_numeric($value)) {
            $model = $this->resolveRouteBindingQuery($this, $value, $this->getKeyName())->first();
            if ($model) {
                return $model;
            }
        }

        return $this->resolveRouteBindingQuery($this, $value, $this->getRouteKeyName())->first()
            ?? $this->resolveRouteBindingQuery($this, $value, $this->getKeyName())->first();
    }
}

