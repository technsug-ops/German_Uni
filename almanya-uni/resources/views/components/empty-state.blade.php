@props([
    'icon' => '📭',
    'title' => null,
    'description' => null,
    'actions' => [],
])

{{--
    Reusable empty-state card.
    Usage:
        <x-empty-state
            icon="🎓"
            :title="__('No universities match')"
            :description="__('Loosen a filter or browse below.')"
            :actions="[
                ['label' => __('Reset'), 'url' => route('universities.index'), 'primary' => true],
                ['label' => __('Cities'), 'url' => route('cities.index')],
            ]"
        />
--}}
<div class="bg-white rounded-2xl border border-gray-200 p-8 md:p-12 text-center shadow-sm max-w-2xl mx-auto">
    @if ($icon)
        <div class="text-5xl mb-4" aria-hidden="true">{{ $icon }}</div>
    @endif
    @if ($title)
        <p class="text-xl font-bold text-gray-900 mb-2">{{ $title }}</p>
    @endif
    @if ($description)
        <p class="text-sm text-gray-500 mb-6">{!! $description !!}</p>
    @endif
    {{ $slot }}
    @if (!empty($actions))
        <div class="flex flex-wrap gap-3 justify-center">
            @foreach ($actions as $a)
                <a href="{{ $a['url'] }}"
                   class="px-4 py-2 rounded-lg font-semibold transition {{ !empty($a['primary'])
                        ? 'bg-primary-600 hover:bg-primary-700 text-white'
                        : 'bg-white border border-gray-300 hover:border-primary-400 text-gray-700' }}">
                    @if (!empty($a['icon']))<span class="mr-1">{{ $a['icon'] }}</span>@endif{{ $a['label'] }}
                </a>
            @endforeach
        </div>
    @endif
</div>
