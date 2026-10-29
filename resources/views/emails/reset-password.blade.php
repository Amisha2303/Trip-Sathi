@extends('emails.layouts.email')

@section('title', 'Reset Your Password · ' . ($siteName ?? 'Trip Sathi'))
@section('badge', 'Password Reset')

@section('body')
    <!-- Header -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td width="48" valign="top" style="padding-right:14px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="44" height="44" style="width:44px;height:44px;border-collapse:separate;background-color:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;margin:0;">
                    <tr>
                        <td align="center" valign="middle" style="width:44px;height:44px;text-align:center;vertical-align:middle;padding:0;margin:0;font-size:22px;line-height:1;mso-line-height-rule:exactly;">
                            🔑
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="middle">
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#1e293b;line-height:1.25;">
                    Reset Your Password
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    Follow the link below to set a new password.
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.65;color:#334155;">
        We received a request to reset the password associated with <strong>{{ $user->email }}</strong>.
        Click the button below to choose a secure new password.
    </p>

    <!-- Reset Button -->
    @include('emails.partials.button', ['url' => $resetUrl, 'label' => 'Reset My Password', 'color' => 'primary'])

    <!-- Security Expiry Notice -->
    <div style="background-color:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 16px;margin:20px 0;font-size:13px;color:#92400e;line-height:1.5;">
        ⏳ <strong>Security Note:</strong> This password reset link is valid for <strong>{{ $expiresIn }} minutes</strong>. If you did not request a password reset, no action is needed &mdash; your password remains completely unchanged.
    </div>
@endsection
