<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RendersCatalogueDetail;
use App\Models\Destination;
use App\Models\FoodExperience;
use App\Models\Restaurant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Food & Dining — restaurants and hosted food experiences.
 *
 * Restaurants get a filterable listing plus a detail page (menus, hours,
 * reservation info). Food experiences surface as a curated strip on the index
 * and reuse the same catalogue detail renderer.
 */
class FoodController extends Controller
{
    use RendersCatalogueDetail;

    /** Sort options exposed in the toolbar → query order. */
    private const SORTS = [
        'popular' => ['rating', 'desc', 'review_count', 'desc'],
        'name' => ['name', 'asc'],
        'cost_low' => ['average_cost', 'asc'],
        'cost_high' => ['average_cost', 'desc'],
    ];

    public function index(Request $request): \Inertia\Response
    {
        $query = Restaurant::query()->active()->with('destination');

        if ($term = trim((string) $request->query('q'))) {
            $query->search($term);
        }

        if ($slug = $request->query('destination')) {
            $destination = Destination::query()->active()->where('slug', $slug)->first();
            $query->when($destination, fn ($q) => $q->where('destination_id', $destination->id));
        }

        if ($cuisine = trim((string) $request->query('cuisine'))) {
            $query->where('cuisine', 'like', '%' . $cuisine . '%');
        }

        $sort = $request->query('sort');
        if (isset(self::SORTS[$sort])) {
            [$column, $direction] = self::SORTS[$sort];
            $query->orderBy($column, $direction);
        } else {
            $query->orderByDesc('rating')->orderByDesc('review_count');
        }

        $restaurants = $query->paginate(9)->withQueryString();

        $experiences = FoodExperience::query()
            ->active()
            ->with(['destination', 'restaurant'])
            ->orderByDesc('is_featured')
            ->take(4)
            ->get();

        $destinations = Destination::query()
            ->active()
            ->whereHas('restaurants')
            ->orderBy('name')
            ->pluck('name', 'slug');

        $cuisines = Restaurant::query()
            ->active()
            ->whereNotNull('cuisine')
            ->distinct()
            ->orderBy('cuisine')
            ->pluck('cuisine');

        return \Inertia\Inertia::render('Food/Index', [
            'restaurants' => $restaurants,
            'experiences' => $experiences,
            'destinations' => $destinations,
            'cuisines' => $cuisines,
            'total' => Restaurant::query()->active()->count(),
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'destination' => (string) $request->query('destination', ''),
                'cuisine' => (string) $request->query('cuisine', ''),
                'sort' => in_array($sort, array_keys(self::SORTS), true) ? $sort : 'popular',
            ],
        ]);
    }

    public function show(string $slug, Request $request): \Inertia\Response
    {
        // Restaurants and food experiences share the /food/{slug} namespace.
        if (FoodExperience::query()->active()->where('slug', $slug)->exists()) {
            return $this->renderCatalogueDetail('food', $slug, $request);
        }

        return $this->renderCatalogueDetail('restaurant', $slug, $request);
    }
}
