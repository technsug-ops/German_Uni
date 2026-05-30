{{-- Professions grid partial — also returned standalone for XHR async-filter updates. --}}
@php
    $typeLabels = $typeLabels ?? [
        'ausbildung'    => ['Ausbildung', 'wrench-screwdriver', 'green'],
        'studienberuf'  => ['Studienberuf', 'academic-cap', 'blue'],
        'weiterbildung' => ['Weiterbildung', 'chart-bar', 'purple'],
        'grundberuf'    => ['Grundberuf', 'briefcase', 'amber'],
        'other'         => [__('Other'), 'book-open', 'gray'],
    ];
    $hasFilter = $hasFilter ?? (bool) (($filters['q'] ?? null) || ($filters['type'] ?? null) || ($filters['field'] ?? null));
@endphp

<div class="flex items-center justify-between mb-4 flex-wrap gap-2">
    <p class="text-sm text-gray-700">
        <strong>{{ number_format($professions->total(), 0, ',', '.') }}</strong> {{ __('professions') }}
        @if ($hasFilter)
            <a href="{{ route('professions.index') }}" class="ml-3 text-accent-600 hover:text-accent-800">↻ {{ __('Clear') }}</a>
        @endif
    </p>
    <p class="text-sm text-gray-500">{{ __('Page :current / :total', ['current' => $professions->currentPage(), 'total' => max(1, $professions->lastPage())]) }}</p>
</div>

@if ($professions->isEmpty())
    <x-empty-state
        icon="💼"
        :title="__('No profession found.')"
        :description="__('Try the full profession list or browse by field of study.')"
        :actions="[
            ['label' => __('All Professions'), 'url' => route('professions.index'), 'primary' => true],
            ['label' => __('Browse by field'), 'url' => route('fields.index')],
        ]"
    />
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($professions as $p)
            @php
                [$tLabel, $tIcon, $tColor] = $typeLabels[$p->type ?? 'other'];
                // Locale-aware description (model trait picks $loc → fallback chain)
                if ($desc = $p->description) {
                    $desc = preg_replace('/^(Studienfach|Aufgaben und Tätigkeiten|Aufgaben und Tatigkeiten|Beruf|Berufsbezeichnung)\s*/u', '', $desc);
                    $body = \Illuminate\Support\Str::limit(trim($desc), 180);
                } elseif ($p->clean_steckbrief) {
                    $body = \Illuminate\Support\Str::limit($p->clean_steckbrief, 180);
                } else {
                    $body = null;
                }
            @endphp
            <a href="{{ route('professions.show', $p->slug) }}"
               class="group flex flex-col bg-white border border-gray-200 hover:border-primary-400 hover:shadow-md transition rounded-xl p-5 min-h-[200px] overflow-hidden">
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full max-w-full
                        @switch($tColor)
                            @case('green')  bg-green-100 text-green-700 @break
                            @case('blue')   bg-blue-100 text-blue-700 @break
                            @case('purple') bg-purple-100 text-purple-700 @break
                            @case('amber')  bg-amber-100 text-amber-800 @break
                            @default        bg-gray-100 text-gray-700
                        @endswitch
                    ">
                        <x-svg-icon :name="$tIcon" class="w-3 h-3" />
                        <span class="truncate">{{ $tLabel }}</span>
                    </span>
                    @if ($p->kldb_code)
                        <span class="text-[10px] text-gray-400 font-mono">KldB {{ $p->kldb_code }}</span>
                    @endif
                </div>
                <h3 class="font-bold text-gray-900 leading-snug group-hover:text-primary-700 transition mb-2 break-words">
                    {{ \Illuminate\Support\Str::limit($p->name, 70) }}
                </h3>

                @if ($body)
                    <p class="text-sm text-gray-700 line-clamp-3 flex-1">{{ $body }}</p>
                @else
                    <div class="flex-1 flex items-center justify-center text-center py-3">
                        <p class="text-xs text-gray-400 italic">
                            @if ($p->field)
                                {{ __(':field area', ['field' => $p->field->name]) }}
                            @else
                                {{ __('Click for details') }}
                            @endif
                        </p>
                    </div>
                @endif

                <span class="mt-3 inline-flex items-center gap-1 text-xs text-primary-600 group-hover:text-primary-800 font-semibold">
                    {{ __('See details →') }}
                </span>
            </a>
        @endforeach
    </div>

    <div class="mt-8">{{ $professions->links() }}</div>
@endif
