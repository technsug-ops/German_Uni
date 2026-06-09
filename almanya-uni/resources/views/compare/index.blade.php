@extends('layouts.app')

@section('title', __('Compare Universities') . ' — ' . brand('name'))

@php
    $selectedSlugCsv = implode(',', array_column($selected, 'slug'));

    $addUrl = function (string $newSlug) use ($selected, $q) {
        $slugs = array_column($selected, 'slug');
        $slugs[] = $newSlug;
        return route('compare.index', array_filter([
            'slugs' => implode(',', array_unique($slugs)),
            'q' => $q,
        ]));
    };

    $removeUrl = function (string $removeSlug) use ($selected, $q) {
        $slugs = array_values(array_filter(
            array_column($selected, 'slug'),
            fn ($s) => $s !== $removeSlug
        ));
        return route('compare.index', array_filter([
            'slugs' => $slugs ? implode(',', $slugs) : null,
            'q' => $q,
        ]));
    };
@endphp

@section('content')
<div class="max-w-[1400px] mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold mb-2">{{ __('Compare Universities') }}</h1>
    <p class="text-gray-600 mb-8">{{ __('Pick 2-:max universities and compare them side by side.', ['max' => $max_items]) }}</p>

    <!-- Selected Universities (Cart) -->
    <div class="bg-primary-50 border-2 border-primary-200 rounded-lg p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-primary-900">
                {{ __('Selected') }} ({{ count($selected) }}/{{ $max_items }})
            </h2>
            @if (count($selected) >= 2)
                <a href="{{ route('compare.show', ['slugs' => $selectedSlugCsv]) }}"
                   class="bg-accent-500 hover:bg-accent-600 text-white px-6 py-2 rounded font-semibold transition">
                    {{ __('Compare') }} →
                </a>
            @endif
        </div>

        @if (count($selected) === 0)
            <p class="text-gray-600 italic">{{ __('No selection yet. Search below and hit "+ Add".') }}</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach ($selected as $uni)
                    <div class="bg-white border border-primary-200 rounded p-3 flex gap-3 items-start">
                        @if ($uni['logo_url'])
                            <img src="{{ $uni['logo_url'] }}" alt="{{ $uni['name_de'] }}" class="w-12 h-12 object-contain bg-gray-50 rounded flex-shrink-0" loading="lazy" decoding="async">
                        @else
                            <div class="w-12 h-12 bg-primary-100 rounded flex items-center justify-center flex-shrink-0">
                                <span class="text-primary-600 font-bold text-sm">{{ mb_substr($uni['name_de'], 0, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm truncate">{{ $uni['name_de'] }}</p>
                            <p class="text-xs text-gray-600 truncate">{{ $uni['city_name'] ?? '-' }}</p>
                            <a href="{{ $removeUrl($uni['slug']) }}" class="text-red-600 hover:text-red-800 text-xs font-semibold">× {{ __('Remove') }}</a>
                        </div>
                    </div>
                @endforeach
            </div>

            @if (count($selected) < 2)
                <p class="text-sm text-primary-700 mt-4">{{ __('Pick at least 2 universities to compare.') }}</p>
            @endif
        @endif
    </div>

    <!-- Search -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">{{ __('Search universities') }}</h2>

        <form method="GET" action="{{ route('compare.index') }}" class="mb-6 flex gap-2">
            <input type="hidden" name="slugs" value="{{ $selectedSlugCsv }}">
            <input
                type="text"
                name="q"
                value="{{ $q }}"
                placeholder="{{ __('Start typing a university name…') }}"
                class="flex-1 px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-primary-500"
                autofocus
            >
            <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded font-semibold transition">
                {{ __('Search') }}
            </button>
        </form>

        @if ($q !== '' && empty($candidates))
            <p class="text-gray-600 italic">{{ __('No result for ":q".', ['q' => $q]) }}</p>
        @elseif (!empty($candidates))
            <div class="space-y-2">
                @foreach ($candidates as $cand)
                    <div class="flex items-center gap-4 p-3 border border-gray-200 rounded hover:bg-gray-50 transition">
                        @if ($cand['logo_url'])
                            <img src="{{ $cand['logo_url'] }}" alt="" class="w-12 h-12 object-contain bg-gray-50 rounded flex-shrink-0" loading="lazy" decoding="async">
                        @else
                            <div class="w-12 h-12 bg-primary-100 rounded flex items-center justify-center flex-shrink-0">
                                <span class="text-primary-600 font-bold text-sm">{{ mb_substr($cand['name_de'], 0, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold">{{ $cand['name_de'] }}</p>
                            <p class="text-sm text-gray-600">
                                {{ $cand['city_name'] ?? '-' }}@if (!empty($cand['state_name'])), {{ $cand['state_name'] }}@endif
                            </p>
                        </div>
                        @if ($can_add_more)
                            <a href="{{ $addUrl($cand['slug']) }}"
                               class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded font-semibold transition flex-shrink-0">
                                + {{ __('Add') }}
                            </a>
                        @else
                            <span class="text-gray-400 text-sm flex-shrink-0">{{ __('List full') }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 italic">{{ __('Type a university name in the search box above.') }}</p>
        @endif
    </div>
</div>
@endsection
