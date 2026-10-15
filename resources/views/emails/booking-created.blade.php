@extends('emails.layouts.email')

@section('title', 'Booking received · ' . $booking->booking_number)

@section('body')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.35;">We have your booking</h1>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.6;">
        Thanks {{ $booking->customer_name ?: ($booking->user?->first_name ?? 'there') }} — your request is in.
        Nothing is confirmed until payment is complete, so please settle it to lock the slot.
    </p>

    @include('emails.partials.booking-summary')

    @include('emails.partials.button', ['url' => $payUrl, 'label' => 'Complete payment'])

    <p style="margin:18px 0 0;font-size:13px;color:#5f7275;">
        You can also review it any time from
        <a href="{{ $bookingUrl }}" style="color:#0b6e75;">your bookings page</a>.
    </p>
@endsection
