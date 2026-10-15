@php($items = $items ?? collect())
<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
    style="border:1px solid #e3ecec;border-radius:10px;margin:0 0 18px;">
    <tr>
        <td style="padding:16px 18px;border-bottom:1px solid #e3ecec;">
            <p style="margin:0;font-size:11px;color:#7b8f92;text-transform:uppercase;letter-spacing:.5px;">Booking</p>
            <p style="margin:4px 0 0;font-size:16px;font-weight:700;">{{ $booking->booking_number }}</p>
            <p style="margin:4px 0 0;font-size:13px;color:#5f7275;">
                {{ $booking->type_label }} &middot; {{ $booking->status_label }}</p>
        </td>
    </tr>
    <tr>
        <td style="padding:14px 18px;border-bottom:1px solid #e3ecec;font-size:13px;color:#3c4f52;">
            <p style="margin:0 0 4px;">
                <span style="color:#7b8f92;">Guest:</span>
                {{ $booking->customer_name ?: ($booking->user?->name ?? '—') }}</p>
            <p style="margin:0 0 4px;">
                <span style="color:#7b8f92;">Date:</span>
                {{ optional($booking->booking_date)->format('d M Y') }}</p>
            @if ($booking->nights)
                <p style="margin:0;"><span style="color:#7b8f92;">Nights:</span> {{ $booking->nights }}</p>
            @endif
        </td>
    </tr>
    @if ($items->count())
        <tr>
            <td style="padding:14px 18px;border-bottom:1px solid #e3ecec;">
                <p style="margin:0 0 8px;font-size:11px;color:#7b8f92;text-transform:uppercase;letter-spacing:.5px;">
                    Items</p>
                @foreach ($items as $item)
                    <p style="margin:0 0 6px;font-size:13px;">
                        {{ $item->name }}
                        @if ((int) $item->quantity > 1)
                            &times; {{ $item->quantity }}
                        @endif
                        <span style="float:right;font-weight:600;">
                            &#8377;{{ number_format((float) $item->total_amount / 100, 2) }}</span>
                    </p>
                @endforeach
            </td>
        </tr>
    @endif
    <tr>
        <td style="padding:14px 18px;font-size:14px;">
            <p style="margin:0;font-weight:700;">Total
                <span style="float:right;">&#8377;{{ number_format((float) $booking->final_amount_rupees, 2) }}</span>
            </p>
        </td>
    </tr>
</table>
