<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Concerns\RendersCatalogueDetail;

class HotelController extends Controller
{
    use RendersCatalogueDetail;

    /** Public detail page for a single hotel by slug. */
    public function show(string $slug, Request $request): View
    {
        return $this->renderCatalogueDetail('hotel', $slug, $request);
    }
    private const SORTS = [
        'recommended' => ['is_featured', 'desc', 'rating', 'desc'],
        'price_asc' => ['base_price', 'asc'],
        'price_desc' => ['base_price', 'desc'],
        'rating' => ['rating', 'desc'],
        'stars' => ['star_rating', 'desc'],
    ];

    public function index(Request $request): View
    {
        $query = Hotel::query()->active()->with('destination');

        if ($term = trim((string) $request->query('q'))) {
            $query->search($term, ['name', 'city', 'address']);
        }

        if ($slug = $request->query('destination')) {
            $destination = Destination::query()->active()->where('slug', $slug)->first();
            $query->when($destination, fn ($q) => $q->where('destination_id', $destination->id));
        }

        if ($stars = $request->integer('stars')) {
            $query->where('star_rating', '>=', min($stars, 5));
        }

        if ($min = $request->filled('min_price') ? (float) $request->query('min_price') : null) {
            $query->where('base_price', '>=', $min);
        }

        if ($max = $request->filled('max_price') ? (float) $request->query('max_price') : null) {
            $query->where('base_price', '<=', $max);
        }

        $sort = $request->query('sort');
        if (isset(self::SORTS[$sort])) {
            foreach (array_chunk(self::SORTS[$sort], 2) as [$column, $direction]) {
                $query->orderBy($column, $direction);
            }
        } else {
            $query->orderBy('is_featured', 'desc')->orderBy('rating', 'desc');
        }

        $hotels = $query->paginate(12)->withQueryString();

        return view('hotels.index', [
            'hotels' => $hotels,
            'destinations' => Destination::query()->active()->orderBy('name')->get(['id', 'name', 'slug']),
            'total' => Hotel::query()->active()->count(),
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'destination' => (string) $request->query('destination', ''),
                'stars' => $request->integer('stars'),
                'min_price' => (string) $request->query('min_price', ''),
                'max_price' => (string) $request->query('max_price', ''),
                'sort' => in_array($sort, array_keys(self::SORTS), true) ? $sort : 'recommended',
            ],
        ]);
    }
}
