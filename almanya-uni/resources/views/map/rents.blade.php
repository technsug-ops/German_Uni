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
        #rentMap { height: 70vh; min-height: 480px; border-radius: 0.75rem; position: relative; z-index: 0; isolation: isolate; }
        .leaflet-pane, .leaflet-top, .leaflet-bottom { z-index: 1; }
        .rent-popup h3 { font-weight: 700; font-size: 0.95rem; margin: 0 0 .25rem; }
        .rent-popup .big { font-size: 1.25rem; font-weight: 800; }
        .rent-popup .idx-up { color: #b91c1c; } .rent-popup .idx-down { color: #047857; }
        .rent-popup a.btn { display:inline-block; background:#1E40AF; color:#fff; padding:.35rem .8rem; border-radius:.375rem; font-weight:600; font-size:.8rem; text-decoration:none; margin-top:.5rem; }
        .rent-legend { background:#fff; padding:.5rem .7rem; border-radius:.5rem; box-shadow:0 1px 4px rgba(0,0,0,.2); font-size:.8rem; line-height:1.4; }
        .rent-legend i { display:inline-block; width:12px; height:12px; border-radius:50%; margin-right:6px; vertical-align:middle; }
        .rent-table { width:100%; border-collapse:collapse; font-size:.9rem; }
        .rent-table th, .rent-table td { padding:.5rem .6rem; border-bottom:1px solid #e5e7eb; text-align:left; }
        .rent-table th { cursor:pointer; user-select:none; background:#f9fafb; }
        .rent-dot { display:inline-block; width:11px; height:11px; border-radius:50%; margin-right:7px; vertical-align:middle; }
    </style>
@endpush

@php
    use Illuminate\Support\Facades\Route as RouteFacade;
    $payload = $cities->map(function ($c) {
        $name = $c->name_tr ?: $c->name_de;
        return [
            'name' => $name,
            'rent' => (int) $c->student_rent_warm30,
            'idx' => $c->student_rent_index !== null ? (float) $c->student_rent_index : null,
            'lat' => (float) $c->latitude,
            'lng' => (float) $c->longitude,
            'url' => RouteFacade::has('cities.show') ? route('cities.show', $c->slug) : url('/cities/' . $c->slug),
        ];
    })->values();
    $min = (int) $cities->min('student_rent_warm30');
    $max = (int) $cities->max('student_rent_warm30');
    $source = optional($cities->first())->student_rent_source ?: 'MLP Studentenwohnreport 2025';
@endphp

<main class="container" style="max-width:1100px;margin:0 auto;padding:1.5rem 1rem;">
    <h1 style="font-size:1.6rem;font-weight:800;margin-bottom:.4rem;">{{ __('Student Rent Map of Germany') }}</h1>
    <p style="color:#4b5563;margin-bottom:.25rem;">
        {{ __('Average monthly rent for a 30 m² student flat (warm rent) across :n university cities. Click a city for details.', ['n' => $cities->count()]) }}
    </p>
    <p style="color:#9ca3af;font-size:.8rem;margin-bottom:1rem;">{{ __('Source') }}: {{ $source }} · {{ __('indicative figures; verify current rents locally.') }}</p>

    <div id="rentMap" aria-label="{{ __('Interactive student rent map') }}"></div>

    <h2 style="font-size:1.2rem;font-weight:700;margin:1.75rem 0 .6rem;">{{ __('All cities by rent') }}</h2>
    <div style="overflow-x:auto;">
    <table class="rent-table" id="rentTable">
        <thead><tr>
            <th data-k="name">{{ __('City') }}</th>
            <th data-k="rent">{{ __('Rent (30 m², warm)') }}</th>
            <th data-k="idx">{{ __('Yearly change') }}</th>
        </tr></thead>
        <tbody>
        @foreach($cities as $c)
            <tr>
                <td><span class="rent-dot" data-rent="{{ $c->student_rent_warm30 }}"></span><a href="{{ RouteFacade::has('cities.show') ? route('cities.show', $c->slug) : url('/cities/'.$c->slug) }}">{{ $c->name_tr ?: $c->name_de }}</a></td>
                <td><strong>{{ $c->student_rent_warm30 }} €</strong></td>
                <td style="color:{{ $c->student_rent_index < 0 ? '#047857' : '#b91c1c' }};">
                    {{ $c->student_rent_index !== null ? (($c->student_rent_index >= 0 ? '+' : '') . rtrim(rtrim(number_format($c->student_rent_index, 1), '0'), '.') . '%') : '—' }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</main>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    var CITIES = @json($payload);
    var MIN = {{ $min }}, MAX = {{ $max }};

    // yeşil(ucuz) → sarı → kırmızı(pahalı) renk skalası
    function rentColor(r) {
        var t = Math.max(0, Math.min(1, (r - MIN) / Math.max(1, (MAX - MIN))));
        var stops = [[16,185,129],[250,204,21],[239,68,68]]; // green, amber, red
        var a, b, f;
        if (t < 0.5) { a = stops[0]; b = stops[1]; f = t / 0.5; }
        else { a = stops[1]; b = stops[2]; f = (t - 0.5) / 0.5; }
        var c = a.map(function (v, i) { return Math.round(v + (b[i] - v) * f); });
        return 'rgb(' + c[0] + ',' + c[1] + ',' + c[2] + ')';
    }
    function radius(r) { var t = (r - MIN) / Math.max(1, (MAX - MIN)); return 7 + t * 13; }

    var map = L.map('rentMap', { scrollWheelZoom: false }).setView([51.1, 10.2], 6);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap, © CARTO', maxZoom: 18
    }).addTo(map);

    CITIES.forEach(function (c) {
        var idxTxt = c.idx === null ? '' :
            '<span class="' + (c.idx < 0 ? 'idx-down' : 'idx-up') + '">' +
            (c.idx >= 0 ? '+' : '') + (Math.round(c.idx * 10) / 10) + '% {{ __('yearly') }}</span>';
        var html = '<div class="rent-popup"><h3>' + c.name + '</h3>' +
            '<div class="big">' + c.rent + ' €</div>' +
            '<div style="color:#6b7280;font-size:.8rem;">{{ __('30 m² student flat, warm rent') }}</div>' +
            (idxTxt ? '<div style="margin-top:.25rem;">' + idxTxt + '</div>' : '') +
            '<a class="btn" href="' + c.url + '">{{ __('Explore city') }}</a></div>';
        L.circleMarker([c.lat, c.lng], {
            radius: radius(c.rent), fillColor: rentColor(c.rent), color: '#fff',
            weight: 1.5, fillOpacity: 0.9
        }).addTo(map).bindPopup(html);
    });

    // legend
    var legend = L.control({ position: 'bottomright' });
    legend.onAdd = function () {
        var d = L.DomUtil.create('div', 'rent-legend');
        d.innerHTML = '<strong>{{ __('Rent (€/month)') }}</strong><br>' +
            '<i style="background:' + rentColor(MAX) + '"></i>{{ __('high') }} (' + MAX + ' €)<br>' +
            '<i style="background:' + rentColor((MIN + MAX) / 2) + '"></i>{{ __('medium') }}<br>' +
            '<i style="background:' + rentColor(MIN) + '"></i>{{ __('low') }} (' + MIN + ' €)';
        return d;
    };
    legend.addTo(map);

    // tablo nokta renkleri + sıralama
    document.querySelectorAll('#rentTable .rent-dot').forEach(function (el) {
        el.style.background = rentColor(parseInt(el.dataset.rent, 10));
    });
    var tbody = document.querySelector('#rentTable tbody');
    document.querySelectorAll('#rentTable th').forEach(function (th) {
        var dir = 1;
        th.addEventListener('click', function () {
            dir *= -1;
            var k = th.dataset.k;
            var rows = Array.from(tbody.querySelectorAll('tr'));
            rows.sort(function (a, b) {
                var av, bv;
                if (k === 'name') { av = a.children[0].innerText; bv = b.children[0].innerText; return av.localeCompare(bv) * dir; }
                if (k === 'rent') { av = parseInt(a.children[1].innerText); bv = parseInt(b.children[1].innerText); }
                else { av = parseFloat(a.children[2].innerText) || 0; bv = parseFloat(b.children[2].innerText) || 0; }
                return (av - bv) * dir;
            });
            rows.forEach(function (r) { tbody.appendChild(r); });
        });
    });
})();
</script>
@endpush
