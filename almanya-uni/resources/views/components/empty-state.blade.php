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
        <div class="inline-flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-primary-50 text-primary-600" aria-hidden="true">{!! e_icon($icon, 'w-9 h-9') !!}</div>
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
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg font-semibold transition {{ !empty($a['primary'])
                        ? 'bg-primary-600 hover:bg-primary-700 text-white'
                        : 'bg-white border border-gray-300 hover:border-primary-400 text-gray-700' }}">
                    @if (!empty($a['icon'])){!! e_icon($a['icon'], 'w-4 h-4') !!}@endif{{ $a['label'] }}
                </a>
            @endforeach
        </div>
    @endif
</div>
