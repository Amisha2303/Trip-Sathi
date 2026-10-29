@extends('emails.layouts.email')

@section('title', 'Welcome to the Trip Sathi Travel Club!')
@section('badge', 'Travel Club Insider')

@section('body')
    <!-- Header -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td width="48" valign="top" style="padding-right:14px;">
                <div style="background-color:#fef3c7;border:1px solid #fde68a;border-radius:12px;width:44px;height:44px;text-align:center;line-height:44px;font-size:24px;">
                    🎉
                </div>
            </td>
            <td valign="middle">
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#0b6e75;line-height:1.25;">
                    You are in! Welcome to the Club
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    Thank you for subscribing to the {{ $siteName }} newsletter.
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.65;color:#334155;">
        You have officially joined our community of passionate travelers, pilgrims, and explorers across India.
        Here is our gift to help you kickstart your next journey:
    </p>

    <!-- Coupon Gift Box -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background:linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);border:2px dashed #059669;border-radius:14px;margin:20px 0 24px;">
        <tr>
            <td style="padding:22px 24px;text-align:center;">
                <span style="font-size:12px;font-weight:700;color:#047857;text-transform:uppercase;letter-spacing:1px;display:block;">
                    Your Exclusive Welcome Gift
                </span>
                <span style="font-size:26px;font-weight:800;color:#065f46;display:block;margin:6px 0 4px;">
                    {{ $discountPercent }}% OFF Any Booking
                </span>
                <p style="margin:0 0 14px;font-size:13px;color:#047857;">
                    Use this promo code at checkout on your next hotel, darshan, or adventure booking:
                </p>
                <div style="display:inline-block;background-color:#ffffff;border:1px solid #10b981;border-radius:8px;padding:8px 20px;font-family:monospace;font-size:18px;font-weight:800;letter-spacing:2px;color:#0b6e75;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                    {{ $couponCode }}
                </div>
                <span style="display:block;font-size:11px;color:#059669;margin-top:10px;">
                    Valid for all newly booked experiences &middot; No minimum spend required
                </span>
            </td>
        </tr>
    </table>

    <!-- Insider Perks -->
    <div style="background-color:#f8fbfb;border:1px solid #dce8e8;border-radius:12px;padding:18px 20px;margin-bottom:22px;">
        <p style="margin:0 0 10px;font-size:13px;font-weight:700;color:#0b6e75;text-transform:uppercase;letter-spacing:0.5px;">
            What you'll receive from us:
        </p>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="24" valign="top" style="font-size:14px;padding:3px 0;">✨</td>
                <td style="font-size:13px;color:#475569;line-height:1.5;padding:3px 0 3px 8px;">
                    <strong>Secret Weekend Escapes:</strong> Hidden heritage stays and offbeat trails.
                </td>
            </tr>
            <tr>
                <td width="24" valign="top" style="font-size:14px;padding:3px 0;">🛕</td>
                <td style="font-size:13px;color:#475569;line-height:1.5;padding:3px 0 3px 8px;">
                    <strong>Spiritual Calendars:</strong> VIP darshan booking windows and festival schedules.
                </td>
            </tr>
            <tr>
                <td width="24" valign="top" style="font-size:14px;padding:3px 0;">🏷️</td>
                <td style="font-size:13px;color:#475569;line-height:1.5;padding:3px 0 3px 8px;">
                    <strong>Subscriber-Only Flash Deals:</strong> Up to 30% off hotel rooms and experiences.
                </td>
            </tr>
        </table>
    </div>

    <!-- CTA Button -->
    @include('emails.partials.button', ['url' => $exploreUrl, 'label' => 'Explore Destinations & Use Coupon', 'color' => 'primary'])

    <p style="margin:16px 0 0;font-size:12px;color:#94a3b8;text-align:center;">
        Sent to {{ $subscriberEmail }}. You can update your preferences or unsubscribe at any time.
    </p>
@endsection
