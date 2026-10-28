<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageUpload;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DestinationRequest;
use App\Models\Category;
use App\Models\Destination;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    use HandlesImageUpload;

    public function index(Request $request): Response
    {
        $destinations = Destination::query()
            ->with('category:id,name')
            ->search($request->string('q')->toString(), ['name', 'city', 'state', 'country'])
            ->when($request->filled('status'), fn ($q) => $request->string('status') === 'active'
                ? $q->where('is_active', true)
                : $q->where('is_active', false))
            ->when($request->boolean('featured'), fn ($q) => $q->where('is_featured', true))
            ->orderByDesc('popularity')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Destinations/Index', [
            'destinations' => $destinations,
            'filters' => $request->only(['q', 'status', 'featured']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Destinations/Form', [
            'destination' => new Destination(),
            'categories' => Category::where('type', 'destination')->orderBy('name')->get(),
        ]);
    }

    public function store(DestinationRequest $request): RedirectResponse
    {
        $data = $this->payload($request);
        $data['main_image'] = $this->storeImage($request->file('main_image'));

        Destination::create($data);

        Destination::flushCatalogueCache();

        return redirect()->route('admin.destinations.index')->with('success', 'Destination created.');
    }

    public function edit(Destination $destination): Response
    {
        return Inertia::render('Admin/Destinations/Form', [
            'destination' => $destination,
            'categories' => Category::where('type', 'destination')->orderBy('name')->get(),
        ]);
    }

    public function update(DestinationRequest $request, Destination $destination): RedirectResponse
    {
        $data = $this->payload($request);
        $data['main_image'] = $this->replaceImage(
            $destination,
            $request->file('main_image'),
            $request->boolean('remove_image')
        );

        $destination->update($data);

        Destination::flushCatalogueCache();

        return redirect()->route('admin.destinations.index')->with('success', 'Destination updated.');
    }

    public function destroy(Destination $destination): RedirectResponse
    {
        try {
            $destination->delete();
            Destination::flushCatalogueCache();

            return redirect()->route('admin.destinations.index')->with('success', 'Destination deleted.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Cannot delete destination: ' . $e->getMessage());
        }
    }

    /** @return array<string, mixed> */
    private function payload(DestinationRequest $request): array
    {
        return $request->safe()->only([
            'name', 'category_id', 'country', 'state', 'city', 'short_description',
            'description', 'best_time_to_visit', 'latitude', 'longitude', 'language',
        ]) + [
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
