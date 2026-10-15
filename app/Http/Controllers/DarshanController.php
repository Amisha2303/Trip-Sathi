<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Darshan;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class DarshanController extends Controller
{
    private const SORTS = [
        'popular' => ['is_featured', 'desc', 'price', 'asc'],
        'price_asc' => ['price', 'asc'],
        'price_desc' => ['price', 'desc'],
        'duration' => ['duration_minutes', 'asc'],
    ];

    private const TYPES = [
        'vip' => 'VIP Darshan',
        'regular' => 'Regular',
        'special' => 'Special Darshan',
        'online' => 'Online / Virtual',
    ];

    public function index(Request $request): View
    {
        $query = Darshan::query()->active()->with('destination');

        if ($term = trim((string) $request->query('q'))) {
            $query->search($term);
        }

        if ($slug = $request->query('destination')) {
            $destination = Destination::query()->active()->where('slug', $slug)->first();
            $query->when($destination, fn ($q) => $q->where('destination_id', $destination->id));
        }

        if ($type = $request->query('type')) {
            $query->where('darshan_type', $type);
        }

        $sort = $request->query('sort');
        if (isset(self::SORTS[$sort])) {
            foreach (array_chunk(self::SORTS[$sort], 2) as [$column, $direction]) {
                $query->orderBy($column, $direction);
            }
        } else {
            $query->orderBy('is_featured', 'desc')->orderBy('price', 'asc');
        }

        $darshans = $query->paginate(9)->withQueryString();

        return view('darshan.index', [
            'darshans' => $darshans,
            'destinations' => Destination::query()->active()->orderBy('name')->get(['id', 'name', 'slug']),
            'types' => self::TYPES,
            'total' => Darshan::query()->active()->count(),
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'destination' => (string) $request->query('destination', ''),
                'type' => (string) $request->query('type', ''),
                'sort' => in_array($sort, array_keys(self::SORTS), true) ? $sort : 'popular',
            ],
        ]);
    }
}
