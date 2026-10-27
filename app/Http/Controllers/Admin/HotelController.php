<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageUpload;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HotelRequest;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Hotel;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    use HandlesImageUpload;

    public function index(Request $request): Response
    {
        $hotels = Hotel::query()
            ->with(['destination:id,name', 'category:id,name'])
            ->search($request->string('q')->toString(), ['name', 'address', 'city'])
            ->when($request->filled('destination'), fn ($q) => $q->where('destination_id', (int) $request->input('destination')))
            ->when($request->filled('status'), fn ($q) => $request->string('status') === 'active'
                ? $q->where('is_active', true)
                : $q->where('is_active', false))
            ->when($request->boolean('featured'), fn ($q) => $q->where('is_featured', true))
            ->orderByDesc('rating')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Hotels/Index', [
            'hotels' => $hotels,
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['q', 'destination', 'status', 'featured']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Hotels/Form', [
            'hotel' => new Hotel(),
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
            'categories' => Category::where('type', 'hotel')->orderBy('name')->get(),
        ]);
    }

    public function store(HotelRequest $request): RedirectResponse
    {
        $data = $this->payload($request);
        $data['main_image'] = $this->storeImage($request->file('main_image'));

        Hotel::create($data);

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel created.');
    }

    public function edit(Hotel $hotel): Response
    {
        return Inertia::render('Admin/Hotels/Form', [
            'hotel' => $hotel,
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
            'categories' => Category::where('type', 'hotel')->orderBy('name')->get(),
        ]);
    }

    public function update(HotelRequest $request, Hotel $hotel): RedirectResponse
    {
        $data = $this->payload($request);
        $data['main_image'] = $this->replaceImage(
            $hotel,
            $request->file('main_image'),
            $request->boolean('remove_image')
        );

        $hotel->update($data);

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel updated.');
    }

    public function destroy(Hotel $hotel): RedirectResponse
    {
        $hotel->delete();

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel deleted.');
    }

    /** @return array<string, mixed> */
    private function payload(HotelRequest $request): array
    {
        return $request->safe()->only([
            'destination_id', 'category_id', 'name', 'address', 'city', 'state', 'pincode',
            'short_description', 'description', 'star_rating', 'base_price', 'discount_percent',
            'contact_phone', 'contact_email', 'check_in_time', 'check_out_time', 'cancellation_policy',
        ]) + [
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
