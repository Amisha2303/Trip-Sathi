<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\SanitisesRichText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Shared behaviour for every `<owner>_images` gallery row.
 *
 * A gallery is the same shape everywhere: path, caption, primary flag, ordering.
 * One parent per row avoids a join and keeps `is_primary` a per-table concern.
 */
abstract class ImageModel extends Model
{
    use SanitisesRichText;

    public $timestamps = true;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /** Morph target on the owning entity (hotel_image → Hotel::class). */
    abstract public function ownerModel(): string;

    /** FK column on this table. */
    abstract public function ownerForeignKey(): string;

    public function owner(): MorphTo
    {
        return $this->morphTo('owner', $this->ownerModel(), $this->ownerForeignKey());
    }

    public function getUrlAttribute(): string
    {
        return BaseModel::imageUrl($this->path);
    }

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('is_primary')->orderBy('sort_order');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Store an upload and return a ready-to-save row.
     * Storage paths come from FileUploadService; this only shapes the row.
     *
     * @param  array<string, mixed>  $attributes
     */
    public static function makeRow(string $path, array $attributes = []): self
    {
        $model = new static();
        $model->forceFill(array_merge([
            'path' => $path,
            'sort_order' => 0,
            'is_primary' => false,
        ], $attributes));

        return $model;
    }
}
