{{--
    Kaynak künyesi + tazelik rozeti — güven/şeffaflık sinyali (GEO #3 promptu).
    AI/arama motorları kaynağını gösteren + tarihli içeriği "authoritative" sayar.

    OSV Faz 0 (kontrol tarihi ≠ yürürlük tarihi ≠ kayıt güncelleme tarihi):
      - checked : resmî kaynağın GERÇEKTEN okunduğu tarih → "Checked on …" / "Kontrol tarihi: …" / "Geprüft am …".
                  Yalnız elle doğrulanmış tarih verilir; asla $model->updated_at verilmez.
      - updated : yalnız "Last updated" (sayfa/kayıt güncellemesi) — doğrulama iddiası DEĞİL.
      - İkisi de yoksa yalnız kaynaklar gösterilir; "verified" denmez.

    Kullanım:
      <x-source-note :sources="[['name' => 'BAföG', 'url' => 'https://...']]" checked="2026-06-01" note="..." />
      <x-source-note :sources="['DAAD', 'Hochschulkompass']" :updated="$item->updated_at" />
--}}
@props([
    'sources' => [],
    'checked' => null,
    'updated' => null,
    'note' => null,
    'icon' => true,
])

@php
    $sourceList = collect((array) $sources)
        ->map(fn ($s) => is_array($s) ? $s : ['name' => $s, 'url' => null])
        ->filter(fn ($s) => ! empty($s['name']))
        ->values();

    $fmt = function ($d): array {
        if (empty($d)) {
            return [null, null];
        }
        try {
            $c = \Illuminate\Support\Carbon::parse($d);

            return [$c->locale(app()->getLocale())->translatedFormat('j M Y'), $c->format('Y-m-d')];
        } catch (\Throwable $e) {
            return [is_string($d) ? $d : null, null];
        }
    };
    [$checkedLabel, $checkedIso] = $fmt($checked);
    [$updatedLabel, $updatedIso] = $checkedLabel ? [null, null] : $fmt($updated);
@endphp

@if($sourceList->isNotEmpty() || $checkedLabel || $updatedLabel || $note)
    <div {{ $attributes->merge(['class' => 'mt-4 flex flex-wrap items-center gap-x-2 gap-y-1 rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-xs text-gray-500']) }}>
        @if($icon)
            <svg class="h-3.5 w-3.5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
            </svg>
        @endif

        @if($sourceList->isNotEmpty())
            <span class="font-medium text-gray-600">{{ __('Source') }}:</span>
            <span>
                @foreach($sourceList as $s)
                    @if(! empty($s['url']))<a href="{{ $s['url'] }}" target="_blank" rel="noopener nofollow" class="underline decoration-gray-300 underline-offset-2 hover:text-gray-700">{{ $s['name'] }}</a>@else{{ $s['name'] }}@endif{{ ! $loop->last ? ', ' : '' }}
                @endforeach
            </span>
        @endif

        @if($checkedLabel)
            @if($sourceList->isNotEmpty())<span class="text-gray-300">·</span>@endif
            <span>{!! __('Checked on :date', ['date' => '<time' . ($checkedIso ? ' datetime="' . e($checkedIso) . '"' : '') . '>' . e($checkedLabel) . '</time>']) !!}</span>
        @elseif($updatedLabel)
            @if($sourceList->isNotEmpty())<span class="text-gray-300">·</span>@endif
            <span>{!! __('Last updated: :date', ['date' => '<time' . ($updatedIso ? ' datetime="' . e($updatedIso) . '"' : '') . '>' . e($updatedLabel) . '</time>']) !!}</span>
        @endif

        @if($note)
            <span class="text-gray-300">·</span>
            <span>{{ $note }}</span>
        @endif
    </div>
@endif
