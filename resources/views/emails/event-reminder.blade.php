@extends('emails.layouts.email')

@section('title', 'Event reminder · ' . $booking->booking_number)

@section('body')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.35;">Your event is coming up</h1>

    @if ($startsAt)
        <p style="margin:0 0 16px;font-size:15px;line-height:1.6;">
            Starts <strong>{{ $startsAt->format('D, d M Y · h:i A') }}</strong>. Reach the venue a little early
            and keep this booking number handy at the gate.
        </p>
    @endif

    @include('emails.partials.booking-summary')

    @include('emails.partials.button', ['url' => $bookingUrl, 'label' => 'View booking'])
@endsection
