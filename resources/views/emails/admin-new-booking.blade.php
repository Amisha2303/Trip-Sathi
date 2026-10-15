@extends('emails.layouts.email')

@section('title', 'New booking · ' . $booking->booking_number)

@section('body')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.35;">New booking on {{ $siteName }}</h1>

    <p style="margin:0 0 16px;font-size:15px;line-height:1.6;">
        A traveller just placed booking <strong>{{ $booking->booking_number }}</strong>.
    </p>

    @include('emails.partials.booking-summary')

    <p style="margin:0 0 16px;font-size:13px;color:#5f7275;">
        Customer: {{ $booking->customer_email }}{{ $booking->customer_phone ? ' · ' . $booking->customer_phone : '' }}
    </p>

    @include('emails.partials.button', ['url' => $adminUrl, 'label' => 'Open in admin'])
@endsection
