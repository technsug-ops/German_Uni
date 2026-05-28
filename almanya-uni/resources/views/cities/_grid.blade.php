{{-- Cities grid partial — also returned standalone for XHR async-filter updates. --}}
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-600">
        {!! __('Found <strong class="text-gray-900">:n</strong> cities', ['n' => $cities->total()]) !!}
    </p>
    <p class="text-xs text-gray-500">{{ __('Sorted by number of universities') }}</p>
</div>

@if($cities->isEmpty())
    <x-empty-state
        icon="🏙️"
        :title="__('No city matches these filters.')"
        :description="__('Try removing a filter or browse universities and states.')"
        :actions="[
            ['label' => __('Clear filters'), 'url' => route('cities.index'), 'primary' => true, 'icon' => '↺'],
            ['label' => __('States'), 'url' => route('states.index'), 'icon' => '🗺️'],
            ['label' => __('Universities'), 'url' => route('universities.index'), 'icon' => '🎓'],
        ]"
    />
@else
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
        @foreach($cities as $city)
            @php
                $seed = crc32($city->name);
                $palettes = [
                    'from-blue-500 to-cyan-400', 'from-purple-500 to-pink-500',
                    'from-amber-500 to-orange-400', 'from-emerald-500 to-teal-400',
                    'from-rose-500 to-fuchsia-500', 'from-indigo-500 to-violet-500',
                ];
                $palette = $palettes[$seed % count($palettes)];
                $initial = mb_substr($city->name, 0, 1);
            @endphp
            <a href="{{ route('cities.show', $city->slug) }}"
               class="group bg-white rounded-xl overflow-hidden border border-gray-200 hover:border-primary-500 hover:shadow-lg hover:-translate-y-0.5 transition-all flex flex-col">
                <div class="aspect-[4/3] overflow-hidden bg-gray-100 relative">
                    @if($city->image_url)
                        <img src="{{ $city->image_url }}" alt="{{ $city->name }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"/>
                    @else
                        <div class="w-full h-full bg-gradient-to-br {{ $palette }} flex items-center justify-center">
                            <span class="text-5xl font-extrabold text-white/90 drop-shadow">{{ $initial }}</span>
                        </div>
                    @endif
                    <span class="absolute bottom-2 right-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-black/60 backdrop-blur text-white text-xs font-semibold">
                        {{ __(':n unis', ['n' => $city->universities_count]) }}
                    </span>
                </div>
                <div class="p-3">
                    <h3 class="font-bold text-gray-900 group-hover:text-primary-600 transition leading-tight truncate">
                        {{ $city->name }}
                    </h3>
                    @if($city->state)
                        <p class="text-xs text-gray-500 truncate mt-0.5">{{ $city->state->name }}</p>
                    @endif
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8">{{ $cities->links() }}</div>
@endif
