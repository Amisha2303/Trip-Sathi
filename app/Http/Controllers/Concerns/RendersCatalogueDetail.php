<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Support\Catalogue;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * One detail-page renderer for every catalogue type.
 *
 * Controllers only declare their type key and call renderCatalogueDetail();
 * routing, eager loads, wishlist state, related items and breadcrumbs are all
 * handled here so destinations/hotels/events/adventures/darshan/pooja/food
 * stay perfectly consistent.
 */
trait RendersCatalogueDetail
{
    protected function renderCatalogueDetail(string $type, string $slug, Request $request): View
    {
        $class = Catalogue::modelClass($type);
        abort_unless($class !== null, 404);

        // Destination is the root of the graph — it has no `destination()` relation.
        $relations = [
            'images',
            'reviews' => fn ($q) => $q->approved()->with('user')->latest()->limit(8),
        ];
        if ($type !== 'destination') {
            $relations[] = 'destination';
        }

        $model = $class::query()
            ->active()
            ->where('slug', $slug)
            ->with($relations)
            ->firstOrFail();

        $detail = Catalogue::detail($model, $type);

        $related = $this->relatedCatalogue($class, $model, $type);

        $wished = false;
        if ($request->user()) {
            $wished = $request->user()
                ->wishlistItems()
                ->where('type', Catalogue::wishType($type))
                ->pluck('wishlistable_id')
                ->contains($model->getKey());
        }

        $indexRoute = Catalogue::indexRoute($type);
        $crumbs = [
            ['label' => 'Home', 'url' => route('home')],
        ];
        if ($indexRoute) {
            $crumbs[] = ['label' => Catalogue::label($type), 'url' => route($indexRoute)];
        }
        $crumbs[] = ['label' => $model->name];

        return view('catalogue.show', [
            'detail' => $detail,
            'model' => $model,
            'reviews' => $model->reviews->values(),
            'related' => $related,
            'relatedLabel' => $this->relatedLabel($type),
            'relatedIndexUrl' => $indexRoute ? route($indexRoute) : null,
            'wished' => $wished,
            'crumbs' => $crumbs,
        ]);
    }

    /** Same-destination siblings first, then featured fallback, capped at 3. */
    private function relatedCatalogue(string $class, object $model, string $type): \Illuminate\Support\Collection
    {
        $base = $class::query()->active()->whereKeyNot($model->getKey());

        $siblings = $model->destination_id ?? null;
        $items = $siblings
            ? $base->where('destination_id', $siblings)->take(3)->get()
            : collect();

        if ($items->count() < 3) {
            $filler = $base->whereKeyNot($model->getKey())
                ->orderByDesc('is_featured')
                ->take(3)
                ->get();
            $items = $items->concat($filler)->unique('id')->take(3);
        }

        return Catalogue::cards($items, $type);
    }

    private function relatedLabel(string $type): string
    {
        return match ($type) {
            'destination' => 'destinations',
            'hotel' => 'stays',
            'event' => 'events',
            'adventure' => 'activities',
            'darshan' => 'darshan slots',
            'pooja' => 'pooja services',
            'restaurant', 'food' => 'dining picks',
            default => 'listings',
        };
    }
}
