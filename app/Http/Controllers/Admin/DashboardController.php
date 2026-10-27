<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Adventure;
use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\Darshan;
use App\Models\Destination;
use App\Models\Event;
use App\Models\Hotel;
use App\Models\Pooja;
use App\Models\Trip;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $revenue = (int) Booking::query()
            ->whereIn('payment_status', [PaymentStatus::Paid->value, PaymentStatus::PartiallyRefunded->value])
            ->sum('final_amount');

        $kpis = [
            ['label' => 'Revenue', 'value' => \App\Support\Money::format($revenue), 'icon' => 'bi-currency-rupee', 'note' => 'Paid bookings'],
            ['label' => 'Bookings', 'value' => Booking::query()->count(), 'icon' => 'bi-ticket-perforated', 'note' => Booking::query()->where('booking_date', '>=', today())->count() . ' dated today onwards'],
            ['label' => 'Customers', 'value' => User::query()->count(), 'icon' => 'bi-people', 'note' => User::query()->where('is_admin', false)->count() . ' travellers'],
            ['label' => 'Catalogue', 'value' => $this->catalogueCount(), 'icon' => 'bi-collection', 'note' => 'Live listings across all categories'],
        ];

        $statusBreakdown = Booking::query()
            ->select('status', DB::raw('COUNT(*) as total'), DB::raw('COALESCE(SUM(final_amount), 0) as amount'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        $recentBookings = Booking::query()->with(['user', 'destination'])->latest()->limit(8)->get();

        $recentUsers = User::query()->latest()->limit(6)->get();

        $pendingMessages = ContactMessage::query()->where('is_read', false)->latest()->limit(5)->get();
        $unreadMessages = ContactMessage::query()->where('is_read', false)->count();

        $topDestinations = Destination::query()
            ->active()
            ->withCount(['bookings', 'hotels' => fn ($q) => $q->active()])
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'kpis' => $kpis,
            'statusBreakdown' => $statusBreakdown,
            'recentBookings' => $recentBookings,
            'recentUsers' => $recentUsers,
            'pendingMessages' => $pendingMessages,
            'unreadMessages' => $unreadMessages,
            'topDestinations' => $topDestinations,
            'statusLabels' => BookingStatus::options(),
        ]);
    }

    private function catalogueCount(): int
    {
        return Destination::query()->active()->count()
            + Hotel::query()->active()->count()
            + Event::query()->active()->count()
            + Adventure::query()->active()->count()
            + Darshan::query()->active()->count()
            + Pooja::query()->active()->count()
            + Trip::query()->count();
    }
}
