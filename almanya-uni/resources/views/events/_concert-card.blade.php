{{--
    Görsel konser kartı (Ticketmaster banner_url).
    $event — Event
    $big   — true ise daha büyük (highlight) kart
--}}
@php $big = $big ?? false; @endphp
<a href="{{ route('events.show', $event->slug) }}"
   class="group block rounded-2xl overflow-hidden border border-gray-200 bg-white hover:shadow-lg transition">
    <div class="relative {{ $big ? 'h-52' : 'h-40' }} overflow-hidden">
        @if ($event->banner_url)
            <img src="{{ $event->banner_url }}" alt="{{ $event->title }}" loading="lazy"
                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">
        @else
            <div class="absolute inset-0" style="background: linear-gradient(135deg, {{ $event->type_color }}, rgba(0,0,0,.5));"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/15 to-transparent"></div>

        <span class="absolute top-3 left-3 inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wide text-white px-2 py-0.5 rounded-full shadow"
              style="background: {{ $event->type_color }};">{!! e_icon($event->type_emoji, 'w-3 h-3') !!} {{ $event->type_label }}</span>
        <span class="absolute top-3 right-3 text-[11px] font-semibold text-white bg-black/45 backdrop-blur px-2 py-0.5 rounded-full whitespace-nowrap">{{ $event->starts_at->translatedFormat('d M') }}</span>

        <div class="absolute inset-x-0 bottom-0 p-3">
            <h3 class="font-bold text-white leading-tight line-clamp-2 drop-shadow {{ $big ? 'text-lg' : 'text-sm' }}">{{ $event->title }}</h3>
        </div>
    </div>
    <div class="px-3 py-2.5 flex items-center justify-between text-xs gap-2">
        <span class="inline-flex items-center gap-1 text-gray-600 truncate min-w-0">
            <x-svg-icon name="map-pin" class="w-3.5 h-3.5 text-rose-500 shrink-0" /> <span class="truncate">{{ $event->location_city ?: '—' }}</span>
            <span class="text-gray-300">·</span> <span class="shrink-0">{{ $event->starts_at->format('H:i') }}</span>
        </span>
        <span class="font-semibold text-rose-600 inline-flex items-center gap-0.5 shrink-0 group-hover:translate-x-0.5 transition">{{ __('Details') }} →</span>
    </div>
</a>
