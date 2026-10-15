<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class ContactController extends Controller
{
    public function create(SettingsService $settings): View
    {
        return view('contact', [
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

        ContactMessage::create([
            ...$data,
            'user_id' => $request->user()?->id,
            'ip_address' => $request->ip(),
            'status' => 'new',
            'is_read' => false,
        ]);

        // Admin notification mail is intentionally not sent here: the support
        // inbox row is the source of truth and the admin panel surfaces it.

        return redirect()
            ->route('contact')
            ->with('success', 'Thanks for reaching out — our team will reply within one business day.');
    }
}
