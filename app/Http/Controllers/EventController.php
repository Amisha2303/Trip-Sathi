<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Concerns\RendersCatalogueDetail;

class EventController extends Controller
{
    use RendersCatalogueDetail;

    /** Public detail page for a single event by slug. */
    public function show(string $slug, Request $request): \Inertia\Response
    {
        return $this->renderCatalogueDetail('event', $slug, $request);
    }
    private const SORTS = [
        'soonest' => ['event_date', 'asc'],
        'latest' => ['event_date', 'desc'],
        'price_asc' => ['base_price', 'asc'],
        'price_desc' => ['base_price', 'desc'],
        'popular' => ['sold_count', 'desc'],
    ];

    public function index(Request $request): \Inertia\Response
    {
        $when = $request->query('when', 'upcoming');

        $query = Event::query()->active()->with('destination');

        if ($when === 'past') {
            $query->where('ends_at', '<', now());
        } elseif ($when === 'all') {
            // no date filter
        } else {
            $when = 'upcoming';
            $query->where('ends_at', '>=', now());
        }

        if ($term = trim((string) $request->query('q'))) {
            $query->search($term, ['name', 'venue', 'city', 'organiser', 'short_description']);
        }

        if ($slug = $request->query('destination')) {
            $destination = Destination::query()->active()->where('slug', $slug)->first();
            $query->when($destination, fn ($q) => $q->where('destination_id', $destination->id));
        }

        $sort = $request->query('sort');
        if (isset(self::SORTS[$sort])) {
            [$column, $direction] = self::SORTS[$sort];
            $query->orderBy($column, $direction);
        } else {
            $query->orderBy('event_date', 'asc');
        }

        $events = $query->paginate(9)->withQueryString();

        return \Inertia\Inertia::render('Events/Index', [
            'events' => $events,
            'destinations' => Destination::query()->active()->orderBy('name')->get(['id', 'name', 'slug']),
            'total' => Event::query()->active()->count(),
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'destination' => (string) $request->query('destination', ''),
                'when' => $when,
                'sort' => in_array($sort, array_keys(self::SORTS), true) ? $sort : 'soonest',
            ],
        ]);
    }
}
