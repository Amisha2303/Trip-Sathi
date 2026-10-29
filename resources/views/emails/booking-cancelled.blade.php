@extends('emails.layouts.email')

@section('title', 'Booking Cancelled · ' . $booking->booking_number)
@section('badge', 'Booking Cancelled')

@section('body')
    <!-- Header -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td width="48" valign="top" style="padding-right:14px;">
                <div style="background-color:#fef2f2;border:1px solid #fecaca;border-radius:12px;width:44px;height:44px;text-align:center;line-height:44px;font-size:22px;">
                    ✖️
                </div>
            </td>
            <td valign="middle">
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#991b1b;line-height:1.25;">
                    Booking Has Been Cancelled
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    Reference {{ $booking->booking_number }}
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;">
        Hello {{ $booking->customer_name ?: ($booking->user?->name ?? 'there') }},
        this is to inform you that booking <strong>{{ $booking->booking_number }}</strong> ({{ $booking->title }}) has been cancelled.
    </p>

    @if(!empty($refundNote))
    <!-- Refund Status Box -->
    <div style="background-color:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px 18px;margin-bottom:20px;">
        <strong style="color:#166534;font-size:14px;display:block;margin-bottom:4px;">Refund Information:</strong>
        <p style="margin:0;font-size:13px;color:#15803d;line-height:1.5;">{{ $refundNote }}</p>
    </div>
    @endif

    <!-- Booking Summary Card -->
    @include('emails.partials.booking-summary')

    <!-- Rebook CTA -->
    <p style="margin:20px 0 16px;font-size:14px;line-height:1.6;color:#475569;text-align:center;">
        Plans change! When you're ready to plan your next getaway, our catalogue is always open.
    </p>

    @include('emails.partials.button', ['url' => $bookingUrl, 'label' => 'View Booking in Dashboard', 'color' => 'dark'])
@endsection
