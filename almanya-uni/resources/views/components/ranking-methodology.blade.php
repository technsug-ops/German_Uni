@props([
    'methodology' => null,
])

@if ($methodology)
    <section class="mt-8 bg-white border border-gray-200 rounded-xl p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-3">{{ $methodology['title'] }}</h3>

        @if (! empty($methodology['intro']))
            <p class="text-sm text-gray-600 mb-4">{{ $methodology['intro'] }}</p>
        @endif

        @if (! empty($methodology['indicators']))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                @foreach ($methodology['indicators'] as $key => $meta)
                    <div class="flex items-start gap-2">
                        <span class="font-bold text-primary-600 shrink-0 w-12 text-right">{{ $meta['weight'] }}%</span>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-900">{{ __($meta['label']) }}</p>
                            <p class="text-xs text-gray-500 leading-relaxed">{{ __($meta['tooltip']) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if (! empty($methodology['note']))
            <p class="text-xs text-gray-500 mt-3 italic">{{ $methodology['note'] }}</p>
        @endif

        @if (! empty($methodology['source_url']))
            <p class="text-xs text-gray-400 mt-4">
                {{ $methodology['source_label'] ?? __('Source:') }}
                <a href="{{ $methodology['source_url'] }}" target="_blank" rel="noopener" class="text-primary-600 hover:underline">
                    {{ $methodology['source_text'] ?? $methodology['source_url'] }}
                </a>
            </p>
        @endif
    </section>
@endif
