<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Adventure;
use App\Models\Amenity;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Event;
use App\Models\Restaurant;
use App\Models\Testimonial;
use App\Models\Trip;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Cached read models for the public marketing pages.
 *
 * Everything here is public, non-personal, and invalidated by
 * `Destination::flushCatalogueCache()` on catalogue writes. Nothing
 * user-specific is ever cached.
 */
class CachedContent
{
    public function popularDestinations(int $limit = 8): Collection
    {
        return Cache::remember(
            'cache.destinations.popular.' . $limit,
            (int) config('tripsathi.cache.destinations', 1800),
            fn () => Destination::query()
                ->active()
                ->with('category')
                ->popular()
                ->limit($limit)
                ->get(),
        );
    }

    public function featuredHotels(int $limit = 6): Collection
    {
        return Cache::remember(
            'cache.hotels.featured.' . $limit,
            (int) config('tripsathi.cache.featured', 900),
            fn () => \App\Models\Hotel::query()
                ->active()
                ->featured()
                ->with(['destination', 'images'])
                ->withAvg('reviews as avg_rating', fn ($q) => $q->where('status', 'approved'))
                ->orderByDesc('rating')
                ->limit($limit)
                ->get(),
        );
    }

    public function featuredAdventures(int $limit = 6): Collection
    {
        return Cache::remember(
            'cache.adventures.featured.' . $limit,
            (int) config('tripsathi.cache.featured', 900),
            fn () => Adventure::query()
                ->active()
                ->featured()
                ->with('destination')
                ->orderByDesc('price')
                ->limit($limit)
                ->get(),
        );
    }

    public function upcomingEvents(int $limit = 6): Collection
    {
        return Cache::remember(
            'cache.events.upcoming.' . $limit,
            (int) config('tripsathi.cache.featured', 900),
            fn () => Event::query()
                ->active()
                ->upcoming()
                ->with(['destination', 'ticketTypes'])
                ->orderBy('starts_at')
                ->limit($limit)
                ->get(),
        );
    }

    public function featuredRestaurants(int $limit = 6): Collection
    {
        return Cache::remember(
            'cache.restaurants.featured.' . $limit,
            (int) config('tripsathi.cache.featured', 900),
            fn () => Restaurant::query()
                ->active()
                ->featured()
                ->with('destination')
                ->orderByDesc('rating')
                ->limit($limit)
                ->get(),
        );
    }

    public function testimonials(int $limit = 6): Collection
    {
        return Cache::remember(
            'cache.testimonials.' . $limit,
            (int) config('tripsathi.cache.featured', 900),
            fn () => Testimonial::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->limit($limit)
                ->get(),
        );
    }

    public function sharedTrips(int $limit = 3): Collection
    {
        return Cache::remember(
            'cache.trips.shared.' . $limit,
            (int) config('tripsathi.cache.featured', 900),
            fn () => Trip::query()
                ->shared()
                ->with(['user:id,name,avatar', 'destination'])
                ->orderByDesc('view_count')
                ->limit($limit)
                ->get(),
        );
    }

    /** Search-bar dropdown data, cached for a day. */
    public function searchOptions(): array
    {
        return Cache::remember('destinations.search_options', 86400, fn () => [
            'destinations' => Destination::query()
                ->active()
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'city', 'state', 'main_image'])
                ->map(fn (Destination $d) => [
                    'value' => $d->id,
                    'label' => $d->name,
                    'hint' => $d->location,
                    'image' => $d->image_url,
                ])
                ->all(),
            'categories' => Category::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->groupBy('type')
                ->all(),
            'travelers' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 15, 20],
        ]);
    }

    public function amenitiesFor(string $group = 'general'): Collection
    {
        return Cache::remember(
            'cache.amenities.' . $group,
            (int) config('tripsathi.cache.categories', 3600),
            fn () => Amenity::query()
                ->where('is_active', true)
                ->where('group', $group)
                ->orderBy('sort_order')
                ->get(),
        );
    }

    /** Called after any catalogue write. */
    public function flush(): void
    {
        foreach (['destinations.popular', 'hotels.featured', 'adventures.featured', 'events.upcoming', 'restaurants.featured', 'testimonials', 'trips.shared', 'amenities'] as $key) {
            foreach ([6, 8, 10, 12] as $suffix) {
                Cache::forget('cache.' . $key . '.' . $suffix);
            }
            Cache::forget('cache.' . $key);
        }

        Cache::forget('destinations.search_options');
        Cache::forget('settings.all');
    }
}
