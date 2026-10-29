@extends('emails.layouts.email')

@section('title', 'Payment Failed · ' . $booking->booking_number)
@section('badge', 'Payment Incomplete')

@section('body')
    <!-- Header -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td width="48" valign="top" style="padding-right:14px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="44" height="44" style="width:44px;height:44px;border-collapse:separate;background-color:#fff1f2;border:1px solid #fecdd3;border-radius:12px;margin:0;">
                    <tr>
                        <td align="center" valign="middle" style="width:44px;height:44px;text-align:center;vertical-align:middle;padding:0;margin:0;font-size:22px;line-height:44px;mso-line-height-rule:exactly;">
                            <center style="text-align:center;margin:0 auto;line-height:44px;">⚠️</center>
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="middle">
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#991b1b;line-height:1.25;">
                    Payment Could Not Be Processed
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    Booking {{ $booking->booking_number }}
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;">
        We were unable to complete your transaction for <strong>{{ $booking->title }}</strong>.
        Don't worry &mdash; your card or bank account has not been charged, or any hold will be reversed automatically.
    </p>

    @if ($reason)
    <!-- Failure Reason Box -->
    <div style="background-color:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:10px;padding:14px 16px;margin:18px 0;">
        <span style="font-size:11px;font-weight:700;color:#92400e;text-transform:uppercase;display:block;">Decline Reason</span>
        <p style="margin:4px 0 0;font-size:13px;color:#78350f;line-height:1.5;">{{ $reason }}</p>
    </div>
    @endif

    <!-- Booking Summary Card -->
    @include('emails.partials.booking-summary')

    <p style="margin:16px 0 6px;font-size:14px;line-height:1.5;color:#475569;text-align:center;">
        Your slot is currently held for a short period. Please retry with another payment method to secure your spot:
    </p>

    <!-- Retry Button -->
    @include('emails.partials.button', ['url' => $retryUrl, 'label' => 'Retry Payment Now', 'color' => 'primary'])

    <p style="margin:16px 0 0;font-size:13px;color:#64748b;text-align:center;">
        You can also review your reservation from <a href="{{ $bookingUrl }}" style="color:#0b6e75;font-weight:600;">your bookings dashboard</a>.
    </p>
@endsection
