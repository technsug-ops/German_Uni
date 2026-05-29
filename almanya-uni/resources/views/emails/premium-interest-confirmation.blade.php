@php
    app()->setLocale($locale ?? 'tr');
    $name = $interest->name ? trim(explode(' ', $interest->name)[0]) : null;
    $tierLabel = match ($interest->tier_interest) {
        'premium' => __('Premium €14/month'),
        'pro'     => __('Pro €49 one-time'),
        default   => __('Not sure yet'),
    };
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $isBeta ? __('Welcome to the beta!') : __('You\'re on the list') }} — {{ $brandName }}</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Inter,sans-serif;background:#F3F4F6;color:#1F2937;">

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background:#F3F4F6;padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width:600px;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.04);">

                <tr>
                    <td style="background:linear-gradient(135deg,#F59E0B 0%,#EF4444 100%);padding:36px 32px 32px;text-align:center;">
                        <div style="display:inline-block;width:56px;height:56px;background:#fff;border-radius:14px;text-align:center;line-height:56px;font-size:28px;margin-bottom:14px;">
                            {{ $isBeta ? '🚀' : '⭐' }}
                        </div>
                        <h1 style="color:#fff;font-size:24px;font-weight:800;margin:0 0 8px;">
                            {{ $name ? __('Hi :name!', ['name' => $name]) : __('Hi there!') }}
                        </h1>
                        <p style="color:#FEF3C7;margin:0;font-size:15px;">
                            @if ($isBeta)
                                {{ __('You\'re on the beta tester list.') }}
                            @else
                                {{ __('You\'re locked in for the 20% early-bird discount.') }}
                            @endif
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:28px 32px 18px;">
                        @if ($isBeta)
                            <p style="font-size:15px;line-height:1.65;color:#374151;margin:0 0 14px;">
                                {{ __('Thanks for signing up to test Premium before public launch. We\'ll send your invite within 2 weeks with:') }}
                            </p>
                            <ul style="font-size:14px;color:#374151;margin:0 0 16px;padding-left:20px;line-height:1.7;">
                                <li>{{ __('Free access for 3 months') }}</li>
                                <li>{{ __('Direct line to give feature feedback') }}</li>
                                <li>{{ __('Founder credit on the launch page') }}</li>
                                <li>{{ __('Lifetime 30% off when you switch to paid') }}</li>
                            </ul>
                        @else
                            <p style="font-size:15px;line-height:1.65;color:#374151;margin:0 0 14px;">
                                {{ __('Thanks for your interest in :brand Premium!', ['brand' => $brandName]) }}
                            </p>
                            <p style="font-size:15px;line-height:1.65;color:#374151;margin:0 0 14px;">
                                {{ __('We\'re polishing the experience. When Premium launches (target Q2 2026), you\'ll be the first to know — with a 20% lifetime discount locked in.') }}
                            </p>
                        @endif

                        <div style="background:#F9FAFB;border-radius:10px;border:1px solid #E5E7EB;padding:16px 20px;margin:18px 0;">
                            <p style="font-size:12px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 6px;">{{ __('Your interest') }}</p>
                            <p style="font-size:16px;font-weight:700;color:#1F2937;margin:0;">{{ $tierLabel }}</p>
                            @if ($interest->note)
                                <p style="font-size:12px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:0.05em;margin:12px 0 6px;">{{ __('You told us') }}</p>
                                <p style="font-size:14px;color:#374151;margin:0;font-style:italic;">"{{ $interest->note }}"</p>
                            @endif
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 32px 24px;">
                        <p style="font-size:14px;color:#374151;margin:0 0 14px;font-weight:600;">{{ __('While you wait, here\'s what\'s free today:') }}</p>
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td style="padding:8px 0;">
                                    <a href="{{ $brandHomeUrl }}/{{ $locale }}/tools/pathway-finder" style="color:#1E40AF;text-decoration:none;font-weight:600;font-size:14px;">🧭 {{ __('Pathway Finder quiz') }} →</a>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:8px 0;">
                                    <a href="{{ $brandHomeUrl }}/{{ $locale }}/tools/eligibility-checker" style="color:#1E40AF;text-decoration:none;font-weight:600;font-size:14px;">✅ {{ __('Eligibility Checker') }} →</a>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:8px 0;">
                                    <a href="{{ $brandHomeUrl }}/{{ $locale }}/journey" style="color:#1E40AF;text-decoration:none;font-weight:600;font-size:14px;">🗺️ {{ __('Application Tracker (8 steps)') }} →</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="background:#F9FAFB;padding:18px 32px;border-top:1px solid #E5E7EB;text-align:center;">
                        <p style="font-size:12px;color:#6B7280;margin:0;">
                            <a href="{{ $pricingUrl }}" style="color:#1E40AF;">{{ __('Pricing page') }}</a>
                            ·
                            <a href="{{ $brandHomeUrl }}" style="color:#1E40AF;">{{ $brandName }}</a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
