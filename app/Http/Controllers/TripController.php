<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\TripCreatedMail;
use App\Models\Destination;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TripController extends Controller
{
    public function index(Request $request): \Inertia\Response
    {
        $scope = (string) $request->query('scope', 'community');
        $available = ['community' => true, 'mine' => auth()->check()];

        if (! isset($available[$scope]) || ! $available[$scope]) {
            $scope = 'community';
        }

        $query = Trip::query()->with(['destination', 'user']);

        if ($scope === 'mine') {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('is_public', true);
        }

        if ($term = trim((string) $request->query('q'))) {
            $query->where(function ($inner) use ($term): void {
                $inner->where('name', 'LIKE', '%' . addcslashes($term, '%_\\') . '%')
                    ->orWhere('description', 'LIKE', '%' . addcslashes($term, '%_\\') . '%');
            });
        }

        $sort = $request->query('sort');
        match ($sort) {
            'upcoming' => $query->orderBy('start_date', 'asc'),
            'longest' => $query->orderBy('total_days', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $trips = $query->paginate(9)->withQueryString();

        return \Inertia\Inertia::render('Trips/Index', [
            'trips' => $trips,
            'destinations' => Destination::query()->active()->orderBy('name')->get(['id', 'name', 'slug']),
            'scope' => $scope,
            'canCreate' => auth()->check(),
            'total' => Trip::query()->where('is_public', true)->count(),
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'sort' => in_array($sort, ['upcoming', 'longest'], true) ? $sort : 'newest',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'destination_id' => ['nullable', 'integer', 'exists:destinations,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'travelers' => ['required', 'integer', 'min:1', 'max:50'],
            'budget' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_public' => ['sometimes', 'boolean'],
        ]);

        $start = \Illuminate\Support\Carbon::parse($data['start_date']);
        $end = \Illuminate\Support\Carbon::parse($data['end_date']);

        $trip = Trip::create([
            'user_id' => Auth::id(),
            'destination_id' => $data['destination_id'] ?? null,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'total_days' => max(1, (int) $start->diffInDays($end) + 1),
            'travelers' => (int) $data['travelers'],
            'budget' => $data['budget'] ?? null,
            'is_public' => $request->boolean('is_public'),
        ]);

        if ($request->user()?->email) {
            try {
                Mail::to($request->user()->email)->send(new TripCreatedMail($trip, $request->user()));
            } catch (\Throwable $e) {
                Log::warning('Trip created email could not be sent: ' . $e->getMessage());
            }
        }

        return redirect()
            ->route('trips.index', ['scope' => 'mine'])
            ->with('success', 'Trip "' . $trip->name . '" created. Add days and items as you plan.');
    }
}
