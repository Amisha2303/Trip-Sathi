<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\NewsletterSubscribedMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:190'],
        ], [], ['email' => 'email address']);

        $email = strtolower($data['email']);

        NewsletterSubscriber::updateOrCreate(
            ['email' => $email],
            [
                'status' => 'subscribed',
                'source' => 'website',
                'ip_address' => $request->ip(),
                'subscribed_at' => now(),
            ],
        );

        try {
            Mail::to($email)->send(new NewsletterSubscribedMail($email));
        } catch (\Throwable $e) {
            Log::warning('Newsletter welcome email could not be sent: ' . $e->getMessage());
        }

        return back()->with('success', 'You are subscribed! We have sent a welcome gift code to your email.');
    }
}
