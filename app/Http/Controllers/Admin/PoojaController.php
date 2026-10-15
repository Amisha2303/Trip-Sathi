<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageUpload;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PoojaRequest;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Pooja;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PoojaController extends Controller
{
    use HandlesImageUpload;

    public function index(Request $request): View
    {
        $poojas = Pooja::query()
            ->with(['destination:id,name', 'category:id,name'])
            ->search($request->string('q')->toString(), ['name', 'temple_name', 'deity'])
            ->when($request->filled('destination'), fn ($q) => $q->where('destination_id', (int) $request->input('destination')))
            ->when($request->filled('status'), fn ($q) => $request->string('status') === 'active'
                ? $q->where('is_active', true)
                : $q->where('is_active', false))
            ->when($request->boolean('featured'), fn ($q) => $q->where('is_featured', true))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.pooja.index', [
            'poojas' => $poojas,
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        return view('admin.pooja.form', [
            'pooja' => new Pooja(),
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
            'categories' => Category::where('type', 'pooja')->orderBy('name')->get(),
        ]);
    }

    public function store(PoojaRequest $request): RedirectResponse
    {
        $data = $this->payload($request);
        $data['main_image'] = $this->storeImage($request->file('main_image'));

        Pooja::create($data);

        return redirect()->route('admin.pooja.index')->with('success', 'Pooja created.');
    }

    public function edit(Pooja $pooja): View
    {
        return view('admin.pooja.form', [
            'pooja' => $pooja,
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
            'categories' => Category::where('type', 'pooja')->orderBy('name')->get(),
        ]);
    }

    public function update(PoojaRequest $request, Pooja $pooja): RedirectResponse
    {
        $data = $this->payload($request);
        $data['main_image'] = $this->replaceImage(
            $pooja,
            $request->file('main_image'),
            $request->boolean('remove_image')
        );

        $pooja->update($data);

        return redirect()->route('admin.pooja.index')->with('success', 'Pooja updated.');
    }

    public function destroy(Pooja $pooja): RedirectResponse
    {
        $pooja->delete();

        return redirect()->route('admin.pooja.index')->with('success', 'Pooja deleted.');
    }

    /** @return array<string, mixed> */
    private function payload(PoojaRequest $request): array
    {
        return $request->safe()->only([
            'destination_id', 'category_id', 'name', 'temple_name', 'deity',
            'short_description', 'description', 'benefits', 'duration_minutes',
            'max_participants', 'min_participants', 'price', 'discount_percent',
            'requirements', 'process', 'dress_code', 'cancellation_policy',
        ]) + [
            'includes' => $this->lines($request->input('includes')),
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    /** @return array<int, string> */
    private function lines(mixed $text): array
    {
        if (! is_string($text)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/\R+/', $text) ?: [])));
    }
}
