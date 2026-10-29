<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>@yield('title', $siteName ?? 'Trip Sathi')</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        /* Base Resets */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            background-color: #f1f5f7;
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
            border-collapse: collapse !important;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            display: block;
        }
        a {
            text-decoration: none;
            color: #0b6e75;
        }
        /* Mobile Breakpoints */
        @media only screen and (max-width: 620px) {
            .email-container {
                width: 100% !important;
                margin: auto !important;
            }
            .mobile-p-20 {
                padding-left: 18px !important;
                padding-right: 18px !important;
            }
            .mobile-py-20 {
                padding-top: 20px !important;
                padding-bottom: 20px !important;
            }
            .stack-column {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                direction: ltr !important;
            }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1e293b;">

    <!-- Email Body Wrapper -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f1f5f7;width:100%;table-layout:fixed;">
        <tr>
            <td align="center" style="padding:28px 12px 36px;">
                
                <!-- Main Container (600px Max) -->
                <table role="presentation" class="email-container" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background-color:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 12px 34px rgba(11,110,117,0.08);border:1px solid #e1ebed;">
                    
                    <!-- Header Banner -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#0b6e75 0%,#06454a 100%);padding:26px 32px 24px;border-bottom:3px solid #ef8f1c;" class="mobile-p-20">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td valign="middle">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <!-- Brand Icon -->
                                                <td style="vertical-align:middle;padding-right:12px;">
                                                    <div style="background-color:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.25);border-radius:10px;width:38px;height:38px;text-align:center;line-height:38px;font-size:20px;color:#ffffff;display:inline-block;">
                                                        ✈️
                                                    </div>
                                                </td>
                                                <!-- Brand Name & Tagline -->
                                                <td style="vertical-align:middle;">
                                                    <a href="{{ $homeUrl ?? 'https://the-trip-sathi.vercel.app' }}" style="color:#ffffff;text-decoration:none;">
                                                        <span style="display:block;font-size:22px;font-weight:800;letter-spacing:-0.3px;color:#ffffff;line-height:1.2;">
                                                            {{ $siteName ?? 'Trip Sathi' }}
                                                        </span>
                                                    </a>
                                                    <span style="display:block;font-size:11px;color:#b2dfe2;letter-spacing:0.8px;text-transform:uppercase;margin-top:2px;font-weight:600;">
                                                        {{ $tagline ?? 'Your Journey, Curated' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    @hasSection('badge')
                                    <td align="right" valign="middle">
                                        <span style="display:inline-block;padding:6px 14px;background-color:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.3);border-radius:20px;font-size:11px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;color:#ffffff;">
                                            @yield('badge')
                                        </span>
                                    </td>
                                    @endif
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding:34px 34px 30px;background-color:#ffffff;" class="mobile-p-20 mobile-py-20">
                            @yield('body')
                        </td>
                    </tr>

                    <!-- Need Help & Quick Support Banner -->
                    <tr>
                        <td style="padding:0 34px 24px;" class="mobile-p-20">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f6fbfb;border:1px solid #e1eded;border-radius:12px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td width="30" valign="top" style="font-size:18px;line-height:22px;">💬</td>
                                                <td valign="top" style="font-size:13px;line-height:1.5;color:#475569;">
                                                    <strong style="color:#0b6e75;">Questions or need assistance?</strong><br>
                                                    Our travel concierge is here for you 24/7.
                                                    Write to <a href="mailto:{{ $supportEmail ?? 'amisha@tripsathi.com' }}" style="color:#0b6e75;font-weight:600;text-decoration:underline;">{{ $supportEmail ?? 'amisha@tripsathi.com' }}</a>
                                                    @if(!empty($supportPhone))
                                                        or call <a href="tel:{{ preg_replace('/[^0-9+]/', '', $supportPhone) }}" style="color:#0b6e75;font-weight:600;text-decoration:underline;">{{ $supportPhone }}</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f8fbfc;padding:26px 32px 28px;border-top:1px solid #e7eff0;" class="mobile-p-20">
                            <!-- Quick Links -->
                            <p style="margin:0 0 14px;font-size:12px;font-weight:600;color:#64748b;text-align:center;letter-spacing:0.3px;">
                                <a href="{{ $destinationsUrl ?? 'https://the-trip-sathi.vercel.app/destinations' }}" style="color:#0b6e75;text-decoration:none;margin:0 8px;">Explore Destinations</a>
                                &middot;
                                <a href="{{ $hotelsUrl ?? 'https://the-trip-sathi.vercel.app/hotels' }}" style="color:#0b6e75;text-decoration:none;margin:0 8px;">Hotels &amp; Stays</a>
                                &middot;
                                <a href="{{ $darshanUrl ?? 'https://the-trip-sathi.vercel.app/darshan' }}" style="color:#0b6e75;text-decoration:none;margin:0 8px;">VIP Darshan</a>
                                &middot;
                                <a href="{{ $tripsUrl ?? 'https://the-trip-sathi.vercel.app/trips' }}" style="color:#0b6e75;text-decoration:none;margin:0 8px;">Trip Planner</a>
                            </p>

                            <!-- Divider -->
                            <div style="border-top:1px solid #e2ecee;margin:14px 0 16px;"></div>

                            <!-- Footer Tagline & Legal -->
                            <p style="margin:0 0 6px;font-size:12px;color:#64748b;text-align:center;line-height:1.5;">
                                <strong>{{ $siteName ?? 'Trip Sathi' }}</strong> &mdash; Curating unforgettable journeys, holy pilgrimages, and boutique stays across India.
                            </p>
                            <p style="margin:0;font-size:11px;color:#94a3b8;text-align:center;line-height:1.5;">
                                &copy; {{ $year ?? date('Y') }} {{ $siteName ?? 'Trip Sathi' }}. All rights reserved.<br>
                                You received this email because of your activity on {{ rtrim((string) config('app.url', 'https://the-trip-sathi.vercel.app'), '/') }}.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- End Main Container -->

            </td>
        </tr>
    </table>
    <!-- End Email Body Wrapper -->

</body>
</html>
