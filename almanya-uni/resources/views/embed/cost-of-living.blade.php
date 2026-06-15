@php
    $locale  = app()->getLocale();
    $domain  = 'https://' . brand('domain');
    $name    = brand('name');
    $fullUrl = $domain . '/' . $locale . '/tools/cost-of-living';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>{{ __('Cost of Living Calculator') }} — {{ $name }}</title>
<style>
    *{box-sizing:border-box;margin:0;padding:0}
    :root{--brand:#1a368d;--brand-dark:#162c6b;--ink:#111827;--muted:#6b7280;--line:#e5e7eb;--bg:#f9fafb}
    html,body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;color:var(--ink);background:#fff}
    .w{max-width:480px;margin:0 auto;padding:16px}
    .card{border:1px solid var(--line);border-radius:14px;overflow:hidden;background:#fff}
    .hd{background:linear-gradient(135deg,var(--brand),var(--brand-dark));color:#fff;padding:16px 18px}
    .hd h1{font-size:17px;font-weight:800;line-height:1.25;display:flex;align-items:center;gap:8px}
    .hd p{font-size:12px;opacity:.85;margin-top:4px}
    .body{padding:16px 18px}
    .row{margin-bottom:12px}
    label{display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.03em;color:var(--muted);margin-bottom:5px}
    select{width:100%;border:1px solid var(--line);border-radius:9px;padding:9px 10px;font-size:14px;background:#fff;color:var(--ink);appearance:none;cursor:pointer}
    select:focus{outline:none;border-color:var(--brand)}
    .seg{display:flex;gap:6px}
    .seg button{flex:1;border:1px solid var(--line);background:#fff;color:var(--muted);border-radius:9px;padding:8px 4px;font-size:12px;font-weight:600;cursor:pointer;transition:.12s}
    .seg button.on{background:var(--brand);color:#fff;border-color:var(--brand)}
    .total{margin-top:14px;background:var(--bg);border-radius:12px;padding:14px 16px;text-align:center}
    .total .lbl{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.03em;color:var(--muted)}
    .total .val{font-size:34px;font-weight:800;color:var(--brand);line-height:1.1;margin-top:2px}
    .total .sub{font-size:11px;color:var(--muted);margin-top:2px}
    .lines{margin-top:12px;border-top:1px solid var(--line)}
    .ln{display:flex;justify-content:space-between;align-items:center;padding:8px 2px;font-size:13px;border-bottom:1px solid var(--line)}
    .ln span:first-child{color:#374151}
    .ln span:last-child{font-weight:700}
    .ft{display:flex;justify-content:space-between;align-items:center;gap:8px;padding:12px 18px;border-top:1px solid var(--line);background:var(--bg);flex-wrap:wrap}
    .ft a{font-size:12px;font-weight:700;text-decoration:none;color:var(--brand)}
    .ft .by{font-size:11px;color:var(--muted)}
    .ft .by a{color:var(--ink)}
    .empty{text-align:center;color:var(--muted);font-size:13px;padding:18px 0}
</style>
</head>
<body>
<div class="w">
    <div class="card">
        <div class="hd">
            <h1>🇩🇪 {{ __('Germany Cost of Living Calculator') }}</h1>
            <p>{{ __('Estimated monthly student expenses') }}</p>
        </div>
        <div class="body">
            <div class="row">
                <label for="city">{{ __('City') }}</label>
                <select id="city">
                    <option value="">— {{ __('Choose city') }} —</option>
                    @foreach ($cities as $i => $c)
                        <option value="{{ $i }}">{{ $c['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <label>{{ __('Housing type') }}</label>
                <div class="seg" id="housing">
                    <button data-v="wg" class="on">{{ __('Shared (WG)') }}</button>
                    <button data-v="studio">{{ __('Studio') }}</button>
                    <button data-v="apartment">{{ __('Apartment') }}</button>
                </div>
            </div>
            <div class="row">
                <label>{{ __('Lifestyle') }}</label>
                <div class="seg" id="lifestyle">
                    <button data-v="frugal">{{ __('Frugal') }}</button>
                    <button data-v="normal" class="on">{{ __('Normal') }}</button>
                    <button data-v="comfortable">{{ __('Comfortable') }}</button>
                </div>
            </div>

            <div id="result">
                <div class="empty">{{ __('Pick a city to see the estimate.') }}</div>
            </div>
        </div>
        <div class="ft">
            <span class="by">{{ __('Data by') }} <a href="{{ $fullUrl }}" target="_blank" rel="noopener">{{ $name }}</a></span>
            <a href="{{ $fullUrl }}" target="_blank" rel="noopener">{{ __('Full calculator') }} →</a>
        </div>
    </div>
</div>

<script>
(function () {
    var CITIES = @json($cities);
    var T = {
        rent: @json(__('Rent')),
        food: @json(__('Food')),
        transport: @json(__('Transport')),
        utilities: @json(__('Utilities')),
        insurance: @json(__('Health insurance')),
        fun: @json(__('Entertainment')),
        misc: @json(__('Other')),
        total: @json(__('Estimated monthly cost')),
        perMonth: @json(__('per month')),
        pick: @json(__('Pick a city to see the estimate.')),
    };
    var MULT = { frugal: 0.75, normal: 1.0, comfortable: 1.30 };

    var housing = 'wg', lifestyle = 'normal', cityIdx = '';
    var citySel = document.getElementById('city');
    var resultEl = document.getElementById('result');

    function eur(n) { return '€' + Math.round(n).toLocaleString('de-DE'); }

    function seg(id, current, setter) {
        document.getElementById(id).addEventListener('click', function (e) {
            var btn = e.target.closest('button'); if (!btn) return;
            setter(btn.getAttribute('data-v'));
            this.querySelectorAll('button').forEach(function (b) { b.classList.remove('on'); });
            btn.classList.add('on');
            render();
        });
    }
    seg('housing', housing, function (v) { housing = v; });
    seg('lifestyle', lifestyle, function (v) { lifestyle = v; });
    citySel.addEventListener('change', function () { cityIdx = this.value; render(); });

    function render() {
        if (cityIdx === '' || !CITIES[cityIdx]) {
            resultEl.innerHTML = '<div class="empty">' + T.pick + '</div>';
            return;
        }
        var c = CITIES[cityIdx];
        var rent = housing === 'studio' ? c.studio : housing === 'apartment' ? c.apartment : c.wg;
        var m = MULT[lifestyle];
        var food = Math.round(c.food * m), fun = Math.round(c.fun * m), misc = Math.round(c.misc * m);
        var total = rent + c.transport + c.utilities + c.insurance + food + fun + misc;

        var lines = [
            [T.rent, rent], [T.food, food], [T.transport, c.transport],
            [T.utilities, c.utilities], [T.insurance, c.insurance], [T.fun, fun], [T.misc, misc],
        ];
        var html = '<div class="total"><div class="lbl">' + T.total + '</div>' +
            '<div class="val">' + eur(total) + '</div>' +
            '<div class="sub">' + T.perMonth + '</div></div><div class="lines">';
        lines.forEach(function (l) {
            html += '<div class="ln"><span>' + l[0] + '</span><span>' + eur(l[1]) + '</span></div>';
        });
        html += '</div>';
        resultEl.innerHTML = html;
    }
})();
</script>
</body>
</html>
