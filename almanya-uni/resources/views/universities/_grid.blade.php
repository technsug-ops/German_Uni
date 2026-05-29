{{-- Universities grid partial — also returned standalone for XHR async-filter updates --}}
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-600">
        {!! __('<strong class="text-gray-900">:count</strong> universities found', ['count' => $total]) !!}
    </p>
    <p class="text-xs text-gray-500">{{ __('Sorted by student count') }}</p>
</div>

@if ($universities && count($universities) > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach ($universities as $uni)
            @php
                $seed = crc32($uni['name_de']);
                $palettes = [
                    'from-blue-500 to-cyan-400',
                    'from-purple-500 to-pink-500',
                    'from-amber-500 to-orange-400',
                    'from-emerald-500 to-teal-400',
                    'from-rose-500 to-fuchsia-500',
                    'from-indigo-500 to-violet-500',
                ];
                $palette = $palettes[$seed % count($palettes)];
                $cover = \App\Support\CoverImage::forUniversity($uni);
                $coverUrl = $cover['url'];
                $coverIsPool = $cover['source'] === 'pool';
            @endphp
            <a href="{{ route('universities.show', $uni['slug']) }}"
               class="group bg-white rounded-xl overflow-hidden border border-gray-200 hover:border-primary-500 hover:shadow-lg hover:-translate-y-0.5 transition-all flex flex-col">

                {{-- Cover image — gradient baseline always renders; image overlays on top.
                     If the URL 404s, errors, or is blocked, onerror removes the <img> and the
                     gradient + initials show through. alt="" prevents broken-image text leaks. --}}
                <div class="aspect-[16/9] overflow-hidden relative bg-gradient-to-br {{ $palette }}">
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <span class="text-4xl font-extrabold text-white/90 drop-shadow text-center px-4 select-none">
                            {{ mb_substr($uni['name_de'], 0, 2) }}
                        </span>
                    </div>

                    @if($coverUrl)
                        <img src="{{ $coverUrl }}" alt=""
                             loading="lazy" decoding="async"
                             onerror="this.remove()"
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 {{ $coverIsPool ? 'opacity-90' : '' }}"/>
                        @if($coverIsPool && !empty($uni['city_name']))
                            {{-- City landmark fallback — overlay pin so users know it's the city, not the uni --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent pointer-events-none"></div>
                            <span class="absolute bottom-2 right-2 inline-flex items-center gap-1 text-[10px] text-white/90 bg-black/40 backdrop-blur px-1.5 py-0.5 rounded">
                                📍 {{ $uni['city_name'] }}
                            </span>
                        @endif
                    @endif

                    @if($uni['type'])
                        <span class="absolute top-2 left-2 inline-block px-2 py-0.5 rounded {{ $typeBadgeColor($uni['type']) }} text-xs font-semibold ring-1 ring-white/40 shadow-sm">
                            {{ $typeLabel($uni['type']) }}
                        </span>
                    @endif

                    @if($uni['logo_url'] && $coverUrl)
                        <div class="absolute bottom-2 left-2 w-12 h-12 bg-white rounded-lg ring-1 ring-white/60 shadow-md p-1 flex items-center justify-center">
                            <img src="{{ $uni['logo_url'] }}" alt="" class="max-w-full max-h-full object-contain" loading="lazy" decoding="async"/>
                        </div>
                    @endif
                </div>

                <div class="p-4 flex-1 flex flex-col">
                    <h3 class="font-bold text-gray-900 group-hover:text-primary-600 transition leading-tight line-clamp-2 mb-1">
                        {{ $uni['name_de'] }}
                    </h3>
                    <p class="text-xs text-gray-500 mb-3">
                        📍 {{ $uni['city_name'] ?? __('Unknown') }}@if (!empty($uni['state_name'])) · {{ $uni['state_name'] }}@endif
                    </p>
                    <div class="mt-auto flex items-center justify-between pt-2 border-t border-gray-100 text-xs">
                        @if($uni['student_count'])
                            <span class="text-accent-700 font-bold">
                                {{ number_format($uni['student_count']) }}
                                <span class="font-normal text-gray-500">{{ __('students') }}</span>
                            </span>
                        @else
                            <span></span>
                        @endif
                        @if($uni['founded_year'])
                            <span class="text-gray-500">est. {{ $uni['founded_year'] }}</span>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8">{{ $universities->links() }}</div>
@else
    <x-empty-state
        icon="🎓"
        :title="__('No universities match these criteria.')"
        :description="__('Try loosening one of the filters or browse all universities below.')"
        :actions="[
            ['label' => __('Reset all filters'), 'url' => route('universities.index'), 'primary' => true, 'icon' => '↺'],
            ['label' => __('Browse by city'), 'url' => route('cities.index'), 'icon' => '🏙️'],
            ['label' => __('Browse by field'), 'url' => route('fields.index'), 'icon' => '🎯'],
        ]"
    />
@endif
