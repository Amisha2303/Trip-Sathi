<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Adventure;
use App\Models\Darshan;
use App\Models\Destination;
use App\Models\Event;
use App\Models\FoodExperience;
use App\Models\Hotel;
use App\Models\Pooja;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Normalises every catalogue model into one shape so a single detail view and
 * a single card partial can render destinations, hotels, events, adventures,
 * darshan, pooja, restaurants and food experiences without eight copies.
 */
final class Catalogue
{
    /** Canonical type key → model class. */
    public const TYPES = [
        'destination' => Destination::class,
        'hotel' => Hotel::class,
        'event' => Event::class,
        'adventure' => Adventure::class,
        'darshan' => Darshan::class,
        'pooja' => Pooja::class,
        'restaurant' => Restaurant::class,
        'food' => FoodExperience::class,
    ];

    /** Named route for a type's listing page (null for food until wired). */
    public static function indexRoute(string $type): ?string
    {
        return match ($type) {
            'destination' => 'destinations.index',
            'hotel' => 'hotels.index',
            'event' => 'events.index',
            'adventure' => 'adventures.index',
            'darshan' => 'darshan.index',
            'pooja' => 'pooja.index',
            'restaurant' => 'food.index',
            'food' => 'food.index',
            default => null,
        };
    }

    public static function showRoute(string $type): string
    {
        return match ($type) {
            'destination' => 'destinations.show',
            'hotel' => 'hotels.show',
            'event' => 'events.show',
            'adventure' => 'adventures.show',
            'darshan' => 'darshan.show',
            'pooja' => 'pooja.show',
            'restaurant' => 'food.show',
            'food' => 'food.show',
            default => 'home',
        };
    }

    /** The wishlist / placeholder type key (food maps to restaurant). */
    public static function wishType(string $type): string
    {
        return $type === 'food' ? 'restaurant' : $type;
    }

    /** Resolve the model class for a type key. */
    public static function modelClass(string $type): ?string
    {
        return self::TYPES[$type] ?? null;
    }

    /**
     * Build the detail view-model for a catalogue model.
     *
     * @return array<string, mixed>
     */
    public static function detail(Model $model, string $type): array
    {
        $gallery = method_exists($model, 'images')
            ? $model->images()->ordered()->limit(8)->get()->map(fn ($img) => [
                'url' => $img->url,
                'caption' => $img->caption,
            ])->values()->all()
            : [];

        $base = [
            'type' => $type,
            'wishType' => self::wishType($type),
            'wishId' => $model->getKey(),
            'name' => (string) $model->name,
            'eyebrow' => self::eyebrow($type),
            'location' => self::location($model, $type),
            'lede' => $model->short_description ?? null,
            'description' => (string) ($model->description ?? ''),
            'mainImage' => $model->image_url,
            'gallery' => $gallery,
            'rating' => isset($model->rating) && $model->rating !== null ? (float) $model->rating : null,
            'reviewCount' => (int) ($model->review_count ?? ($model->reviews()->approved()->count() ?? 0)),
            'stars' => $model->star_rating ?? null,
            'price' => self::price($model),
            'priceNote' => self::priceNote($type),
            'badge' => $model->is_featured ? 'Featured' : null,
            'facts' => self::facts($model, $type),
            'sections' => self::sections($model, $type),
            'cta' => self::cta($model, $type),
        ];

        return $base;
    }

    /**
     * Build a card-ready array for the shared item-card partial.
     *
     * @return array<string, mixed>
     */
    public static function card(Model $model, string $type): array
    {
        $wishType = self::wishType($type);
        $route = self::showRoute($type);

        return [
            'image' => $model->image_url,
            'title' => $model->name,
            'subtitle' => self::location($model, $type),
            'text' => $model->short_description
                ?? (Str::limit((string) $model->description, 110) ?: null),
            'badge' => $model->is_featured ? 'Featured' : null,
            'meta' => self::cardMeta($model, $type),
            'stars' => $model->star_rating ?? null,
            'rating' => isset($model->rating) && $model->rating !== null ? (float) $model->rating : null,
            'ratingNote' => ($model->review_count ?? 0) > 0 ? (int) $model->review_count.' reviews' : null,
            'price' => self::price($model),
            'priceNote' => self::priceNote($type),
            'wishType' => $wishType,
            'wishId' => $model->getKey(),
            'placeholder' => asset('images/'.$wishType.'-placeholder.svg'),
            'cta' => 'View details',
            'ctaUrl' => route($route, ['slug' => $model->slug]),
        ];
    }

    /** Map a paginator/collection of models to card arrays. */
    public static function cards(iterable $models, string $type): Collection
    {
        return collect($models)->map(fn (Model $m) => self::card($m, $type))->values();
    }

    // ---------------------------------------------------------------------
    // Pieces
    // ---------------------------------------------------------------------

    private static function eyebrow(string $type): string
    {
        return match ($type) {
            'destination' => 'Destination',
            'hotel' => 'Stay',
            'event' => 'Event',
            'adventure' => 'Adventure',
            'darshan' => 'VIP Darshan',
            'pooja' => 'Pooja Vidhi',
            'restaurant' => 'Dining',
            'food' => 'Food experience',
            default => 'Explore',
        };
    }

    private static function location(Model $model, string $type): ?string
    {
        if ($type === 'destination') {
            return $model->location;
        }

        $destination = $model->relationLoaded('destination') ? $model->destination : null;
        $place = collect([
            $model->city ?? null,
            $model->state ?? null,
            $destination?->name,
        ])->filter()->unique()->implode(', ');

        if ($place !== '') {
            return $place;
        }

        return match ($type) {
            'restaurant' => $model->address ?? null,
            'darshan', 'pooja' => $model->temple_address ?? $model->temple_name ?? null,
            default => null,
        };
    }

    private static function price(Model $model): ?float
    {
        if (isset($model->effective_price) && $model->effective_price !== null) {
            return (float) $model->effective_price;
        }

        if ($model instanceof Restaurant) {
            return $model->average_cost > 0 ? (float) $model->average_cost : null;
        }

        foreach (['price', 'base_price'] as $key) {
            if (isset($model->{$key}) && (float) $model->{$key} > 0) {
                return (float) $model->{$key};
            }
        }

        return null;
    }

    private static function priceNote(string $type): ?string
    {
        return match ($type) {
            'hotel' => 'per night',
            'event' => 'per ticket',
            'adventure' => 'per person',
            'darshan' => 'per person',
            'pooja' => 'per pooja',
            'food' => 'per person',
            'restaurant' => 'avg. for two',
            default => null,
        };
    }

    /** Small label/meta chips shown on cards. */
    private static function cardMeta(Model $model, string $type): array
    {
        $meta = [];

        if ($type === 'adventure' && $model->difficulty_label) {
            $meta['bi-speedometer2'] = $model->difficulty_label;
        }

        if ($type === 'event' && $model->event_date) {
            $meta['bi-calendar-event'] = Carbon::parse($model->event_date)->format('d M Y');
        }

        if ($type === 'restaurant' && $model->cuisine) {
            $meta['bi-cup-hot'] = $model->cuisine;
        }

        if ($type === 'food' && $model->cuisine) {
            $meta['bi-cup-hot'] = $model->cuisine;
        }

        if ($type === 'darshan' && $model->temple_name) {
            $meta['bi-building'] = $model->temple_name;
        }

        if ($type === 'pooja' && $model->temple_name) {
            $meta['bi-building'] = $model->temple_name;
        }

        return $meta;
    }

    /** Fact grid shown on detail pages: [icon, label, value]. */
    private static function facts(Model $model, string $type): array
    {
        $facts = [];
        $add = function (string $icon, string $label, mixed $value) use (&$facts): void {
            if ($value === null || $value === '' || $value === false) {
                return;
            }
            if (is_bool($value)) {
                $value = 'Yes';
            }
            if (is_array($value)) {
                $value = implode(', ', array_filter(array_map(
                    static fn (mixed $item): string => is_scalar($item) ? (string) $item : '',
                    $value
                )));
            }
            if ($value === '') {
                return;
            }
            $facts[] = ['icon' => $icon, 'label' => $label, 'value' => (string) $value];
        };

        match ($type) {
            'destination' => [
                $add('bi-geo-alt', 'State', $model->state),
                $add('bi-globe2', 'Country', $model->country),
                $add('bi-calendar2-week', 'Best time to visit', $model->best_time_to_visit),
                $add('bi-translate', 'Languages', $model->language),
                $add('bi-cash-coin', 'Currency', $model->currency),
                $add('bi-clock-history', 'Timezone', $model->timezone),
            ],
            'hotel' => [
                $add('bi-star-fill', 'Star rating', $model->star_rating ? $model->star_rating.' star' : null),
                $add('bi-box-arrow-in-right', 'Check-in', $model->check_in_time),
                $add('bi-box-arrow-right', 'Check-out', $model->check_out_time),
                $add('bi-calendar-x', 'Min nights', $model->min_nights),
                $add('bi-shield-check', 'Cancellation', $model->cancellation_policy),
            ],
            'event' => [
                $add('bi-calendar-event', 'Date', $model->event_date ? Carbon::parse($model->event_date)->format('D, d M Y') : null),
                $add('bi-clock', 'Time', $model->starts_at ? Carbon::parse($model->starts_at)->format('h:i A') : null),
                $add('bi-geo-alt', 'Venue', $model->venue),
                $add('bi-person', 'Organiser', $model->organiser),
                $add('bi-people', 'Capacity', $model->capacity),
            ],
            'adventure' => [
                $add('bi-speedometer2', 'Difficulty', $model->difficulty_label),
                $add('bi-clock', 'Duration', $model->duration_minutes ? self::duration($model->duration_minutes) : null),
                $add('bi-activity', 'Activity', $model->activity_type),
                $add('bi-person-bounding-box', 'Ages', self::range($model->min_age, $model->max_age, ' yrs')),
                $add('bi-people', 'Group size', self::range($model->min_participants, $model->max_participants, '')),
                $add('bi-geo-alt', 'Meeting point', $model->meeting_point),
            ],
            'darshan' => [
                $add('bi-building', 'Temple', $model->temple_name),
                $add('bi-stars', 'Deity', $model->deity),
                $add('bi-ticket-perforated', 'Type', $model->darshan_type),
                $add('bi-clock', 'Duration', $model->duration_minutes ? self::duration($model->duration_minutes) : null),
                $add('bi-people', 'Visitors / booking', $model->max_visitors_per_booking),
                $add('bi-person-vcard', 'Dress code', $model->dress_code),
                $add('bi-card-checklist', 'Documents', $model->documents_required),
            ],
            'pooja' => [
                $add('bi-building', 'Temple', $model->temple_name),
                $add('bi-stars', 'Deity', $model->deity),
                $add('bi-clock', 'Duration', $model->duration_minutes ? self::duration($model->duration_minutes) : null),
                $add('bi-people', 'Participants', self::range($model->min_participants, $model->max_participants, '')),
                $add('bi-person-vcard', 'Dress code', $model->dress_code),
            ],
            'restaurant' => [
                $add('bi-cup-hot', 'Cuisine', $model->cuisine),
                $add('bi-currency-dollar', 'Price range', $model->price_range),
                $add('bi-clock', 'Hours', $model->opening_hours),
                $add('bi-geo-alt', 'Address', $model->address),
                $add('bi-calendar-check', 'Reservations', $model->online_reservation ? 'Online booking available' : 'Walk-in only'),
            ],
            'food' => [
                $add('bi-cup-hot', 'Cuisine', $model->cuisine),
                $add('bi-clock', 'Duration', $model->duration_minutes ? self::duration($model->duration_minutes) : null),
                $add('bi-people', 'Guests', self::range($model->min_guests, $model->max_guests, '')),
                $add('bi-person', 'Hosted by', $model->restaurant?->name),
            ],
            default => [],
        };

        return $facts;
    }

    /**
     * Rich type-specific content blocks rendered under the fact grid.
     *
     * @return array<int, array{title: string, body: string, list?: array<int, string>}>
     */
    private static function sections(Model $model, string $type): array
    {
        $sections = [];
        $paragraphs = fn (mixed $text): ?string => self::paragraphs($text);

        if ($type === 'adventure') {
            if ($list = self::toList($model->inclusions)) {
                $sections[] = ['title' => "What's included", 'list' => $list];
            }
            if ($list = self::toList($model->equipment)) {
                $sections[] = ['title' => 'Equipment provided', 'list' => $list];
            }
            if ($body = $paragraphs($model->safety_instructions)) {
                $sections[] = ['title' => 'Safety instructions', 'body' => $body];
            }
            if ($body = $paragraphs($model->cancellation_policy)) {
                $sections[] = ['title' => 'Cancellation policy', 'body' => $body];
            }
        }

        if ($type === 'hotel') {
            if ($body = $paragraphs($model->policies)) {
                $sections[] = ['title' => 'House policies', 'body' => $body];
            }
            if ($body = $paragraphs($model->cancellation_policy)) {
                $sections[] = ['title' => 'Cancellation policy', 'body' => $body];
            }
        }

        if ($type === 'event') {
            if ($body = $paragraphs($model->terms)) {
                $sections[] = ['title' => 'Terms & conditions', 'body' => $body];
            }
        }

        if ($type === 'darshan') {
            if ($body = $paragraphs($model->instructions)) {
                $sections[] = ['title' => 'Before you go', 'body' => $body];
            }
            if ($body = $paragraphs($model->cancellation_policy)) {
                $sections[] = ['title' => 'Cancellation policy', 'body' => $body];
            }
        }

        if ($type === 'pooja') {
            if ($body = $paragraphs($model->process)) {
                $sections[] = ['title' => 'How it is performed', 'body' => $body];
            }
            if ($list = self::toList($model->includes)) {
                $sections[] = ['title' => "What's included", 'list' => $list];
            }
            if ($list = self::toList($model->requirements)) {
                $sections[] = ['title' => 'What you should arrange', 'list' => $list];
            }
            if ($body = $paragraphs($model->benefits)) {
                $sections[] = ['title' => 'Why perform this pooja', 'body' => $body];
            }
        }

        if ($type === 'food') {
            if ($list = self::toList($model->includes)) {
                $sections[] = ['title' => "What's included", 'list' => $list];
            }
            if ($list = self::toList($model->menu_highlights)) {
                $sections[] = ['title' => 'Menu highlights', 'list' => $list];
            }
            if ($body = $paragraphs($model->safety_notes)) {
                $sections[] = ['title' => 'Good to know', 'body' => $body];
            }
        }

        return $sections;
    }

    private static function cta(Model $model, string $type): array
    {
        $label = match ($type) {
            'hotel' => 'Book this stay',
            'event' => 'Buy tickets',
            'adventure' => 'Book this activity',
            'darshan' => 'Book VIP darshan',
            'pooja' => 'Book this pooja',
            'restaurant' => 'Reserve a table',
            'food' => 'Book this experience',
            default => 'Explore stays nearby',
        };

        $url = $type === 'destination'
            ? route('hotels.index', ['destination' => $model->slug])
            : route('bookings.create', ['type' => $type, 'slug' => $model->slug]);

        return ['label' => $label, 'url' => $url, 'icon' => 'bi-arrow-right'];
    }

    // ---------------------------------------------------------------------
    // Small formatters
    // ---------------------------------------------------------------------

    /** Newline / list text → safe HTML paragraphs. */
    public static function paragraphs(?string $text): ?string
    {
        $text = trim((string) $text);
        if ($text === '') {
            return null;
        }

        $safe = e($text);
        $parts = preg_split('/\R{2,}/u', $safe) ?: [$safe];

        return collect($parts)
            ->map(fn (string $p) => '<p>'.nl2br(trim($p)).'</p>')
            ->implode('');
    }

    /** JSON / newline / comma list → array of clean strings. */
    private static function toList(mixed $value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $value = $decoded;
            } else {
                $value = preg_split('/[\n,]+/u', $value) ?: [];
            }
        }

        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->map(fn ($v) => trim(strip_tags((string) $v)))
            ->filter()
            ->values()
            ->all();
    }

    private static function duration(int $minutes): string
    {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;

        return trim(($h ? "{$h} hr" : '').($m ? " {$m} min" : ''));
    }

    private static function range(?int $min, ?int $max, string $suffix): ?string
    {
        if ($min === null && $max === null) {
            return null;
        }
        if ($min !== null && $max !== null) {
            return $min === $max ? "{$min}{$suffix}" : "{$min}–{$max}{$suffix}";
        }

        return ($min ?? $max).$suffix;
    }

    /** Public helper: split a slug-ish string for breadcrumbs. */
    public static function label(string $type): string
    {
        return Str::headline($type === 'food' ? 'Food & Dining' : $type.'s');
    }
}
