@extends('emails.layouts.email')

@section('title', 'Booking cancelled · ' . $booking->booking_number)

@section('body')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.35;">Booking cancelled</h1>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.6;">
        {{ $booking->customer_name ?: ($booking->user?->name ?? 'Your booking') }}'s booking
        <strong>{{ $booking->booking_number }}</strong> has been cancelled.
        {{ $refundNote }}
    </p>

    @include('emails.partials.booking-summary')

    @include('emails.partials.button', ['url' => $bookingUrl, 'label' => 'View booking'])

    <p style="margin:18px 0 0;font-size:13px;color:#5f7275;">
        Changed your mind? Most listings can be rebooked while slots last.
    </p>
@endsection
