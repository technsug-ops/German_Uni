@php
    // Brand context (Mailable'dan with(...) ile geliyor; fallback'lerle güvene al)
    $brandName    = $brandName    ?? brand('name');
    $brandDomain  = $brandDomain  ?? brand('domain');
    $brandHomeUrl = $brandHomeUrl ?? ('https://' . $brandDomain);

    // Kartları bölümlere grupla (city+university → tek "Keşif" bölümü).
    // EMOJİ YOK — spam tetikleyici; ayrımı renk + tipografi ile yap.
    $sectionDefs = [
        'blog'        => ['label' => __('Blog'),        'color' => '#2563eb'],
        'news'        => ['label' => __('News'),        'color' => '#dc2626'],
        'scholarship' => ['label' => __('Scholarship'), 'color' => '#b45309'],
        'discovery'   => ['label' => __('Discover'),    'color' => '#0e7490'],
    ];
    $grouped = [];
    foreach ($items as $it) {
        $sec = in_array($it['type'] ?? '', ['city', 'university'], true) ? 'discovery' : ($it['type'] ?? 'blog');
        $grouped[$sec][] = $it;
    }
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandName }} {{ __('Weekly') }}</title>
</head>
<body style="margin:0;padding:0;background:#eef1f6;font-family:-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#111827;line-height:1.6;">

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background:#eef1f6;">
    <tr><td align="center" style="padding:24px 10px;">

        {{-- Email container (geniş: 680px) --}}
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="680" style="max-width:680px;background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 6px 24px rgba(17,24,39,0.08);">

            {{-- Header --}}
            <tr><td style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 55%,#ea580c 100%);padding:34px 28px 28px;text-align:center;">
                <h1 style="margin:0;font-size:26px;font-weight:800;color:#ffffff;letter-spacing:-0.02em;">{{ $brandName }}</h1>
                <p style="margin:8px 0 0;font-size:11px;color:#dbeafe;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;">{{ __('Weekly') }} &nbsp;·&nbsp; {{ now()->format('d F Y') }} &nbsp;·&nbsp; {{ __('Week :week', ['week' => now()->weekOfYear]) }}</p>
            </td></tr>

            {{-- Greeting --}}
            <tr><td style="padding:24px 30px 6px;">
                <h2 style="margin:0 0 6px;font-size:18px;font-weight:700;color:#111827;">{{ __('Hello :name,', ['name' => $subscriber->name ?: __('friend')]) }}</h2>
                <p style="margin:0;color:#4b5563;font-size:14px;">
                    {{ __('This week: :count fresh picks and :deadlines upcoming application deadlines, hand-picked for your study-in-Germany journey.', [
                        'count' => $stats['total'] ?? count($items),
                        'deadlines' => $stats['deadlines'] ?? 0,
                    ]) }}
                </p>
            </td></tr>

            {{-- Bölümler — her biri 2 sütunlu kompakt kart grid'i --}}
            @foreach ($sectionDefs as $key => $sd)
                @if (!empty($grouped[$key]))
                    {{-- Bölüm başlığı (renk + alt çizgi, emoji yok) --}}
                    <tr><td style="padding:20px 30px 0;">
                        <div style="padding-bottom:8px;border-bottom:2px solid {{ $sd['color'] }};">
                            <span style="font-size:12px;font-weight:800;color:{{ $sd['color'] }};letter-spacing:0.08em;text-transform:uppercase;">{{ $sd['label'] }}</span>
                        </div>
                    </td></tr>

                    {{-- Kartlar: 2'şerli satırlar --}}
                    @foreach (array_chunk($grouped[$key], 2) as $pair)
                        <tr><td style="padding:10px 24px 0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tr>
                                @foreach ($pair as $i)
                                    <td width="50%" style="padding:0 6px;vertical-align:top;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #eef0f3;border-left:3px solid {{ $sd['color'] }};border-radius:8px;background:#ffffff;">
                                            <tr><td style="padding:12px 14px;">
                                                <a href="{{ $i['url'] }}" style="font-size:14px;font-weight:700;color:#111827;text-decoration:none;display:block;margin-bottom:5px;line-height:1.3;">{{ $i['title'] }}</a>
                                                <p style="margin:0 0 8px;font-size:12px;color:#6b7280;line-height:1.45;">{{ \Illuminate\Support\Str::limit($i['description'], 90) }}</p>
                                                <a href="{{ $i['url'] }}" style="color:{{ $sd['color'] }};font-size:12px;font-weight:700;text-decoration:none;">{{ __('See details →') }}</a>
                                            </td></tr>
                                        </table>
                                    </td>
                                @endforeach
                                @if (count($pair) === 1)
                                    <td width="50%" style="padding:0 6px;">&nbsp;</td>
                                @endif
                            </tr></table>
                        </td></tr>
                    @endforeach
                @endif
            @endforeach

            {{-- Yaklaşan başvuru deadline'ları --}}
            @if (!empty($deadlines))
                <tr><td style="padding:22px 30px 0;">
                    <div style="padding-bottom:8px;border-bottom:2px solid #dc2626;">
                        <span style="font-size:12px;font-weight:800;color:#dc2626;letter-spacing:0.08em;text-transform:uppercase;">{{ __('Upcoming Deadlines') }}</span>
                    </div>
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:12px;border:1px solid #fee2e2;border-radius:8px;background:#fef2f2;">
                        @foreach ($deadlines as $d)
                            <tr><td style="padding:11px 14px;{{ ! $loop->last ? 'border-bottom:1px solid #fde8e8;' : '' }}font-size:13px;line-height:1.5;">
                                <span style="display:inline-block;background:#dc2626;color:#ffffff;font-weight:700;border-radius:5px;padding:3px 9px;font-size:12px;white-space:nowrap;margin-right:6px;">{{ \Illuminate\Support\Carbon::parse($d['date'])->format('d.m.Y') }}</span>
                                <a href="{{ $d['url'] }}" style="color:#111827;text-decoration:none;font-weight:600;">{{ \Illuminate\Support\Str::limit($d['program'], 50) }}</a>@if (!empty($d['university']))<span style="color:#9ca3af;"> · {{ $d['university'] }}</span>@endif
                            </td></tr>
                        @endforeach
                    </table>
                    <p style="margin:10px 0 0;text-align:right;"><a href="{{ route('tools.deadlines') }}" style="color:#dc2626;font-size:12px;font-weight:700;text-decoration:none;">{{ __('See all deadlines →') }}</a></p>
                </td></tr>
            @endif

            {{-- CTA --}}
            <tr><td style="padding:28px 30px 30px;text-align:center;">
                <a href="{{ $brandHomeUrl }}" style="display:inline-block;padding:13px 32px;background:linear-gradient(135deg,#1e3a8a,#2563eb);color:#ffffff;text-decoration:none;border-radius:9px;font-weight:700;font-size:14px;box-shadow:0 3px 10px rgba(30,58,138,0.22);">{{ __('Explore :brand →', ['brand' => $brandName]) }}</a>
            </td></tr>

            {{-- Footer --}}
            <tr><td style="padding:22px 30px;background:#f9fafb;border-top:1px solid #eef0f3;text-align:center;font-size:12px;color:#6b7280;">
                <p style="margin:0 0 8px;">
                    @if (($brandKey ?? null) === 'almanyauni' && app()->getLocale() === 'tr')
                        {{ $brandName }} — Türk öğrencileri için Almanya rehberi
                    @else
                        {{ $brandName . ' — ' . __('Germany guide for international students') }}
                    @endif
                </p>
                <p style="margin:0;">
                    <a href="{{ $unsubscribeUrl }}" style="color:#6b7280;text-decoration:underline;">{{ __('Unsubscribe') }}</a>
                    ·
                    <a href="{{ $brandHomeUrl }}" style="color:#6b7280;text-decoration:underline;">{{ __('Go to our site') }}</a>
                </p>
                <p style="margin:12px 0 0;font-size:11px;color:#9ca3af;">
                    {{ __('You received this email at :email because you subscribed to :domain.', ['email' => $subscriber->email, 'domain' => $brandDomain]) }}
                </p>
            </td></tr>

        </table>

        <p style="margin:14px 0 0;font-size:11px;color:#9ca3af;">© {{ now()->format('Y') }} {{ $brandName }}</p>

    </td></tr>
</table>

</body>
</html>
