<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt {{ $booking->booking_number }} — Trip Sathi</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 32px 16px;
            background: #f4f6f7;
            color: #22303c;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }
        .sheet {
            max-width: 820px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #dfe6e8;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(20, 50, 60, .08);
        }
        .top { display: flex; justify-content: space-between; gap: 24px; flex-wrap: wrap; }
        .brand { font-size: 22px; font-weight: 800; letter-spacing: -.02em; color: #0b6e75; }
        .muted { color: #6b7c86; }
        .right { text-align: right; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 28px; }
        th, td { padding: 11px 8px; border-bottom: 1px solid #e7edef; text-align: left; vertical-align: top; }
        th { font-size: 12px; text-transform: uppercase; letter-spacing: .06em; color: #6b7c86; }
        td.num, th.num { text-align: right; }
        .totals { margin-left: auto; max-width: 320px; margin-top: 22px; }
        .totals div { display: flex; justify-content: space-between; padding: 6px 0; }
        .totals .grand { border-top: 2px solid #22303c; margin-top: 6px; padding-top: 10px; font-size: 17px; font-weight: 800; }
        .badge {
            display: inline-block; padding: 3px 10px; border-radius: 999px;
            font-size: 12px; font-weight: 700; background: #e4f6ee; color: #18794e;
        }
        .badge.is-due { background: #fdf1dd; color: #9a6206; }
        .meta { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-top: 26px; }
        .meta span { display: block; font-size: 12px; text-transform: uppercase; letter-spacing: .06em; color: #6b7c86; }
        .footer { margin-top: 34px; padding-top: 18px; border-top: 1px solid #e7edef; font-size: 12px; color: #6b7c86; }
        .print { max-width: 820px; margin: 0 auto 14px; text-align: right; }
        button {
            border: 0; border-radius: 7px; padding: 9px 16px; background: #0b6e75; color: #fff;
            font: inherit; font-weight: 700; cursor: pointer;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .sheet { border: 0; border-radius: 0; box-shadow: none; padding: 0; max-width: none; }
            .print { display: none; }
        }
    </style>
</head>

<body>
    <div class="print"><button type="button" onclick="window.print()">Print / Save as PDF</button></div>

    <main class="sheet">
        <div class="top">
            <div>
                <div class="brand">Trip Sathi</div>
                <div class="muted">Trips, stays and experiences across India</div>
                <div class="muted">{{ $appSettings['contact_email'] ?? 'hello@tripsathi.com' }}</div>
                <div class="muted">{{ $appSettings['contact_phone'] ?? '+91 9904530641' }}</div>
            </div>
            <div class="right">
                <h1>{{ ($booking->payment_status->isSettled() ? 'Payment receipt' : 'Booking summary') }}</h1>
                <div class="muted">{{ $booking->booking_number }}</div>
                <div style="margin-top:8px;">
                    <span class="badge {{ $booking->payment_status->isSettled() ? '' : 'is-due' }}">
                        {{ $booking->payment_status->label() }}
                    </span>
                </div>
                <div class="muted" style="margin-top:8px;">Issued {{ now()->format('d M Y') }}</div>
            </div>
        </div>

        <div class="meta">
            <div><span>Customer</span>{{ $booking->customer_name }}</div>
            <div><span>Email</span>{{ $booking->customer_email }}</div>
            <div><span>Phone</span>{{ $booking->customer_phone ?: '—' }}</div>
            <div><span>Booking date</span>{{ $booking->booking_date?->format('d M Y') }}</div>
            <div><span>Payment method</span>{{ $payment?->method_label ?? '—' }}</div>
            <div><span>Reference</span>{{ $payment?->transaction_reference ?? '—' }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Date</th>
                    <th class="num">Qty</th>
                    <th class="num">Unit</th>
                    <th class="num">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($booking->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->name }}</strong>
                            <div class="muted">{{ $booking->type_label }}</div>
                        </td>
                        <td>{{ $item->service_date?->format('d M Y') ?? '—' }}</td>
                        <td class="num">{{ $item->quantity }}</td>
                        <td class="num">{{ \App\Support\Money::format((int) $item->unit_price) }}</td>
                        <td class="num">{{ \App\Support\Money::format((int) $item->total_amount) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div><span>Subtotal</span><span>{{ \App\Support\Money::format((int) $booking->subtotal) }}</span></div>
            @if ((int) $booking->discount > 0)
                <div><span>Discount</span><span>-{{ \App\Support\Money::format((int) $booking->discount) }}</span></div>
            @endif
            <div><span>Taxes &amp; fees</span><span>{{ \App\Support\Money::format((int) $booking->tax) }}</span></div>
            @if ((int) $booking->refunded_amount > 0)
                <div><span>Refunded</span><span>-{{ \App\Support\Money::format((int) $booking->refunded_amount) }}</span></div>
            @endif
            <div class="grand"><span>Total</span><span>{{ \App\Support\Money::format((int) $booking->final_amount) }}</span></div>
        </div>

        <div class="footer">
            This document is generated from the Trip Sathi booking record. For changes or refunds, contact support with
            the booking number above. Amounts are shown in {{ $booking->currency }}.
        </div>
    </main>
</body>

</html>
