@extends('emails.layouts.email')

@section('title', 'Admin Alert: New Booking ' . $booking->booking_number)
@section('badge', 'New Booking Alert')

@section('body')
    <!-- Header -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td width="48" valign="top" style="padding-right:14px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="44" height="44" style="width:44px;height:44px;border-collapse:separate;background-color:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;margin:0;">
                    <tr>
                        <td align="center" valign="middle" style="width:44px;height:44px;text-align:center;vertical-align:middle;padding:0;margin:0;font-size:22px;line-height:44px;mso-line-height-rule:exactly;">
                            <center style="text-align:center;margin:0 auto;line-height:44px;">🛎️</center>
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="middle">
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#0b6e75;line-height:1.25;">
                    New Customer Booking!
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    A customer just booked {{ $booking->booking_number }} on {{ $siteName }}.
                </p>
            </td>
        </tr>
    </table>

    <!-- Customer Contact Pill -->
    <div style="background-color:#f1f5f9;border:1px solid #e2e8f0;border-radius:12px;padding:14px 18px;margin-bottom:20px;font-size:13px;color:#334155;">
        <strong>Customer:</strong> {{ $booking->customer_name ?: ($booking->user?->name ?? 'Guest') }}<br>
        <strong>Email:</strong> <a href="mailto:{{ $booking->customer_email }}" style="color:#0b6e75;">{{ $booking->customer_email }}</a>
        @if ($booking->customer_phone)
            &middot; <strong>Phone:</strong> <a href="tel:{{ $booking->customer_phone }}" style="color:#0b6e75;">{{ $booking->customer_phone }}</a>
        @endif
    </div>

    <!-- Booking Summary Card -->
    @include('emails.partials.booking-summary')

    <!-- Admin CTA Button -->
    @include('emails.partials.button', ['url' => $adminUrl, 'label' => 'Open Booking in Admin Panel', 'color' => 'primary'])
@endsection
