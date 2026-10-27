<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageUpload;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventRequest;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Event;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use HandlesImageUpload;

    public function index(Request $request): Response
    {
        $events = Event::query()
            ->with(['destination:id,name', 'category:id,name'])
            ->search($request->string('q')->toString(), ['name', 'venue', 'city', 'organiser'])
            ->when($request->filled('destination'), fn ($q) => $q->where('destination_id', (int) $request->input('destination')))
            ->when($request->filled('status'), fn ($q) => $request->string('status') === 'active'
                ? $q->where('is_active', true)
                : $q->where('is_active', false))
            ->when($request->boolean('featured'), fn ($q) => $q->where('is_featured', true))
            ->orderByDesc('event_date')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Events/Index', [
            'events' => $events,
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['q', 'destination', 'status', 'featured']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Events/Form', [
            'event' => new Event(),
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
            'categories' => Category::where('type', 'event')->orderBy('name')->get(),
        ]);
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $data = $this->payload($request);
        $data['main_image'] = $this->storeImage($request->file('main_image'));

        Event::create($data);

        return redirect()->route('admin.events.index')->with('success', 'Event created.');
    }

    public function edit(Event $event): Response
    {
        return Inertia::render('Admin/Events/Form', [
            'event' => $event,
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
            'categories' => Category::where('type', 'event')->orderBy('name')->get(),
        ]);
    }

    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $data = $this->payload($request);
        $data['main_image'] = $this->replaceImage(
            $event,
            $request->file('main_image'),
            $request->boolean('remove_image')
        );

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', 'Event updated.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event deleted.');
    }

    /** @return array<string, mixed> */
    private function payload(EventRequest $request): array
    {
        return $request->safe()->only([
            'destination_id', 'category_id', 'name', 'venue', 'address', 'city', 'organiser',
            'event_date', 'starts_at', 'ends_at', 'capacity', 'base_price', 'discount_percent',
            'short_description', 'description', 'terms',
        ]) + [
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
