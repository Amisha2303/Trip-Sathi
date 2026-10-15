@extends('emails.layouts.email')

@section('title', 'New contact message · ' . $siteName)

@section('body')
    <h1 style="margin:0 0 12px;font-size:22px;line-height:1.35;">New message in the inbox</h1>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="border:1px solid #e3ecec;border-radius:10px;margin:0 0 18px;font-size:13px;">
        <tr>
            <td style="padding:14px 18px;border-bottom:1px solid #e3ecec;">
                <span style="color:#7b8f92;">From:</span>
                <strong>{{ $inquiry->name }}</strong> &lt;{{ $inquiry->email }}&gt;
                @if ($inquiry->phone)
                    <br><span style="color:#7b8f92;">Phone:</span> {{ $inquiry->phone }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding:14px 18px;border-bottom:1px solid #e3ecec;">
                <span style="color:#7b8f92;">Subject:</span> <strong>{{ $inquiry->subject }}</strong>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 18px;line-height:1.6;color:#3c4f52;">
                {!! nl2br(e($inquiry->message)) !!}
            </td>
        </tr>
    </table>

    @include('emails.partials.button', ['url' => $adminUrl, 'label' => 'Open in admin'])
@endsection
