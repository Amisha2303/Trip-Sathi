@extends('emails.layouts.email')

@section('title', 'Trip reminder · ' . $trip->name)

@section('body')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.35;">{{ $trip->name }} is almost here</h1>

    <p style="margin:0 0 12px;font-size:15px;line-height:1.6;">
        @if ($daysAway > 0)
            Your trip starts in <strong>{{ $daysAway }} day{{ $daysAway === 1 ? '' : 's' }}</strong>
            ({{ $trip->date_range_label }}).
        @else
            Your trip is under way — have a great time!
        @endif
    </p>

    @if ($items->count())
        <p style="margin:0 0 8px;font-size:11px;color:#7b8f92;text-transform:uppercase;letter-spacing:.5px;">On the
            plan</p>
        <ul style="margin:0 0 18px;padding-left:20px;font-size:14px;line-height:1.7;color:#3c4f52;">
            @foreach ($items as $item)
                <li>{{ $item->name }}</li>
            @endforeach
        </ul>
    @endif

    @include('emails.partials.button', ['url' => $tripUrl, 'label' => 'Open trip plan'])
@endsection
