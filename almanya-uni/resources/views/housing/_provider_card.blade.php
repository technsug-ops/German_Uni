<a href="{{ route('housing.provider-show', $provider->slug) }}"
   class="group block bg-white border border-gray-200 hover:border-emerald-400 hover:shadow-md transition rounded-xl p-5">
    <div class="flex items-start gap-3 mb-3">
        @if ($provider->logo_url)
            <img src="{{ $provider->logo_url }}" alt="{{ $provider->name }}" loading="lazy"
                 class="w-14 h-14 rounded object-contain bg-gray-50 p-1">
        @else
            <div class="w-14 h-14 rounded bg-gradient-to-br
                @if ($provider->type === 'studierendenwerk') from-emerald-500 to-teal-500
                @elseif ($provider->type === 'private_chain') from-indigo-500 to-purple-500
                @else from-amber-500 to-orange-500
                @endif
                text-white flex items-center justify-center">
                {!! e_icon($provider->type_emoji, 'w-7 h-7') !!}
            </div>
        @endif
        <div class="flex-1 min-w-0">
            <h3 class="font-bold text-gray-900 group-hover:text-emerald-700 leading-tight">{{ $provider->name }}</h3>
            <p class="text-[11px] text-gray-500 mt-0.5">{{ $provider->type_label }}</p>
            @if ($provider->is_featured)
                <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700">
                    <x-svg-icon name="star" class="w-3 h-3" />
                    Öne Çıkan
                </span>
            @endif
        </div>
    </div>

    @if ($provider->description)
        <p class="text-sm text-gray-600 line-clamp-3 mb-3">{{ \Illuminate\Support\Str::limit($provider->description, 140) }}</p>
    @endif

    <div class="space-y-1.5 text-xs">
        @if ($provider->price_min)
            <div class="flex items-baseline justify-between">
                <span class="text-gray-500">Fiyat aralığı</span>
                <strong class="text-amber-700">{{ $provider->price_range }} <span class="text-[10px] text-gray-500 font-normal">/ay</span></strong>
            </div>
        @endif
        @if ($provider->total_capacity)
            <div class="flex items-baseline justify-between">
                <span class="text-gray-500">Kapasite</span>
                <span class="font-semibold">{{ number_format($provider->total_capacity, 0, ',', '.') }} yatak</span>
            </div>
        @endif
        @if ($provider->waiting_period)
            <div class="flex items-baseline justify-between">
                <span class="text-gray-500">Bekleme</span>
                <span class="font-semibold">{{ $provider->waiting_period }}</span>
            </div>
        @endif
        @if (! empty($provider->cities) && is_array($provider->cities))
            <div class="flex items-baseline justify-between">
                <span class="text-gray-500">Şehir</span>
                <span class="text-right max-w-[60%] truncate">
                    @if (count($provider->cities) === 1)
                        {{ $provider->cities[0] }}
                    @elseif (count($provider->cities) > 5)
                        {{ count($provider->cities) }} şehir
                    @else
                        {{ implode(', ', $provider->cities) }}
                    @endif
                </span>
            </div>
        @endif
    </div>

    <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
        <span class="text-emerald-700 font-semibold group-hover:translate-x-0.5 transition">{{ __('Detail & Contact') }} →</span>
        @if ($provider->website)
            <span class="text-gray-400 truncate max-w-[50%]">{{ parse_url($provider->website, PHP_URL_HOST) }}</span>
        @endif
    </div>
</a>
