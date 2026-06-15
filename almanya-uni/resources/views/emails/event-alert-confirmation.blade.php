<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Confirm your event alerts') }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#111827;line-height:1.6;">

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background:#f3f4f6;">
    <tr><td align="center" style="padding:24px 12px;">

        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.05);">

            <tr><td style="background:linear-gradient(135deg,#be123c 0%,#e11d48 50%,#f97316 100%);padding:28px 24px;color:#ffffff;text-align:center;">
                <h1 style="margin:0;font-size:23px;font-weight:800;">🎵 {{ __('Event alerts for :city', ['city' => $city?->name]) }}</h1>
            </td></tr>

            <tr><td style="padding:24px 24px 8px;">
                <p style="margin:0 0 14px;color:#374151;font-size:15px;">
                    {{ __('One last step — confirm your email to start receiving a weekly summary of concerts, theatre and cultural events in :city.', ['city' => $city?->name]) }}
                </p>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr><td align="center" style="padding:8px 0 18px;">
                        <a href="{{ $confirmUrl }}" style="display:inline-block;background:#e11d48;color:#ffffff;font-weight:700;font-size:15px;text-decoration:none;padding:13px 30px;border-radius:9px;">
                            {{ __('Confirm subscription') }} →
                        </a>
                    </td></tr>
                </table>
                <p style="margin:0;color:#9ca3af;font-size:12px;">
                    {{ __('If you did not request this, just ignore this email — no subscription will be created without confirmation.') }}
                </p>
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
