<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Pooja;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Concerns\RendersCatalogueDetail;

class PoojaController extends Controller
{
    use RendersCatalogueDetail;

    /** Public detail page for a single pooja by slug. */
    public function show(string $slug, Request $request): \Inertia\Response
    {
        return $this->renderCatalogueDetail('pooja', $slug, $request);
    }
    private const SORTS = [
        'popular' => ['is_featured', 'desc', 'price', 'asc'],
        'price_asc' => ['price', 'asc'],
        'price_desc' => ['price', 'desc'],
        'duration' => ['duration_minutes', 'asc'],
    ];

    public function index(Request $request): \Inertia\Response
    {
        $query = Pooja::query()->active()->with('destination');

        if ($term = trim((string) $request->query('q'))) {
            $query->search($term);
        }

        if ($slug = $request->query('destination')) {
            $destination = Destination::query()->active()->where('slug', $slug)->first();
            $query->when($destination, fn ($q) => $q->where('destination_id', $destination->id));
        }

        $sort = $request->query('sort');
        if (isset(self::SORTS[$sort])) {
            foreach (array_chunk(self::SORTS[$sort], 2) as [$column, $direction]) {
                $query->orderBy($column, $direction);
            }
        } else {
            $query->orderBy('is_featured', 'desc')->orderBy('price', 'asc');
        }

        $poojas = $query->paginate(9)->withQueryString();

        return \Inertia\Inertia::render('Pooja/Index', [
            'poojas' => $poojas,
            'destinations' => Destination::query()->active()->orderBy('name')->get(['id', 'name', 'slug']),
            'total' => Pooja::query()->active()->count(),
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'destination' => (string) $request->query('destination', ''),
                'sort' => in_array($sort, array_keys(self::SORTS), true) ? $sort : 'popular',
            ],
        ]);
    }
}
