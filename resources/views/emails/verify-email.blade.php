@extends('emails.layouts.email')

@section('title', 'Verify your email · ' . $siteName)

@section('body')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.35;">Confirm your email address</h1>

    <p style="margin:0 0 12px;font-size:15px;line-height:1.6;">
        Hi {{ explode(' ', trim($user->name))[0] ?? $user->name }}, one quick step before your account is
        fully active. Confirm this address so we can send booking receipts and trip reminders.
    </p>

    @include('emails.partials.button', ['url' => $verificationUrl, 'label' => 'Verify email address'])

    <p style="margin:22px 0 0;font-size:13px;color:#5f7275;">
        If the button does not work, paste this link into your browser:<br>
        <span style="word-break:break-all;">{{ $verificationUrl }}</span>
    </p>
@endsection
