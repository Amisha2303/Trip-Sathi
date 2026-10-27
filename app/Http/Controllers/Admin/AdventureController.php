<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageUpload;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdventureRequest;
use App\Models\Adventure;
use App\Models\Category;
use App\Models\Destination;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdventureController extends Controller
{
    use HandlesImageUpload;

    public function index(Request $request): Response
    {
        $adventures = Adventure::query()
            ->with(['destination:id,name', 'category:id,name'])
            ->search($request->string('q')->toString(), ['name', 'activity_type', 'meeting_point'])
            ->when($request->filled('destination'), fn ($q) => $q->where('destination_id', (int) $request->input('destination')))
            ->when($request->filled('status'), fn ($q) => $request->string('status') === 'active'
                ? $q->where('is_active', true)
                : $q->where('is_active', false))
            ->when($request->boolean('featured'), fn ($q) => $q->where('is_featured', true))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Adventures/Index', [
            'adventures' => $adventures,
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
            'difficulties' => \App\Enums\Difficulty::options(),
            'filters' => $request->only(['q', 'destination', 'status', 'featured']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Adventures/Form', [
            'adventure' => new Adventure(),
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
            'categories' => Category::where('type', 'adventure')->orderBy('name')->get(),
            'difficulties' => \App\Enums\Difficulty::options(),
        ]);
    }

    public function store(AdventureRequest $request): RedirectResponse
    {
        $data = $this->payload($request);
        $data['main_image'] = $this->storeImage($request->file('main_image'));

        Adventure::create($data);

        return redirect()->route('admin.adventures.index')->with('success', 'Adventure created.');
    }

    public function edit(Adventure $adventure): Response
    {
        return Inertia::render('Admin/Adventures/Form', [
            'adventure' => $adventure,
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
            'categories' => Category::where('type', 'adventure')->orderBy('name')->get(),
            'difficulties' => \App\Enums\Difficulty::options(),
        ]);
    }

    public function update(AdventureRequest $request, Adventure $adventure): RedirectResponse
    {
        $data = $this->payload($request);
        $data['main_image'] = $this->replaceImage(
            $adventure,
            $request->file('main_image'),
            $request->boolean('remove_image')
        );

        $adventure->update($data);

        return redirect()->route('admin.adventures.index')->with('success', 'Adventure updated.');
    }

    public function destroy(Adventure $adventure): RedirectResponse
    {
        $adventure->delete();

        return redirect()->route('admin.adventures.index')->with('success', 'Adventure deleted.');
    }

    /** @return array<string, mixed> */
    private function payload(AdventureRequest $request): array
    {
        return $request->safe()->only([
            'destination_id', 'category_id', 'name', 'activity_type', 'short_description',
            'description', 'price', 'discount_percent', 'duration_minutes', 'difficulty',
            'min_age', 'max_age', 'max_participants', 'min_participants', 'meeting_point',
            'inclusions', 'safety_instructions', 'cancellation_policy',
        ]) + [
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
