@extends('layouts.app')

@section('title', __('Germany Universities Map') . '  — ' . brand('name'))

<x-seo
    :title="__('Germany Universities Map') . ' - ' . $stats['total'] . ' ' . __('universities')"
    :description="__('See all :n universities in Germany on the map. Public/private filter, explore by state, jump to the detail page in one click.', ['n' => $stats['total']])"
/>

@push('head')
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin="">
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">
    <style>
        #uniMap { height: 70vh; min-height: 500px; border-radius: 0.75rem; }
        .uni-popup { font-family: inherit; }
        .uni-popup h3 { font-weight: 700; font-size: 0.95rem; margin: 0 0 0.3rem; line-height: 1.25; }
        .uni-popup .meta { color: #6b7280; font-size: 0.78rem; margin-bottom: 0.5rem; }
        .uni-popup .badge { display: inline-block; padding: 0.1rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }
        .badge-public { background: #d1fae5; color: #065f46; }
        .badge-private { background: #dbeafe; color: #1e40af; }
        .badge-religion { background: #ede9fe; color: #5b21b6; }
        .uni-popup a.btn { display: inline-block; background: #1E40AF; color: #fff; padding: 0.4rem 0.85rem; border-radius: 0.375rem; font-weight: 600; font-size: 0.8rem; text-decoration: none; margin-top: 0.5rem; }
        .uni-popup a.btn:hover { background: #162c6b; }
        /* marker icons */
        .uni-marker { background: #1E40AF; color: #fff; border-radius: 50%; width: 28px !important; height: 28px !important; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.3); border: 2px solid #fff; }
        .uni-marker.private { background: #2563eb; }
        .uni-marker.religion { background: #7c3aed; }
    </style>
@endpush

@section('content')
<div class="bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white py-10"
     data-i18n
     data-i18n-students="{{ __('students') }}"
     data-i18n-public="{{ __('Public') }}"
     data-i18n-private="{{ __('Private') }}"
     data-i18n-religion="{{ __('Religious') }}"
     data-i18n-detail="{{ __('Detail page') }}"
     data-i18n-germany="{{ __('Germany') }}"
     data-i18n-error="{{ __('error') }}"
     data-locale-prefix="{{ app()->getLocale() === config('locale.default') ? '' : '/'.app()->getLocale() }}"
     id="mapI18n">
    <div class="max-w-[1400px] mx-auto px-4">
        <h1 class="text-3xl md:text-4xl font-bold mb-3">🗺️ {{ __('Universities Map') }}</h1>
        <p class="text-primary-100 max-w-3xl">
            {!! __('Explore <strong>:n</strong> universities in Germany on the map. Click, jump to the detail page, compare.', ['n' => $stats['total']]) !!}
        </p>
    </div>
</div>

<div class="max-w-[1400px] mx-auto px-4 py-8">
    {{-- Filters --}}
    <div class="bg-white border border-gray-200 rounded-lg p-4 mb-5 space-y-3">
        <div class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label for="filter-q" class="block text-xs font-semibold text-gray-600 mb-1">🔍 {{ __('Search university name') }}</label>
                <input type="text" id="filter-q" placeholder="TUM, Heidelberg, RWTH..."
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
            </div>
            <div>
                <label for="filter-type" class="block text-xs font-semibold text-gray-600 mb-1">{{ __('Type') }}</label>
                <select id="filter-type" class="border border-gray-300 rounded px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                    <option value="">{{ __('All') }} ({{ $stats['total'] }})</option>
                    <option value="public">{{ __('Public') }} ({{ $stats['public'] }})</option>
                    <option value="private">{{ __('Private') }} ({{ $stats['private'] }})</option>
                    <option value="religion">{{ __('Religious') }} ({{ $stats['religion'] }})</option>
                </select>
            </div>
            <div>
                <label for="filter-state" class="block text-xs font-semibold text-gray-600 mb-1">{{ __('State') }}</label>
                <select id="filter-state" class="border border-gray-300 rounded px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                    <option value="">{{ __('All states') }}</option>
                    @foreach ($states as $s)
                        <option value="{{ $s->slug }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <button id="filter-reset" type="button"
                    class="text-primary-600 hover:text-primary-800 font-semibold text-sm">
                ↻ {{ __('Reset') }}
            </button>
        </div>

        {{-- Quick chip filters --}}
        <div class="flex items-center flex-wrap gap-2 pt-2 border-t border-gray-100">
            <span class="text-xs text-gray-500 mr-1">{{ __('Quick:') }}</span>

            <button type="button" id="chip-english"
                    data-active="false"
                    class="text-xs px-3 py-1.5 rounded-full border bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100 transition data-[active=true]:bg-blue-600 data-[active=true]:text-white">
                🇬🇧 {{ __('Has English programs') }}
            </button>

            <span class="text-xs text-gray-400 mx-2">·</span>
            <span class="text-xs text-gray-500">{{ __('Size:') }}</span>

            <button type="button" data-size="small"
                    class="size-chip text-xs px-3 py-1.5 rounded-full border bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100 transition data-[active=true]:bg-amber-600 data-[active=true]:text-white">
                🏘️ {{ __('Small (<5K)') }}
            </button>
            <button type="button" data-size="medium"
                    class="size-chip text-xs px-3 py-1.5 rounded-full border bg-orange-50 text-orange-700 border-orange-200 hover:bg-orange-100 transition data-[active=true]:bg-orange-600 data-[active=true]:text-white">
                🏙️ {{ __('Medium (5K-20K)') }}
            </button>
            <button type="button" data-size="large"
                    class="size-chip text-xs px-3 py-1.5 rounded-full border bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100 transition data-[active=true]:bg-rose-600 data-[active=true]:text-white">
                🌆 {{ __('Large (>20K)') }}
            </button>

            <div class="ml-auto flex items-center gap-3 text-xs text-gray-500">
                <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-full bg-primary-500"></span> {{ __('Public') }}</span>
                <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-full bg-blue-600"></span> {{ __('Private') }}</span>
                <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-full bg-purple-600"></span> {{ __('Religious') }}</span>
            </div>
        </div>
    </div>

    {{-- Map container --}}
    <div id="uniMap" class="bg-gray-100 border border-gray-200 shadow-md"></div>

    <p class="text-xs text-gray-500 mt-3">
        {{ __('Map data:') }} <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener" class="underline">© OpenStreetMap contributors</a>.
        {{ __('University coordinates: Wikidata.') }} <span id="map-count" class="font-semibold">{{ $stats['total'] }}</span> {{ __('universities shown.') }}
    </p>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
(function () {
    'use strict';

    const i18nEl = document.getElementById('mapI18n');
    const i18n = {
        students: i18nEl?.dataset.i18nStudents || 'students',
        public:   i18nEl?.dataset.i18nPublic   || 'Public',
        private:  i18nEl?.dataset.i18nPrivate  || 'Private',
        religion: i18nEl?.dataset.i18nReligion || 'Religious',
        detail:   i18nEl?.dataset.i18nDetail   || 'Detail page',
        germany:  i18nEl?.dataset.i18nGermany  || 'Germany',
        error:    i18nEl?.dataset.i18nError    || 'error',
    };
    const localePrefix = i18nEl?.dataset.localePrefix || '';

    const map = L.map('uniMap', {
        center: [51.1657, 10.4515],   // Almanya merkez
        zoom: 6,
        minZoom: 5,
        maxZoom: 18,
        scrollWheelZoom: false,        // sayfa kaydırması haritayı zoom'lamasın
    });

    // İyi UX: haritaya tıklayınca tekerlek-zoom açılır, fare çıkınca kapanır.
    // Böylece sayfa rahat kayar; zoom istenince haritaya tıklamak yeter.
    map.on('click', () => map.scrollWheelZoom.enable());
    map.on('mouseout', () => map.scrollWheelZoom.disable());

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    const clusterGroup = L.markerClusterGroup({
        chunkedLoading: true,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        maxClusterRadius: 60,
    });
    map.addLayer(clusterGroup);

    const typeSelect  = document.getElementById('filter-type');
    const stateSelect = document.getElementById('filter-state');
    const qInput      = document.getElementById('filter-q');
    const englishChip = document.getElementById('chip-english');
    const sizeChips   = document.querySelectorAll('.size-chip');
    const resetBtn    = document.getElementById('filter-reset');
    const countLabel  = document.getElementById('map-count');
    let activeSize = null;

    function escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, m =>
            ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m])
        );
    }

    function typeLabel(t) {
        return { public: i18n.public, private: i18n.private, religion: i18n.religion }[t] || t || '—';
    }

    function buildIcon(type) {
        const cls = type === 'private' ? 'uni-marker private'
                  : type === 'religion' ? 'uni-marker religion'
                  : 'uni-marker';
        return L.divIcon({
            html: '<div class="' + cls + '">U</div>',
            className: '',
            iconSize: [28, 28],
            iconAnchor: [14, 14],
            popupAnchor: [0, -14],
        });
    }

    function buildPopup(u) {
        const badgeCls = u.type === 'private' ? 'badge-private'
                       : u.type === 'religion' ? 'badge-religion'
                       : 'badge-public';
        const students = u.students
            ? '<div class="meta">' + Number(u.students).toLocaleString() + ' ' + i18n.students + '</div>'
            : '';
        return '<div class="uni-popup">'
            + '<h3>' + escapeHtml(u.name) + '</h3>'
            + '<div class="meta">' + escapeHtml(u.city || i18n.germany) + '</div>'
            + students
            + '<span class="badge ' + badgeCls + '">' + typeLabel(u.type) + '</span>'
            + '<br><a class="btn" href="' + localePrefix + '/universities/' + encodeURIComponent(u.slug) + '">' + i18n.detail + ' →</a>'
            + '</div>';
    }

    function loadMarkers() {
        const params = new URLSearchParams();
        if (typeSelect.value)  params.set('type',  typeSelect.value);
        if (stateSelect.value) params.set('state', stateSelect.value);
        if (qInput.value.trim().length >= 2) params.set('q', qInput.value.trim());
        if (englishChip.dataset.active === 'true') params.set('english', '1');
        if (activeSize) params.set('size', activeSize);

        clusterGroup.clearLayers();
        countLabel.textContent = '...';

        fetch('/api/map/universities?' + params.toString(), {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            const markers = data.items.map(u => {
                const m = L.marker([u.lat, u.lng], { icon: buildIcon(u.type), title: u.name });
                m.bindPopup(buildPopup(u), { maxWidth: 280 });
                return m;
            });
            clusterGroup.addLayers(markers);
            countLabel.textContent = data.count;
        })
        .catch(err => {
            console.error('Map load failed', err);
            countLabel.textContent = i18n.error;
        });
    }

    // Real-time filtering — dropdown'lar değişince hemen tetikle
    typeSelect.addEventListener('change', loadMarkers);
    stateSelect.addEventListener('change', loadMarkers);

    // Debounced search
    let qTimer;
    qInput.addEventListener('input', () => {
        clearTimeout(qTimer);
        qTimer = setTimeout(loadMarkers, 300);
    });

    // İngilizce chip toggle
    englishChip.addEventListener('click', () => {
        const isActive = englishChip.dataset.active === 'true';
        englishChip.dataset.active = isActive ? 'false' : 'true';
        loadMarkers();
    });

    // Boyut chip'leri (mutex — sadece biri aktif)
    sizeChips.forEach(chip => {
        chip.addEventListener('click', () => {
            const val = chip.dataset.size;
            if (activeSize === val) {
                activeSize = null;
                chip.dataset.active = 'false';
            } else {
                activeSize = val;
                sizeChips.forEach(c => c.dataset.active = (c.dataset.size === val) ? 'true' : 'false');
            }
            loadMarkers();
        });
    });

    resetBtn.addEventListener('click', () => {
        typeSelect.value = '';
        stateSelect.value = '';
        qInput.value = '';
        englishChip.dataset.active = 'false';
        activeSize = null;
        sizeChips.forEach(c => c.dataset.active = 'false');
        loadMarkers();
    });

    loadMarkers();
})();
</script>
@endpush
