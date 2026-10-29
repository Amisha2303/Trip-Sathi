@extends('emails.layouts.email')

@section('title', 'Trip Reminder: ' . $trip->name)
@section('badge', 'Upcoming Trip')

@section('body')
    <!-- Header -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td width="48" valign="top" style="padding-right:14px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="44" height="44" style="width:44px;height:44px;border-collapse:separate;background-color:#ecfdf5;border:1px solid #a7f3d0;border-radius:12px;margin:0;">
                    <tr>
                        <td align="center" valign="middle" style="width:44px;height:44px;text-align:center;vertical-align:middle;padding:0;margin:0;font-size:22px;line-height:44px;mso-line-height-rule:exactly;">
                            <center style="text-align:center;margin:0 auto;line-height:44px;">🌄</center>
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="middle">
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#0b6e75;line-height:1.25;">
                    {{ $trip->name }} is Almost Here!
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    @if ($daysAway > 0)
                        Your journey begins in <strong>{{ $daysAway }} {{ Str::plural('day', $daysAway) }}</strong>.
                    @else
                        Your trip is underway &mdash; enjoy every moment!
                    @endif
                </p>
            </td>
        </tr>
    </table>

    <!-- Trip Date Pill -->
    <div style="background-color:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:14px 18px;margin-bottom:20px;font-size:14px;color:#166534;">
        🗓️ <strong>Scheduled Dates:</strong> {{ $trip->date_range_label }}
    </div>

    @if ($items && $items->count())
    <div style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:16px 20px;margin-bottom:22px;">
        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.6px;display:block;margin-bottom:8px;">
            On Your Itinerary:
        </span>
        <ul style="margin:0;padding-left:18px;font-size:13px;line-height:1.7;color:#334155;">
            @foreach ($items as $item)
                <li><strong>{{ $item->name }}</strong></li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- CTA Button -->
    @include('emails.partials.button', ['url' => $tripUrl, 'label' => 'Open Trip Plan & Voucher', 'color' => 'primary'])
@endsection
