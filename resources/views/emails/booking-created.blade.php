@extends('emails.layouts.email')

@section('title', 'Booking Received · ' . $booking->booking_number)
@section('badge', 'Booking Held')

@section('body')
    <!-- Header -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td width="48" valign="top" style="padding-right:14px;">
                <div style="background-color:#fffbeb;border:1px solid #fde68a;border-radius:12px;width:44px;height:44px;text-align:center;line-height:44px;font-size:22px;">
                    ⏳
                </div>
            </td>
            <td valign="middle">
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#0b6e75;line-height:1.25;">
                    We Have Your Booking Request!
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    Thanks {{ $booking->customer_name ?: ($booking->user?->first_name ?? 'there') }}, your slot is currently held.
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;">
        Your booking request for <strong>{{ $booking->title }}</strong> is in our system.
        Please complete payment to guarantee your slot and receive your official confirmed voucher.
    </p>

    <!-- Booking Summary Card -->
    @include('emails.partials.booking-summary')

    <!-- Complete Payment Button -->
    @include('emails.partials.button', ['url' => $payUrl, 'label' => 'Complete Payment to Confirm Slot', 'color' => 'primary'])

    <!-- Helpful Review Note -->
    <p style="margin:16px 0 0;font-size:13px;color:#64748b;text-align:center;">
        You can also review or settle this booking any time from
        <a href="{{ $bookingUrl }}" style="color:#0b6e75;font-weight:600;">your bookings dashboard</a>.
    </p>
@endsection
