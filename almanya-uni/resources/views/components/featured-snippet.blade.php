@props([
    'question' => null,
    'answer' => null,
    'steps' => [],
    'icon' => '💡',
])

@php
    // Featured-snippet structure: h2 question + concise 40-60 word paragraph answer
    // + optional ordered list. Google preferentially extracts this pattern.
    $hasSteps = is_array($steps) && count($steps) > 0;
@endphp

@if ($question && $answer)
    <section class="featured-snippet bg-gradient-to-br from-primary-50 to-accent-50 border-l-4 border-primary-500 rounded-r-xl p-5 md:p-6 my-6 shadow-sm">
        <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-3 flex items-start gap-2">
            <span class="shrink-0 mt-0.5">{{ $icon }}</span>
            <span>{{ $question }}</span>
        </h2>
        <p class="text-gray-800 leading-relaxed">
            {{ $answer }}
        </p>
        @if ($hasSteps)
            <ol class="mt-4 space-y-2 list-decimal list-inside text-sm text-gray-800">
                @foreach ($steps as $step)
                    @if (is_array($step))
                        <li>
                            <strong class="text-gray-900">{{ $step['title'] ?? '' }}</strong>
                            @if (! empty($step['description']))
                                <span class="text-gray-700"> — {{ $step['description'] }}</span>
                            @endif
                        </li>
                    @else
                        <li class="text-gray-800">{{ $step }}</li>
                    @endif
                @endforeach
            </ol>
        @endif
    </section>
@endif
