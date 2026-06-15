<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Events in :city', ['city' => $city->name]) }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#111827;line-height:1.6;">

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background:#f3f4f6;">
    <tr><td align="center" style="padding:24px 12px;">

        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.05);">

            <tr><td style="background:linear-gradient(135deg,#be123c 0%,#e11d48 50%,#f97316 100%);padding:28px 24px;color:#ffffff;text-align:center;">
                <h1 style="margin:0;font-size:23px;font-weight:800;">🎵 {{ __('Events in :city', ['city' => $city->name]) }}</h1>
                <p style="margin:6px 0 0;font-size:13px;color:#fecdd3;">{{ now()->translatedFormat('d F Y') }}</p>
            </td></tr>

            <tr><td style="padding:22px 24px 6px;">
                <p style="margin:0;color:#4b5563;font-size:14px;">
                    {{ __('New concerts, theatre and cultural events added for :city:', ['city' => $city->name]) }}
                </p>
            </td></tr>

            @foreach ($events as $event)
                <tr><td style="padding:8px 24px;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border:1px solid #fbcfe8;background:#fff1f2;border-radius:8px;">
                        <tr><td style="padding:13px 15px;">
                            <p style="margin:0 0 4px;font-weight:700;color:#9f1239;font-size:15px;">
                                {{ $event->type_emoji ?? '🎵' }} {{ $event->title }}
                            </p>
                            <p style="margin:0;font-size:13px;color:#be123c;">
                                {{ $event->starts_at->translatedFormat('d M Y · H:i') }}
                                @if ($event->location_name) · {{ $event->location_name }} @endif
                            </p>
                            <a href="{{ $baseUrl . '/events/' . $event->slug }}" style="display:inline-block;margin-top:8px;color:#e11d48;font-size:12px;font-weight:600;text-decoration:none;">{{ __('View event') }} →</a>
                        </td></tr>
                    </table>
                </td></tr>
            @endforeach

            <tr><td style="padding:14px 24px 8px;" align="center">
                <a href="{{ $eventsUrl }}" style="display:inline-block;background:#e11d48;color:#ffffff;font-weight:700;font-size:14px;text-decoration:none;padding:11px 26px;border-radius:9px;">
                    {{ __('See all events') }} →
                </a>
            </td></tr>

            <tr><td style="padding:16px 24px 24px;border-top:1px solid #f3f4f6;">
                <p style="margin:0;color:#9ca3af;font-size:12px;text-align:center;">
                    <a href="{{ $brandHomeUrl }}" style="color:#6b7280;text-decoration:none;">{{ $brandName }}</a>
                    &nbsp;·&nbsp;
                    <a href="{{ $unsubscribeUrl }}" style="color:#9ca3af;text-decoration:underline;">{{ __('Unsubscribe') }}</a>
                </p>
            </td></tr>

        </table>

    </td></tr>
</table>

</body>
</html>
