<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>@yield('title', $siteName)</title>
</head>

<body
    style="margin:0;padding:0;background-color:#f2f5f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#243436;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f2f5f6;">
        <tr>
            <td align="center" style="padding:28px 12px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                    style="width:100%;max-width:600px;background-color:#ffffff;border-radius:14px;overflow:hidden;">
                    <tr>
                        <td style="background-color:#0b6e75;padding:22px 28px;">
                            <span style="color:#ffffff;font-size:19px;font-weight:700;letter-spacing:.2px;">
                                {{ $siteName }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            @yield('body')
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#f0f5f5;padding:18px 28px;border-top:1px solid #e2ecec;">
                            <p style="margin:0 0 6px;font-size:12px;color:#5f7275;">{{ $tagline }}</p>
                            <p style="margin:0;font-size:12px;color:#5f7275;">
                                {{ $supportEmail }} &nbsp;&middot;&nbsp; {{ $supportPhone }}
                            </p>
                            <p style="margin:8px 0 0;font-size:12px;color:#8aa0a3;">
                                &copy; {{ $year }} {{ $siteName }}. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
