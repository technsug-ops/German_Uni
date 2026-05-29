@php
    $isMentor = $recipient === 'mentor';
    $headline = $isMentor
        ? __(':user booked a session with you', ['user' => $user->name])
        : __('Your session with :mentor is booked', ['mentor' => $mentor->name]);
    $localeStr = $scheduledAt->translatedFormat('l, d M Y · H:i');
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $headline }} — {{ $brandName }}</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Inter,sans-serif;background:#F3F4F6;color:#1F2937;">

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background:#F3F4F6;padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width:600px;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.04);">

                <tr>
                    <td style="background:linear-gradient(135deg,#10B981 0%,#1E40AF 100%);padding:32px 32px 28px;text-align:center;">
                        <div style="display:inline-block;width:56px;height:56px;background:#fff;border-radius:14px;text-align:center;line-height:56px;font-size:28px;margin-bottom:14px;">🤝</div>
                        <h1 style="color:#fff;font-size:22px;font-weight:800;margin:0;">{{ $headline }}</h1>
                    </td>
                </tr>

                <tr>
                    <td style="padding:28px 32px;">
                        <p style="font-size:15px;line-height:1.6;color:#374151;margin:0 0 18px;">
                            @if ($isMentor)
                                {{ __('A new mentee has booked a session with you. Details below — Jitsi link is unique to this session.') }}
                            @else
                                {{ __('Your session is confirmed. Save the Jitsi link below — it works without an account.') }}
                            @endif
                        </p>

                        {{-- Session card --}}
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background:#F9FAFB;border-radius:10px;border:1px solid #E5E7EB;">
                            <tr>
                                <td style="padding:18px 22px;">
                                    <p style="font-size:12px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 6px;">📅 {{ __('When') }}</p>
                                    <p style="font-size:16px;font-weight:700;color:#1F2937;margin:0 0 16px;">{{ $localeStr }}</p>

                                    <p style="font-size:12px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 6px;">⏱️ {{ __('Duration') }}</p>
                                    <p style="font-size:16px;font-weight:700;color:#1F2937;margin:0 0 16px;">{{ $duration }} {{ __('minutes') }}</p>

                                    <p style="font-size:12px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 6px;">
                                        {{ $isMentor ? '🎓 ' . __('Mentee') : '🤝 ' . __('Mentor') }}
                                    </p>
                                    <p style="font-size:16px;font-weight:700;color:#1F2937;margin:0 0 4px;">
                                        {{ $isMentor ? $user->name : $mentor->name }}
                                    </p>
                                    @if (! $isMentor && $mentor->headline)
                                        <p style="font-size:13px;color:#6B7280;margin:0;">{{ $mentor->headline }}</p>
                                    @endif

                                    @if ($session->topic)
                                        <p style="font-size:12px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:0.05em;margin:16px 0 6px;">💬 {{ __('Topic') }}</p>
                                        <p style="font-size:15px;color:#1F2937;margin:0;">{{ $session->topic }}</p>
                                    @endif

                                    @if ($session->notes && $isMentor)
                                        <p style="font-size:12px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:0.05em;margin:16px 0 6px;">📝 {{ __('Mentee notes') }}</p>
                                        <p style="font-size:14px;color:#374151;margin:0;line-height:1.55;">{{ $session->notes }}</p>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        {{-- Jitsi CTA --}}
                        <div style="text-align:center;margin:24px 0 8px;">
                            <a href="{{ $jitsiUrl }}" style="display:inline-block;background:#1E40AF;color:#fff;padding:14px 32px;border-radius:12px;font-weight:700;text-decoration:none;font-size:16px;box-shadow:0 4px 12px rgba(30,64,175,0.3);">
                                🎥 {{ __('Open meeting room') }}
                            </a>
                        </div>
                        <p style="font-size:12px;text-align:center;color:#6B7280;margin:8px 0 20px;">
                            {{ __('Bookmark this link — works in any browser, no account needed.') }}
                        </p>

                        {{-- Tips --}}
                        <div style="background:#EFF6FF;border-left:4px solid #3B82F6;padding:14px 18px;border-radius:6px;">
                            <p style="font-size:13px;font-weight:700;color:#1E3A8A;margin:0 0 6px;">💡 {{ __('Tips') }}</p>
                            <ul style="font-size:13px;color:#1E40AF;margin:0;padding-left:18px;line-height:1.7;">
                                @if ($isMentor)
                                    <li>{{ __('Confirm 24h before the session — message via your contact channel.') }}</li>
                                    <li>{{ __('If the mentee no-shows, mark them as such in your dashboard.') }}</li>
                                @else
                                    <li>{{ __('Test your microphone + camera 5 minutes before.') }}</li>
                                    <li>{{ __('Prepare 2-3 specific questions — the more concrete, the more useful.') }}</li>
                                    <li>{{ __('If you need to cancel, please do so 24h in advance.') }}</li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="background:#F9FAFB;padding:18px 32px;border-top:1px solid #E5E7EB;text-align:center;">
                        <p style="font-size:12px;color:#6B7280;margin:0 0 6px;">
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
