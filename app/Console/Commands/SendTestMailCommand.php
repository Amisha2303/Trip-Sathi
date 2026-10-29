<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\BookingCancelledMail;
use App\Mail\BookingConfirmedMail;
use App\Mail\BookingCreatedMail;
use App\Mail\ContactConfirmationMail;
use App\Mail\ContactReplyMail;
use App\Mail\LoginAlertMail;
use App\Mail\NewsletterSubscribedMail;
use App\Mail\PasswordChangedMail;
use App\Mail\PaymentSuccessfulMail;
use App\Mail\TripCreatedMail;
use App\Mail\WelcomeMail;
use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestMailCommand extends Command
{
    protected $signature = 'mail:test 
                            {recipient? : Target email address} 
                            {--type=all : Email type to test (welcome, login, trip, newsletter, booking, contact, all)}';

    protected $description = 'Send test emails through configured SMTP to verify templates and delivery';

    public function handle(): int
    {
        $recipient = $this->argument('recipient') ?: config('mail.from.address', 'rathodjignesh863@gmail.com');
        $type = strtolower((string) $this->option('type'));

        $this->info("📧 Testing Mail System with recipient: {$recipient}");
        $this->info("⚙️  Mailer: " . config('mail.default'));
        $this->info("⚙️  Host: " . config('mail.mailers.smtp.host') . ":" . config('mail.mailers.smtp.port'));
        $this->info("⚙️  From: " . config('mail.from.address') . " (" . config('mail.from.name') . ")");

        // Prepare or fetch a dummy/real user
        $user = User::query()->first() ?? new User([
            'id' => 1,
            'name' => 'Trip Sathi Traveler',
            'email' => $recipient,
        ]);

        // Prepare or fetch a booking
        $booking = Booking::query()->with('items')->latest()->first();
        if (! $booking) {
            $booking = new Booking([
                'id' => 999,
                'booking_number' => 'TS-2026-TEST01',
                'customer_name' => $user->name,
                'customer_email' => $recipient,
                'customer_phone' => '+91 99045 30641',
                'booking_type' => 'hotel',
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'booking_date' => now()->addDays(7),
                'total_amount' => 850000,
                'final_amount' => 850000,
                'discount_amount' => 0,
                'tax_amount' => 102000,
            ]);
            $booking->setRelation('items', collect([
                new \App\Models\BookingItem([
                    'name' => 'Royal Heritage Suite (2 Nights)',
                    'quantity' => 1,
                    'unit_price' => 748000,
                    'total_amount' => 850000,
                ]),
            ]));
        }

        // Prepare or fetch a trip
        $trip = Trip::query()->latest()->first();
        if (! $trip) {
            $trip = new Trip([
                'name' => 'Spiritual Somnath & Dwarka Yatra',
                'start_date' => now()->addDays(14),
                'end_date' => now()->addDays(19),
                'total_days' => 6,
                'travelers' => 4,
                'budget' => 45000,
                'description' => 'Family spiritual pilgrimage covering Somnath Jyotirlinga, Dwarkadhish temple, and Beyt Dwarka.',
            ]);
            $trip->setRelation('user', $user);
        }

        // Prepare or fetch a contact message
        $contact = ContactMessage::query()->latest()->first();
        if (! $contact) {
            $contact = new ContactMessage([
                'id' => 101,
                'name' => $user->name,
                'email' => $recipient,
                'phone' => '+91 99045 30641',
                'subject' => 'VIP Darshan Slot Booking Assistance',
                'message' => 'Hello team, we are planning a 4-person family darshan for Somnath temple next weekend. Could you please confirm if queue fast-track passes are available?',
            ]);
        }

        $tests = [];

        if (in_array($type, ['all', 'welcome'], true)) {
            $tests['Welcome Email'] = fn () => Mail::to($recipient)->send(new WelcomeMail($user));
        }

        if (in_array($type, ['all', 'login'], true)) {
            $tests['Login Security Alert'] = fn () => Mail::to($recipient)->send(new LoginAlertMail($user, [
                'ip' => '103.56.183.203',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/122.0.0.0',
                'time' => now()->format('d M Y, h:i A T'),
            ]));
        }

        if (in_array($type, ['all', 'trip'], true)) {
            $tests['Trip Plan / Itinerary'] = fn () => Mail::to($recipient)->send(new TripCreatedMail($trip, $user));
        }

        if (in_array($type, ['all', 'newsletter'], true)) {
            $tests['Newsletter Travel Club'] = fn () => Mail::to($recipient)->send(new NewsletterSubscribedMail($recipient));
        }

        if (in_array($type, ['all', 'booking', 'booking-created'], true)) {
            $tests['Booking Received (Pending)'] = fn () => Mail::to($recipient)->send(new BookingCreatedMail($booking));
        }

        if (in_array($type, ['all', 'booking', 'booking-confirmed'], true)) {
            $tests['Booking Confirmed Voucher'] = fn () => Mail::to($recipient)->send(new BookingConfirmedMail($booking));
        }

        if (in_array($type, ['all', 'contact'], true)) {
            $tests['Contact Confirmation'] = fn () => Mail::to($recipient)->send(new ContactConfirmationMail($contact));
            $tests['Contact Support Reply'] = fn () => Mail::to($recipient)->send(
                new ContactReplyMail($contact, "Namaste! Yes, fast-track VIP darshan slots are open for next weekend. Please proceed to book on our darshan portal or reply with your preferred time slot.")
            );
        }

        if (in_array($type, ['all', 'password'], true)) {
            $tests['Password Changed Security Alert'] = fn () => Mail::to($recipient)->send(new PasswordChangedMail($user));
        }

        $this->newLine();
        $this->line("🚀 Executing " . count($tests) . " mail tests...");
        $this->newLine();

        $successCount = 0;
        foreach ($tests as $title => $callable) {
            $this->output->write("Sending: {$title} ... ");
            try {
                $callable();
                $this->info("DONE");
                $successCount++;
            } catch (\Throwable $e) {
                $this->error("FAILED: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("🎉 Completed: {$successCount} / " . count($tests) . " emails transmitted successfully!");

        return $successCount === count($tests) ? Command::SUCCESS : Command::FAILURE;
    }
}
