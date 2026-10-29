@extends('emails.layouts.email')

@section('title', 'Reply to Your Enquiry · ' . ($siteName ?? 'Trip Sathi'))
@section('badge', 'Support Update')

@section('body')
    <!-- Header -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td width="48" valign="top" style="padding-right:14px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="44" height="44" style="width:44px;height:44px;border-collapse:separate;background-color:#ecfdf5;border:1px solid #a7f3d0;border-radius:12px;margin:0;">
                    <tr>
                        <td align="center" valign="middle" style="width:44px;height:44px;text-align:center;vertical-align:middle;padding:0;margin:0;font-size:22px;line-height:1;mso-line-height-rule:exactly;">
                            💬
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="middle">
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#0b6e75;line-height:1.25;">
                    Response to Your Enquiry
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    Hi {{ $inquiry->name }}, our support team has an update regarding your request.
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;">
        Thank you for contacting <strong>{{ $siteName }}</strong>. Here is the response from <strong>{{ $responderName }}</strong> regarding ticket <strong>{{ $ticketId }}</strong>:
    </p>

    <!-- Response Bubble Card -->
    <div style="background:linear-gradient(135deg, #f0fdf4 0%, #edfbf8 100%);border:1px solid #6ee7b7;border-left:5px solid #059669;border-radius:12px;padding:20px 22px;margin:20px 0 24px;">
        <span style="font-size:11px;font-weight:700;color:#047857;text-transform:uppercase;letter-spacing:0.8px;display:block;margin-bottom:8px;">
            Reply from Support Team
        </span>
        <div style="font-size:15px;line-height:1.65;color:#1e293b;">
            {!! nl2br(e($replyText)) !!}
        </div>
    </div>

    <!-- Original Enquiry Context -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;margin-bottom:24px;">
        <tr>
            <td style="padding:12px 18px;background-color:#edf2f7;border-bottom:1px solid #e2e8f0;">
                <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">
                    Your Original Message:
                </span>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 18px;font-size:13px;line-height:1.5;color:#475569;">
                <strong>Subject:</strong> {{ $inquiry->subject ?: 'General Enquiry' }}<br>
                <div style="margin-top:6px;color:#64748b;font-style:italic;">
                    "{!! nl2br(e($inquiry->message)) !!}"
                </div>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#475569;">
        Need more help? Simply reply directly to this email to continue the conversation.
    </p>

    <!-- CTA Button -->
    @include('emails.partials.button', ['url' => $homeUrl, 'label' => 'Visit Trip Sathi', 'color' => 'teal'])
@endsection
