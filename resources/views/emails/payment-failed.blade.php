@extends('emails.layouts.email')

@section('title', 'Payment failed · ' . $booking->booking_number)

@section('body')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.35;">Payment did not go through</h1>

    <p style="margin:0 0 12px;font-size:15px;line-height:1.6;">
        We could not complete the payment for booking <strong>{{ $booking->booking_number }}</strong>.
    </p>

    @if ($reason)
        <p style="margin:0 0 16px;font-size:14px;color:#8a4b08;background-color:#fff6e8;border-radius:8px;padding:12px 14px;">
            Reason: {{ $reason }}
        </p>
    @endif

    @include('emails.partials.booking-summary')

    @include('emails.partials.button', ['url' => $retryUrl, 'label' => 'Try payment again'])

    <p style="margin:18px 0 0;font-size:13px;color:#5f7275;">
        Your booking is held for now — retry before the slot sells out, or
        <a href="{{ $bookingUrl }}" style="color:#0b6e75;">open the booking</a> for details.
    </p>
@endsection
