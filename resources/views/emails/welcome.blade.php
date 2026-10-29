@extends('emails.layouts.email')

@section('title', 'Welcome to ' . ($siteName ?? 'Trip Sathi'))
@section('badge', 'Welcome Aboard')

@section('body')
    <!-- Greeting Header -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td width="48" valign="top" style="padding-right:14px;">
                <div style="background-color:#ecfdf5;border:1px solid #a7f3d0;border-radius:12px;width:44px;height:44px;text-align:center;line-height:44px;font-size:24px;">
                    🌿
                </div>
            </td>
            <td valign="middle">
                <h1 style="margin:0;font-size:24px;font-weight:800;color:#0b6e75;line-height:1.25;">
                    Namaste, {{ $firstName }}!
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    Your {{ $siteName }} account is ready to unlock incredible journeys.
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 18px;font-size:15px;line-height:1.65;color:#334155;">
        Welcome to <strong>{{ $siteName }}</strong> — your companion for curated travel adventures, spiritual VIP darshan slots, heritage hotel stays, and unforgettable Indian experiences.
    </p>

    <!-- Quick Start Card -->
    <div style="background-color:#f8fbfb;border:1px solid #dce8e8;border-radius:14px;padding:20px 22px;margin:22px 0;">
        <p style="margin:0 0 14px;font-size:13px;font-weight:700;color:#0b6e75;text-transform:uppercase;letter-spacing:0.6px;">
            Here is how to get started:
        </p>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="30" valign="top" style="padding:6px 0;font-size:18px;">📍</td>
                <td valign="top" style="padding:6px 0 6px 10px;font-size:14px;line-height:1.5;color:#334155;">
                    <strong>Explore &amp; Save:</strong> Browse top handpicked destinations and add favorites to your wishlist.
                </td>
            </tr>
            <tr>
                <td width="30" valign="top" style="padding:6px 0;font-size:18px;">🗓️</td>
                <td valign="top" style="padding:6px 0 6px 10px;font-size:14px;line-height:1.5;color:#334155;">
                    <strong>Build Itineraries:</strong> Use our smart Trip Planner to customize dates, budget, and activities.
                </td>
            </tr>
            <tr>
                <td width="30" valign="top" style="padding:6px 0;font-size:18px;">🛕</td>
                <td valign="top" style="padding:6px 0 6px 10px;font-size:14px;line-height:1.5;color:#334155;">
                    <strong>VIP Darshan &amp; Pooja:</strong> Book certified temple slots without standing in long queues.
                </td>
            </tr>
        </table>
    </div>

    <!-- CTA Button -->
    @include('emails.partials.button', ['url' => $exploreUrl, 'label' => 'Start Exploring Destinations', 'color' => 'primary'])

    <p style="margin:20px 0 0;font-size:13px;color:#64748b;text-align:center;">
        Questions? We're just an email away at <a href="mailto:{{ $supportEmail }}" style="color:#0b6e75;font-weight:600;">{{ $supportEmail }}</a>.
    </p>
@endsection
