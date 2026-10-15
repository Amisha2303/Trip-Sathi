<?php

declare(strict_types=1);

namespace App\Models;

/**
 * A dish or set menu shown on a restaurant page.
 */
class RestaurantMenu extends BaseModel
{
    public static bool $usesSlug = false;

    protected $table = 'restaurant_menus';

    protected $fillable = [
        'restaurant_id', 'section', 'name', 'description', 'price',
        'is_vegetarian', 'is_available', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_vegetarian' => 'boolean',
            'is_available' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function restaurant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /** Sections in display order for the menu tab. */
    public static function sectionOrder(): array
    {
        return ['Starters', 'Main Course', 'Breads & Rice', 'Desserts', 'Beverages', 'Thali Sets'];
    }

    public function activityModule(): string
    {
        return 'restaurant_menus';
    }
}
