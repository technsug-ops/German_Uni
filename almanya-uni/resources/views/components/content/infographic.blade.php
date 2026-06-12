{{-- #12 storytelling: ContentAsset infographic_data JSON'unu görsel infografik olarak render eder.
     Şema: { title, subtitle, intro, hero_stat{value,label,source}, sections[{type,title,items[{icon,label,value,note}]}] }
     type: stat_grid | process | comparison | timeline | callout | fact_list --}}
@props(['data'])
@php
    $data = is_array($data) ? $data : [];
    $sections = is_array($data['sections'] ?? null) ? $data['sections'] : [];
    $hero = is_array($data['hero_stat'] ?? null) ? $data['hero_stat'] : null;
@endphp

@if (! empty($data['title']))
<figure class="not-prose my-8 rounded-2xl border border-gray-200 overflow-hidden bg-white shadow-sm">
    {{-- Başlık --}}
    <div class="bg-gradient-to-br from-primary-700 via-primary-600 to-accent-500 text-white px-6 py-6">
        <p class="text-[11px] font-bold uppercase tracking-widest text-white/70 mb-1 inline-flex items-center gap-1.5">
            <x-svg-icon name="chart-bar" class="w-3.5 h-3.5" /> {{ __('Infographic') }}
        </p>
        <h3 class="text-xl md:text-2xl font-extrabold leading-tight">{{ $data['title'] }}</h3>
        @if (! empty($data['subtitle']))
            <p class="text-sm text-white/85 mt-1">{{ $data['subtitle'] }}</p>
        @endif
    </div>

    <div class="p-6 space-y-6">
        @if (! empty($data['intro']))
            <p class="text-sm text-gray-600 leading-relaxed">{{ $data['intro'] }}</p>
        @endif

        {{-- Hero stat --}}
        @if ($hero && ! empty($hero['value']))
            <div class="text-center bg-primary-50 rounded-xl py-6 px-4">
                <div class="text-4xl md:text-5xl font-extrabold text-primary-700">{{ $hero['value'] }}</div>
                @if (! empty($hero['label']))
                    <div class="text-sm font-semibold text-gray-700 mt-1">{{ $hero['label'] }}</div>
                @endif
                @if (! empty($hero['source']))
                    <div class="text-[11px] text-gray-400 mt-1">{{ $hero['source'] }}</div>
                @endif
            </div>
        @endif

        {{-- Bölümler --}}
        @foreach ($sections as $sec)
            @php $items = is_array($sec['items'] ?? null) ? $sec['items'] : []; $type = $sec['type'] ?? 'fact_list'; @endphp
            @if (! empty($items))
                <div>
                    @if (! empty($sec['title']))
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-3">{{ $sec['title'] }}</h4>
                    @endif

                    @if ($type === 'stat_grid')
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach ($items as $it)
                                <div class="bg-gray-50 rounded-lg p-3 text-center">
                                    <div class="text-2xl mb-1">{{ $it['icon'] ?? '📌' }}</div>
                                    <div class="text-lg font-extrabold text-gray-900 leading-tight">{{ $it['value'] ?? '' }}</div>
                                    <div class="text-xs text-gray-600 mt-0.5">{{ $it['label'] ?? '' }}</div>
                                </div>
                            @endforeach
                        </div>
                    @elseif ($type === 'process' || $type === 'timeline')
                        <ol class="space-y-2">
                            @foreach ($items as $i => $it)
                                <li class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-7 h-7 rounded-full bg-primary-100 text-primary-700 font-bold text-sm flex items-center justify-center">{{ $i + 1 }}</span>
                                    <div class="flex-1 min-w-0 pt-0.5">
                                        <span class="font-semibold text-gray-900">{{ $it['label'] ?? '' }}</span>
                                        @if (! empty($it['value'])) <span class="text-gray-500 text-sm">— {{ $it['value'] }}</span> @endif
                                        @if (! empty($it['note'])) <p class="text-xs text-gray-500 mt-0.5">{{ $it['note'] }}</p> @endif
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @elseif ($type === 'comparison')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach ($items as $it)
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <div class="font-semibold text-gray-900 flex items-center gap-2">{{ $it['icon'] ?? '' }} {{ $it['label'] ?? '' }}</div>
                                    @if (! empty($it['value'])) <div class="text-sm text-gray-700 mt-1">{{ $it['value'] }}</div> @endif
                                    @if (! empty($it['note'])) <div class="text-xs text-gray-500 mt-0.5">{{ $it['note'] }}</div> @endif
                                </div>
                            @endforeach
                        </div>
                    @else {{-- callout / fact_list --}}
                        <ul class="space-y-2">
                            @foreach ($items as $it)
                                <li class="flex items-start gap-2.5 text-sm">
                                    <span class="flex-shrink-0">{{ $it['icon'] ?? '✅' }}</span>
                                    <span class="text-gray-700">
                                        @if (! empty($it['label'])) <span class="font-semibold text-gray-900">{{ $it['label'] }}</span> @endif
                                        @if (! empty($it['value'])) {{ $it['label'] ? '— ' : '' }}{{ $it['value'] }} @endif
                                        @if (! empty($it['note'])) <span class="text-gray-500">({{ $it['note'] }})</span> @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif
        @endforeach
    </div>
</figure>
@endif
