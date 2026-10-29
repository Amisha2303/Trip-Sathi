@extends('emails.layouts.email')

@section('title', 'Refund Processed · ' . $booking->booking_number)
@section('badge', 'Refund Processed')

@section('body')
    <!-- Header -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td width="48" valign="top" style="padding-right:14px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="44" height="44" style="width:44px;height:44px;border-collapse:separate;background-color:#ecfdf5;border:1px solid #a7f3d0;border-radius:12px;margin:0;">
                    <tr>
                        <td align="center" valign="middle" style="width:44px;height:44px;text-align:center;vertical-align:middle;padding:0;margin:0;font-size:22px;line-height:44px;mso-line-height-rule:exactly;">
                            <center style="text-align:center;margin:0 auto;line-height:44px;">💰</center>
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="middle">
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#0b6e75;line-height:1.25;">
                    Your Refund is on the Way
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    Booking {{ $booking->booking_number }}
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;">
        We have initiated a refund for booking <strong>{{ $booking->booking_number }}</strong>.
        The amount has been released from our merchant account to your original payment method.
    </p>

    @if ($refund)
    <!-- Refund Summary Card -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;margin:18px 0 20px;">
        <tr>
            <td style="padding:14px 18px;font-size:13px;color:#166534;">
                <span style="color:#15803d;display:inline-block;width:140px;font-weight:600;">Refunded Amount:</span>
                <strong style="font-size:18px;color:#047857;">&#8377;{{ number_format((float) $refund->amount / 100, 2) }}</strong>
            </td>
        </tr>
        @if ($refund->reference)
        <tr>
            <td style="padding:14px 18px;border-top:1px solid #dcfce7;font-size:13px;color:#166534;">
                <span style="color:#15803d;display:inline-block;width:140px;font-weight:600;">Bank Reference:</span>
                <strong style="color:#065f46;font-family:monospace;">{{ $refund->reference }}</strong>
            </td>
        </tr>
        @endif
        @if ($refund->processed_at)
        <tr>
            <td style="padding:14px 18px;border-top:1px solid #dcfce7;font-size:13px;color:#166534;">
                <span style="color:#15803d;display:inline-block;width:140px;font-weight:600;">Processed Time:</span>
                <span style="color:#166534;">{{ $refund->processed_at->format('d M Y, h:i A') }}</span>
            </td>
        </tr>
        @endif
    </table>
    @endif

    <!-- Booking Summary Card -->
    @include('emails.partials.booking-summary')

    <div style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px 16px;margin:16px 0 20px;font-size:13px;color:#64748b;line-height:1.5;">
        ℹ️ <strong>Note on bank timelines:</strong> Depending on your issuing bank, the refunded funds usually reflect in your account or statement within <strong>3 to 5 business days</strong>.
    </div>

    <!-- CTA Button -->
    @include('emails.partials.button', ['url' => $bookingUrl, 'label' => 'View Booking in Dashboard', 'color' => 'teal'])
@endsection
