@extends('layouts.app')

@section('title', __('Mentors — AlmanyaUni Alumni Network') . ' — ' . brand('name'))

<x-seo
    :title="__('Mentors — AlmanyaUni Alumni Network')"
    :description="__('1-on-1 mentorship with successful alumni who built careers in Germany. Medicine, engineering, AI, startup, life — ask the expert.')"
/>

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-emerald-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('Mentors') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">🤝 {{ __('Mentors') }}</h1>
        <p class="text-lg md:text-xl text-emerald-100 max-w-3xl">
            {{ __('1-on-1 mentorship sessions with alumni + experts who built careers in Germany. University choice, application, visa, career or daily life — ask the expert.') }}
        </p>
        @if ($mentors->total() > 0)
            <p class="text-sm text-emerald-100 mt-4">
                {!! __('<strong class="text-white">:n</strong> active mentors · free + premium session options', ['n' => $mentors->total()]) !!}
            </p>
        @endif
    </div>
</section>

{{-- Filter chips --}}
@if (! empty($allTopics))
    <section class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-[1400px] mx-auto px-4 py-3 flex items-center flex-wrap gap-2">
            <span class="text-xs text-gray-500 mr-1">{{ __('Topic:') }}</span>
            <a href="{{ route('mentors.index') }}"
               class="text-xs px-3 py-1.5 rounded-full border transition
                      {{ ! ($filters['topic'] ?? null) ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100' }}">
                {{ __('All') }}
            </a>
            @foreach ($allTopics as $t)
                <a href="{{ route('mentors.index', ['topic' => $t]) }}"
                   class="text-xs px-3 py-1.5 rounded-full border transition
                          {{ ($filters['topic'] ?? null) === $t ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }}">
                    {{ $t }}
                </a>
            @endforeach

            <span class="text-xs text-gray-400 mx-2">·</span>
            <a href="{{ route('mentors.index', array_merge(request()->query(), ['free' => ($filters['freeOnly'] ?? false) ? null : 1])) }}"
               class="text-xs px-3 py-1.5 rounded-full border transition
                      {{ ($filters['freeOnly'] ?? false) ? 'bg-amber-600 text-white border-amber-600' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' }}">
                🎁 {{ __('Free only') }}
            </a>
        </div>
    </section>
@endif

<div class="max-w-[1400px] mx-auto px-4 py-10">
    @if ($mentors->isEmpty())
        <div class="bg-gradient-to-br from-emerald-50 to-white border border-emerald-200 rounded-xl p-12 text-center">
            <div class="text-6xl mb-4">🤝</div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Mentor network is still in its early phase') }}</h2>
            <p class="text-gray-600 max-w-xl mx-auto mb-6">
                {{ __('No active mentors right now, but coming soon! We are bringing alumni with successful careers in Germany into our network. If you want to be a mentor, reach out.') }}
            </p>
            <div class="flex flex-wrap gap-3 justify-center">
                <a href="mailto:technsug@gmail.com?subject=AlmanyaUni%20Mentor%20Application"
                   class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg font-semibold shadow-md transition">
                    ✍ {{ __('Apply as a mentor') }}
                </a>
                <a href="{{ route('events.index', ['type' => 'mentorship_match']) }}"
                   class="inline-block bg-white border border-emerald-300 hover:bg-emerald-50 text-emerald-700 px-6 py-3 rounded-lg font-semibold transition">
                    📅 {{ __('Mentor matching events') }}
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($mentors as $m)
                @include('mentors._card', ['mentor' => $m])
            @endforeach
        </div>

        <div class="mt-8">{{ $mentors->links() }}</div>
    @endif
</div>

{{-- CTA: mentor ol --}}
<section class="bg-gradient-to-r from-emerald-700 to-teal-600 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 text-center">
        <h2 class="text-2xl md:text-3xl font-extrabold mb-3">🌟 {{ __('Have you built a career in Germany?') }}</h2>
        <p class="text-emerald-100 mb-6 max-w-2xl mx-auto">
            {{ __('Join as a mentor and guide newly arrived students. 30 min sharing = years of a student\'s journey.') }}
        </p>
        <a href="mailto:technsug@gmail.com?subject=AlmanyaUni%20Mentor%20Application"
           class="inline-block bg-white text-emerald-700 hover:bg-gray-100 px-8 py-3 rounded-lg font-bold shadow-lg transition">
            ✍ {{ __('Apply as a mentor') }}
        </a>
    </div>
</section>
@endsection
