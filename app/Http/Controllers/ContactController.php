<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\ContactConfirmationMail;
use App\Mail\ContactMessageNotificationMail;
use App\Models\ContactMessage;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function create(SettingsService $settings): \Inertia\Response
    {
        return \Inertia\Inertia::render('Contact', [
            'settings' => [
                'owner' => $settings->string('owner_name', 'Amisha Chauhan'),
                'email' => $settings->string('contact_email'),
                'phone' => $settings->string('contact_phone'),
                'address' => $settings->string('contact_address'),
                'whatsapp' => $settings->string('contact_whatsapp'),
            ],
            'subjects' => [
                'General enquiry' => 'General enquiry',
                'Booking help' => 'Booking help',
                'Payment & refunds' => 'Payment & refunds',
                'Hotel / venue partnership' => 'Hotel / venue partnership',
                'Darshan & pooja' => 'Darshan & pooja',
                'Feedback' => 'Feedback',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:20'],
            'subject' => ['nullable', 'string', 'max:190'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        $contact = ContactMessage::create([
            ...$data,
            'user_id' => $request->user()?->id,
            'ip_address' => $request->ip(),
            'status' => 'new',
            'is_read' => false,
        ]);

        // Send confirmation email to customer
        try {
            Mail::to($contact->email)->send(new ContactConfirmationMail($contact));
        } catch (\Throwable $e) {
            Log::warning('Contact confirmation email could not be sent: ' . $e->getMessage());
        }

        // Send alert email to admin support team
        try {
            $adminMail = new ContactMessageNotificationMail($contact);
            foreach ($adminMail->recipients() as $recipient) {
                Mail::to($recipient)->send(new ContactMessageNotificationMail($contact));
            }
        } catch (\Throwable $e) {
            Log::warning('Admin contact notification email could not be sent: ' . $e->getMessage());
        }

        return redirect()
            ->route('contact')
            ->with('success', 'Thanks for reaching out — our team will reply within one business day.');
    }
}
