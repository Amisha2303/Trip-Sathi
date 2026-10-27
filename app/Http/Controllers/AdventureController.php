<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Difficulty;
use App\Models\Adventure;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Concerns\RendersCatalogueDetail;

class AdventureController extends Controller
{
    use RendersCatalogueDetail;

    /** Public detail page for a single adventure by slug. */
    public function show(string $slug, Request $request): \Inertia\Response
    {
        return $this->renderCatalogueDetail('adventure', $slug, $request);
    }
    private const SORTS = [
        'popular' => ['is_featured', 'desc', 'price', 'asc'],
        'price_asc' => ['price', 'asc'],
        'price_desc' => ['price', 'desc'],
        'duration' => ['duration_minutes', 'asc'],
    ];

    /** Activity types offered in the catalogue (mirrors the migration comment). */
    private const TYPES = [
        'trekking' => 'Trekking',
        'rafting' => 'Rafting',
        'paragliding' => 'Paragliding',
        'scuba' => 'Scuba',
        'camping' => 'Camping',
        'safari' => 'Safari',
        'zipline' => 'Zipline',
        'watersports' => 'Water sports',
    ];

    public function index(Request $request): \Inertia\Response
    {
        $query = Adventure::query()->active()->with('destination');

        if ($term = trim((string) $request->query('q'))) {
            $query->search($term, ['name', 'activity_type', 'short_description']);
        }

        if ($slug = $request->query('destination')) {
            $destination = Destination::query()->active()->where('slug', $slug)->first();
            $query->when($destination, fn ($q) => $q->where('destination_id', $destination->id));
        }

        if ($type = $request->query('type')) {
            $query->where('activity_type', $type);
        }

        if ($difficulty = $request->query('difficulty')) {
            $query->where('difficulty', $difficulty);
        }

        $sort = $request->query('sort');
        if (isset(self::SORTS[$sort])) {
            foreach (array_chunk(self::SORTS[$sort], 2) as [$column, $direction]) {
                $query->orderBy($column, $direction);
            }
        } else {
            $query->orderBy('is_featured', 'desc')->orderBy('price', 'asc');
        }

        $adventures = $query->paginate(9)->withQueryString();

        return \Inertia\Inertia::render('Adventures/Index', [
            'adventures' => $adventures,
            'destinations' => Destination::query()->active()->orderBy('name')->get(['id', 'name', 'slug']),
            'types' => self::TYPES,
            'difficulties' => Difficulty::options(),
            'total' => Adventure::query()->active()->count(),
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'destination' => (string) $request->query('destination', ''),
                'type' => (string) $request->query('type', ''),
                'difficulty' => (string) $request->query('difficulty', ''),
                'sort' => in_array($sort, array_keys(self::SORTS), true) ? $sort : 'popular',
            ],
        ]);
    }
}
