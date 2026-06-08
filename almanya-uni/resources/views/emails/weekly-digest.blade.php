@php
    // Brand context (Mailable'dan with(...) ile geliyor; fallback'lerle güvene al)
    $brandName    = $brandName    ?? brand('name');
    $brandDomain  = $brandDomain  ?? brand('domain');
    $brandHomeUrl = $brandHomeUrl ?? ('https://' . $brandDomain);

    // Site paleti (kullanıcının tasarımından — gerçek site renkleri)
    $navy   = '#172C6B'; // header/footer
    $dark   = '#0C1729'; // koyu kutu
    $orange = '#FB7116'; // vurgu/buton/link
    $cream  = '#F8F4EB'; // zemin
    $ink     = '#1A1A1A';
    $muted   = '#6B7280';

    // Logo: hosted PNG (SVG email'de render olmaz, data: URI Gmail'de bloklanır)
    $logoIcon = $brandHomeUrl . '/img/logos/atg-icon.png';

    // Kartlar: ilk N tanesini "fırsat" olarak numaralandır (klasik tasarım numaralı kart)
    $picks = array_slice($items, 0, 8);
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $brandName }} {{ __('Weekly') }}</title>
    <!--[if mso]><style>table,td,div,p,a{font-family:Arial,sans-serif!important}</style><![endif]-->
    <style>
        @media only screen and (max-width:680px){
            .container{width:100%!important}
            .px{padding-left:24px!important;padding-right:24px!important}
            .stack{display:block!important;width:100%!important}
            .h1{font-size:26px!important}
        }
        a{text-decoration:none}
    </style>
</head>
<body style="margin:0;padding:0;background:{{ $cream }};font-family:Arial,Helvetica,sans-serif;">

{{-- Preheader (gizli özet) --}}
<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:{{ $cream }};font-size:1px;line-height:1px;">{{ __('This week’s German-study opportunities, deadlines and news.') }}</div>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:{{ $cream }};"><tr><td align="center" style="padding:28px 12px;">
<table role="presentation" class="container" width="680" cellpadding="0" cellspacing="0" style="width:680px;max-width:680px;background:#FFFFFF;border-radius:16px;overflow:hidden;border:1px solid #E8E8EC;">

    {{-- HEADER (lacivert, navbar gibi) --}}
    <tr><td style="background:{{ $navy }};padding:22px 44px;"><table role="presentation" width="100%"><tr>
        <td align="left" style="vertical-align:middle;">
            <img src="{{ $logoIcon }}" width="30" height="30" alt="" style="display:inline-block;vertical-align:middle;border:0;width:30px;height:30px;">
            <span style="display:inline-block;vertical-align:middle;margin-left:10px;font-size:19px;font-weight:bold;color:#ffffff;letter-spacing:-0.01em;">{{ $brandName }}</span>
        </td>
        <td align="right" style="font-size:11px;color:#9AA8C7;letter-spacing:0.5px;text-align:right;line-height:1.5;">{{ strtoupper(__('Weekly')) }}<br><span style="color:#fff;font-weight:bold;">{{ now()->format('d F Y') }} · {{ __('Week :week', ['week' => now()->weekOfYear]) }}</span></td>
    </tr></table></td></tr>

    {{-- ince turuncu çizgi --}}
    <tr><td style="height:3px;background:{{ $orange }};font-size:0;line-height:3px;">&nbsp;</td></tr>

    {{-- Giriş --}}
    <tr><td class="px" style="padding:30px 44px 6px;">
        <p style="margin:0 0 8px;font-size:11px;font-weight:bold;letter-spacing:2px;color:{{ $orange }};text-transform:uppercase;">{{ __('This Week') }}</p>
        <h1 class="h1" style="margin:0;font-family:Georgia,serif;font-size:28px;line-height:1.25;color:{{ $navy }};">{{ __('Hello :name,', ['name' => $subscriber->name ?: __('friend')]) }}</h1>
        <p style="margin:14px 0 0;font-size:15px;line-height:1.6;color:{{ $muted }};">
            {{ __('This week: :count fresh picks and :deadlines upcoming application deadlines, hand-picked for your study-in-Germany journey.', [
                'count' => $stats['total'] ?? count($items),
                'deadlines' => $stats['deadlines'] ?? 0,
            ]) }}
        </p>
    </td></tr>

    {{-- ana CTA: ilk içeriği oku --}}
    @if (!empty($picks))
        <tr><td class="px" style="padding:20px 44px 6px;"><table role="presentation" cellpadding="0" cellspacing="0"><tr><td style="border-radius:8px;background:{{ $orange }};"><a href="{{ $picks[0]['url'] }}" style="display:inline-block;padding:13px 28px;font-size:14px;font-weight:bold;color:#fff;">{{ __('Read the story →') }}</a></td></tr></table></td></tr>
    @endif

    <tr><td class="px" style="padding:24px 44px 0;"><div style="height:1px;background:#E8E8EC;font-size:0;line-height:1px;">&nbsp;</div></td></tr>

    {{-- Bu Haftanın Fırsatları — numaralı kartlar --}}
    <tr><td class="px" style="padding:22px 44px 14px;"><p style="margin:0;font-size:13px;font-weight:bold;letter-spacing:1.5px;color:{{ $navy }};text-transform:uppercase;"><span style="color:{{ $orange }};">&#9632;</span> {{ __('This Week’s Highlights') }}</p></td></tr>

    @foreach ($picks as $idx => $i)
        <tr><td class="px" style="padding:0 44px 14px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:{{ $cream }};border-radius:10px;border:1px solid #E8E8EC;">
                <tr>
                    <td width="46" style="vertical-align:top;padding:16px 0 16px 16px;">
                        <table role="presentation" cellpadding="0" cellspacing="0"><tr><td align="center" style="width:34px;height:34px;background:{{ $navy }};border-radius:8px;font-family:Georgia,serif;font-size:16px;font-weight:bold;color:{{ $orange }};line-height:34px;">{{ $idx + 1 }}</td></tr></table>
                    </td>
                    <td style="padding:15px 18px 15px 12px;">
                        <a href="{{ $i['url'] }}" style="display:block;margin:0 0 4px;font-size:15px;font-weight:bold;color:{{ $ink }};">{{ $i['title'] }}</a>
                        <p style="margin:0 0 6px;font-size:13px;line-height:1.55;color:{{ $muted }};">{{ \Illuminate\Support\Str::limit($i['description'], 120) }}</p>
                        <a href="{{ $i['url'] }}" style="font-size:12px;font-weight:bold;color:{{ $orange }};">{{ __('See details →') }}</a>
                    </td>
                </tr>
            </table>
        </td></tr>
    @endforeach

    {{-- Yaklaşan deadline'lar — koyu lacivert kutu (ipucu yerine gerçek veri) --}}
    @if (!empty($deadlines))
        <tr><td class="px" style="padding:10px 44px 0;">
            <table role="presentation" width="100%" style="background:{{ $dark }};border-radius:12px;"><tr><td style="padding:22px 24px;">
                <p style="margin:0 0 12px;font-size:12px;font-weight:bold;letter-spacing:2px;color:{{ $orange }};text-transform:uppercase;">{{ __('Upcoming Deadlines') }}</p>
                @foreach ($deadlines as $d)
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td style="padding:7px 0;{{ ! $loop->last ? 'border-bottom:1px solid rgba(255,255,255,0.08);' : '' }}font-size:13px;line-height:1.5;">
                        <span style="display:inline-block;background:#D81E2C;color:#fff;font-weight:bold;border-radius:5px;padding:2px 8px;font-size:11px;white-space:nowrap;margin-right:8px;">{{ \Illuminate\Support\Carbon::parse($d['date'])->format('d.m.Y') }}</span>
                        <a href="{{ $d['url'] }}" style="color:#E8EDF6;font-weight:600;">{{ \Illuminate\Support\Str::limit($d['program'], 46) }}</a>@if (!empty($d['university']))<span style="color:#9AA8C7;"> · {{ $d['university'] }}</span>@endif
                    </td></tr></table>
                @endforeach
                <p style="margin:12px 0 0;"><a href="{{ route('tools.deadlines') }}" style="color:{{ $orange }};font-size:12px;font-weight:bold;">{{ __('See all deadlines →') }}</a></p>
            </td></tr></table>
        </td></tr>
    @endif

    {{-- iki sütun CTA (gerçek site bölümleri) --}}
    <tr><td class="px" style="padding:24px 44px 6px;"><table role="presentation" width="100%"><tr>
        <td class="stack" width="50%" style="vertical-align:top;padding-right:8px;"><table role="presentation" width="100%" style="border:1px solid #E8E8EC;border-radius:10px;"><tr><td style="padding:18px;">
            <p style="margin:0 0 4px;font-size:14px;font-weight:bold;color:{{ $navy }};">{{ __('Free Tools') }}</p>
            <p style="margin:0 0 10px;font-size:12px;line-height:1.5;color:{{ $muted }};">{{ __('Sperrkonto comparison, cost-of-living calculator and more.') }}</p>
            <a href="{{ $brandHomeUrl }}/tools" style="font-size:13px;font-weight:bold;color:{{ $orange }};">{{ __('Explore →') }}</a>
        </td></tr></table></td>
        <td class="stack" width="50%" style="vertical-align:top;padding-left:8px;"><table role="presentation" width="100%" style="border:1px solid #E8E8EC;border-radius:10px;"><tr><td style="padding:18px;">
            <p style="margin:0 0 4px;font-size:14px;font-weight:bold;color:{{ $navy }};">{{ __('Application Journey') }}</p>
            <p style="margin:0 0 10px;font-size:12px;line-height:1.5;color:{{ $muted }};">{{ __('Your personal step-by-step checklist from eligibility to visa.') }}</p>
            <a href="{{ $brandHomeUrl }}/journey" style="font-size:13px;font-weight:bold;color:{{ $orange }};">{{ __('Learn more →') }}</a>
        </td></tr></table></td>
    </tr></table></td></tr>

    {{-- FOOTER lacivert --}}
    <tr><td style="background:{{ $navy }};padding:28px 44px 30px;">
        <p style="margin:0;font-size:11px;color:#9AA8C7;text-align:center;line-height:1.7;">
            @if (($brandKey ?? null) === 'almanyauni' && app()->getLocale() === 'tr')
                <strong style="color:#fff;">{{ $brandName }}</strong> — Türk öğrencileri için Almanya rehberi.<br>
            @else
                <strong style="color:#fff;">{{ $brandName }}</strong> — {{ __('Germany guide for international students') }}.<br>
            @endif
            <a href="{{ $brandHomeUrl }}" style="color:#9AA8C7;text-decoration:underline;">{{ __('Go to our site') }}</a> · <a href="{{ $unsubscribeUrl }}" style="color:#9AA8C7;text-decoration:underline;">{{ __('Unsubscribe') }}</a>
        </p>
        <p style="margin:12px 0 0;font-size:10px;color:#6B7A9E;text-align:center;">{{ __('You received this email at :email because you subscribed to :domain.', ['email' => $subscriber->email, 'domain' => $brandDomain]) }}</p>
    </td></tr>

</table>
</td></tr></table>
</body>
</html>
