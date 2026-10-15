<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Adventure;
use App\Models\Booking;
use App\Models\Darshan;
use App\Models\Destination;
use App\Models\Event;
use App\Models\Hotel;
use App\Models\Pooja;
use App\Models\Trip;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Demo catalogue so every screen renders with realistic content.
 *
 * Images are generated locally as SVGs under storage/app/public/demo — no
 * external image host is required for the site to look complete.
 */
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        if (Destination::query()->exists()) {
            $this->command?->info('Demo catalogue already present — skipped.');

            return;
        }

        $admin = $this->ensureUsers();
        $destinations = $this->seedDestinations();
        $this->seedHotels($destinations);
        $this->seedEvents($destinations);
        $this->seedAdventures($destinations);
        $this->seedDarshan($destinations);
        $this->seedPoojas($destinations);
        $this->seedTripsAndBookings($admin, $destinations);
        $this->wireBundledPhotos();

        $this->command?->info('Demo catalogue seeded.');
    }

    /**
     * Point every seeded record at the bundled photo in public/photos when one
     * exists on disk; otherwise keep the generated SVG as a graceful fallback.
     *
     * Photos are static assets shipped with the app, so they survive redeploys
     * on serverless hosts where storage/app/public is ephemeral.
     */
    private function wireBundledPhotos(): void
    {
        $map = [
            'destinations' => ['main_image', 'destination'],
            'hotels'       => ['main_image', 'hotel'],
            'events'       => ['main_image', 'event'],
            'adventures'   => ['main_image', 'adventure'],
            'darshan'      => ['main_image', 'darshan'],
            'poojas'       => ['main_image', 'pooja'],
            'trips'        => ['cover_image', 'trip'],
        ];

        foreach ($map as $table => [$column, $prefix]) {
            foreach (DB::table($table)->select('id', 'slug')->get() as $row) {
                $path = 'photos/' . $prefix . '-' . $row->slug . '.jpg';

                if (is_file(public_path($path))) {
                    DB::table($table)->where('id', $row->id)->update([$column => $path]);
                }
            }
        }
    }

    /** Admin + traveller accounts used by the local demo. */
    private function ensureUsers(): User
    {
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@tripsathi.test'],
            [
                'name' => 'Trip Sathi Admin',
                // User::setPasswordAttribute() hashes this — plain value only.
                'password' => env('TRIPSATHI_ADMIN_PASSWORD', 'Admin@12345'),
                'is_admin' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        // The factory-created demo account already exists in DatabaseSeeder.
        User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Demo Traveller',
                'password' => 'password', // hashed by the User mutator
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        return $admin;
    }

    /** @return array<string, Destination> keyed by slug */
    private function seedDestinations(): array
    {
        $rows = [
            ['Ahmedabad', 'Gujarat', 'Ahmedabad', 'Sabarmati riverfront, stepwells and a food scene that runs late.', true, 9600],
            ['Dwarka', 'Gujarat', 'Dwarka', 'One of the four Char Dham shrines, sitting where the Gomti meets the sea.', true, 8400],
            ['Somnath', 'Gujarat', 'Somnath', 'The first of the twelve Jyotirlingas, rebuilt against the Arabian Sea.', true, 7900],
            ['Bhavnagar', 'Gujarat', 'Bhavnagar', 'Gateway to Velavadar and the quiet beaches of Saurashtra.', false, 4200],
            ['Vadodara', 'Gujarat', 'Vadodara', 'Laxmi Vilas Palace, museums and the cultural capital of Gujarat.', false, 5100],
            ['Surat', 'Gujarat', 'Surat', 'Diamond capital with a street-food reputation that travels.', false, 5600],
            ['Udaipur', 'Rajasthan', 'Udaipur', 'Lake palaces, Aravalli ridgelines and sunset boat rides.', true, 9100],
            ['Jaipur', 'Rajasthan', 'Jaipur', 'Pink City forts, bazaars and the stepwells of Abhaneri.', true, 9400],
            ['Mumbai', 'Maharashtra', 'Mumbai', 'Sea-facing promades, Art Deco skylines and never-sleeping streets.', true, 9800],
            ['Goa', 'Goa', 'Panaji', 'Beach towns, Portuguese quarters and monsoon green hinterland.', true, 9700],
            ['Delhi', 'Delhi', 'New Delhi', 'Monuments, markets and Mughal-era gardens across centuries.', true, 9500],
            ['Varanasi', 'Uttar Pradesh', 'Varanasi', 'Ghats at dawn, aarti at dusk, and the oldest living city.', true, 9300],
        ];

        $created = [];

        foreach ($rows as [$name, $state, $city, $blurb, $featured, $popularity]) {
            $destination = Destination::create([
                'name' => $name,
                'country' => 'India',
                'state' => $state,
                'city' => $city,
                'short_description' => $blurb,
                'description' => $blurb . ' Plan stays, experiences and local transport with Trip Sathi and keep the whole itinerary in one place.',
                'best_time_to_visit' => 'October to March offers the most comfortable weather for sightseeing.',
                'main_image' => $this->image(strtolower($name), $name, $state),
                'popularity' => $popularity,
                'is_featured' => $featured,
                'is_active' => true,
                'language' => $state === 'Gujarat' ? 'Gujarati, Hindi, English' : 'Hindi, English',
            ]);

            $created[$destination->slug] = $destination;
        }

        return $created;
    }

    /** @param array<string, Destination> $destinations */
    private function seedHotels(array $destinations): void
    {
        $rows = [
            ['ahmedabad', 'Riverfront Grand', 4, 6400, 'Business-district rooms overlooking the Sabarmati, ten minutes from Law Garden.'],
            ['ahmedabad', 'Pol House Heritage Stay', 3, 3600, 'Restored haveli inside the old pol quarter with courtyards and breakfast.'],
            ['dwarka', 'Dham Gateway Hotel', 4, 5200, 'Five minutes from Dwarkadhish Temple with an early-morning aarti wake-up call.'],
            ['somnath', 'Somnath Sea View', 4, 5800, 'Cliff-side rooms facing the temple and the Arabian Sea sunset.'],
            ['udaipur', 'Lake Pichola Haveli', 5, 12400, 'Courtyard suites a short walk from Gangaur Ghat and the city palace.'],
            ['udaipur', 'Aravalli Boutique Rooms', 4, 7200, 'Quiet rooms under the Aravalli hills with a rooftop dinner service.'],
            ['jaipur', 'Amber Fort View Residency', 4, 6900, 'Pastel rooms near Jal Mahal with a rooftop café and cycle hire.'],
            ['jaipur', 'Pink City Lodge', 3, 3200, 'Clean, central rooms inside the old city walls near Johari Bazaar.'],
            ['mumbai', 'Marine Drive Suites', 5, 14800, 'Sea-facing suites on the Queen’s Necklace with late check-out.'],
            ['goa', 'Palolem Beach Cottages', 3, 4300, 'Barefoot cottages steps from the sand, with shack dinners next door.'],
            ['delhi', 'Lodhi Garden Business Hotel', 5, 11200, 'Quiet rooms beside Lodhi Gardens with a 24-hour business desk.'],
            ['varanasi', 'Ghat View Retreat', 4, 5400, 'Rooftop rooms above Assi Ghat for sunrise boats and evening aarti.'],
            ['surat', 'Diamond Exchange Hotel', 4, 5900, 'Modern rooms near Ring Road with conference facilities and parking.'],
            ['vadodara', 'Laxmi Vilas Heritage Inn', 4, 4800, 'Colonial-style stay ten minutes from the palace and museum.'],
        ];

        foreach ($rows as $i => [$slug, $name, $stars, $price, $blurb]) {
            $destination = $destinations[$slug];

            Hotel::create([
                'destination_id' => $destination->id,
                'name' => $name,
                'address' => $destination->city . ' Central, ' . $destination->state,
                'city' => $destination->city,
                'state' => $destination->state,
                'short_description' => $blurb,
                'description' => $blurb . ' Free Wi-Fi, 24×7 front desk and complimentary breakfast are included on every tariff.',
                'rating' => min(5, 3.8 + (($i % 12) / 10)),
                'review_count' => 40 + ($i * 17) % 320,
                'star_rating' => $stars,
                'base_price' => $price,
                'discount_percent' => $i % 3 === 0 ? 12 : 0,
                'is_featured' => $i % 4 === 0,
                'is_active' => true,
                'main_image' => $this->image('hotel-' . $slug . '-' . ($i + 1), $name, 'hotel'),
                'min_nights' => 1,
            ]);
        }
    }

    /** @param array<string, Destination> $destinations */
    private function seedEvents(array $destinations): void
    {
        $rows = [
            ['ahmedabad', 'Ahmedabad Kite Festival Night', 'Sabarmati Riverfront Ground', 900, 180, 'Dawn-to-dusk kite celebrations with food stalls along the riverfront.', 45],
            ['jaipur', 'Amber Fort Light & Sound Show', 'Amber Fort', 1200, 300, 'Fort history told through projected light against the ramparts.', 90],
            ['varanasi', 'Ganga Aarti Special Seating', 'Dashashwamedh Ghat', 750, 220, 'Reserved front-row seating for the evening aarti with a guide.', 20],
            ['mumbai', 'Bandra Indie Music Night', 'Bandra Amphitheatre', 1500, 400, 'Independent artists across three stages until midnight.', 120],
            ['goa', 'Saturday Night Beach Market', 'Anjuna Beach', 300, 800, 'Live bands, craft stalls and coastal kitchens by the shore.', 60],
            ['udaipur', 'Mewar Cultural Evening', 'Bagore Ki Haveli', 850, 260, 'Folk dance, puppetry and music on the lakefront terrace.', 35],
            ['delhi', 'Delhi Heritage Walk: Old Delhi', 'Chandni Chowk', 1100, 40, 'Dawn walk through Mughal-era lanes with a historian.', 15],
            ['dwarka', 'Janmashtami Night Celebration', 'Dwarkadhish Temple Area', 500, 500, 'Midnight celebration with bhajan groups and prasad service.', 70],
        ];

        foreach ($rows as $i => [$slug, $name, $venue, $price, $capacity, $blurb, $daysOut]) {
            $destination = $destinations[$slug];
            $date = now()->addDays($daysOut)->startOfDay();

            Event::create([
                'destination_id' => $destination->id,
                'name' => $name,
                'venue' => $venue,
                'address' => $venue . ', ' . $destination->city,
                'city' => $destination->city,
                'organiser' => 'Trip Sathi Experiences',
                'starts_at' => $date->copy()->setTime(18, 0),
                'ends_at' => $date->copy()->setTime(22, 0),
                'event_date' => $date->toDateString(),
                'capacity' => $capacity,
                'sold_count' => (int) ($capacity * 0.35),
                'base_price' => $price,
                'discount_percent' => $i % 2 === 0 ? 10 : 0,
                'short_description' => $blurb,
                'description' => $blurb . ' Tickets are e-delivered instantly and can be shown on your phone at the gate.',
                'main_image' => $this->image('event-' . $slug . '-' . ($i + 1), $name, 'event'),
                'is_featured' => $i % 3 === 0,
                'is_active' => true,
            ]);
        }
    }

    /** @param array<string, Destination> $destinations */
    private function seedAdventures(array $destinations): void
    {
        $rows = [
            ['gir-near-bhavnagar', 'bhavnagar', 'Blackbuck Safari Drive', 'safari', 'moderate', 1400, 150, 'Morning drive through Velavadar grasslands in search of blackbuck and wolves.'],
            ['ahmedabad', 'ahmedabad', 'Sabarmati Kayaking Session', 'watersports', 'easy', 900, 90, 'Guided sunrise kayak on calm river waters with all gear provided.'],
            ['udaipur', 'udaipur', 'Aravalli Sunrise Trek', 'trekking', 'moderate', 1200, 240, 'Ridge-line trek ending at a viewpoint above the lake city.'],
            ['udaipur', 'udaipur', 'Campfire Night in the Hills', 'camping', 'easy', 2200, 600, 'Overnight camp with dinner, telescope session and breakfast.'],
            ['jaipur', 'jaipur', 'Hot Air Balloon over the Farms', 'zipline', 'easy', 6500, 60, 'Dawn balloon ride over the countryside outside Jaipur.'],
            ['goa', 'goa', 'Scuba Diving at Grande Island', 'scuba', 'moderate', 4500, 180, 'Two-tank intro dive with certification-trained instructors.'],
            ['goa', 'goa', 'Bumper Tube Ride', 'watersports', 'easy', 1100, 20, 'High-speed tube tow behind a speed boat, shared or private.'],
            ['mumbai', 'mumbai', 'Sewri Flamingo Walk', 'trekking', 'easy', 700, 120, 'Dawn birding walk across the mudflats during flamingo season.'],
            ['varanasi', 'varanasi', 'Sunrise Ganga Rowboat', 'watersports', 'easy', 850, 45, 'Wooden boat from Assi to Dashashwamedh with a local rower.'],
            ['delhi', 'delhi', 'Delhi Ridge Trail Run', 'trekking', 'hard', 600, 90, 'Coach-led trail run through the northern ridge forest.'],
            ['somnath', 'somnath', 'Coastal Cliff Cycling', 'trekking', 'moderate', 1000, 120, 'Clifftop coastal route with temple stops and snack breaks.'],
            ['dwarka', 'dwarka', 'Dwarka Beach Volleyball', 'camping', 'easy', 600, 60, 'Organised beach games with equipment and refreshments.'],
        ];

        foreach ($rows as $i => [$key, $slug, $name, $type, $difficulty, $price, $minutes, $blurb]) {
            if (! isset($destinations[$slug])) {
                continue;
            }

            $destination = $destinations[$slug];

            Adventure::create([
                'destination_id' => $destination->id,
                'name' => $name,
                'activity_type' => $type,
                'difficulty' => $difficulty,
                'price' => $price,
                'duration_minutes' => $minutes,
                'min_age' => 8,
                'max_participants' => 20,
                'short_description' => $blurb,
                'description' => $blurb . ' Equipment, safety briefing and an instructor are included in the price.',
                'inclusions' => 'Equipment, instructor, refreshments, insurance',
                'meeting_point' => $destination->city . ' central meeting point',
                'main_image' => $this->image('adventure-' . $key, $name, 'adventure'),
                'is_featured' => $i % 3 === 0,
                'is_active' => true,
            ]);
        }
    }

    /** @param array<string, Destination> $destinations */
    private function seedDarshan(array $destinations): void
    {
        $rows = [
            ['dwarka', 'Dwarkadhish VIP Darshan', 'Dwarkadhish Temple', 'Dwarkadhish', 'vip', 750, 'Fast-entry darshan with a guided temple circuit and reserved queue lane.'],
            ['somnath', 'Somnath Morning VIP Darshan', 'Somnath Temple', 'Somnath Mahadev', 'vip', 650, 'Sunrise slot before general queues open, with an audio guide.'],
            ['varanasi', 'Kashi Vishwanath Corridor VIP', 'Kashi Vishwanath Temple', 'Vishwanath', 'vip', 900, 'Priority access through the corridor with a temple volunteer.'],
            ['ahmedabad', 'Akshardham Special Darshan', 'Akshardham Temple', 'Swaminarayan', 'special', 400, 'Timed special darshan with an exhibition entry pass included.'],
            ['jaipur', 'Govind Dev Ji Aarti Darshan', 'Govind Dev Ji Temple', 'Govind Dev Ji', 'regular', 250, 'Evening aarti viewing from the inner courtyard.'],
            ['udaipur', 'Jagdish Temple Guided Darshan', 'Jagdish Temple', 'Jagannath', 'regular', 200, 'Guided explanation of the shikhara carvings and the main shrine.'],
        ];

        foreach ($rows as $i => [$slug, $name, $temple, $deity, $type, $price, $blurb]) {
            $destination = $destinations[$slug];

            Darshan::create([
                'destination_id' => $destination->id,
                'name' => $name,
                'temple_name' => $temple,
                'deity' => $deity,
                'darshan_type' => $type,
                'temple_address' => $temple . ', ' . $destination->city,
                'price' => $price,
                'discount_percent' => $i % 2 === 0 ? 0 : 10,
                'duration_minutes' => 45,
                'max_visitors_per_booking' => 6,
                'max_visitors_per_day' => 400,
                'short_description' => $blurb,
                'description' => $blurb . ' Carry a government photo ID — entry is refused without it.',
                'instructions' => 'Report 30 minutes before your slot at the designated VIP gate.',
                'documents_required' => ['Government photo ID', 'Booking confirmation'],
                'dress_code' => 'Traditional attire; shoulders and knees covered.',
                'cancellation_policy' => 'Full refund up to 24 hours before the slot.',
                'main_image' => $this->image('darshan-' . $slug, $name, 'darshan'),
                'is_featured' => $i < 3,
                'is_active' => true,
            ]);
        }
    }

    /** @param array<string, Destination> $destinations */
    private function seedPoojas(array $destinations): void
    {
        $rows = [
            ['ahmedabad', 'Griha Pravesh Vidhi', 'Mahalaxmi Temple', 'Lakshmi', 2500, 'House-warming ritual with samagri, pandit and a printed certificate.'],
            ['ahmedabad', 'Satyanarayan Puja', 'Swaminarayan Mandir', 'Satyanarayan', 1400, 'Classic family puja with prasad for up to 20 guests.'],
            ['udaipur', 'Mangal Karya Havan', 'Eklingji Temple', 'Ekling', 3200, 'Vedic havan performed by two pandits with full samagri.'],
            ['jaipur', 'Navagraha Shanti Puja', 'Birla Mandir', 'Navagraha', 1900, 'Planetary peace ritual timed to an auspicious muhurat.'],
            ['varanasi', 'Ganga Aarti + Deep Dan', 'Dashashwamedh Ghat', 'Ganga', 1100, 'Participate in the evening aarti with an offering kit.'],
            ['dwarka', 'Kanya Daan Vidhi Service', 'Dwarkadhish Temple', 'Dwarkadhish', 4200, 'Ceremony support with pandit, samagri and venue coordination.'],
        ];

        foreach ($rows as $i => [$slug, $name, $temple, $deity, $price, $blurb]) {
            $destination = $destinations[$slug];

            Pooja::create([
                'destination_id' => $destination->id,
                'name' => $name,
                'temple_name' => $temple,
                'deity' => $deity,
                'price' => $price,
                'discount_percent' => $i % 2 ? 0 : 8,
                'duration_minutes' => 60,
                'max_participants' => 40,
                'short_description' => $blurb,
                'benefits' => $blurb,
                'description' => $blurb . ' Book a slot and the pandit arrives with everything required.',
                'includes' => ['Samagri kit', 'Pandit honorarium', 'Prasad', 'Printed sankalp'],
                'requirements' => 'A clean space facing east or north-east.',
                'process' => 'Sankalp, kalash sthapana, main mantra japa, aarti and prasad distribution.',
                'dress_code' => 'Traditional attire preferred.',
                'cancellation_policy' => 'Free cancellation up to 12 hours before the slot.',
                'main_image' => $this->image('pooja-' . $slug . '-' . ($i + 1), $name, 'pooja'),
                'is_featured' => $i < 2,
                'is_active' => true,
            ]);
        }
    }

    /** @param array<string, Destination> $destinations */
    private function seedTripsAndBookings(User $admin, array $destinations): void
    {
        $traveller = User::query()->where('email', 'test@example.com')->first();

        $trips = [
            ['Gujarat Temple Trail', 'dwarka', 14, 4, 45000, 'Dwarka, Somnath and the Saurashtra coast in one loop with two rest days.'],
            ['Rajasthan Lakes & Forts', 'udaipur', 7, 2, 62000, 'Udaipur to Jaipur with a heritage stay and a balloon morning.'],
            ['Varanasi in Three Days', 'varanasi', 3, 3, 18000, 'Ghats, silk lanes, Sarnath and two sunrise boat rides.'],
            ['Goa Beach Hop', 'goa', 5, 4, 30000, 'North to south beaches with a diving day in the middle.'],
        ];

        $tripModels = [];

        foreach ($trips as $i => [$name, $slug, $days, $travellers, $budget, $blurb]) {
            $destination = $destinations[$slug];
            $start = now()->addDays(20 + ($i * 14));

            $tripModels[] = Trip::create([
                'user_id' => $traveller?->id ?? 1,
                'destination_id' => $destination->id,
                'name' => $name,
                'description' => $blurb,
                'start_date' => $start->toDateString(),
                'end_date' => $start->copy()->addDays($days - 1)->toDateString(),
                'total_days' => $days,
                'travelers' => $travellers,
                'budget' => $budget,
                'is_public' => true,
                'cover_image' => $destination->main_image,
            ]);
        }

        // Bookings give the dashboard and admin screens something to show.
        $hotel = Hotel::query()->first();
        $event = Event::query()->first();
        $darshan = Darshan::query()->first();
        $adventure = Adventure::query()->first();

        $bookings = [
            [$hotel?->destination_id, 'hotel', $hotel?->name, 'confirmed', 'paid', now()->addDays(6), 2, 1280000],
            [$event?->destination_id, 'event', $event?->name, 'pending', 'pending', now()->addDays(12), 2, 162000],
            [$darshan?->destination_id, 'darshan', $darshan?->name, 'completed', 'paid', now()->subDays(21), 4, 300000],
            [$adventure?->destination_id, 'adventure', $adventure?->name, 'cancelled', 'refunded', now()->subDays(9), 1, 140000],
        ];

        foreach ($bookings as $i => [$destinationId, $type, $title, $status, $payment, $date, $guests, $amountMinor]) {
            if ($title === null) {
                continue;
            }

            Booking::create([
                'booking_number' => sprintf('TS-%s-%06d', now()->year, $i + 1),
                'user_id' => $traveller?->id ?? 1,
                'destination_id' => $destinationId,
                'booking_type' => $type,
                'customer_name' => $traveller?->name ?? 'Demo Traveller',
                'customer_email' => $traveller?->email ?? 'test@example.com',
                'customer_phone' => '+91 98765 43210',
                'booking_date' => $date->toDateString(),
                'nights' => $type === 'hotel' ? 2 : 1,
                'quantity' => 1,
                'guests' => $guests,
                'subtotal' => $amountMinor,
                'discount' => 0,
                'tax' => (int) round($amountMinor * 0.05),
                'final_amount' => $amountMinor + (int) round($amountMinor * 0.05),
                'payment_status' => $payment,
                'status' => $status,
                'cancelled' => $status === 'cancelled',
                'cancelled_at' => $status === 'cancelled' ? now()->subDays(8) : null,
                'confirmed_at' => in_array($status, ['confirmed', 'completed'], true) ? $date->copy()->subDays(2) : null,
                'completed_at' => $status === 'completed' ? $date->copy()->addDay() : null,
                'source' => 'web',
            ]);
        }

        // A starter wishlist so the saved-items screen is not empty on first run.
        $wishlistTargets = [
            ['destination', Destination::class, $destinations['udaipur']->id],
            ['destination', Destination::class, $destinations['varanasi']->id],
            ['hotel', Hotel::class, Hotel::query()->first()?->id],
            ['event', Event::class, Event::query()->first()?->id],
            ['darshan', Darshan::class, Darshan::query()->first()?->id],
        ];

        foreach ($wishlistTargets as [$type, $class, $id]) {
            if ($id === null || $traveller === null) {
                continue;
            }

            Wishlist::firstOrCreate(
                [
                    'user_id' => $traveller->id,
                    'wishlistable_type' => $type,
                    'wishlistable_id' => $id,
                ],
                ['type' => $type],
            );
        }

        unset($admin, $tripModels);
    }

    /**
     * Write a self-contained SVG so demo records never depend on a remote host.
     */
    private function image(string $slug, string $label, string $palette): string
    {
        $slug = preg_replace('/[^a-z0-9\-]/', '', str_replace([' ', '_'], '-', strtolower($slug)));

        $directory = storage_path('app/public/demo');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $file = $directory . '/' . $slug . '.svg';

        if (! file_exists($file)) {
            $hue = crc32($palette . $slug) % 360;
            $hue2 = ($hue + 32) % 360;
            $initial = mb_strtoupper(mb_substr($label, 0, 1));
            $safeLabel = htmlspecialchars($label, ENT_XML1);

            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="800" height="600" role="img" aria-label="{$safeLabel}">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="hsl({$hue}, 58%, 34%)"/>
      <stop offset="100%" stop-color="hsl({$hue2}, 66%, 52%)"/>
    </linearGradient>
    <pattern id="dots" width="24" height="24" patternUnits="userSpaceOnUse">
      <circle cx="2" cy="2" r="2" fill="rgba(255,255,255,.09)"/>
    </pattern>
  </defs>
  <rect width="800" height="600" fill="url(#g)"/>
  <rect width="800" height="600" fill="url(#dots)"/>
  <circle cx="660" cy="80" r="170" fill="rgba(255,255,255,.08)"/>
  <circle cx="90" cy="540" r="190" fill="rgba(255,255,255,.07)"/>
  <g transform="translate(400 250)">
    <circle r="96" fill="rgba(255,255,255,.16)"/>
    <text y="34" text-anchor="middle" font-family="Segoe UI, Inter, Arial, sans-serif" font-size="84" font-weight="800" fill="rgba(255,255,255,.95)">{$initial}</text>
  </g>
  <text x="400" y="440" text-anchor="middle" font-family="Segoe UI, Inter, Arial, sans-serif" font-size="42" font-weight="700" fill="rgba(255,255,255,.96)">{$safeLabel}</text>
  <text x="400" y="486" text-anchor="middle" font-family="Segoe UI, Inter, Arial, sans-serif" font-size="24" font-weight="500" fill="rgba(255,255,255,.72)">Trip Sathi</text>
</svg>
SVG;

            file_put_contents($file, $svg);
        }

        return 'demo/' . $slug . '.svg';
    }
}
