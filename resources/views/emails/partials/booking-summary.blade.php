@php
    $items = $items ?? ($booking->items ?? collect());
    $statusColor = match ($booking->status?->value ?? (string) $booking->status) {
        'confirmed', 'completed' => ['bg' => '#ecfdf5', 'text' => '#065f46', 'border' => '#a7f3d0'],
        'cancelled', 'failed' => ['bg' => '#fef2f2', 'text' => '#991b1b', 'border' => '#fecaca'],
        default => ['bg' => '#fffbeb', 'text' => '#92400e', 'border' => '#fde68a'],
    };
@endphp

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
       style="background-color:#f9fbfb;border:1px solid #dbe8e8;border-radius:14px;overflow:hidden;margin:20px 0 24px;">
    
    <!-- Top Summary Header -->
    <tr>
        <td style="padding:16px 20px;background-color:#edf5f5;border-bottom:1px solid #dbe8e8;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td>
                        <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.8px;display:block;">
                            Booking Reference
                        </span>
                        <span style="font-size:18px;font-weight:800;color:#0b6e75;letter-spacing:-0.2px;display:inline-block;margin-top:2px;">
                            {{ $booking->booking_number }}
                        </span>
                    </td>
                    <td align="right" valign="middle">
                        <span style="display:inline-block;padding:5px 12px;background-color:{{ $statusColor['bg'] }};color:{{ $statusColor['text'] }};border:1px solid {{ $statusColor['border'] }};border-radius:16px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;">
                            {{ $booking->status_label }}
                        </span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Booking Details Grid -->
    <tr>
        <td style="padding:18px 20px;border-bottom:1px solid #e5efef;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="50%" valign="top" style="padding-right:10px;">
                        <span style="font-size:11px;font-weight:600;color:#78909c;text-transform:uppercase;letter-spacing:0.4px;display:block;">Primary Guest</span>
                        <span style="font-size:14px;font-weight:700;color:#1e293b;display:block;margin-top:3px;">
                            {{ $booking->customer_name ?: ($booking->user?->name ?? 'Guest') }}
                        </span>
                        @if($booking->customer_email)
                            <span style="font-size:12px;color:#64748b;display:block;">{{ $booking->customer_email }}</span>
                        @endif
                    </td>
                    <td width="50%" valign="top" style="padding-left:10px;">
                        <span style="font-size:11px;font-weight:600;color:#78909c;text-transform:uppercase;letter-spacing:0.4px;display:block;">Booking Date &amp; Slot</span>
                        <span style="font-size:14px;font-weight:700;color:#1e293b;display:block;margin-top:3px;">
                            {{ optional($booking->booking_date)->format('D, d M Y') ?? 'Flexible Date' }}
                        </span>
                        @if($booking->nights)
                            <span style="font-size:12px;color:#64748b;display:block;">{{ $booking->nights }} {{ Str::plural('Night', $booking->nights) }}</span>
                        @endif
                        @if($booking->start_time)
                            <span style="font-size:12px;color:#64748b;display:block;">Time: {{ $booking->start_time }}</span>
                        @endif
                    </td>
                </tr>
                @if($booking->destination)
                <tr>
                    <td colspan="2" style="padding-top:12px;">
                        <span style="font-size:11px;font-weight:600;color:#78909c;text-transform:uppercase;letter-spacing:0.4px;display:block;">Destination / Venue</span>
                        <span style="font-size:13px;font-weight:600;color:#0b6e75;display:block;margin-top:2px;">
                            📍 {{ $booking->destination->name }}
                        </span>
                    </td>
                </tr>
                @endif
            </table>
        </td>
    </tr>

    <!-- Line Items Table -->
    @if ($items && $items->count())
    <tr>
        <td style="padding:16px 20px;border-bottom:1px solid #e5efef;">
            <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.8px;display:block;margin-bottom:10px;">
                Reserved Items &amp; Services
            </span>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                @foreach ($items as $item)
                <tr>
                    <td valign="top" style="padding:5px 0;font-size:13px;color:#334155;">
                        <strong>{{ $item->name }}</strong>
                        @if ((int) $item->quantity > 1)
                            <span style="color:#64748b;font-weight:normal;">&times; {{ $item->quantity }}</span>
                        @endif
                    </td>
                    <td align="right" valign="top" style="padding:5px 0;font-size:13px;font-weight:700;color:#1e293b;white-space:nowrap;">
                        &#8377;{{ number_format((float) $item->total_amount / 100, 2) }}
                    </td>
                </tr>
                @endforeach
            </table>
        </td>
    </tr>
    @endif

    <!-- Financial Totals -->
    <tr>
        <td style="padding:16px 20px;background-color:#ffffff;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                @if((float) ($booking->discount_rupees ?? 0) > 0)
                <tr>
                    <td style="padding:3px 0;font-size:13px;color:#059669;">
                        Coupon Discount ({{ $booking->coupon?->code ?? 'Applied' }})
                    </td>
                    <td align="right" style="padding:3px 0;font-size:13px;font-weight:600;color:#059669;">
                        -&#8377;{{ number_format((float) $booking->discount_rupees, 2) }}
                    </td>
                </tr>
                @endif
                @if((float) ($booking->tax_rupees ?? 0) > 0)
                <tr>
                    <td style="padding:3px 0;font-size:13px;color:#64748b;">
                        Taxes &amp; Fees (GST)
                    </td>
                    <td align="right" style="padding:3px 0;font-size:13px;font-weight:600;color:#64748b;">
                        +&#8377;{{ number_format((float) $booking->tax_rupees, 2) }}
                    </td>
                </tr>
                @endif
                <tr>
                    <td style="padding:8px 0 2px;font-size:15px;font-weight:800;color:#1e293b;">
                        Total Paid / Payable
                    </td>
                    <td align="right" style="padding:8px 0 2px;font-size:18px;font-weight:800;color:#0b6e75;">
                        &#8377;{{ number_format((float) $booking->final_amount_rupees, 2) }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</table>
