@extends('emails.layouts.email')

@section('title', $siteName)

@section('body')
    <p style="margin:0 0 12px;font-size:15px;line-height:1.6;">
        {{ $tagline }}
    </p>
@endsection
