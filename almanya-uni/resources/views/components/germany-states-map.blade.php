@php
    use Illuminate\Support\Facades\Cache;

    // Coğrafi yol verisi: @svg-maps/germany (MIT). 16 eyalet, gerçek sınırlar.
    $mapData = Cache::rememberForever('germany_states_map_v1', function () {
        $path = resource_path('data/germany-states-map.json');
        return is_file($path) ? json_decode((string) file_get_contents($path), true) : null;
    });

    // svg-maps ISO id → bizim state slug
    $idToSlug = [
        'bw' => 'baden-wurttemberg', 'by' => 'bayern', 'be' => 'berlin',
        'bb' => 'brandenburg', 'hb' => 'freie-hansestadt-bremen', 'hh' => 'hamburg',
        'he' => 'hessen', 'ni' => 'niedersachsen', 'mv' => 'mecklenburg-vorpommern',
        'nw' => 'nordrhein-westfalen', 'rp' => 'rheinland-pfalz', 'sl' => 'saarland',
        'sn' => 'sachsen', 'st' => 'sachsen-anhalt', 'sh' => 'schleswig-holstein',
        'th' => 'thuringen',
    ];

    // Her eyalete ayrı renk (16 benzersiz ton → komşular hep farklı renkte)
    $palette = ['#fca5a5','#fdba74','#fcd34d','#fde047','#bef264','#86efac','#6ee7b7','#5eead4',
                '#67e8f9','#7dd3fc','#93c5fd','#a5b4fc','#c4b5fd','#d8b4fe','#f0abfc','#f9a8d4'];

    $states = \App\Models\State::query()
        ->get(['id', 'slug', 'name_tr', 'name_de', 'name_en', 'flag_url', 'coat_of_arms_url'])
        ->keyBy('slug');
@endphp

@if($mapData && ! empty($mapData['locations']))
<div class="gm-wrap">
    <p class="gm-hint">{{ __('Click a state to explore its universities and cities') }}</p>
    <div class="gm-figure">
        <svg viewBox="{{ $mapData['viewBox'] }}" class="gm-svg" role="img"
             aria-label="{{ __('Interactive map of German federal states') }}"
             xmlns="http://www.w3.org/2000/svg">
            @foreach($mapData['locations'] as $i => $loc)
                @php
                    $slug = $idToSlug[$loc['id']] ?? null;
                    $st = $slug ? ($states[$slug] ?? null) : null;
                    $color = $palette[$i % count($palette)];
                @endphp
                @if($st)
                    <a href="{{ route('states.show', $st->slug) }}" class="gm-state"
                       data-name="{{ $st->name }}" data-coa="{{ $st->coat_of_arms_url }}" aria-label="{{ $st->name }}">
                        <path d="{{ $loc['path'] }}" style="fill: {{ $color }}"><title>{{ $st->name }}</title></path>
                    </a>
                @else
                    <path d="{{ $loc['path'] }}" class="gm-disabled" />
                @endif
            @endforeach
        </svg>
        <span class="gm-tip" hidden></span>
    </div>
</div>

<style>
    .gm-wrap { max-width: 430px; margin: 0 auto; }
    .gm-hint { text-align: center; font-size: .875rem; color: #6b7280; margin-bottom: .75rem; }
    .gm-figure { position: relative; }
    .gm-svg { width: 100%; height: auto; display: block; overflow: visible; }
    .gm-state path { stroke: #ffffff; stroke-width: 1.2; cursor: pointer; transition: filter .15s ease, stroke-width .15s ease; }
    .gm-state:hover path,
    .gm-state:focus path { filter: brightness(.86); stroke-width: 1.8; outline: none; }
    .gm-disabled { fill: #e5e7eb; stroke: #ffffff; stroke-width: 1.2; }
    .gm-coa { filter: drop-shadow(0 1px 1.5px rgba(0,0,0,.45)); }
    .gm-tip { position: absolute; pointer-events: none; background: #111827; color: #fff;
              font-size: .8rem; font-weight: 600; padding: .3rem .55rem; border-radius: .375rem;
              transform: translate(-50%, -135%); white-space: nowrap; z-index: 10; box-shadow: 0 2px 8px rgba(0,0,0,.25);
              display: flex; align-items: center; gap: .4rem; }
    .gm-tip img { width: 20px; height: 24px; object-fit: contain; }
</style>

<script>
(function () {
    var SVGNS = 'http://www.w3.org/2000/svg', XLINK = 'http://www.w3.org/1999/xlink';

    function init() {
        document.querySelectorAll('.gm-wrap').forEach(function (fig) {
            var svg = fig.querySelector('.gm-svg');
            var tip = fig.querySelector('.gm-tip');
            if (!svg) return;

            // Armaları (Wappen) haritanın üstüne, her eyaletin merkezine yerleştir
            fig.querySelectorAll('.gm-state').forEach(function (a) {
                if (a.dataset.emblem) return;
                var path = a.querySelector('path'), coa = a.getAttribute('data-coa');
                if (!path || !coa) return;
                var bb;
                try { bb = path.getBBox(); } catch (e) { return; }
                if (!bb || !bb.width) return;
                var w = Math.max(20, Math.min(bb.width * 0.5, 34)), h = w * 1.2;
                var img = document.createElementNS(SVGNS, 'image');
                img.setAttribute('href', coa);
                img.setAttributeNS(XLINK, 'xlink:href', coa);
                img.setAttribute('width', w); img.setAttribute('height', h);
                img.setAttribute('x', bb.x + bb.width / 2 - w / 2);
                img.setAttribute('y', bb.y + bb.height / 2 - h / 2);
                img.setAttribute('preserveAspectRatio', 'xMidYMid meet');
                img.setAttribute('pointer-events', 'none');
                img.setAttribute('class', 'gm-coa');
                svg.appendChild(img);
                a.dataset.emblem = '1';
            });

            if (tip && ! fig.dataset.tip) {
                fig.dataset.tip = '1';
                fig.querySelectorAll('.gm-state').forEach(function (el) {
                    el.addEventListener('mousemove', function (e) {
                        var r = fig.querySelector('.gm-figure').getBoundingClientRect();
                        var coa = el.getAttribute('data-coa');
                        tip.innerHTML = (coa ? '<img src="' + coa + '" alt="">' : '') +
                                        '<span>' + el.getAttribute('data-name') + '</span>';
                        tip.style.left = (e.clientX - r.left) + 'px';
                        tip.style.top = (e.clientY - r.top) + 'px';
                        tip.hidden = false;
                    });
                    el.addEventListener('mouseleave', function () { tip.hidden = true; });
                });
            }
        });
    }

    if (document.readyState !== 'loading') init();
    document.addEventListener('DOMContentLoaded', init);
    window.addEventListener('load', init);
})();
</script>
@endif
