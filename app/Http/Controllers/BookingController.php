<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $statuses = [...BookingStatus::values(), 'upcoming'];

        $query = $request->user()->bookings()->with(['destination', 'items']);

        if ($status === 'upcoming') {
            $query->upcoming();
        } elseif (in_array($status, BookingStatus::values(), true)) {
            $query->where('status', $status);
        } else {
            $status = null;
        }

        $bookings = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'all' => $request->user()->bookings()->count(),
            'upcoming' => $request->user()->bookings()->upcoming()->count(),
            'pending' => $request->user()->bookings()->where('status', 'pending')->count(),
            'completed' => $request->user()->bookings()->where('status', 'completed')->count(),
            'cancelled' => $request->user()->bookings()->where('status', 'cancelled')->count(),
        ];

        return view('account.bookings', [
            'bookings' => $bookings,
            'status' => $status,
            'statuses' => BookingStatus::cases(),
            'counts' => $counts,
        ]);
    }
}
