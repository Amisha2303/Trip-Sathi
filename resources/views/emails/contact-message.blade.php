@extends('emails.layouts.email')

@section('title', 'Support Alert: New Message from ' . $inquiry->name)
@section('badge', 'New Support Enquiry')

@section('body')
    <!-- Header -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td width="48" valign="top" style="padding-right:14px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="44" height="44" style="width:44px;height:44px;border-collapse:separate;background-color:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;margin:0;">
                    <tr>
                        <td align="center" valign="middle" style="width:44px;height:44px;text-align:center;vertical-align:middle;padding:0;margin:0;font-size:22px;line-height:1;mso-line-height-rule:exactly;">
                            📥
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="middle">
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#0b6e75;line-height:1.25;">
                    New Support Inquiry Received
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    Submitted through the public contact form on {{ $siteName }}.
                </p>
            </td>
        </tr>
    </table>

    <!-- Enquiry Card -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;margin:18px 0 24px;">
        <tr>
            <td style="padding:14px 18px;border-bottom:1px solid #e2e8f0;font-size:13px;">
                <span style="color:#64748b;display:inline-block;width:90px;font-weight:600;">Sender:</span>
                <strong style="color:#1e293b;">{{ $inquiry->name }}</strong> &lt;<a href="mailto:{{ $inquiry->email }}" style="color:#0b6e75;">{{ $inquiry->email }}</a>&gt;
                @if ($inquiry->phone)
                    <br><span style="color:#64748b;display:inline-block;width:90px;font-weight:600;">Phone:</span>
                    <strong style="color:#1e293b;">{{ $inquiry->phone }}</strong>
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding:14px 18px;border-bottom:1px solid #e2e8f0;font-size:13px;">
                <span style="color:#64748b;display:inline-block;width:90px;font-weight:600;">Subject:</span>
                <strong style="color:#0b6e75;">{{ $inquiry->subject ?: 'General Enquiry' }}</strong>
            </td>
        </tr>
        <tr>
            <td style="padding:16px 18px;font-size:14px;line-height:1.6;color:#334155;">
                <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;display:block;margin-bottom:6px;">Message Content:</span>
                <div style="background-color:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:14px 16px;color:#1e293b;line-height:1.65;">
                    {!! nl2br(e($inquiry->message)) !!}
                </div>
            </td>
        </tr>
    </table>

    <!-- CTA Button -->
    @include('emails.partials.button', ['url' => $adminUrl, 'label' => 'View & Reply in Admin Inbox', 'color' => 'primary'])
@endsection
