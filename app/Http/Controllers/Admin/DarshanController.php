<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageUpload;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DarshanRequest;
use App\Models\Category;
use App\Models\Darshan;
use App\Models\Destination;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DarshanController extends Controller
{
    use HandlesImageUpload;

    private const TYPES = ['vip' => 'VIP Darshan', 'regular' => 'Regular', 'special' => 'Special', 'online' => 'Online'];

    public function index(Request $request): View
    {
        $darshans = Darshan::query()
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

        return view('admin.darshan.index', [
            'darshans' => $darshans,
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
            'types' => self::TYPES,
        ]);
    }

    public function create(): View
    {
        return view('admin.darshan.form', [
            'darshan' => new Darshan(),
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
            'categories' => Category::where('type', 'darshan')->orderBy('name')->get(),
            'types' => self::TYPES,
        ]);
    }

    public function store(DarshanRequest $request): RedirectResponse
    {
        $data = $this->payload($request);
        $data['main_image'] = $this->storeImage($request->file('main_image'));

        Darshan::create($data);

        return redirect()->route('admin.darshan.index')->with('success', 'Darshan created.');
    }

    public function edit(Darshan $darshan): View
    {
        return view('admin.darshan.form', [
            'darshan' => $darshan,
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
            'categories' => Category::where('type', 'darshan')->orderBy('name')->get(),
            'types' => self::TYPES,
        ]);
    }

    public function update(DarshanRequest $request, Darshan $darshan): RedirectResponse
    {
        $data = $this->payload($request);
        $data['main_image'] = $this->replaceImage(
            $darshan,
            $request->file('main_image'),
            $request->boolean('remove_image')
        );

        $darshan->update($data);

        return redirect()->route('admin.darshan.index')->with('success', 'Darshan updated.');
    }

    public function destroy(Darshan $darshan): RedirectResponse
    {
        $darshan->delete();

        return redirect()->route('admin.darshan.index')->with('success', 'Darshan deleted.');
    }

    /** @return array<string, mixed> */
    private function payload(DarshanRequest $request): array
    {
        return $request->safe()->only([
            'destination_id', 'category_id', 'name', 'temple_name', 'deity', 'darshan_type',
            'temple_address', 'short_description', 'description', 'price', 'discount_percent',
            'duration_minutes', 'max_visitors_per_booking', 'max_visitors_per_day',
            'instructions', 'dress_code', 'cancellation_policy',
        ]) + [
            'documents_required' => $this->lines($request->input('documents_required')),
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
