@php
    $brandName    = $brandName    ?? brand('name');
    $brandDomain  = $brandDomain  ?? brand('domain');
    $brandHomeUrl = $brandHomeUrl ?? ('https://' . $brandDomain);
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Email confirmation') }} — {{ $brandName }}</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Inter,sans-serif;background:#F3F4F6;color:#1F2937;">

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background:#F3F4F6;padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width:600px;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.04);">

                <!-- Header -->
                <tr>
                    <td style="background:linear-gradient(135deg,#1E40AF 0%,#1E3A8A 100%);padding:32px 32px 28px;border-bottom:3px solid #F97316;">
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td>
                                    <img src="{{ asset('img/logos/atg-icon.png') }}" width="44" height="44" alt="" style="display:inline-block;width:44px;height:44px;border-radius:10px;vertical-align:middle;">
                                    <span style="color:#fff;font-size:22px;font-weight:800;margin-left:12px;vertical-align:middle;">{{ $brandName }}</span>
                                </td>
                            </tr>
                        </table>
                        <p style="color:#DBEAFE;margin:18px 0 0;font-size:13px;">{{ __('Studying in Germany — Guide for international students') }}</p>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:36px 32px 8px;">
                        <h1 style="margin:0 0 16px;font-size:24px;color:#1F2937;font-weight:700;line-height:1.3;">
                            {{ __('Hi') }}{{ $subscriber->name ? ' ' . $subscriber->name : '' }},
                        </h1>
                        <p style="margin:0 0 16px;font-size:16px;line-height:1.6;color:#374151;">
                            {!! __('Thanks for subscribing to the <strong>:brand newsletter</strong>! 🎉', ['brand' => $brandName]) !!}
                        </p>
                        <p style="margin:0 0 24px;font-size:16px;line-height:1.6;color:#374151;">
                            {{ __('To confirm your email address, click the button below. One click — after that, weekly digests and important updates will start landing in your inbox.') }}
                        </p>

                        <!-- CTA button -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:8px 0 28px;">
                            <tr>
                                <td style="background:#F97316;border-radius:8px;">
                                    <a href="{{ $confirmUrl }}"
                                       style="display:inline-block;padding:14px 28px;color:#fff;font-weight:700;font-size:15px;text-decoration:none;border-radius:8px;">
                                        ✅ {{ __('Confirm my email') }}
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:0 0 8px;font-size:13px;color:#6B7280;line-height:1.5;">
                            {{ __('If the button doesn\'t work, copy and paste this link:') }}
                        </p>
                        <p style="margin:0 0 28px;font-size:12px;color:#1E40AF;word-break:break-all;background:#F9FAFB;padding:10px 12px;border-radius:6px;border:1px solid #E5E7EB;">
                            {{ $confirmUrl }}
                        </p>

                        <hr style="border:none;border-top:1px solid #E5E7EB;margin:0 0 20px;">

                        <h3 style="margin:0 0 12px;font-size:15px;color:#1F2937;">📬 {{ __('What\'s in the newsletter?') }}</h3>
                        <ul style="margin:0 0 24px;padding-left:20px;color:#374151;font-size:14px;line-height:1.7;">
                            <li>{{ __('New blog posts (1-2 per week)') }}</li>
                            <li>{{ __('Application deadlines + reminders') }}</li>
                            <li>{{ __('Newly added universities + programs') }}</li>
                            <li>{{ __('Scholarship and visa updates') }}</li>
                        </ul>

                        <p style="margin:0 0 8px;font-size:12px;color:#9CA3AF;line-height:1.5;">
                            {{ __('If you didn\'t subscribe, ignore this email — your record will be deleted automatically if not confirmed.') }}
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background:#1F2937;padding:20px 32px;border-top:3px solid #F97316;">
                        <p style="margin:0;font-size:12px;color:#9CA3AF;line-height:1.5;">
                            © {{ date('Y') }} {{ $brandName }} · <a href="{{ $brandHomeUrl }}" style="color:#9CA3AF;text-decoration:none;">{{ $brandDomain }}</a><br>
                            <a href="{{ $unsubscribeUrl }}" style="color:#FB923C;text-decoration:underline;">{{ __('Unsubscribe') }}</a>
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
