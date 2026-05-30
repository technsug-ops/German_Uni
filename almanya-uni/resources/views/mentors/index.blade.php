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
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3 flex items-center gap-3"><x-svg-icon name="users" class="w-8 h-8 md:w-10 md:h-10 text-white" /> {{ __('Mentors') }}</h1>
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
                      {{ ($filters['freeOnly'] ?? false) ? 'bg-amber-600 text-white border-amber-600' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' }} inline-flex items-center gap-1">
                <x-svg-icon name="sparkles" class="w-3.5 h-3.5" /> {{ __('Free only') }}
            </a>
        </div>
    </section>
@endif

<div class="max-w-[1400px] mx-auto px-4 py-10">
    @if ($mentors->isEmpty())
        <div class="bg-gradient-to-br from-emerald-50 to-white border border-emerald-200 rounded-xl p-8 md:p-10 text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 mx-auto mb-3 rounded-full bg-emerald-100 text-emerald-600"><x-svg-icon name="users" class="w-9 h-9" /></div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-2">{{ __('Mentor network is still in its early phase') }}</h2>
            <p class="text-sm text-gray-600 max-w-xl mx-auto mb-5">
                {{ __('No active mentors right now. We are recruiting alumni with successful careers in Germany. Apply below or check matching events.') }}
            </p>
            <div class="flex flex-wrap gap-3 justify-center">
                <a href="mailto:technsug@gmail.com?subject=AlmanyaUni%20Mentor%20Application"
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg font-semibold shadow-md transition">
                    <x-svg-icon name="pencil" class="w-4 h-4" /> {{ __('Apply as a mentor') }}
                </a>
                <a href="{{ route('events.index', ['type' => 'mentorship_match']) }}"
                   class="inline-flex items-center gap-2 bg-white border border-emerald-300 hover:bg-emerald-50 text-emerald-700 px-6 py-3 rounded-lg font-semibold transition">
                    <x-svg-icon name="calendar" class="w-4 h-4" /> {{ __('Mentor matching events') }}
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

    {{-- Hangi konularda mentor arıyoruz --}}
    <section class="mt-14">
        <div class="text-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2 inline-flex items-center gap-2 justify-center"><x-svg-icon name="target" class="w-7 h-7 text-emerald-600" /> {{ __('Which topics are we recruiting mentors for?') }}</h2>
            <p class="text-sm text-gray-600 max-w-2xl mx-auto">{{ __('We pair students with alumni based on their stage of the journey. If your experience matches any of the topics below, your help is invaluable.') }}</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
                $topics = [
                    ['icon' => 'academic-cap', 'title' => __('University selection (BSc/MSc)'), 'desc' => __('Which uni for which field, NC vs NC-frei, ranking vs fit decisions.')],
                    ['icon' => 'pencil', 'title' => __('Application & uni-assist'), 'desc' => __('VPD timing, document set, common rejection reasons, dual application strategy.')],
                    ['icon' => 'shield-check', 'title' => __('Visa & Sperrkonto'), 'desc' => __('Consulate appointment hacks, Sperrkonto provider choice, financial proof alternatives.')],
                    ['icon' => 'home', 'title' => __('Anmeldung & first week'), 'desc' => __('Bürgeramt strategy, Wohnungsgeberbestätigung, GEZ exemption, IBAN setup.')],
                    ['icon' => 'briefcase', 'title' => __('Werkstudent + job hunting'), 'desc' => __('Where to find roles, CV/Anschreiben format, interview language strategy.')],
                    ['icon' => 'beaker', 'title' => __('Master / PhD path'), 'desc' => __('Research proposal, supervisor contact, scholarship + Stipendium applications.')],
                    ['icon' => 'heart', 'title' => __('Medicine / dentistry'), 'desc' => __('Approbation, Heilpraktikergesetz, hospital placement (Famulatur, PJ).')],
                    ['icon' => 'language', 'title' => __('German language B1→C1'), 'desc' => __('TestDaF/DSH prep, immersion routines, Goethe/telc exam strategy.')],
                    ['icon' => 'leaf', 'title' => __('Daily life & integration'), 'desc' => __('Healthcare, taxes, German bureaucracy survival, community + Turkish associations.')],
                ];
            @endphp
            @foreach ($topics as $t)
                <div class="bg-white border border-gray-200 rounded-xl p-5 hover:border-emerald-300 transition">
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 mb-2"><x-svg-icon :name="$t['icon']" class="w-5 h-5" /></div>
                    <h3 class="font-bold text-gray-900 text-base mb-1">{{ $t['title'] }}</h3>
                    <p class="text-xs text-gray-600 leading-relaxed">{{ $t['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Mentor olma süreci --}}
    <section class="mt-14 bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-xl p-6 md:p-8">
        <div class="text-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2 inline-flex items-center gap-2 justify-center"><x-svg-icon name="list-bullet" class="w-7 h-7 text-emerald-600" /> {{ __('How becoming a mentor works') }}</h2>
            <p class="text-sm text-gray-600">{{ __('Light process — most applications are reviewed within 7 days.') }}</p>
        </div>
        <ol class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <li class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-600 text-white font-bold text-sm">1</span>
                    <h3 class="font-bold text-gray-900">{{ __('Apply') }}</h3>
                </div>
                <p class="text-xs text-gray-600 leading-relaxed">{{ __('Send an email with LinkedIn, current city/role, the topic(s) you can mentor on, and (optional) hourly rate or "free" preference.') }}</p>
            </li>
            <li class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-600 text-white font-bold text-sm">2</span>
                    <h3 class="font-bold text-gray-900">{{ __('Verification') }}</h3>
                </div>
                <p class="text-xs text-gray-600 leading-relaxed">{{ __('We verify your LinkedIn / education / current role and reach out for a short 15-min video chat to confirm fit and tone.') }}</p>
            </li>
            <li class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-600 text-white font-bold text-sm">3</span>
                    <h3 class="font-bold text-gray-900">{{ __('Go live') }}</h3>
                </div>
                <p class="text-xs text-gray-600 leading-relaxed">{{ __('Your profile + Calendly is published. Students book directly. You receive bookings via email; AlmanyaUni handles discovery + landing page.') }}</p>
            </li>
        </ol>
    </section>

    {{-- Avantajlar --}}
    <section class="mt-14">
        <div class="text-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2 inline-flex items-center gap-2 justify-center"><x-svg-icon name="star" class="w-7 h-7 text-amber-500" /> {{ __('Why become a mentor on AlmanyaUni') }}</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $perks = [
                    ['icon' => 'bell', 'title' => __('Visibility'), 'desc' => __('Personal mentor profile + dedicated SEO-indexed URL — your expertise gets discovered.')],
                    ['icon' => 'currency-euro', 'title' => __('Earning option'), 'desc' => __('Set your own hourly rate (or stay free). AlmanyaUni does not take commission in the first year.')],
                    ['icon' => 'sparkles', 'title' => __('Premium access'), 'desc' => __('Active mentors get free AlmanyaUni Premium membership + early access to new tools.')],
                    ['icon' => 'users', 'title' => __('Community'), 'desc' => __('Private Slack with other mentors, monthly peer-learning calls, networking with alumni.')],
                ];
            @endphp
            @foreach ($perks as $p)
                <div class="bg-white border border-gray-200 rounded-xl p-5 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 mx-auto rounded-full bg-emerald-50 text-emerald-600 mb-2"><x-svg-icon :name="$p['icon']" class="w-6 h-6" /></div>
                    <h3 class="font-bold text-gray-900 text-sm mb-1">{{ $p['title'] }}</h3>
                    <p class="text-xs text-gray-600 leading-relaxed">{{ $p['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- SSS --}}
    <x-faq-section
        class="mt-14"
        :title="__('Frequently Asked Questions for Mentors')"
        :subtitle="__('How sessions work, what we expect, and what you get.')"
        :faqs="[
            ['q' => __('Do I have to be alumni of a specific university?'), 'a' => __('No — anyone who has built professional or academic experience in Germany can apply (Bachelor onwards). What matters is the real-life knowledge you can pass on.')],
            ['q' => __('Can I mentor in Turkish only?'), 'a' => __('Yes. Most mentees prefer Turkish for clarity. German + English mentors are also welcome and often paired with international audiences.')],
            ['q' => __('How much time do I need to commit?'), 'a' => __('Average mentor takes 1-3 sessions per month (30-60 min each). You set your own availability through Calendly — pause anytime.')],
            ['q' => __('Is mentoring paid?'), 'a' => __('You decide. Many mentors stay free (especially when starting). Others set €15-60/hour. AlmanyaUni takes 0% commission in the first year of the program.')],
            ['q' => __('What if a student asks a question I don\'t know the answer to?'), 'a' => __('Honesty is the rule — saying \"this is outside my experience, here is a resource I trust\" is more valuable than guessing. We provide a knowledge base + can connect you with other mentors.')],
        ]"
    />
</div>

{{-- CTA: mentor ol --}}
<section class="bg-gradient-to-r from-emerald-700 to-teal-600 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 text-center">
        <h2 class="text-2xl md:text-3xl font-extrabold mb-3 inline-flex items-center gap-2 justify-center"><x-svg-icon name="star" class="w-7 h-7 text-amber-300" /> {{ __('Have you built a career in Germany?') }}</h2>
        <p class="text-emerald-100 mb-6 max-w-2xl mx-auto">
            {{ __('Join as a mentor and guide newly arrived students. 30 min sharing = years of a student\'s journey.') }}
        </p>
        <a href="mailto:technsug@gmail.com?subject=AlmanyaUni%20Mentor%20Application"
           class="inline-flex items-center gap-2 bg-white text-emerald-700 hover:bg-gray-100 px-8 py-3 rounded-lg font-bold shadow-lg transition">
            <x-svg-icon name="pencil" class="w-4 h-4" /> {{ __('Apply as a mentor') }}
        </a>
    </div>
</section>
@endsection
