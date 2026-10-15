@extends('emails.layouts.email')

@section('title', 'Payment received · ' . $booking->booking_number)

@section('body')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.35;">Payment received</h1>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.6;">
        Thanks — we received your payment for booking <strong>{{ $booking->booking_number }}</strong>.
        Your confirmation is attached to the booking page.
    </p>

    @include('emails.partials.booking-summary')

    @if ($payment)
        <p style="margin:0 0 16px;font-size:13px;color:#5f7275;">
            Gateway reference: <strong>{{ $payment->reference ?? '—' }}</strong>
        </p>
    @endif

    @include('emails.partials.button', ['url' => $bookingUrl, 'label' => 'View booking'])

    <p style="margin:18px 0 0;font-size:13px;color:#5f7275;">
        Invoice: <a href="{{ $invoiceUrl }}" style="color:#0b6e75;">download</a>.
    </p>
@endsection
