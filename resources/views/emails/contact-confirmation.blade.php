@extends('emails.layouts.email')

@section('title', 'We Have Received Your Message · ' . ($siteName ?? 'Trip Sathi'))
@section('badge', 'Enquiry Received')

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
                    Thanks for Reaching Out, {{ $name }}!
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    We have received your message and our team is on it.
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;">
        Our customer support team has logged your enquiry under reference ticket <strong>{{ $ticketId }}</strong>.
        One of our specialists will review your request and get back to you within <strong>one business day</strong>.
    </p>

    <!-- Message Summary Card -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;margin:20px 0 24px;">
        <tr>
            <td style="padding:14px 18px;background-color:#edf2f7;border-bottom:1px solid #e2e8f0;">
                <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.6px;display:block;">
                    Enquiry Details ({{ $ticketId }})
                </span>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 18px;border-bottom:1px solid #e2e8f0;font-size:13px;">
                <span style="color:#64748b;font-weight:600;display:inline-block;width:80px;">Subject:</span>
                <strong style="color:#1e293b;">{{ $inquiry->subject ?: 'General Enquiry' }}</strong>
            </td>
        </tr>
        <tr>
            <td style="padding:16px 18px;font-size:14px;line-height:1.6;color:#334155;">
                <span style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;display:block;margin-bottom:6px;">Your Message:</span>
                <div style="background-color:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:12px 14px;color:#334155;font-style:italic;">
                    "{!! nl2br(e($inquiry->message)) !!}"
                </div>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 20px;font-size:14px;line-height:1.6;color:#475569;">
        If you have urgent travel questions regarding an ongoing booking, you can reply directly to this email or call our hotline at <strong>{{ $supportPhone }}</strong>.
    </p>

    <!-- CTA Button -->
    @include('emails.partials.button', ['url' => $homeUrl, 'label' => 'Return to Trip Sathi', 'color' => 'teal'])
@endsection
