<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AdventureSlot;
use App\Models\DarshanSlot;
use App\Models\Event;
use App\Models\FoodExperience;
use App\Models\Hotel;
use App\Models\PoojaSlot;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class BookingInventoryService
{
    private const HORIZON_DAYS = 45;

    public function ensure(Model $model, string $type): void
    {
        match ($type) {
            'hotel' => $this->ensureHotelRoom($model),
            'event' => $this->ensureTicketType($model),
            'restaurant' => $this->ensureRestaurantSlots($model),
            'food' => $this->ensureFoodSlots($model),
            'adventure' => $this->ensureDatedSlots($model, $type),
            'darshan' => $this->ensureDatedSlots($model, $type),
            'pooja' => $this->ensureDatedSlots($model, $type),
            default => null,
        };
    }

    private function ensureHotelRoom(Model $model): void
    {
        if (! $model instanceof Hotel) {
            return;
        }

        if ($model->rooms()->where('is_active', true)->exists()) {
            return;
        }

        $model->rooms()->create([
            'name' => 'Standard Room',
            'slug' => $this->uniqueRoomSlug($model, 'standard-room'),
            'description' => $model->short_description,
            'room_type' => 'double',
            'max_adults' => 2,
            'max_children' => 1,
            'max_occupancy' => 3,
            'price' => $model->base_price ?: 0,
            'discount_percent' => 0,
            'total_rooms' => 5,
            'bed_type' => 'Double bed',
            'main_image' => $model->main_image,
            'breakfast_included' => false,
            'is_refundable' => true,
            'is_active' => true,
            'sort_order' => 0,
        ]);
    }

    private function uniqueRoomSlug(Hotel $hotel, string $base): string
    {
        $slug = $base;
        $suffix = 2;

        while ($hotel->rooms()->withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    private function ensureTicketType(Model $model): void
    {
        if (! $model instanceof Event) {
            return;
        }

        if ($model->ticketTypes()->where('is_active', true)->exists()) {
            return;
        }

        $model->ticketTypes()->create([
            'name' => 'General Admission',
            'description' => 'Standard entry to '.$model->name.'.',
            'price' => $model->base_price ?: 0,
            'discount_percent' => 0,
            'capacity' => max(1, (int) ($model->capacity ?: 100)),
            'per_order_limit' => 10,
            'is_active' => true,
            'sort_order' => 0,
        ]);
    }

    private function ensureRestaurantSlots(Model $model): void
    {
        if (! $model instanceof Restaurant) {
            return;
        }

        if ($model->slots()->where('is_active', true)->exists()) {
            return;
        }

        $capacity = max(4, (int) ($model->total_tables ?: 2) * 2);

        $slots = [
            ['12:00:00', '13:30:00'],
            ['19:00:00', '20:30:00'],
            ['21:00:00', '22:30:00'],
        ];

        foreach ($slots as [$start, $end]) {
            $model->slots()->create([
                'start_time' => $start,
                'end_time' => $end,
                'capacity' => $capacity,
                'tables' => $model->total_tables ?: 2,
                'is_active' => true,
            ]);
        }
    }

    private function ensureFoodSlots(Model $model): void
    {
        if (! $model instanceof FoodExperience) {
            return;
        }

        if ($model->slots()->where('is_active', true)->exists()) {
            return;
        }

        $capacity = max(1, (int) ($model->max_guests ?: 8));

        $slots = [
            ['11:00:00', '13:00:00'],
            ['17:00:00', '19:00:00'],
        ];

        foreach ($slots as [$start, $end]) {
            $model->slots()->create([
                'start_time' => $start,
                'end_time' => $end,
                'capacity' => $capacity,
                'is_active' => true,
            ]);
        }
    }

    private function ensureDatedSlots(Model $model, string $type): void
    {
        if (! in_array($type, ['adventure', 'darshan', 'pooja'], true)) {
            return;
        }

        $relation = $model->slots();

        $existing = $relation
            ->where('slot_date', '>=', today()->toDateString())
            ->pluck('slot_date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->all();

        [$startTime, $capacity] = $this->defaultSchedule($model, $type);

        $start = Carbon::parse(today()->toDateString().' '.$startTime);
        $duration = max(30, (int) ($model->duration_minutes ?? 120));

        if ($start->isPast()) {
            $start->addDay();
        }

        $until = today()->addDays(self::HORIZON_DAYS);

        for ($date = $start->copy(); $date->lte($until); $date->addDay()) {
            $day = $date->toDateString();

            if (in_array($day, $existing, true)) {
                continue;
            }

            $end = $date->copy()->addMinutes($duration);

            $attributes = [
                'slot_date' => $day,
                'start_time' => $date->format('H:i:s'),
                'end_time' => $end->format('H:i:s'),
                'is_active' => true,
            ];

            if ($type === 'adventure') {
                $attributes['capacity'] = $capacity;
            } elseif ($type === 'darshan') {
                $attributes['max_visitors'] = $capacity;
            } else {
                $attributes['max_participants'] = $capacity;
            }

            $relation->create($attributes);
        }
    }

    private function defaultSchedule(Model $model, string $type): array
    {
        return match ($type) {
            'adventure' => [
                '07:00:00',
                max(1, (int) ($model->max_participants ?? 10)),
            ],
            'darshan' => [
                '06:00:00',
                max(1, (int) ($model->max_visitors_per_booking ?? 50)),
            ],
            'pooja' => [
                '08:00:00',
                max(1, (int) ($model->max_participants ?? 20)),
            ],
            default => ['09:00:00', 10],
        };
    }

    public function upcomingSlots(Model $model, string $type): Collection
    {
        if ($type === 'adventure') {
            return $model->slots()
                ->where('is_active', true)
                ->where('slot_date', '>=', today()->toDateString())
                ->orderBy('slot_date')
                ->orderBy('start_time')
                ->limit(30)
                ->get()
                ->filter(fn (AdventureSlot $slot) => ! $slot->isPast() && $slot->remaining() > 0)
                ->values();
        }

        if ($type === 'darshan') {
            return $model->slots()
                ->where('is_active', true)
                ->where('slot_date', '>=', today()->toDateString())
                ->orderBy('slot_date')
                ->orderBy('start_time')
                ->limit(30)
                ->get()
                ->filter(fn (DarshanSlot $slot) => ! $slot->isPast() && $slot->remaining() > 0)
                ->values();
        }

        if ($type === 'pooja') {
            return $model->slots()
                ->where('is_active', true)
                ->where('slot_date', '>=', today()->toDateString())
                ->orderBy('slot_date')
                ->orderBy('start_time')
                ->limit(30)
                ->get()
                ->filter(fn (PoojaSlot $slot) => ! $slot->isPast() && $slot->remaining() > 0)
                ->values();
        }

        return collect();
    }

    public function serviceSlots(Model $model): Collection
    {
        return $model->slots()
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();
    }

    public function rooms(Hotel $hotel): Collection
    {
        return $hotel->rooms()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();
    }

    public function tickets(Event $event): Collection
    {
        return $event->ticketTypes()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}
