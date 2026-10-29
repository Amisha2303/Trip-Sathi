@extends('emails.layouts.email')

@section('title', 'Booking Confirmed · ' . $booking->booking_number)
@section('badge', 'Confirmed Voucher')

@section('body')
    <!-- Header -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td width="48" valign="top" style="padding-right:14px;">
                <div style="background-color:#ecfdf5;border:1px solid #a7f3d0;border-radius:12px;width:44px;height:44px;text-align:center;line-height:44px;font-size:22px;">
                    ✅
                </div>
            </td>
            <td valign="middle">
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#0b6e75;line-height:1.25;">
                    Your Booking is Confirmed!
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    Payment received and your spot is guaranteed. Pack your bags!
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;">
        Dear {{ $booking->customer_name ?: ($booking->user?->name ?? 'Traveler') }},
        we are delighted to confirm your booking for <strong>{{ $booking->title }}</strong>.
        Please keep this voucher handy for check-in or venue entry.
    </p>

    <!-- Booking Summary Card -->
    @include('emails.partials.booking-summary')

    <!-- View Voucher Button -->
    @include('emails.partials.button', ['url' => $bookingUrl, 'label' => 'View Booking in Dashboard', 'color' => 'primary'])

    <!-- Invoice Download Link -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:16px;">
        <tr>
            <td align="center" style="font-size:13px;color:#64748b;">
                📄 Need an official tax receipt?
                <a href="{{ $invoiceUrl }}" style="color:#0b6e75;font-weight:600;text-decoration:underline;">Download Your Tax Invoice (PDF)</a>
            </td>
        </tr>
    </table>
@endsection
