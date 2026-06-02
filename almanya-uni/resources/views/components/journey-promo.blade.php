@php
    $journeySteps = \App\Models\ApplicationTracker::STEPS;
@endphp

<section class="bg-gradient-to-br from-primary-700 via-primary-800 to-primary-900 text-white py-14">
    <div class="max-w-[1100px] mx-auto px-4 text-center">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 mb-3 rounded-full bg-white/15 text-xs font-bold uppercase tracking-wider ring-1 ring-white/20">
            <x-svg-icon name="map" class="w-3.5 h-3.5" /> {{ __('Application Roadmap') }}
        </span>
        <h2 class="text-2xl md:text-3xl font-extrabold mb-3">{{ __('Your path to Germany in 8 steps') }}</h2>
        <p class="text-primary-100 max-w-2xl mx-auto mb-8">
            {{ __('From eligibility to visa — a personal checklist that tells you exactly what to do next, with the right tool at every step.') }}
        </p>

        <div class="flex flex-wrap items-center justify-center gap-2 md:gap-3 mb-8">
            @foreach ($journeySteps as $i => $s)
                <span class="inline-flex w-11 h-11 md:w-12 md:h-12 rounded-2xl bg-white/10 ring-1 ring-white/15 items-center justify-center text-xl" title="{{ __($s['title']) }}">{{ $s['emoji'] }}</span>
                @if ($i < count($journeySteps) - 1)<span class="text-white/30 hidden sm:inline">→</span>@endif
            @endforeach
        </div>

        <a href="{{ route('journey.show') }}"
           class="inline-flex items-center gap-2 bg-accent-500 hover:bg-accent-400 text-white font-bold px-6 py-3 rounded-xl transition shadow-lg">
            <x-svg-icon name="rocket-launch" class="w-5 h-5" /> {{ __('Start your roadmap') }}
        </a>
    </div>
</section>
