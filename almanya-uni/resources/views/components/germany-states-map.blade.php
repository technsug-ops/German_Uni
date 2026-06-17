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

    $states = \App\Models\State::query()
        ->get(['id', 'slug', 'name_tr', 'name_de', 'name_en'])
        ->keyBy('slug');
@endphp

@if($mapData && ! empty($mapData['locations']))
<div class="gm-wrap">
    <p class="gm-hint">{{ __('Click a state to explore its universities and cities') }}</p>
    <div class="gm-figure">
        <svg viewBox="{{ $mapData['viewBox'] }}" class="gm-svg" role="img"
             aria-label="{{ __('Interactive map of German federal states') }}"
             xmlns="http://www.w3.org/2000/svg">
            @foreach($mapData['locations'] as $loc)
                @php
                    $slug = $idToSlug[$loc['id']] ?? null;
                    $st = $slug ? ($states[$slug] ?? null) : null;
                @endphp
                @if($st)
                    <a href="{{ route('states.show', $st->slug) }}" class="gm-state"
                       data-name="{{ $st->name }}" aria-label="{{ $st->name }}">
                        <path d="{{ $loc['path'] }}"><title>{{ $st->name }}</title></path>
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
    .gm-svg { width: 100%; height: auto; display: block; }
    .gm-state path { fill: #dbeafe; stroke: #ffffff; stroke-width: 1.2; cursor: pointer; transition: fill .15s ease; }
    .gm-state:hover path,
    .gm-state:focus path { fill: #4f46e5; outline: none; }
    .gm-disabled { fill: #e5e7eb; stroke: #ffffff; stroke-width: 1.2; }
    .gm-tip { position: absolute; pointer-events: none; background: #111827; color: #fff;
              font-size: .8rem; font-weight: 600; padding: .25rem .55rem; border-radius: .375rem;
              transform: translate(-50%, -130%); white-space: nowrap; z-index: 10; box-shadow: 0 2px 8px rgba(0,0,0,.25); }
    .dark .gm-state path { fill: #1e3a8a; }
    .dark .gm-state:hover path { fill: #6366f1; }
</style>

<script>
(function () {
    var fig = document.currentScript.closest('.gm-wrap');
    if (!fig) return;
    var tip = fig.querySelector('.gm-tip');
    fig.querySelectorAll('.gm-state').forEach(function (el) {
        el.addEventListener('mousemove', function (e) {
            var r = fig.querySelector('.gm-figure').getBoundingClientRect();
            tip.textContent = el.getAttribute('data-name');
            tip.style.left = (e.clientX - r.left) + 'px';
            tip.style.top = (e.clientY - r.top) + 'px';
            tip.hidden = false;
        });
        el.addEventListener('mouseleave', function () { tip.hidden = true; });
    });
})();
</script>
@endif
