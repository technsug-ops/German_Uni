<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Your favorites — weekly digest') }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#111827;line-height:1.6;">

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background:#f3f4f6;">
    <tr><td align="center" style="padding:24px 12px;">

        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.05);">

            <tr><td style="background:linear-gradient(135deg,#1e40af 0%,#3b82f6 50%,#f97316 100%);padding:28px 24px;color:#ffffff;text-align:center;">
                <h1 style="margin:0;font-size:24px;font-weight:800;">⭐ {{ __('Your favorites') }}</h1>
                <p style="margin:6px 0 0;font-size:13px;color:#bfdbfe;">{{ now()->translatedFormat('d F Y') }}</p>
            </td></tr>

            <tr><td style="padding:22px 24px 8px;">
                <h2 style="margin:0 0 6px;font-size:17px;color:#111827;">{{ __('Hello :name', ['name' => $user->name ?: __('friend')]) }}</h2>
                <p style="margin:0;color:#4b5563;font-size:14px;">
                    {{ __('Here is what changed this week for the :n items you saved.', ['n' => $payload['favorites_count']]) }}
                </p>
            </td></tr>

            {{-- DEADLINE'LAR (öncelikli) --}}
            @if (! empty($payload['upcoming_deadlines']))
                <tr><td style="padding:18px 24px 4px;">
                    <h3 style="margin:0 0 10px;font-size:15px;color:#dc2626;">⏰ {{ __('Upcoming deadlines (next 30 days)') }}</h3>
                    @foreach ($payload['upcoming_deadlines'] as $dl)
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom:10px;border:1px solid #fecaca;background:#fef2f2;border-radius:8px;">
                            <tr><td style="padding:12px 14px;">
                                <p style="margin:0 0 4px;font-weight:700;color:#7f1d1d;font-size:14px;">{{ $dl['name_de'] ?? $dl['name_en'] }}</p>
                                <p style="margin:0;font-size:13px;color:#991b1b;">
                                    {{ __('Deadline') }}: <strong>{{ \Carbon\Carbon::parse($dl['deadline'])->translatedFormat('d M Y') }}</strong>
                                    ({{ $dl['semester'] === 'winter' ? __('Winter semester') : __('Summer semester') }})
                                </p>
                                <a href="{{ url('/programs/' . $dl['slug']) }}" style="display:inline-block;margin-top:8px;color:#dc2626;font-size:12px;font-weight:600;text-decoration:none;">{{ __('View program') }} →</a>
                            </td></tr>
                        </table>
                    @endforeach
                </td></tr>
            @endif

            {{-- YENİ PROGRAMLAR (favori üniversitelerden) --}}
            @if (! empty($payload['new_programs']))
                <tr><td style="padding:18px 24px 4px;">
                    <h3 style="margin:0 0 10px;font-size:15px;color:#059669;">✨ {{ __('New programmes at your favorite universities') }}</h3>
                    @foreach ($payload['new_programs'] as $p)
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom:10px;border:1px solid #d1fae5;background:#f0fdf4;border-radius:8px;">
                            <tr><td style="padding:12px 14px;">
                                <p style="margin:0 0 4px;font-weight:700;color:#065f46;font-size:14px;">{{ $p['name_de'] ?? $p['name_en'] }}</p>
                                <p style="margin:0;font-size:12px;color:#047857;">
                                    {{ ucfirst($p['degree'] ?? '') }} · {{ $p['university']['name_de'] ?? '' }}
                                </p>
                                <a href="{{ url('/programs/' . $p['slug']) }}" style="display:inline-block;margin-top:8px;color:#059669;font-size:12px;font-weight:600;text-decoration:none;">{{ __('View program') }} →</a>
                            </td></tr>
                        </table>
                    @endforeach
                </td></tr>
            @endif

            {{-- İLGİLİ BLOG'LAR --}}
            @if (! empty($payload['related_blogs']))
                <tr><td style="padding:18px 24px 4px;">
                    <h3 style="margin:0 0 10px;font-size:15px;color:#1e40af;">📖 {{ __('New blog posts you might find useful') }}</h3>
                    @foreach ($payload['related_blogs'] as $blog)
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom:10px;border:1px solid #dbeafe;background:#eff6ff;border-radius:8px;">
                            <tr><td style="padding:12px 14px;">
                                <p style="margin:0 0 4px;font-weight:700;color:#1e3a8a;font-size:14px;">{{ $blog['title'] }}</p>
                                @if (! empty($blog['excerpt']))
                                    <p style="margin:0;font-size:12px;color:#1e40af;line-height:1.5;">{{ \Illuminate\Support\Str::limit($blog['excerpt'], 110) }}</p>
                                @endif
                                <a href="{{ url('/blog/' . $blog['slug']) }}" style="display:inline-block;margin-top:8px;color:#1e40af;font-size:12px;font-weight:600;text-decoration:none;">{{ __('Read article') }} →</a>
                            </td></tr>
                        </table>
                    @endforeach
                </td></tr>
            @endif

            {{-- BOŞ DURUM (favori var ama haftalık değişim yok) --}}
            @if (empty($payload['upcoming_deadlines']) && empty($payload['new_programs']) && empty($payload['related_blogs']))
                <tr><td style="padding:18px 24px;">
                    <p style="margin:0;color:#6b7280;font-size:14px;font-style:italic;">
                        {{ __('No major updates this week — your favorites are stable. Check back next week!') }}
                    </p>
                </td></tr>
            @endif

            {{-- CTA --}}
            <tr><td style="padding:22px 24px;text-align:center;background:#f9fafb;border-top:1px solid #e5e7eb;">
                <a href="{{ url('/profile/favorites') }}" style="display:inline-block;background:#1e40af;color:#fff;padding:10px 22px;border-radius:8px;font-weight:700;font-size:14px;text-decoration:none;">
                    {{ __('Manage favorites') }} →
                </a>
            </td></tr>

            {{-- Footer --}}
            <tr><td style="padding:18px 24px;background:#f3f4f6;text-align:center;color:#9ca3af;font-size:11px;">
                <p style="margin:0;">{{ __('You receive this email because you saved items as favorites on :brand.', ['brand' => $brandName ?? brand('name')]) }}</p>
                <p style="margin:8px 0 0;">
                    <a href="{{ ($brandHomeUrl ?? ('https://' . brand('domain'))) . '/profile/notifications' }}" style="color:#6b7280;text-decoration:underline;">{{ __('Update preferences') }}</a>
                </p>
            </td></tr>

        </table>
    </td></tr>
</table>

</body>
</html>
