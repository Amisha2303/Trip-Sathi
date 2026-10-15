<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\WishlistType;
use App\Models\Wishlist;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type');

        if (! in_array($type, WishlistType::values(), true)) {
            $type = null;
        }

        $items = $request->user()
            ->wishlistItems()
            ->with('wishlistable')
            ->when($type, fn ($q) => $q->where('type', $type))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $counts = [];
        foreach (WishlistType::cases() as $case) {
            $counts[$case->value] = $request->user()->wishlistItems()->where('type', $case->value)->count();
        }

        return view('account.wishlist', [
            'items' => $items,
            'type' => $type,
            'types' => WishlistType::cases(),
            'counts' => $counts,
            'total' => $request->user()->wishlistItems()->count(),
        ]);
    }

    /** AJAX toggle used by the heart button on catalogue cards. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:' . implode(',', WishlistType::values())],
            'id' => ['required', 'integer', 'min:1'],
        ]);

        $type = WishlistType::from($data['type']);
        $model = $type->modelClass();

        $record = $model::query()->findOrFail((int) $data['id']);

        $existing = Wishlist::query()
            ->where('user_id', $request->user()->getKey())
            ->where('wishlistable_type', $type->value)
            ->where('wishlistable_id', $record->getKey())
            ->first();

        if ($existing !== null) {
            $existing->delete();
            $saved = false;
            $message = 'Removed from your wishlist.';
        } else {
            Wishlist::create([
                'user_id' => $request->user()->getKey(),
                'wishlistable_type' => $type->value,
                'wishlistable_id' => $record->getKey(),
                'type' => $type->value,
            ]);
            $saved = true;
            $message = 'Saved to your wishlist.';
        }

        $count = $request->user()->wishlistItems()->count();

        if ($request->expectsJson()) {
            return response()->json([
                'saved' => $saved,
                'count' => $count,
                'message' => $message,
            ]);
        }

        return back()->with($saved ? 'success' : 'info', $message);
    }

    public function destroy(Request $request, Wishlist $wishlist)
    {
        abort_unless($wishlist->user_id === $request->user()->getKey(), 403);

        $wishlist->delete();

        if ($request->expectsJson()) {
            return response()->json(['saved' => false, 'message' => 'Removed from your wishlist.']);
        }

        return back()->with('success', 'Removed from your wishlist.');
    }
}
