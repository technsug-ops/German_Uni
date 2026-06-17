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

    // Her eyalete ayrı renk (16 benzersiz ton)
    $palette = ['#fca5a5','#fdba74','#fcd34d','#fde047','#bef264','#86efac','#6ee7b7','#5eead4',
                '#67e8f9','#7dd3fc','#93c5fd','#a5b4fc','#c4b5fd','#d8b4fe','#f0abfc','#f9a8d4'];

    // viewBox'ı yanlara doğru genişlet (küçük eyalet armalarını beyaz kenara çıkar)
    $vb = preg_split('/\s+/', trim($mapData['viewBox'] ?? '0 0 586 793'));
    $padX = 95;
    $vbPadded = ($vb[0] - $padX) . ' ' . $vb[1] . ' ' . ($vb[2] + 2 * $padX) . ' ' . $vb[3];

    $states = \App\Models\State::query()
        ->get(['id', 'slug', 'name_tr', 'name_de', 'name_en', 'flag_url', 'coat_of_arms_url'])
        ->keyBy('slug');
@endphp

@if($mapData && ! empty($mapData['locations']))
<div class="gm-wrap">
    <p class="gm-hint">{{ __('Click a state to explore its universities and cities') }}</p>
    <div class="gm-figure">
        <svg viewBox="{{ $vbPadded }}" class="gm-svg" role="img"
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
                       data-name="{{ $st->name }}" data-slug="{{ $st->slug }}"
                       data-coa="{{ $st->coat_of_arms_url }}" aria-label="{{ $st->name }}">
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
    .gm-wrap { max-width: 560px; margin: 0 auto; }
    .gm-hint { text-align: center; font-size: .875rem; color: #6b7280; margin-bottom: .75rem; }
    .gm-figure { position: relative; }
    .gm-svg { width: 100%; height: auto; display: block; overflow: visible; }
    .gm-state path { stroke: #ffffff; stroke-width: 1.2; cursor: pointer; transition: filter .15s ease, stroke-width .15s ease; }
    .gm-state:hover path,
    .gm-state:focus path { filter: brightness(.86); stroke-width: 1.8; outline: none; }
    .gm-disabled { fill: #e5e7eb; stroke: #ffffff; stroke-width: 1.2; }
    .gm-coa { filter: drop-shadow(0 1px 2px rgba(0,0,0,.5)); }
    .gm-label { font: 700 12px system-ui, sans-serif; fill: #1f2937; text-anchor: middle;
                paint-order: stroke; stroke: #ffffff; stroke-width: 2.8px; stroke-linejoin: round;
                pointer-events: none; }
    .gm-lead { stroke: #9ca3af; stroke-width: 1.1; stroke-dasharray: 4 3; }
    .gm-dot { fill: #6b7280; }
    .gm-tip { position: absolute; pointer-events: none; background: #111827; color: #fff;
              font-size: .8rem; font-weight: 600; padding: .3rem .55rem; border-radius: .375rem;
              transform: translate(-50%, -135%); white-space: nowrap; z-index: 10; box-shadow: 0 2px 8px rgba(0,0,0,.25);
              display: flex; align-items: center; gap: .4rem; }
    .gm-tip img { width: 20px; height: 24px; object-fit: contain; }
</style>

<script>
(function () {
    var SVGNS = 'http://www.w3.org/2000/svg', XLINK = 'http://www.w3.org/1999/xlink';

    // Küçük eyaletler (az toprak): armayı kenardaki beyaz alana kesikli çizgiyle taşı
    var CALLOUT = {
        'hamburg':                 { x: -58, y: 112 },
        'freie-hansestadt-bremen': { x: -58, y: 232 },
        'berlin':                  { x: 652, y: 205 },
        'saarland':                { x: -78, y: 615 }   // sol-alt çapraz
    };
    // Büyük eyaletlerde arma daha büyük olsun
    var BIG = { 'bayern': 64, 'baden-wurttemberg': 62 };
    // Arma konum ince-ayarı (merkez kayması)
    var OFFSET = { 'brandenburg': { dx: 4, dy: 34 } };

    function place(svg, a) {
        if (a.dataset.emblem) return;
        var path = a.querySelector('path'), coa = a.getAttribute('data-coa'), slug = a.getAttribute('data-slug');
        if (!path || !coa) return;
        var bb;
        try { bb = path.getBBox(); } catch (e) { return; }
        if (!bb || !bb.width) return;
        var cx = bb.x + bb.width / 2, cy = bb.y + bb.height / 2;
        var co = CALLOUT[slug], off = OFFSET[slug] || { dx: 0, dy: 0 };
        var px = co ? co.x : cx + off.dx, py = co ? co.y : cy + off.dy;
        var w, x, y;

        if (co) {
            // kesikli çizgi: eyalet merkezinden kenardaki armaya
            var line = document.createElementNS(SVGNS, 'line');
            line.setAttribute('x1', cx); line.setAttribute('y1', cy);
            line.setAttribute('x2', co.x); line.setAttribute('y2', co.y);
            line.setAttribute('class', 'gm-lead');
            svg.appendChild(line);
            var dot = document.createElementNS(SVGNS, 'circle');
            dot.setAttribute('cx', cx); dot.setAttribute('cy', cy); dot.setAttribute('r', 2.6);
            dot.setAttribute('class', 'gm-dot');
            svg.appendChild(dot);
            w = 54;
        } else {
            w = BIG[slug] || Math.max(28, Math.min(bb.width * 0.5, 48));
        }
        x = px - w / 2; y = py - (w * 1.2) / 2;

        var img = document.createElementNS(SVGNS, 'image');
        img.setAttribute('href', coa);
        img.setAttributeNS(XLINK, 'xlink:href', coa);
        img.setAttribute('width', w); img.setAttribute('height', w * 1.2);
        img.setAttribute('x', x); img.setAttribute('y', y);
        img.setAttribute('preserveAspectRatio', 'xMidYMid meet');
        img.setAttribute('pointer-events', 'none');
        img.setAttribute('class', 'gm-coa');
        svg.appendChild(img);

        // Armanın altına eyalet adı (beyaz haleli, renkli zeminde okunur)
        var label = document.createElementNS(SVGNS, 'text');
        label.textContent = a.getAttribute('data-name');
        label.setAttribute('x', px);
        label.setAttribute('y', y + w * 1.2 + 11);
        label.setAttribute('class', 'gm-label');
        svg.appendChild(label);

        a.dataset.emblem = '1';
    }

    function init() {
        document.querySelectorAll('.gm-wrap').forEach(function (fig) {
            var svg = fig.querySelector('.gm-svg');
            var tip = fig.querySelector('.gm-tip');
            if (!svg) return;

            fig.querySelectorAll('.gm-state').forEach(function (a) { place(svg, a); });

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
