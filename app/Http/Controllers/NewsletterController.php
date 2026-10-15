<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:190'],
        ], [], ['email' => 'email address']);

        NewsletterSubscriber::updateOrCreate(
            ['email' => strtolower($data['email'])],
            [
                'status' => 'subscribed',
                'source' => 'website',
                'ip_address' => $request->ip(),
                'subscribed_at' => now(),
            ],
        );

        return back()->with('success', 'You are subscribed. Look out for deals and destination ideas.');
    }
}
