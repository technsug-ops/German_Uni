@extends('layouts.app')

@section('title', __('Student Rent Map of Germany') . ' — ' . brand('name'))

<x-seo
    :title="__('Student Rent Map of Germany') . ' - ' . brand('name')"
    :description="__('Average monthly rent (30 m² student flat) across :n German university cities on an interactive map. Source: MLP Studentenwohnreport 2025.', ['n' => $cities->count()])"
/>

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <style>
        #rentMap { height: 72vh; min-height: 520px; border-radius: 0.75rem; position: relative; z-index: 0; isolation: isolate; }
        .leaflet-pane, .leaflet-top, .leaflet-bottom { z-index: 1; }
        .rent-popup h3 { font-weight: 800; font-size: 1rem; margin: 0 0 .15rem; color:#111827; }
        .rent-popup .sub { color:#6b7280; font-size:.72rem; margin-bottom:.4rem; }
        .rent-popup .big { font-size: 1.5rem; font-weight: 900; line-height:1; }
        .rent-popup .idx-up { color: #b91c1c; font-weight:700; } .rent-popup .idx-down { color: #047857; font-weight:700; }
        .rent-popup a.btn { display:inline-block; background:#1E40AF; color:#fff; padding:.4rem .9rem; border-radius:.5rem; font-weight:700; font-size:.8rem; text-decoration:none; margin-top:.6rem; }
        .rent-popup a.btn:hover { background:#162c6b; }
        .rent-legend { background:#fff; padding:.6rem .8rem; border-radius:.6rem; box-shadow:0 2px 10px rgba(0,0,0,.15); font-size:.8rem; line-height:1.5; }
        .rent-legend i { display:inline-block; width:13px; height:13px; border-radius:50%; margin-right:7px; vertical-align:middle; }
        .rent-table th { cursor:pointer; user-select:none; }
        .rent-dot { display:inline-block; width:11px; height:11px; border-radius:50%; margin-right:8px; vertical-align:middle; box-shadow:0 0 0 1px rgba(0,0,0,.08); }
    </style>
@endpush

@php
    use Illuminate\Support\Facades\Route as RouteFacade;
    $isEst = fn($c) => $c->student_rent_source && str_contains($c->student_rent_source, 'ahmin');
    $payload = $cities->map(function ($c) use ($isEst) {
        $name = $c->name;
        return [
            'name' => $name,
            'rent' => (int) $c->student_rent_warm30,
            'kalt' => $c->student_rent_kalt30 ? (int) $c->student_rent_kalt30 : null,
            'wg' => $c->student_rent_wg_warm ? (int) $c->student_rent_wg_warm : null,
            'idx' => $c->student_rent_index !== null ? (float) $c->student_rent_index : null,
            'est' => $isEst($c),
            'lat' => (float) $c->latitude,
            'lng' => (float) $c->longitude,
            'url' => RouteFacade::has('cities.show') ? route('cities.show', $c->slug) : url('/cities/' . $c->slug),
        ];
    })->values();
    $cheapest = $cities->sortBy('student_rent_warm30')->first();
    $expensive = $cities->sortByDesc('student_rent_warm30')->first();
    $min = (int) $cities->min('student_rent_warm30');
    $max = (int) $cities->max('student_rent_warm30');
@endphp

@section('content')
<div class="bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white py-10">
    <div class="max-w-[1400px] mx-auto px-4">
        <h1 class="text-3xl md:text-4xl font-bold mb-3">{{ __('Student Rent Map of Germany') }}</h1>
        <p class="text-primary-100 max-w-3xl mb-6">
            {!! __('Average monthly rent for a <strong>30 m² student flat</strong> (warm rent) across <strong>:n</strong> university cities. Click a city for details.', ['n' => $cities->count()]) !!}
        </p>
        <div class="flex flex-wrap gap-3 text-sm">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/20">
                <span>🏙️</span><span><strong class="text-lg">{{ $cities->count() }}</strong> {{ __('cities') }}</span>
            </div>
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/20">
                <span>🟢</span><span>{{ __('Cheapest') }}: <strong>{{ $cheapest->name }}</strong> {{ $cheapest->student_rent_warm30 }} €</span>
            </div>
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/20">
                <span>🔴</span><span>{{ __('Most expensive') }}: <strong>{{ $expensive->name }}</strong> {{ $expensive->student_rent_warm30 }} €</span>
            </div>
        </div>
    </div>
</div>

<div class="max-w-[1400px] mx-auto px-4 py-8">
    <div class="bg-white border border-gray-200 rounded-xl shadow-md p-3 md:p-4 mb-5">
        <div id="rentMap"></div>
    </div>

    <p class="text-xs text-gray-500 mb-8">
        {{ __('Source') }}: MLP Studentenwohnreport 2025 / Value AG · {{ __('Warm rent (incl. utilities) of a 30 m² model flat. ~ marks estimated cities. Indicative figures — verify current rents locally.') }}
    </p>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">{{ __('All cities by rent') }}</h2>
            <span class="text-xs text-gray-400">{{ __('Click a column to sort') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm rent-table">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th data-k="name" class="text-left px-4 py-2 font-semibold">{{ __('City') }}</th>
                        <th data-k="rent" class="text-left px-4 py-2 font-semibold">{{ __('Flat 30 m² (warm)') }}</th>
                        <th data-k="kalt" class="text-left px-4 py-2 font-semibold hidden sm:table-cell">{{ __('cold') }}</th>
                        <th data-k="wg" class="text-left px-4 py-2 font-semibold">{{ __('Shared room (WG)') }}</th>
                        <th data-k="idx" class="text-left px-4 py-2 font-semibold hidden md:table-cell">{{ __('Yearly change') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($cities->sortByDesc('student_rent_warm30') as $c)
                    @php $est = $isEst($c); @endphp
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-2">
                            <span class="rent-dot" data-rent="{{ $c->student_rent_warm30 }}"></span>
                            <a href="{{ RouteFacade::has('cities.show') ? route('cities.show', $c->slug) : url('/cities/'.$c->slug) }}" class="font-medium text-primary-700 hover:underline">{{ $c->name }}</a>
                            @if($est)<span class="ml-1 text-[10px] font-bold uppercase px-1 py-0.5 rounded bg-amber-100 text-amber-700" title="{{ __('Estimated') }}">{{ __('est.') }}</span>@endif
                        </td>
                        <td class="px-4 py-2"><strong>{{ $est ? '~' : '' }}{{ $c->student_rent_warm30 }} €</strong></td>
                        <td class="px-4 py-2 text-gray-500 hidden sm:table-cell">{{ $c->student_rent_kalt30 ? $c->student_rent_kalt30.' €' : '—' }}</td>
                        <td class="px-4 py-2">{{ $c->student_rent_wg_warm ? $c->student_rent_wg_warm.' €' : '—' }}</td>
                        <td class="px-4 py-2 hidden md:table-cell" style="color:{{ $c->student_rent_index === null ? '#9ca3af' : ($c->student_rent_index < 0 ? '#047857' : '#b91c1c') }};">
                            {{ $c->student_rent_index === null ? '—' : (($c->student_rent_index >= 0 ? '+' : '') . rtrim(rtrim(number_format($c->student_rent_index, 1), '0'), '.') . '%') }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    var CITIES = @json($payload);
    var MIN = {{ $min }}, MAX = {{ $max }};
    function rentColor(r) {
        var t = Math.max(0, Math.min(1, (r - MIN) / Math.max(1, (MAX - MIN))));
        var stops = [[16,185,129],[250,204,21],[239,68,68]];
        var a,b,f;
        if (t < 0.5) { a=stops[0]; b=stops[1]; f=t/0.5; } else { a=stops[1]; b=stops[2]; f=(t-0.5)/0.5; }
        var c = a.map(function (v,i){ return Math.round(v+(b[i]-v)*f); });
        return 'rgb('+c[0]+','+c[1]+','+c[2]+')';
    }
    function radius(r){ var t=(r-MIN)/Math.max(1,(MAX-MIN)); return 8+t*16; }

    var map = L.map('rentMap', { scrollWheelZoom: false, maxZoom: 18 }).setView([51.1, 10.2], 6);
    map.on('click', function () { map.scrollWheelZoom.enable(); });
    map.on('mouseout', function () { map.scrollWheelZoom.disable(); });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(map);

    CITIES.forEach(function (c) {
        var idxTxt = c.idx === null ? '<span style="color:#9ca3af;font-size:.75rem;">{{ __('estimated') }}</span>' :
            '<span class="' + (c.idx<0?'idx-down':'idx-up') + '">' + (c.idx>=0?'+':'') + (Math.round(c.idx*10)/10) + '% {{ __('yearly') }}</span>';
        var kaltTxt = c.kalt ? ' <span style="color:#9ca3af;font-size:.7rem;">({{ __('cold') }} ' + c.kalt + ' €)</span>' : '';
        var wgTxt = c.wg ? '<div style="margin-top:.35rem;font-size:.85rem;"><strong>{{ __('Shared room (WG)') }}:</strong> ' + c.wg + ' €</div>' : '';
        var html = '<div class="rent-popup"><h3>' + c.name + (c.est?' <span style="font-size:.65rem;color:#b45309;">({{ __('est.') }})</span>':'') + '</h3>' +
            '<div class="big">' + (c.est?'~':'') + c.rent + ' €</div>' +
            '<div class="sub">{{ __('30 m² student flat, warm rent') }}' + kaltTxt + '</div>' +
            wgTxt +
            '<div style="margin-top:.35rem;">' + idxTxt + '</div>' +
            '<a class="btn" href="' + c.url + '">{{ __('Explore city') }}</a></div>';
        L.circleMarker([c.lat, c.lng], {
            radius: radius(c.rent), fillColor: rentColor(c.rent), color:'#fff',
            weight: 2, fillOpacity: 0.92, dashArray: c.est ? '3,3' : null
        }).addTo(map).bindPopup(html);
    });

    var legend = L.control({ position: 'bottomright' });
    legend.onAdd = function () {
        var d = L.DomUtil.create('div', 'rent-legend');
        d.innerHTML = '<strong>{{ __('Rent (€/month)') }}</strong><br>' +
            '<i style="background:'+rentColor(MAX)+'"></i>{{ __('high') }} ('+MAX+' €)<br>' +
            '<i style="background:'+rentColor((MIN+MAX)/2)+'"></i>{{ __('medium') }}<br>' +
            '<i style="background:'+rentColor(MIN)+'"></i>{{ __('low') }} ('+MIN+' €)';
        return d;
    };
    legend.addTo(map);

    document.querySelectorAll('#rentMap');
    document.querySelectorAll('.rent-dot').forEach(function (el) {
        el.style.background = rentColor(parseInt(el.dataset.rent, 10));
    });
    var tbody = document.querySelector('.rent-table tbody');
    var COL = { name: 0, rent: 1, kalt: 2, wg: 3, idx: 4 };
    document.querySelectorAll('.rent-table th').forEach(function (th) {
        var dir = th.dataset.k === 'name' ? -1 : 1;
        th.addEventListener('click', function () {
            dir *= -1;
            var k = th.dataset.k, ci = COL[k];
            Array.from(tbody.querySelectorAll('tr')).sort(function (a,b) {
                var at = a.children[ci].innerText, bt = b.children[ci].innerText;
                if (k === 'name') return at.localeCompare(bt) * dir;
                var an = parseFloat(at.replace('~','').replace(',','.')) || -Infinity;
                var bn = parseFloat(bt.replace('~','').replace(',','.')) || -Infinity;
                return (an - bn) * dir;
            }).forEach(function (r){ tbody.appendChild(r); });
        });
    });
})();
</script>
@endpush
