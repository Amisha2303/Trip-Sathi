@extends('emails.layouts.email')

@section('title', 'Booking confirmed · ' . $booking->booking_number)

@section('body')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.35;">You are confirmed!</h1>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.6;">
        Your payment cleared and the booking is locked in. Keep this email — the booking number is on every
        receipt.
    </p>

    @include('emails.partials.booking-summary')

    @include('emails.partials.button', ['url' => $bookingUrl, 'label' => 'View booking'])

    <p style="margin:18px 0 0;font-size:13px;color:#5f7275;">
        Invoice copy:
        <a href="{{ $invoiceUrl }}" style="color:#0b6e75;">download here</a>.
    </p>
@endsection
