@extends('emails.layouts.email')

@section('title', 'Event Reminder · ' . $booking->booking_number)
@section('badge', 'Upcoming Event')

@section('body')
    <!-- Header -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td width="48" valign="top" style="padding-right:14px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="44" height="44" style="width:44px;height:44px;border-collapse:separate;background-color:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;margin:0;">
                    <tr>
                        <td align="center" valign="middle" style="width:44px;height:44px;text-align:center;vertical-align:middle;padding:0;margin:0;font-size:22px;line-height:1;mso-line-height-rule:exactly;">
                            🎫
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="middle">
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#0b6e75;line-height:1.25;">
                    Your Event is Coming Up!
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    Booking {{ $booking->booking_number }}
                </p>
            </td>
        </tr>
    </table>

    @if ($startsAt)
    <div style="background-color:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:14px 18px;margin-bottom:20px;font-size:14px;color:#166534;">
        ⏰ <strong>Event Time:</strong> {{ $startsAt->format('D, d M Y · h:i A') }}<br>
        <span style="font-size:12px;color:#15803d;">We recommend arriving 15 minutes before showtime to verify entry.</span>
    </div>
    @endif

    <!-- Booking Summary Card -->
    @include('emails.partials.booking-summary')

    <!-- CTA Button -->
    @include('emails.partials.button', ['url' => $bookingUrl, 'label' => 'View Ticket & Instructions', 'color' => 'primary'])
@endsection
