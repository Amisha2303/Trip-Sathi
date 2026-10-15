@extends('emails.layouts.email')

@section('title', 'Reset your password · ' . $siteName)

@section('body')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.35;">Reset your password</h1>

    <p style="margin:0 0 12px;font-size:15px;line-height:1.6;">
        We received a request to reset the password for
        <strong>{{ $user->email }}</strong>. The link below is valid for {{ $expiresIn }} minutes.
    </p>

    @include('emails.partials.button', ['url' => $resetUrl, 'label' => 'Choose a new password'])

    <p style="margin:22px 0 0;font-size:13px;color:#5f7275;">
        If you did not request this, ignore this email — your password stays unchanged.
    </p>
@endsection
