@extends('emails.layouts.email')

@section('title', 'Verify Your Email · ' . ($siteName ?? 'Trip Sathi'))
@section('badge', 'Account Verification')

@section('body')
    <!-- Header -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td width="48" valign="top" style="padding-right:14px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="44" height="44" style="width:44px;height:44px;border-collapse:separate;background-color:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;margin:0;">
                    <tr>
                        <td align="center" valign="middle" style="width:44px;height:44px;text-align:center;vertical-align:middle;padding:0;margin:0;font-size:22px;line-height:1;mso-line-height-rule:exactly;">
                            ✉️
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="middle">
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#0b6e75;line-height:1.25;">
                    Confirm Your Email Address
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    Hi {{ explode(' ', trim($user->name))[0] ?? $user->name }}, one quick tap to activate your profile.
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.65;color:#334155;">
        Thank you for creating an account on <strong>{{ $siteName }}</strong>. Confirming your email ensures you receive your booking vouchers, ticket QR codes, and travel itinerary reminders securely.
    </p>

    <!-- Verification Button -->
    @include('emails.partials.button', ['url' => $verificationUrl, 'label' => 'Verify My Email Address', 'color' => 'primary'])

    <!-- Fallback Box -->
    <div style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;margin:22px 0 10px;font-size:12px;color:#64748b;line-height:1.6;">
        <span style="font-weight:600;color:#475569;display:block;margin-bottom:4px;">Button not working?</span>
        Copy and paste this link into your web browser:<br>
        <a href="{{ $verificationUrl }}" style="color:#0b6e75;word-break:break-all;">{{ $verificationUrl }}</a>
    </div>
@endsection
