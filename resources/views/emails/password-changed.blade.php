@extends('emails.layouts.email')

@section('title', 'Password Updated · ' . ($siteName ?? 'Trip Sathi'))
@section('badge', 'Security Notice')

@section('body')
    <!-- Header -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td width="48" valign="top" style="padding-right:14px;">
                <div style="background-color:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;width:44px;height:44px;text-align:center;line-height:44px;font-size:22px;">
                    🔒
                </div>
            </td>
            <td valign="middle">
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#1e293b;line-height:1.25;">
                    Your Password Was Changed
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    Security confirmation for {{ $name }} ({{ $user->email }}).
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;">
        This is a quick security confirmation that your <strong>{{ $siteName }}</strong> account password was updated on <strong>{{ $updatedAt }}</strong>.
    </p>

    <!-- Security Information Box -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;margin:20px 0 24px;">
        <tr>
            <td style="padding:16px 20px;">
                <p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#1e293b;">
                    ✅ What this means:
                </p>
                <ul style="margin:0;padding-left:18px;font-size:13px;line-height:1.6;color:#475569;">
                    <li>Your account is now secured with your new credentials.</li>
                    <li>All other browser sessions have been securely signed out.</li>
                    <li>Your next sign-in will require your new password.</li>
                </ul>
            </td>
        </tr>
    </table>

    <!-- Danger Alert if unauthorized -->
    <div style="background-color:#fff1f2;border:1px solid #fecdd3;border-radius:12px;padding:16px 18px;margin-bottom:22px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="26" valign="top" style="font-size:16px;">🚨</td>
                <td valign="top" style="font-size:13px;line-height:1.5;color:#9f1239;padding-left:8px;">
                    <strong>Did not make this change?</strong>
                    If you did not authorize this password update, someone else may have gained access to your account.
                    Please reset your password immediately and contact our support team.
                </td>
            </tr>
        </table>
    </div>

    <!-- CTA Button -->
    @include('emails.partials.button', ['url' => $resetUrl, 'label' => 'Reset Password If Unauthorized', 'color' => 'danger'])

    <p style="margin:16px 0 0;font-size:13px;color:#64748b;text-align:center;">
        If you made this change yourself, no further action is required!
    </p>
@endsection
