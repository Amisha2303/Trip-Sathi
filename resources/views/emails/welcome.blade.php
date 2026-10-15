@extends('emails.layouts.email')

@section('title', 'Welcome to ' . $siteName)

@section('body')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.35;">Hi {{ $firstName }}, welcome aboard!</h1>

    <p style="margin:0 0 12px;font-size:15px;line-height:1.6;">
        Your {{ $siteName }} account is ready. Use it to plan trips, save destinations and stays,
        book VIP darshan slots and keep every confirmation in one place.
    </p>

    <p style="margin:0 0 4px;font-size:15px;line-height:1.6;">A good first step:</p>
    <ul style="margin:0 0 8px;padding-left:20px;font-size:14px;line-height:1.7;color:#3c4f52;">
        <li>Save a few destinations to your wishlist</li>
        <li>Build a day-by-day plan in the trip planner</li>
        <li>Complete your profile so bookings are faster</li>
    </ul>

    @include('emails.partials.button', ['url' => $exploreUrl, 'label' => 'Explore destinations'])

    <p style="margin:22px 0 0;font-size:13px;color:#5f7275;">
        Need a hand? Just reply to this address or write to {{ $supportEmail }}.
    </p>
@endsection
