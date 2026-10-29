@extends('emails.layouts.email')

@section('title', 'Your Trip Plan: ' . $trip->name . ' · ' . ($siteName ?? 'Trip Sathi'))
@section('badge', 'Trip Plan Ready')

@section('body')
    <!-- Header -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
        <tr>
            <td width="48" valign="top" style="padding-right:14px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="44" height="44" style="width:44px;height:44px;border-collapse:separate;background-color:#ecfdf5;border:1px solid #a7f3d0;border-radius:12px;margin:0;">
                    <tr>
                        <td align="center" valign="middle" style="width:44px;height:44px;text-align:center;vertical-align:middle;padding:0;margin:0;font-size:22px;line-height:44px;mso-line-height-rule:exactly;">
                            <center style="text-align:center;margin:0 auto;line-height:44px;">🗺️</center>
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="middle">
                <h1 style="margin:0;font-size:22px;font-weight:800;color:#0b6e75;line-height:1.25;">
                    Your Trip Itinerary is Ready!
                </h1>
                <p style="margin:4px 0 0;font-size:14px;color:#64748b;">
                    Hi {{ $user->first_name ?? $user->name ?? 'Traveler' }}, your trip plan has been saved to your account.
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;">
        Congratulations on planning your upcoming journey <strong>"{{ $trip->name }}"</strong>! Here is an overview of what you have planned so far:
    </p>

    <!-- Trip Details Card -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:#f9fbfb;border:1px solid #dce8e8;border-radius:14px;overflow:hidden;margin:20px 0 24px;">
        <tr>
            <td style="padding:16px 20px;background-color:#edf5f5;border-bottom:1px solid #dce8e8;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td>
                            <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.8px;display:block;">
                                Trip Name
                            </span>
                            <span style="font-size:18px;font-weight:800;color:#0b6e75;display:block;margin-top:2px;">
                                {{ $trip->name }}
                            </span>
                        </td>
                        <td align="right" valign="middle">
                            <span style="display:inline-block;padding:5px 12px;background-color:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;border-radius:16px;font-size:12px;font-weight:700;">
                                {{ $trip->total_days }} {{ Str::plural('Day', $trip->total_days) }}
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:18px 20px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="50%" valign="top" style="padding-bottom:12px;">
                            <span style="font-size:11px;font-weight:600;color:#78909c;text-transform:uppercase;display:block;">Destination</span>
                            <strong style="font-size:14px;color:#1e293b;display:block;margin-top:2px;">📍 {{ $destinationName }}</strong>
                        </td>
                        <td width="50%" valign="top" style="padding-bottom:12px;">
                            <span style="font-size:11px;font-weight:600;color:#78909c;text-transform:uppercase;display:block;">Travelers</span>
                            <strong style="font-size:14px;color:#1e293b;display:block;margin-top:2px;">👥 {{ $trip->travelers }} {{ Str::plural('Person', $trip->travelers) }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" valign="top">
                            <span style="font-size:11px;font-weight:600;color:#78909c;text-transform:uppercase;display:block;">Dates</span>
                            <span style="font-size:13px;color:#1e293b;display:block;margin-top:2px;font-weight:600;">
                                {{ optional($trip->start_date)->format('d M Y') }} &rarr; {{ optional($trip->end_date)->format('d M Y') }}
                            </span>
                        </td>
                        <td width="50%" valign="top">
                            <span style="font-size:11px;font-weight:600;color:#78909c;text-transform:uppercase;display:block;">Planned Budget</span>
                            <strong style="font-size:14px;color:#0b6e75;display:block;margin-top:2px;">
                                @if($trip->budget)
                                    &#8377;{{ number_format((float) $trip->budget, 2) }}
                                @else
                                    Flexible Budget
                                @endif
                            </strong>
                        </td>
                    </tr>
                    @if($trip->description)
                    <tr>
                        <td colspan="2" style="padding-top:14px;border-top:1px dashed #e2ecec;margin-top:12px;">
                            <span style="font-size:11px;font-weight:600;color:#78909c;text-transform:uppercase;display:block;">Trip Notes</span>
                            <p style="margin:4px 0 0;font-size:13px;color:#475569;line-height:1.5;">{{ $trip->description }}</p>
                        </td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <!-- Next Steps -->
    <p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#475569;">
        Ready to make this trip real? You can now add hotel bookings, book temple darshan tickets, and reserve curated adventure experiences directly into your trip itinerary!
    </p>

    <!-- CTA Button -->
    @include('emails.partials.button', ['url' => $tripUrl, 'label' => 'Open Your Trip Planner', 'color' => 'primary'])

    <p style="margin:16px 0 0;font-size:13px;color:#64748b;text-align:center;">
        Looking for places to stay? <a href="{{ $exploreUrl }}" style="color:#0b6e75;font-weight:600;">Explore hotels and activities</a> for this destination.
    </p>
@endsection
