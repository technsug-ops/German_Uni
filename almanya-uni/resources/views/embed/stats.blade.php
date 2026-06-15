@php
    $locale  = app()->getLocale();
    $domain  = 'https://' . brand('domain');
    $name    = brand('name');
    $fullUrl = $domain . '/germany-study-statistics';
    $cells = [
        [number_format($stats['universities']), __('Universities')],
        [number_format($stats['programs']),     __('Study programs')],
        [$stats['en_pct'] . '%',                __('English-taught')],
        [number_format($stats['cities']),       __('Student cities')],
        [number_format($stats['scholarships']), __('Scholarships')],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>{{ __('Germany Study Statistics') }} — {{ $name }}</title>
<style>
    *{box-sizing:border-box;margin:0;padding:0}
    :root{--brand:#1a368d;--brand-dark:#162c6b;--ink:#111827;--muted:#6b7280;--line:#e5e7eb;--bg:#f9fafb}
    html,body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;color:var(--ink);background:#fff}
    .w{max-width:560px;margin:0 auto;padding:14px}
    .card{border:1px solid var(--line);border-radius:14px;overflow:hidden;background:#fff}
    .hd{background:linear-gradient(135deg,var(--brand),var(--brand-dark));color:#fff;padding:12px 16px;font-size:14px;font-weight:800;display:flex;align-items:center;gap:8px}
    .grid{display:grid;grid-template-columns:repeat(5,1fr);gap:1px;background:var(--line)}
    @media(max-width:480px){.grid{grid-template-columns:repeat(2,1fr)}}
    .cell{background:#fff;padding:14px 6px;text-align:center}
    .cell .v{font-size:24px;font-weight:800;color:var(--brand);line-height:1.1}
    .cell .l{font-size:10px;color:var(--muted);margin-top:3px;text-transform:uppercase;letter-spacing:.02em}
    .ft{display:flex;justify-content:space-between;align-items:center;gap:8px;padding:10px 16px;border-top:1px solid var(--line);background:var(--bg);flex-wrap:wrap}
    .ft .by{font-size:11px;color:var(--muted)}
    .ft a{font-size:12px;font-weight:700;text-decoration:none;color:var(--brand)}
    .ft .by a{color:var(--ink)}
</style>
</head>
<body>
<div class="w">
    <div class="card">
        <div class="hd">🇩🇪 {{ __('Studying in Germany — by the numbers') }}</div>
        <div class="grid">
            @foreach ($cells as [$v, $l])
                <div class="cell"><div class="v">{{ $v }}</div><div class="l">{{ $l }}</div></div>
            @endforeach
        </div>
        <div class="ft">
            <span class="by">{{ __('Data by') }} <a href="{{ $fullUrl }}" target="_blank" rel="noopener">{{ $name }}</a></span>
            <a href="{{ $fullUrl }}" target="_blank" rel="noopener">{{ __('See full statistics') }} →</a>
        </div>
    </div>
</div>
</body>
</html>
