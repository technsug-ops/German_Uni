@php
    // Force the user's locale for every __() inside this template — queue workers
    // may run with the default app locale, not the subscriber's preference.
    app()->setLocale($locale ?? 'tr');
    $brandName    = $brandName    ?? brand('name');
    $brandDomain  = $brandDomain  ?? brand('domain');
    $brandHomeUrl = $brandHomeUrl ?? ('https://' . $brandDomain);
    $name = $subscriber->name ? trim(explode(' ', $subscriber->name)[0]) : null;
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Welcome to :brand', ['brand' => $brandName]) }}</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Inter,sans-serif;background:#F3F4F6;color:#1F2937;">

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background:#F3F4F6;padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width:600px;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.04);">

                {{-- Hero --}}
                <tr>
                    <td style="background:linear-gradient(135deg,#1E40AF 0%,#7C3AED 100%);padding:36px 32px 32px;border-bottom:3px solid #F97316;text-align:center;">
                        <img src="{{ asset('img/logos/atg-icon.png') }}" width="56" height="56" alt="" style="display:inline-block;width:56px;height:56px;border-radius:12px;margin-bottom:14px;">
                        <h1 style="color:#fff;font-size:26px;font-weight:800;margin:0 0 8px;">
                            {{ $name
                                ? __('Welcome, :name!', ['name' => $name])
                                : __('Welcome to :brand!', ['brand' => $brandName]) }}
                        </h1>
                        <p style="color:#DBEAFE;margin:0;font-size:15px;">
                            {{ __('Your subscription is confirmed. Here\'s where to start.') }}
                        </p>
                    </td>
                </tr>

                {{-- Intro --}}
                <tr>
                    <td style="padding:30px 32px 18px;">
                        <p style="font-size:15px;line-height:1.65;color:#374151;margin:0 0 14px;">
                            {{ __(':brand is a guide for international students applying to German universities. We send 1–2 emails per week — new posts, deadline reminders, scholarship alerts. No spam.', ['brand' => $brandName]) }}
                        </p>
                        <p style="font-size:15px;line-height:1.65;color:#374151;margin:0;">
                            {{ __('To get the most out of the platform, here are 5 free tools we recommend trying first:') }}
                        </p>
                    </td>
                </tr>

                {{-- Top tools --}}
                <tr>
                    <td style="padding:0 32px 24px;">
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                            @foreach ($topTools as $tool)
                                <tr>
                                    <td style="padding:14px 16px;border:1px solid #E5E7EB;border-radius:10px;background:#FAFAFA;margin-bottom:8px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="vertical-align:top;width:46px;font-size:24px;">{{ $tool['emoji'] }}</td>
                                                <td style="vertical-align:top;padding-left:10px;">
                                                    <a href="{{ $tool['url'] }}" style="color:#1E40AF;text-decoration:none;font-weight:700;font-size:15px;display:block;margin-bottom:3px;">{{ $tool['title'] }} →</a>
                                                    <p style="font-size:13px;line-height:1.55;color:#4B5563;margin:0;">{{ $tool['desc'] }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr><td style="height:8px;line-height:8px;font-size:0;">&nbsp;</td></tr>
                            @endforeach
                        </table>
                    </td>
                </tr>

                {{-- What to expect --}}
                <tr>
                    <td style="padding:0 32px 24px;">
                        <div style="background:#FEF3C7;border-left:4px solid #F59E0B;padding:14px 18px;border-radius:6px;">
                            <p style="font-size:13px;line-height:1.6;color:#78350F;margin:0;">
                                <strong>📅 {{ __('What to expect:') }}</strong>
                                {{ __('1–2 emails a week with new guides, application deadlines, and scholarship alerts. Unsubscribe with a single click anytime.') }}
                            </p>
                        </div>
                    </td>
                </tr>

                {{-- CTA --}}
                <tr>
                    <td style="padding:0 32px 30px;text-align:center;">
                        <a href="{{ $brandHomeUrl }}/{{ $locale }}" style="display:inline-block;background:#1E40AF;color:#fff;padding:13px 28px;border-radius:10px;font-weight:700;text-decoration:none;font-size:15px;">
                            🏠 {{ __('Explore :brand', ['brand' => $brandName]) }}
                        </a>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="background:#F9FAFB;padding:20px 32px;border-top:1px solid #E5E7EB;text-align:center;">
                        <p style="font-size:12px;color:#6B7280;margin:0 0 6px;">
                            {{ __('You received this because you subscribed at') }} <a href="{{ $brandHomeUrl }}" style="color:#1E40AF;">{{ $brandDomain }}</a>.
                        </p>
                        <p style="font-size:12px;color:#6B7280;margin:0;">
                            <a href="{{ $unsubscribeUrl }}" style="color:#6B7280;">{{ __('Unsubscribe') }}</a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
