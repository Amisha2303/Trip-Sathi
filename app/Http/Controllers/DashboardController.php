<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $bookings = $user->bookings();

        $stats = [
            'total' => (clone $bookings)->count(),
            'upcoming' => (clone $bookings)->upcoming()->count(),
            'completed' => (clone $bookings)->where('status', 'completed')->count(),
            'cancelled' => (clone $bookings)->where('status', 'cancelled')->count(),
            'trips' => $user->trips()->count(),
            'wishlist' => $user->wishlistItems()->count(),
        ];

        $upcomingBookings = (clone $bookings)
            ->upcoming()
            ->with(['destination', 'items'])
            ->orderBy('booking_date')
            ->limit(4)
            ->get();

        $recentBookings = (clone $bookings)
            ->with(['destination', 'items'])
            ->latest()
            ->limit(5)
            ->get();

        $recommendations = Destination::query()
            ->active()
            ->featured()
            ->withCount(['hotels' => fn ($q) => $q->active()])
            ->orderByDesc('popularity')
            ->limit(4)
            ->get();

        $notifications = $user->notifications()->latest()->limit(5)->get();

        return \Inertia\Inertia::render('Account/Dashboard', [
            'stats' => $stats,
            'upcomingBookings' => $upcomingBookings,
            'recentBookings' => $recentBookings,
            'recommendations' => $recommendations,
            'notifications' => $notifications,
        ]);
    }
}
