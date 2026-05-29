@php
    $name = $user->name ? trim(explode(' ', $user->name)[0]) : null;
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Continue your journey') }} — {{ $brandName }}</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Inter,sans-serif;background:#F3F4F6;color:#1F2937;">

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background:#F3F4F6;padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width:600px;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                <tr>
                    <td style="background:linear-gradient(135deg,#1E40AF 0%,#7C3AED 100%);padding:32px 32px 28px;border-bottom:3px solid #F97316;text-align:center;">
                        <div style="display:inline-block;width:50px;height:50px;background:#F97316;border-radius:12px;text-align:center;line-height:50px;font-size:24px;margin-bottom:12px;">🗺️</div>
                        <h1 style="color:#fff;font-size:24px;font-weight:800;margin:0;">
                            {{ $name ? __('Hi :name,', ['name' => $name]) : __('Your journey awaits') }}
                        </h1>
                        <p style="color:#DBEAFE;margin:8px 0 0;font-size:14px;">
                            {{ __('You\'ve completed :done of :total steps so far.', ['done' => $completedCnt, 'total' => $totalSteps]) }}
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:28px 32px 16px;">
                        <p style="font-size:15px;line-height:1.65;color:#374151;margin:0 0 14px;">
                            {{ __('Your Germany application progress has been quiet for the past two weeks.') }}
                        </p>

                        {{-- Progress bar --}}
                        <div style="background:#F3F4F6;border-radius:8px;overflow:hidden;height:10px;margin-bottom:16px;">
                            <div style="background:linear-gradient(90deg,#1E40AF,#7C3AED);height:10px;width:{{ $progressPct }}%;"></div>
                        </div>
                        <p style="font-size:13px;color:#6B7280;margin:0 0 20px;text-align:center;font-weight:600;">
                            {{ $progressPct }}% {{ __('complete') }}
                        </p>

                        @if ($nextStep)
                            <div style="background:#FEF3C7;border-left:4px solid #F59E0B;padding:14px 18px;border-radius:6px;margin-bottom:20px;">
                                <p style="font-size:12px;font-weight:700;color:#78350F;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 4px;">{{ __('Next step') }}</p>
                                <p style="font-size:16px;font-weight:700;color:#1F2937;margin:0;">{{ $nextStep['emoji'] }} {{ __($nextStep['title']) }}</p>
                                <p style="font-size:13px;color:#4B5563;margin:6px 0 0;">{{ __($nextStep['desc']) }}</p>
                                <p style="font-size:12px;color:#6B7280;margin:6px 0 0;">⏱️ {{ __($nextStep['duration']) }}</p>
                            </div>
                        @else
                            <div style="background:#D1FAE5;border-left:4px solid #10B981;padding:14px 18px;border-radius:6px;margin-bottom:20px;">
                                <p style="font-size:15px;font-weight:700;color:#064E3B;margin:0;">🎉 {{ __('All :total steps complete!', ['total' => $totalSteps]) }}</p>
                                <p style="font-size:13px;color:#065F46;margin:6px 0 0;">{{ __('Viel Erfolg in Deutschland!') }}</p>
                            </div>
                        @endif
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 32px 28px;text-align:center;">
                        <a href="{{ $journeyUrl }}" style="display:inline-block;background:#1E40AF;color:#fff;padding:13px 28px;border-radius:10px;font-weight:700;text-decoration:none;font-size:15px;">
                            🗺️ {{ __('Continue your journey') }}
                        </a>
                    </td>
                </tr>

                <tr>
                    <td style="background:#F9FAFB;padding:18px 32px;border-top:1px solid #E5E7EB;text-align:center;">
                        <p style="font-size:12px;color:#6B7280;margin:0 0 6px;">
                            {{ __('Don\'t want these reminders?') }}
                            <a href="{{ $brandHomeUrl }}/profile/edit?tab=notifications" style="color:#1E40AF;">{{ __('Manage notifications') }}</a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
