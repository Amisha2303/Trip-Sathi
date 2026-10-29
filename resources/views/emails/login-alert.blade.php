@extends('emails.layouts.email')

@section('title', 'Security Alert: New Sign-In · ' . ($siteName ?? 'Trip Sathi'))
@section('badge', 'Security Alert')

@section('body')
    <!-- Alert Header -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td width="48" valign="top" style="padding-right:14px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="44" height="44" style="width:44px;height:44px;border-collapse:separate;background-color:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;margin:0;">
                    <tr>
                        <td align="center" valign="middle" style="width:44px;height:44px;text-align:center;vertical-align:middle;padding:0;margin:0;font-size:22px;line-height:44px;mso-line-height-rule:exactly;">
                            <center style="text-align:center;margin:0 auto;line-height:44px;">🛡️</center>
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="middle">
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#1e293b;line-height:1.25;">
                    New Sign-In Detected
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    Hello {{ $name }}, your {{ $siteName }} account was just accessed.
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;">
        We noticed a sign-in to your account (<strong>{{ $email }}</strong>). Here are the details of this session:
    </p>

    <!-- Session Details Box -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;margin:18px 0 22px;">
        <tr>
            <td style="padding:14px 18px;border-bottom:1px solid #e2e8f0;font-size:13px;">
                <span style="color:#64748b;display:inline-block;width:110px;font-weight:600;">Time:</span>
                <strong style="color:#1e293b;">{{ $loginTime }}</strong>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 18px;border-bottom:1px solid #e2e8f0;font-size:13px;">
                <span style="color:#64748b;display:inline-block;width:110px;font-weight:600;">IP Address:</span>
                <strong style="color:#1e293b;">{{ $ip }}</strong>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 18px;font-size:13px;">
                <span style="color:#64748b;display:inline-block;width:110px;font-weight:600;">Browser / App:</span>
                <span style="color:#334155;word-break:break-all;">{{ $userAgent }}</span>
            </td>
        </tr>
    </table>

    <!-- Security Warning Card -->
    <div style="background-color:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:16px 18px;margin-bottom:20px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="26" valign="top" style="font-size:16px;">⚠️</td>
                <td valign="top" style="font-size:13px;line-height:1.5;color:#92400e;padding-left:8px;">
                    <strong>Was this you?</strong> If you recognize this login, you can safely ignore this email.
                    If you did not sign in, please secure your account immediately by resetting your password below.
                </td>
            </tr>
        </table>
    </div>

    <!-- CTA Button -->
    @include('emails.partials.button', ['url' => $resetUrl, 'label' => 'Secure Account & Reset Password', 'color' => 'danger'])

    <p style="margin:16px 0 0;font-size:13px;color:#64748b;text-align:center;">
        You can also review active sessions in your <a href="{{ $profileUrl }}" style="color:#0b6e75;font-weight:600;">account settings</a>.
    </p>
@endsection
