@php
    // Brand context (Mailable'dan with(...) ile geliyor; fallback'lerle güvene al)
    $brandName    = $brandName    ?? brand('name');
    $brandDomain  = $brandDomain  ?? brand('domain');
    $brandHomeUrl = $brandHomeUrl ?? ('https://' . $brandDomain);
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandName }} {{ __('Weekly') }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#111827;line-height:1.6;">

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background:#f3f4f6;">
    <tr><td align="center" style="padding:24px 12px;">

        {{-- Email container --}}
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.05);">

            {{-- Header --}}
            <tr><td style="background:linear-gradient(135deg,#1e40af 0%,#3b82f6 50%,#f97316 100%);padding:32px 24px;color:#ffffff;text-align:center;">
                <h1 style="margin:0;font-size:28px;font-weight:800;">📬 {{ $brandName }} {{ __('Weekly') }}</h1>
                <p style="margin:8px 0 0;font-size:14px;color:#bfdbfe;">{{ now()->format('d F Y') }} · {{ __('Week :week', ['week' => now()->weekOfYear]) }}</p>
            </td></tr>

            {{-- Greeting --}}
            <tr><td style="padding:24px 24px 8px;">
                <h2 style="margin:0 0 8px;font-size:18px;color:#111827;">{{ __('Hello :name,', ['name' => $subscriber->name ?: __('friend')]) }}</h2>
                <p style="margin:0;color:#4b5563;font-size:15px;">
                    {{ __('This week: :count fresh picks and :deadlines upcoming application deadlines, hand-picked for your study-in-Germany journey.', [
                        'count' => $stats['total'] ?? count($items),
                        'deadlines' => $stats['deadlines'] ?? 0,
                    ]) }}
                </p>
            </td></tr>

            {{-- Items --}}
            <tr><td style="padding:16px 24px;">
                @foreach ($items as $i)
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom:12px;border:1px solid #e5e7eb;border-radius:8px;">
                        <tr>
                            @if (!empty($i['image']))
                                <td style="width:120px;padding:8px;vertical-align:top;">
                                    <img src="{{ $i['image'] }}" alt="" width="104" height="78" style="display:block;width:104px;height:78px;object-fit:cover;border-radius:4px;" loading="lazy" decoding="async">
                                </td>
                            @endif
                            <td style="padding:12px 16px;vertical-align:top;">
                                <p style="margin:0 0 4px;font-size:11px;text-transform:uppercase;letter-spacing:0.05em;color:{{ $i['category_color'] ?? '#6b7280' }};font-weight:600;">{{ $i['category'] }}</p>
                                <a href="{{ $i['url'] }}" style="font-size:16px;font-weight:700;color:#111827;text-decoration:none;display:block;margin-bottom:4px;">
                                    {{ $i['title'] }}
                                </a>
                                <p style="margin:0;font-size:13px;color:#6b7280;line-height:1.5;">
                                    {{ \Illuminate\Support\Str::limit($i['description'], 130) }}
                                </p>
                                <a href="{{ $i['url'] }}" style="display:inline-block;margin-top:8px;color:#2563eb;font-size:13px;font-weight:600;text-decoration:none;">
                                    {{ __('See details →') }}
                                </a>
                            </td>
                        </tr>
                    </table>
                @endforeach
            </td></tr>

            {{-- Yaklaşan başvuru deadline'ları --}}
            @if (!empty($deadlines))
                <tr><td style="padding:8px 24px 0;">
                    <h3 style="margin:0 0 8px;font-size:15px;color:#111827;">⏰ {{ __('Upcoming Deadlines') }}</h3>
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #fee2e2;border-radius:8px;background:#fef2f2;">
                        @foreach ($deadlines as $d)
                            <tr><td style="padding:10px 14px;{{ ! $loop->last ? 'border-bottom:1px solid #fee2e2;' : '' }}font-size:13px;line-height:1.5;">
                                <span style="display:inline-block;background:#dc2626;color:#ffffff;font-weight:700;border-radius:4px;padding:2px 8px;font-size:12px;white-space:nowrap;">{{ \Illuminate\Support\Carbon::parse($d['date'])->format('d.m.Y') }}</span>
                                <a href="{{ $d['url'] }}" style="color:#111827;text-decoration:none;font-weight:600;">{{ \Illuminate\Support\Str::limit($d['program'], 48) }}</a>@if (!empty($d['university']))<span style="color:#6b7280;"> · {{ $d['university'] }}</span>@endif
                            </td></tr>
                        @endforeach
                    </table>
                    <p style="margin:8px 0 0;text-align:right;"><a href="{{ route('tools.deadlines') }}" style="color:#dc2626;font-size:13px;font-weight:600;text-decoration:none;">{{ __('See all deadlines →') }}</a></p>
                </td></tr>
            @endif

            {{-- CTA --}}
            <tr><td style="padding:16px 24px 24px;text-align:center;">
                <a href="{{ $brandHomeUrl }}" style="display:inline-block;padding:12px 24px;background:#1e40af;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:600;font-size:15px;">
                    {{ __('Explore :brand →', ['brand' => $brandName]) }}
                </a>
            </td></tr>

            {{-- Footer --}}
            <tr><td style="padding:24px;background:#f9fafb;border-top:1px solid #e5e7eb;text-align:center;font-size:12px;color:#6b7280;">
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

    </td></tr>
</table>

</body>
</html>
