<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Concerns\RendersCatalogueDetail;

class DestinationController extends Controller
{
    use RendersCatalogueDetail;

    /** Public detail page for a single destination by slug. */
    public function show(string $slug, Request $request): View
    {
        return $this->renderCatalogueDetail('destination', $slug, $request);
    }
    /** Sort options exposed in the toolbar → query order. */
    private const SORTS = [
        'popular' => ['is_featured', 'desc', 'popularity', 'desc'],
        'name' => ['name', 'asc'],
        'newest' => ['created_at', 'desc'],
    ];

    public function index(Request $request): View
    {
        $query = Destination::query()->active();

        if ($term = trim((string) $request->query('q'))) {
            $query->search($term, ['name', 'city', 'state', 'short_description']);
        }

        if ($state = $request->query('state')) {
            $query->where('state', $state);
        }

        $sort = $request->query('sort');
        if (isset(self::SORTS[$sort])) {
            [$column, $direction] = self::SORTS[$sort];
            $query->orderBy($column, $direction);
        } else {
            $query->orderBy('is_featured', 'desc')->orderBy('popularity', 'desc');
        }

        $destinations = $query->paginate(9)->withQueryString();

        $states = Destination::query()
            ->active()
            ->whereNotNull('state')
            ->distinct()
            ->orderBy('state')
            ->pluck('state');

        return view('destinations.index', [
            'destinations' => $destinations,
            'states' => $states,
            'total' => Destination::query()->active()->count(),
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'state' => (string) $request->query('state', ''),
                'sort' => in_array($sort, array_keys(self::SORTS), true) ? $sort : 'popular',
            ],
        ]);
    }
}
