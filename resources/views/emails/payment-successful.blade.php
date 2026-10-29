@extends('emails.layouts.email')

@section('title', 'Payment Received · ' . $booking->booking_number)
@section('badge', 'Official Receipt')

@section('body')
    <!-- Header -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td width="48" valign="top" style="padding-right:14px;">
                <div style="background-color:#ecfdf5;border:1px solid #a7f3d0;border-radius:12px;width:44px;height:44px;text-align:center;line-height:44px;font-size:22px;">
                    💳
                </div>
            </td>
            <td valign="middle">
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#0b6e75;line-height:1.25;">
                    Payment Successfully Received!
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    Thank you &mdash; we have recorded your transaction.
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;">
        We have processed your payment for booking <strong>{{ $booking->booking_number }}</strong>.
        Here is your official payment breakdown:
    </p>

    <!-- Payment Transaction Meta -->
    @if ($payment)
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;margin:18px 0 20px;">
        <tr>
            <td style="padding:14px 18px;font-size:13px;color:#475569;">
                <span style="color:#64748b;display:inline-block;width:140px;font-weight:600;">Payment Gateway:</span>
                <strong style="color:#1e293b;">{{ strtoupper($payment->gateway ?? 'Online Payment') }}</strong>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 18px;border-top:1px solid #edf2f7;font-size:13px;color:#475569;">
                <span style="color:#64748b;display:inline-block;width:140px;font-weight:600;">Transaction Ref:</span>
                <strong style="color:#0b6e75;font-family:monospace;">{{ $payment->reference ?? 'TXN-' . $payment->id }}</strong>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 18px;border-top:1px solid #edf2f7;font-size:13px;color:#475569;">
                <span style="color:#64748b;display:inline-block;width:140px;font-weight:600;">Amount Settled:</span>
                <strong style="color:#059669;font-size:16px;">&#8377;{{ number_format((float) $booking->final_amount_rupees, 2) }}</strong>
            </td>
        </tr>
    </table>
    @endif

    <!-- Booking Summary Card -->
    @include('emails.partials.booking-summary')

    <!-- View Booking Button -->
    @include('emails.partials.button', ['url' => $bookingUrl, 'label' => 'View Booking Details', 'color' => 'primary'])

    <!-- Download Tax Invoice -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:16px;">
        <tr>
            <td align="center" style="font-size:13px;color:#64748b;">
                📄 Official Tax Invoice available:
                <a href="{{ $invoiceUrl }}" style="color:#0b6e75;font-weight:600;text-decoration:underline;">Download PDF Invoice</a>
            </td>
        </tr>
    </table>
@endsection
