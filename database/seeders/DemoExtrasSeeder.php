<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Destination;
use App\Models\FoodExperience;
use App\Models\Restaurant;
use App\Models\RestaurantMenu;
use App\Models\Review;
use App\Models\Testimonial;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Extra demo content that fills out the Food & Dining section, the home
 * testimonials strip, and the reviews widgets on detail pages.
 *
 * Reviews require a booking (booking_id is NOT NULL), so every review is
 * backed by its own completed booking. Idempotent — safe to re-run.
 */
class DemoExtrasSeeder extends Seeder
{
    /** restaurant slug => [destination slug, name, cuisine, price_range, photo, city, featured] */
    private array $restaurants = [
        ['ahmedabad', 'Chandni Chowk Kitchen', 'North Indian, Mughlai', '$$', 'food-1', 'Ahmedabad', true],
        ['ahmedabad', 'Sabarmati Riverside Cafe', 'Cafe, Continental', '$', 'food-2', 'Ahmedabad', false],
        ['jaipur', 'Amber Terrace Rooftop', 'Rajasthani, Fusion', '$$$', 'food-3', 'Jaipur', true],
        ['jaipur', 'Pink City Spice Court', 'Rajasthani, Thali', '$$', 'food-4', 'Jaipur', false],
        ['udaipur', 'Pichola Lake House', 'Seafood, Multi-cuisine', '$$$', 'food-5', 'Udaipur', true],
        ['mumbai', 'Marine Drive Dhaba', 'Street Food, Indian', '$', 'food-6', 'Mumbai', false],
        ['mumbai', 'Gateway Grill House', 'Grill, Continental', '$$$', 'food-7', 'Mumbai', true],
        ['goa', 'Baga Beach Shack', 'Seafood, Goan', '$$', 'food-8', 'Goa', true],
        ['varanasi', 'Ghatside Thali House', 'Vegetarian, Indian', '$', 'food-9', 'Varanasi', false],
        ['dwarka', 'Dwarkadhish Bhojanalay', 'Vegetarian, Gujarati', '$', 'food-10', 'Dwarka', false],
        ['somnath', 'Somnath Sea Breeze', 'Seafood, Gujarati', '$$', 'food-11', 'Somnath', true],
        ['surat', 'Surat Street Bites', 'Street Food, Gujarati', '$', 'food-12', 'Surat', false],
    ];

    /** restaurant slug => array of [section, name, description, price, is_veg] */
    private array $menus = [
        'chandni-chowk-kitchen' => [
            ['Starters', 'Paneer Tikka', 'Char-grilled cottage cheese in a smoky masala.', 280, true],
            ['Main Course', 'Butter Chicken', 'Slow-cooked chicken in a silky tomato-butter gravy.', 420, false],
            ['Breads', 'Garlic Naan', 'Tandoor-baked naan finished with garlic and butter.', 90, true],
        ],
        'amber-terrace-rooftop' => [
            ['Thali', 'Royal Rajasthani Thali', 'Dal-baati-churma, gatte ki sabzi, bajra roti and more.', 550, true],
            ['Main Course', 'Laal Maas', 'Fiery Mewari mutton curry with Mathania chillies.', 640, false],
            ['Desserts', 'Ghewar', 'Traditional honeycomb sweet soaked in rabri.', 180, true],
        ],
        'pichola-lake-house' => [
            ['Seafood', 'Rajasthani Fish Curry', 'Fresh catch simmered in a coconut-coriander gravy.', 520, false],
            ['Main Course', 'Dal Baati Churma', 'The state classic, plated lake-view style.', 430, true],
        ],
        'baga-beach-shack' => [
            ['Seafood', 'Goan Prawn Curry', 'Prawns in a tangy coconut-kokum curry.', 480, false],
            ['Starters', 'Fish Recheado', 'Mackerel stuffed with a fiery red recheado paste.', 360, false],
            ['Breads', 'Poi', 'Soft Goan bread, the perfect curry mop.', 40, true],
        ],
    ];

    private array $experiences = [
        ['ahmedabad', 'chandni-chowk-kitchen', 'Ahmedabad Street Food Walk', 'food-6', 1200, 180, 'Street Food', 'Farsi Puri, Khandvi, Jalebi, Masala Chai'],
        ['goa', 'baga-beach-shack', 'Goa Beachside Seafood Dinner', 'food-8', 2400, 150, 'Seafood', 'Prawn Curry, Fish Recheado, Bebinca'],
        ['jaipur', 'amber-terrace-rooftop', 'Jaipur Royal Thali Experience', 'food-4', 1800, 120, 'Rajasthani', 'Dal Baati Churma, Laal Maas, Ghewar'],
        ['varanasi', null, 'Varanasi Ghat Food Trail', 'food-9', 900, 210, 'Vegetarian', 'Kachori Sabzi, Thandai, Rabri'],
    ];

    private array $testimonials = [
        ['Priya Sharma', 'Solo traveller', 'Udaipur, Rajasthan', 5, 'Booked the VIP Darshan and a lake-view haveli in one go. Every detail — timings, transfers, even the aarti slot — was confirmed before I landed. Zero follow-ups needed.'],
        ['Rahul Mehta', 'Family of four', 'Ahmedabad, Gujarat', 5, 'We planned a Diwali week across three cities from a single dashboard. The kids loved the adventure add-ons and the budget tracker kept us honest.'],
        ['Ananya Iyer', 'Weekend explorer', 'Goa', 4, 'The Food & Dining picks were spot on — we ate at places no guidebook mentions. Only wish the restaurant menus had more photos.'],
        ['Vikram Singh', 'Heritage buff', 'Jaipur, Rajasthan', 5, 'The itinerary builder is genuinely good. I dropped in forts and it routed them in a sensible order with drive times. Felt like a local fixer planned it.'],
        ['Neha Patel', 'Group organiser', 'Surat, Gujarat', 5, 'Coordinating twelve people usually ends in chaos. Here everyone saw the same live plan and I stopped playing telephone.'],
        ['Arjun Nair', 'Photographer', 'Varanasi, Uttar Pradesh', 4, 'Sunrise boat slot, ghats, and a food trail — all pre-booked so I could just shoot. Reviews on each hotel were honest, not the usual five-star fluff.'],
    ];

    /** [subject_type, subject_slug_or_index, rating, title, comment] */
    private array $reviews = [
        ['hotel', 'pol-house-heritage-stay', 5, 'A restored jewel in the old city', 'The Pol House is immaculate — carved wooden facades, a quiet courtyard, and staff who arranged our Modhera sunrise visit at short notice. Breakfast on the terrace was a highlight.'],
        ['hotel', 'riverfront-grand', 4, 'Comfortable and central', 'Rooms are spacious and the river-facing ones are worth the upgrade. Service was friendly, though the lift can be slow at check-out time.'],
        ['hotel', 'lake-pichola-haveli', 5, 'Woke up to the lake every morning', 'Hard to beat this view. The team upgraded us without being asked and the rooftop dinner was unforgettable.'],
        ['destination', 'jaipur', 5, 'The Pink City delivers', 'Amber Fort at opening time is a must. We used the app to chain Hawa Mahal, Jantar Mantar and a block-printing workshop into one smooth day.'],
        ['destination', 'varanasi', 5, 'Spiritual and unforgettable', 'The Old Quarter walk and a sunrise boat on the Ganga were the two best things we did all year.'],
        ['destination', 'goa', 4, 'Goa beyond the beaches', 'The Old Quarter walk and a sunset cruise were the two best things we did all year.'],
        ['restaurant', 'baga-beach-shack', 5, 'Prawn curry worth the sand', 'Sat with our feet in the sand, eating the best Goan prawn curry of the trip. Booked the seafood dinner experience and it did not disappoint.'],
        ['restaurant', 'amber-terrace-rooftop', 5, 'Rooftop thali with a fort view', 'The Rajasthani thali is generous and the Ghewar is house-made. Ask for a window table at sunset.'],
        ['adventure', null, 4, 'Guides made all the difference', 'Safety briefing was thorough and the guide knew every turn. Would have liked a shorter option for beginners though.'],
        ['darshan', null, 5, 'Seamless VIP darshan', 'Timed entry meant we barely waited. The pre-visit notes on dress code and photography were genuinely useful.'],
    ];

    public function run(): void
    {
        $this->command?->info('Seeding restaurants, food experiences, testimonials and reviews…');

        $destinations = Destination::pluck('id', 'slug');
        $destBySlug = static fn (string $slug): ?int => $destinations[$slug] ?? null;

        // Restaurants ----------------------------------------------------
        foreach ($this->restaurants as [$destSlug, $name, $cuisine, $priceRange, $photo, $city, $featured]) {
            $destinationId = $destBySlug($destSlug);
            if (! $destinationId) {
                continue;
            }

            $restaurant = Restaurant::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'destination_id' => $destinationId,
                    'name' => $name,
                    'description' => "A well-loved table in {$city}, recommended by Trip Sathi for reliable food, fair prices and warm service.",
                    'short_description' => "{$cuisine} in {$city}.",
                    'address' => "{$city} Old Quarter",
                    'city' => $city,
                    'cuisine' => $cuisine,
                    'price_range' => $priceRange,
                    'average_cost' => $priceRange === '$$$' ? 1800 : ($priceRange === '$$' ? 900 : 450),
                    'main_image' => "photos/{$photo}.jpg",
                    'rating' => $featured ? 4.8 : 4.5,
                    'review_count' => 60 + random_int(0, 140),
                    'opens_at' => '11:00',
                    'closes_at' => '23:00',
                    'total_tables' => random_int(12, 40),
                    'reservation_slot_minutes' => 30,
                    'online_reservation' => true,
                    'is_featured' => $featured,
                    'is_active' => true,
                ]
            );

            foreach ($this->menus[$restaurant->slug] ?? [] as [$section, $item, $desc, $price, $isVeg]) {
                RestaurantMenu::updateOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'section' => $section,
                        'name' => $item,
                    ],
                    [
                        'description' => $desc,
                        'price' => $price,
                        'is_vegetarian' => $isVeg,
                        'is_available' => true,
                        'sort_order' => 0,
                    ]
                );
            }
        }

        // Food experiences -----------------------------------------------
        foreach ($this->experiences as [$destSlug, $restaurantSlug, $name, $photo, $price, $duration, $cuisine, $includes]) {
            $destinationId = $destBySlug($destSlug);
            if (! $destinationId) {
                continue;
            }

            FoodExperience::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'destination_id' => $destinationId,
                    'restaurant_id' => $restaurantSlug ? Restaurant::where('slug', $restaurantSlug)->value('id') : null,
                    'name' => $name,
                    'description' => "A hosted {$cuisine} experience led by local hosts. {$duration} minutes of eating, walking and stories.",
                    'short_description' => "Hosted {$cuisine} experience.",
                    'main_image' => "photos/{$photo}.jpg",
                    'price' => $price,
                    'duration_minutes' => $duration,
                    'max_guests' => 12,
                    'min_guests' => 2,
                    'cuisine' => $cuisine,
                    'includes' => $includes,
                    'is_featured' => true,
                    'is_active' => true,
                ]
            );
        }

        // Testimonials ---------------------------------------------------
        foreach ($this->testimonials as $i => [$name, $designation, $location, $rating, $quote]) {
            Testimonial::updateOrCreate(
                ['author_name' => $name, 'quote' => $quote],
                [
                    'author_designation' => $designation,
                    'location' => $location,
                    'rating' => $rating,
                    'sort_order' => $i,
                    'is_active' => true,
                ]
            );
        }

        // Reviews (each backed by a completed booking) --------------------
        $users = User::where('is_admin', false)->orderBy('id')->get();
        if ($users->isEmpty()) {
            return;
        }

        foreach ($this->reviews as $i => [$type, $slug, $rating, $title, $comment]) {
            $subject = $this->resolveSubject($type, $slug, $destinations);
            if (! $subject) {
                continue;
            }

            $user = $users[$i % $users->count()];

            $booking = Booking::updateOrCreate(
                ['booking_number' => 'TS-REV-'.str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT)],
                [
                    'user_id' => $user->id,
                    'destination_id' => $subject->destination_id ?? ($type === 'destination' ? $subject->id : null),
                    'booking_type' => $type === 'destination' ? 'hotel' : $type,
                    'bookable_type' => $type === 'destination' ? null : $type,
                    'bookable_id' => $type === 'destination' ? null : $subject->id,
                    'customer_name' => $user->name,
                    'customer_email' => $user->email,
                    'booking_date' => Carbon::now()->subDays(14 + $i)->toDateString(),
                    'quantity' => 1,
                    'guests' => random_int(1, 4),
                    'subtotal' => 2500,
                    'tax' => 450,
                    'final_amount' => 2950,
                    'currency' => 'INR',
                    'payment_status' => 'paid',
                    'status' => 'completed',
                    'confirmed_at' => Carbon::now()->subDays(15 + $i),
                    'completed_at' => Carbon::now()->subDays(14 + $i),
                ]
            );

            Review::updateOrCreate(
                [
                    'booking_id' => $booking->id,
                    'subject_type' => $type,
                    'subject_id' => $subject->id,
                ],
                [
                    'user_id' => $user->id,
                    'destination_id' => $type === 'destination' ? $subject->id : ($subject->destination_id ?? null),
                    'rating' => $rating,
                    'title' => $title,
                    'comment' => $comment,
                    'status' => 'approved',
                    'is_verified' => true,
                    'is_featured' => $i < 3,
                    'helpful_count' => random_int(0, 24),
                    'moderated_at' => Carbon::now()->subDays(13 + $i),
                ]
            );
        }

        $this->command?->info('Demo extras seeded.');
    }

    private function resolveSubject(string $type, ?string $slug, $destinations): mixed
    {
        return match ($type) {
            'hotel' => \App\Models\Hotel::where('slug', $slug)->first(),
            'restaurant' => Restaurant::where('slug', $slug)->first(),
            'destination' => Destination::where('slug', $slug)->first(),
            'adventure' => \App\Models\Adventure::where('is_active', true)->inRandomOrder()->first(),
            'darshan' => \App\Models\Darshan::where('is_active', true)->inRandomOrder()->first(),
            default => null,
        };
    }
}
