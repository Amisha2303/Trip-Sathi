@php
    $bg = match ($color ?? 'primary') {
        'teal' => '#0b6e75',
        'dark' => '#1e293b',
        'danger' => '#dc2626',
        'green' => '#059669',
        default => '#ef8f1c', // Primary Trip Sathi amber
    };
    $textColor = in_array($color ?? 'primary', ['primary', 'amber']) ? '#1e293b' : '#ffffff';
    $align = $align ?? 'center';
@endphp
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:24px 0;width:100%;">
    <tr>
        <td align="{{ $align }}">
            <!--[if mso]>
            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $url }}" style="height:46px;v-text-anchor:middle;width:220px;" arcsize="20%" fillcolor="{{ $bg }}" stroke="f">
            <w:anchorlock/>
            <center style="color:{{ $textColor }};font-family:sans-serif;font-size:14px;font-weight:bold;">{{ $label }}</center>
            </v:roundrect>
            <![endif]-->
            <a href="{{ $url }}"
               style="background-color:{{ $bg }};color:{{ $textColor }};display:inline-block;font-size:14px;font-weight:700;line-height:46px;text-align:center;text-decoration:none;padding:0 28px;border-radius:24px;box-shadow:0 4px 14px rgba(0,0,0,0.12);letter-spacing:0.3px;mso-hide:all;">
                {{ $label }} &rarr;
            </a>
        </td>
    </tr>
</table>
