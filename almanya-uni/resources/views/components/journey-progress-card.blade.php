@php
    $jt      = auth()->user()?->applicationTracker;
    $jSteps  = \App\Models\ApplicationTracker::STEPS;
    $jTotal  = count($jSteps);
    $jDone   = $jt ? $jt->completedCount() : 0;
    $jPct    = $jt ? $jt->progressPercent() : 0;
    $jNext   = $jt ? $jt->currentStep() : $jSteps[0];
@endphp

<section class="bg-gradient-to-r from-primary-600 to-primary-800 text-white rounded-2xl p-6 mb-8 shadow-md">
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <div class="flex items-center gap-2 mb-1 text-sm text-primary-100">
                <x-svg-icon name="map" class="w-4 h-4" /> {{ __('Application Roadmap') }}
            </div>
            <h2 class="text-xl md:text-2xl font-extrabold">{{ $jDone }}/{{ $jTotal }} {{ __('steps complete') }}</h2>
            @if ($jNext)
                <p class="text-primary-100 text-sm mt-1">{{ __('Next') }}: {{ __($jNext['title']) }}</p>
            @else
                <p class="text-primary-100 text-sm mt-1">{{ __('All steps complete!') }}</p>
            @endif
        </div>
        <a href="{{ route('journey.show') }}"
           class="bg-white text-primary-700 font-bold px-5 py-2.5 rounded-xl hover:bg-primary-50 transition whitespace-nowrap">
            {{-- Tamamlanınca "Continue" yanıltıcıydı → "Review and edit" (adımlar hâlâ düzenlenebilir). --}}
            @if ($jDone >= $jTotal && $jTotal > 0)
                {{ __('Review and edit') }} →
            @elseif ($jDone > 0)
                {{ __('Continue') }} →
            @else
                {{ __('Start now') }} →
            @endif
        </a>
    </div>
    <div class="mt-4 h-2 rounded-full bg-white/20 overflow-hidden">
        <div class="h-full bg-accent-400 rounded-full transition-all duration-500" style="width: {{ $jPct }}%"></div>
    </div>
</section>
