{{-- Generic partner kartı: $item, $kind, $showRoute. Tailwind-güvenli (literal class). --}}
@php
    $levels = $kind === 'language_course' ? ($item->levels ?? []) : null;
    $languages = $kind === 'translation_office' ? ($item->languages ?? []) : null;
    $badge = match ($item->type) {
        'university', 'sworn_individual' => 'bg-indigo-100 text-indigo-700',
        'private', 'agency'              => 'bg-emerald-100 text-emerald-700',
        'online'                         => 'bg-amber-100 text-amber-700',
        default                          => 'bg-gray-100 text-gray-700',
    };
@endphp
<a href="{{ route($showRoute, $item->slug) }}"
   class="group block bg-white border border-gray-200 hover:border-indigo-400 hover:shadow-md transition rounded-xl p-5">
    <div class="flex items-start gap-3 mb-3">
        @if ($item->logo_url)
            <img src="{{ $item->logo_url }}" alt="{{ $item->name }}" loading="lazy" class="w-14 h-14 rounded object-contain bg-gray-50 p-1">
        @else
            <div class="w-14 h-14 rounded bg-gradient-to-br from-indigo-500 to-violet-600 text-white flex items-center justify-center text-2xl">
                {{ $item->type_emoji }}
            </div>
        @endif
        <div class="flex-1 min-w-0">
            <h3 class="font-bold text-gray-900 group-hover:text-indigo-700 leading-tight">{{ $item->name }}</h3>
            <span class="inline-block text-[11px] px-1.5 py-0.5 rounded mt-1 {{ $badge }}">{{ $item->type_label }}</span>
            @if ($item->is_featured)
                <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded mt-1 bg-amber-100 text-amber-700">★ {{ __('Featured') }}</span>
            @endif
            @if ($kind === 'translation_office' && $item->is_sworn)
                <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded mt-1 bg-emerald-100 text-emerald-700">{{ __('Sworn') }}</span>
            @endif
        </div>
    </div>

    @if ($item->description)
        <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ \Illuminate\Support\Str::limit($item->description, 130) }}</p>
    @endif

    @if (! empty($levels) && is_array($levels))
        <div class="flex flex-wrap gap-1 mb-2">
            @foreach ($levels as $lv)
                <span class="text-[11px] font-semibold px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-100">{{ $lv }}</span>
            @endforeach
        </div>
    @endif
    @if (! empty($languages) && is_array($languages))
        <div class="flex flex-wrap gap-1 mb-2">
            @foreach (array_slice($languages, 0, 5) as $lg)
                <span class="text-[11px] font-semibold px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-100">{{ $lg }}</span>
            @endforeach
        </div>
    @endif

    <div class="space-y-1.5 text-xs">
        @if ($kind === 'language_course' && $item->price_range)
            <div class="flex items-baseline justify-between">
                <span class="text-gray-500">{{ __('Price') }}</span>
                <strong class="text-amber-700">{{ $item->price_range }}</strong>
            </div>
        @endif
        @if (! empty($item->cities) && is_array($item->cities))
            <div class="flex items-baseline justify-between">
                <span class="text-gray-500">{{ __('Cities') }}</span>
                <span class="text-right max-w-[60%] truncate">
                    {{ count($item->cities) > 4 ? count($item->cities) . ' ' . __('cities') : implode(', ', $item->cities) }}
                </span>
            </div>
        @endif
    </div>

    <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
        <span class="text-indigo-700 font-semibold group-hover:translate-x-0.5 transition">{{ __('Detail & Contact') }} →</span>
        @if ($item->website)
            <span class="text-gray-400 truncate max-w-[45%]">{{ parse_url($item->website, PHP_URL_HOST) }}</span>
        @endif
    </div>
</a>
