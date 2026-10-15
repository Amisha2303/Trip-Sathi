@extends('emails.layouts.email')

@section('title', 'Refund processed · ' . $booking->booking_number)

@section('body')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.35;">Your refund is on its way</h1>

    <p style="margin:0 0 12px;font-size:15px;line-height:1.6;">
        We processed a refund for booking <strong>{{ $booking->booking_number }}</strong>.
    </p>

    @if ($refund)
        <p style="margin:0 0 16px;font-size:14px;background-color:#eef7f2;border-radius:8px;padding:12px 14px;">
            <strong>&#8377;{{ number_format((float) $refund->amount / 100, 2) }}</strong>
            @if ($refund->reference)
                &middot; reference {{ $refund->reference }}
            @endif
            @if ($refund->processed_at)
                &middot; {{ $refund->processed_at->format('d M Y, h:i A') }}
            @endif
        </p>
    @endif

    @include('emails.partials.booking-summary')

    <p style="margin:0 0 16px;font-size:13px;color:#5f7275;">
        Banks usually post the amount within 5&ndash;7 working days.
    </p>

    @include('emails.partials.button', ['url' => $bookingUrl, 'label' => 'View booking'])
@endsection
