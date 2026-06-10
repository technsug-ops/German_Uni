@php
    $isLive = $isLive ?? false;
    $isPast = $isPast ?? false;
@endphp
<a href="{{ route('events.show', $event->slug) }}"
   class="group block bg-white rounded-xl border-2 hover:shadow-md transition overflow-hidden flex flex-col {{ $isLive ? 'border-red-300' : 'border-gray-200 hover:border-indigo-400' }}">

    <div class="px-4 py-3 text-white" style="background: {{ $event->type_color }};">
        <div class="flex items-center justify-between gap-2">
            <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider">
                {!! e_icon($event->type_emoji, 'w-3.5 h-3.5') !!}
                {{ $event->type_label }}
            </span>
            @if ($isLive)
                <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase bg-red-500 px-1.5 py-0.5 rounded">
                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>Canlı
                </span>
            @elseif ($event->mode === 'offline')
                <span class="inline-flex items-center gap-1 text-[10px] font-semibold bg-white/20 px-1.5 py-0.5 rounded">
                    <x-svg-icon name="map-pin" class="w-3 h-3" /> Offline
                </span>
            @endif
        </div>
    </div>

    <div class="p-4 flex-1 flex flex-col">
        <h3 class="font-bold text-gray-900 group-hover:text-indigo-700 leading-snug mb-2 line-clamp-2">{{ $event->title }}</h3>

        <div class="text-xs text-gray-600 space-y-1 mb-3">
            <p class="flex items-center gap-1.5">
                <x-svg-icon name="calendar" class="w-3.5 h-3.5 text-gray-400" /> <span>{{ $event->starts_at->translatedFormat('d M Y · H:i') }}</span>
            </p>
            @if ($event->host)
                <p class="flex items-center gap-1.5 text-gray-500">
                    <x-svg-icon name="user" class="w-3.5 h-3.5 text-gray-400" /> <span class="truncate">{{ $event->host }}</span>
                </p>
            @endif
            @if ($event->language_flag)
                <p class="flex items-center gap-1.5 text-gray-500">
                    <x-svg-icon name="globe" class="w-3.5 h-3.5 text-gray-400" /> <span>{{ $event->language_flag }} {{ $event->language_label }}</span>
                </p>
            @endif
            @if ($event->mode === 'offline' && $event->location_city)
                <p class="flex items-center gap-1.5">
                    <x-svg-icon name="map-pin" class="w-3.5 h-3.5 text-gray-400" /> <span>{{ $event->location_city }}</span>
                </p>
            @endif
            @if ($event->price_eur > 0)
                <p class="flex items-center gap-1.5 text-amber-700 font-semibold">
                    <x-svg-icon name="currency-euro" class="w-3.5 h-3.5" /> <span>{{ number_format($event->price_eur, 0, ',', '.') }} €</span>
                </p>
            @else
                <p class="flex items-center gap-1.5 text-emerald-600 font-semibold text-xs">
                    <x-svg-icon name="tag" class="w-3.5 h-3.5" /> {{ __('Free') }}
                </p>
            @endif
        </div>

        @if (! $isPast)
            <div class="mt-auto inline-flex items-center justify-center text-xs font-semibold py-2 px-3 rounded-lg text-white"
                 style="background: {{ $event->type_color }};">
                {{ $isLive ? 'Şimdi Katıl →' : 'Detayını Gör →' }}
            </div>
        @else
            <p class="mt-auto text-xs text-gray-500 italic">{{ $event->starts_at->diffForHumans() }}</p>
        @endif
    </div>
</a>
