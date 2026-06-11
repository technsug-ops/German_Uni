@extends('layouts.app')

@section('title', __('Career Compass — Find the Right Profession for You') . ' — ' . brand('name'))

<x-seo
    :title="__('Career Compass — Find Your Profession through Technical + Emotional Analysis')"
    :description="__('Discover your talent (RIASEC) + value profile in 12 questions. Find the most suitable matches from 3,500+ real professions in Germany, with education paths and programs.')"
/>

<x-tool-schema tool="career-compass" />

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-rose-600 via-orange-500 to-amber-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-rose-100 mb-3">
            <a href="{{ route('tools.index') }}" class="hover:text-white">{{ __('Tools') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('Career Compass') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3 inline-flex items-center gap-3">
            <x-svg-icon name="map" class="w-8 h-8 md:w-10 md:h-10" />
            {{ __('Career Compass') }}
        </h1>
        <p class="text-lg md:text-xl text-rose-50 max-w-3xl">
            {!! __('Not which university — <strong>which profession</strong>? In 12 questions we analyze your aptitude (RIASEC) + values to match you to suitable professions among 3,500+ real ones in Germany.') !!}
        </p>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10">

@if (! $result)
    @php
        // RIASEC soruları: her seçenek bir harf. Değer soruları: ikili tercih.
        $riasecQuestions = [
            ['q1', 'sparkles', __('A free Saturday — which is most appealing?'), [
                ['R', 'wrench-screwdriver', __('Repair/assemble something')],
                ['I', 'beaker',             __('Explore science/technology')],
                ['A', 'sparkles',           __('Work on art/music/writing')],
                ['S', 'users',              __('Meet friends / help others')],
            ]],
            ['q2', 'users', __('Your natural role in a group project?'), [
                ['R', 'wrench-screwdriver', __('Doing the practical work, building')],
                ['I', 'puzzle',             __('Analyzing the problem')],
                ['A', 'pencil',             __('Making the presentation/design')],
                ['E', 'chat',               __('Leader organizing people')],
            ]],
            ['q3', 'trophy', __('Which praise makes you happier?'), [
                ['R', 'wrench-screwdriver', __('"Well done, you are very skilled"')],
                ['I', 'light-bulb',         __('"You are very smart, great analysis"')],
                ['A', 'sparkles',           __('"You are very creative"')],
                ['S', 'heart',              __('"You get along great with people"')],
            ]],
            ['q4', 'light-bulb', __('What excites you about the business world?'), [
                ['R', 'building-storefront', __('Building something tangible')],
                ['I', 'search',              __('Solving a puzzle/data')],
                ['E', 'rocket-launch',       __('Starting and running your own business')],
                ['C', 'list-bullet',         __('Working in an orderly, systematic way')],
            ]],
            ['q5', 'book-open', __('Favorite type of class at school?'), [
                ['R', 'cog',     __('Lab / workshop')],
                ['I', 'beaker',  __('Math / science')],
                ['A', 'sparkles', __('Art / literature')],
                ['S', 'globe',   __('Social studies / history')],
            ]],
            ['q6', 'mountain', __('When you face a problem?'), [
                ['R', 'wrench-screwdriver', __('I try right away, solve with my hands')],
                ['I', 'beaker',             __('I research, look for a logical solution')],
                ['A', 'sparkles',           __('I try a creative, unconventional way')],
                ['C', 'list-bullet',        __('I plan in detail, go step by step')],
            ]],
            ['q7', 'building-office', __('Which environment suits you?'), [
                ['R', 'wrench-screwdriver',   __('Workshop / field / outdoors')],
                ['I', 'beaker',               __('Laboratory / research center')],
                ['A', 'photo',                __('Studio / creative agency')],
                ['C', 'building-office',      __('Office / corporate')],
                ['S', 'shield-check',         __('Hospital / school / aid organization')],
            ]],
        ];

        $valueQuestions = [
            ['v_income', 'scale', __('More important in career choice?'), [
                ['income',  'banknotes', __('High and guaranteed salary')],
                ['meaning', 'sparkles',  __('Meaningful work that benefits society')],
            ]],
            ['v_rhythm', 'calendar', __('Your ideal workday?'), [
                ['stable',  'arrow-path', __('Same routine, predictable')],
                ['dynamic', 'fire',       __('Different every day, dynamic')],
            ]],
            ['v_place', 'map-pin', __('Where would you like to work?'), [
                ['office', 'building-office', __('Desk / office')],
                ['field',  'truck',           __('In the field / moving around')],
            ]],
            ['v_path', 'academic-cap', __('Education path preference?'), [
                ['theory',   'building-office',    __('University (academic, in-depth)')],
                ['practice', 'wrench-screwdriver', __('Practical vocational training (Ausbildung, on-the-job)')],
            ]],
            ['v_security', 'shield-check', __('Which is closer to you?'), [
                ['security', 'home',         __('Stable, guaranteed position')],
                ['freedom',  'rocket-launch', __('My own business, flexibility, freedom')],
            ]],
        ];

        $allQuestions = array_merge($riasecQuestions, $valueQuestions);
        $totalQ = count($allQuestions);
    @endphp

    <div x-data="compassApp({{ $totalQ }})" x-init="init()" class="bg-white border border-gray-200 rounded-2xl shadow-lg overflow-hidden">
        {{-- Progress --}}
        <div class="bg-gradient-to-r from-rose-50 to-amber-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-700">
                    {{ __('Question') }} <span x-text="currentStep + 1"></span> / {{ $totalQ }}
                </span>
                <span class="text-xs text-gray-500" x-text="Math.round(((currentStep + 1) / totalSteps) * 100) + '%'"></span>
            </div>
            <div class="w-full bg-white rounded-full h-2 overflow-hidden">
                <div class="h-full bg-gradient-to-r from-rose-500 to-amber-500 transition-all duration-500" :style="`width: ${((currentStep + 1) / totalSteps) * 100}%`"></div>
            </div>
        </div>

        <form method="POST" action="{{ route('tools.career-compass') }}" class="p-6 md:p-8">
            @csrf

            @foreach ($allQuestions as $idx => $q)
                @php [$name, $iconKey, $title, $opts] = $q; @endphp
                <div x-show="currentStep === {{ $idx }}" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="space-y-5 md:min-h-[360px]">
                    <div class="text-center mb-6">
                        <div class="flex justify-center mb-3 text-rose-600"><x-svg-icon name="{{ $iconKey }}" class="w-12 h-12" /></div>
                        <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ $title }}</h2>
                        @if ($idx < 7)
                            <p class="text-gray-400 text-xs mt-2 uppercase tracking-wider font-bold">{{ __('Aptitude profile') }}</p>
                        @else
                            <p class="text-rose-400 text-xs mt-2 uppercase tracking-wider font-bold">{{ __('Values & motivation') }}</p>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 {{ count($opts) <= 2 ? 'md:grid-cols-2' : 'md:grid-cols-2' }} gap-3">
                        @foreach ($opts as [$val, $optIcon, $label])
                            <label class="block cursor-pointer">
                                <input type="radio" name="{{ $name }}" value="{{ $val }}" x-model="answers.{{ $name }}" class="sr-only peer">
                                <div class="border-2 border-gray-200 peer-checked:border-rose-500 peer-checked:bg-rose-50 rounded-xl p-4 flex items-center gap-3 transition hover:border-rose-300">
                                    <span class="flex-shrink-0 text-rose-600"><x-svg-icon name="{{ $optIcon }}" class="w-8 h-8" /></span>
                                    <span class="font-semibold text-gray-900">{{ $label }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded p-3 text-sm text-red-700 mt-6">{{ __('Answer all questions.') }}</div>
            @endif

            <div class="mt-8 flex items-center justify-between pt-5 border-t border-gray-100">
                <button type="button" @click="prev()" x-show="currentStep > 0" class="text-sm font-semibold text-gray-500 hover:text-gray-700">← {{ __('Back') }}</button>
                <div x-show="currentStep === 0"></div>
                <div class="flex items-center gap-3">
                    <button type="button" @click="next()" x-show="currentStep < totalSteps - 1" :disabled="! canProceed()"
                            :class="canProceed() ? 'bg-gradient-to-r from-rose-600 to-amber-500 hover:from-rose-700 hover:to-amber-600 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                            class="font-bold px-6 py-3 rounded-lg transition shadow-md">{{ __('Continue') }} →</button>
                    <button type="submit" x-show="currentStep === totalSteps - 1" :disabled="! canProceed()"
                            :class="canProceed() ? 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                            class="font-bold px-6 py-3 rounded-lg transition shadow-md inline-flex items-center gap-2">
                        <x-svg-icon name="map" class="w-4 h-4" />
                        {{ __('Show My Profile') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <p class="text-xs text-gray-400 text-center mt-4 inline-flex items-center gap-1.5 justify-center w-full">
        <x-svg-icon name="light-bulb" class="w-4 h-4" />
        {{ __('This is not a psychological diagnosis — it is a profession discovery tool based on your aptitude + value profile.') }}
    </p>

    <script>
        function compassApp(total) {
            return {
                currentStep: 0,
                totalSteps: total,
                answers: { q1:'',q2:'',q3:'',q4:'',q5:'',q6:'',q7:'', v_income:'',v_rhythm:'',v_place:'',v_path:'',v_security:'' },
                keys: ['q1','q2','q3','q4','q5','q6','q7','v_income','v_rhythm','v_place','v_path','v_security'],
                init() {
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' && this.currentStep < this.totalSteps - 1 && this.canProceed()) { e.preventDefault(); this.next(); }
                    });
                },
                canProceed() { return this.answers[this.keys[this.currentStep]] !== ''; },
                next() { if (this.canProceed() && this.currentStep < this.totalSteps - 1) this.currentStep++; },
                prev() { if (this.currentStep > 0) this.currentStep--; },
            }
        }
    </script>

@else
    {{-- ============ SONUÇ ============ --}}

    {{-- PROFİL KARTI --}}
    <section class="bg-gradient-to-br from-rose-600 via-orange-500 to-amber-500 text-white rounded-2xl p-6 md:p-10 mb-6 shadow-xl text-center">
        <p class="text-xs font-bold uppercase tracking-widest text-rose-100 mb-1">{{ __('Your Profile · Holland Code:') }} {{ $result['holland_code'] }}</p>
        <h2 class="text-3xl md:text-5xl font-extrabold mb-3">{{ $result['profile']['title'] }}</h2>
        <p class="text-lg text-rose-50 max-w-2xl mx-auto">{{ $result['profile']['desc'] }}</p>

        {{-- RIASEC mini bar --}}
        @php
            $riasecLabels = ['R'=>__('Realistic'),'I'=>__('Investigative'),'A'=>__('Artistic'),'S'=>__('Social'),'E'=>__('Enterprising'),'C'=>__('Conventional')];
            $maxR = max($result['riasec']) ?: 1;
        @endphp
        <div class="grid grid-cols-3 md:grid-cols-6 gap-2 mt-6 max-w-2xl mx-auto">
            @foreach ($result['riasec'] as $letter => $val)
                <div class="bg-white/15 backdrop-blur rounded-lg p-2">
                    <div class="text-lg font-extrabold">{{ $val }}</div>
                    <div class="text-[10px] text-rose-100">{{ $riasecLabels[$letter] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- PROFESSIONS --}}
    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-6 inline-flex items-center gap-2">
        <x-svg-icon name="target" class="w-7 h-7" />
        {{ __(':count Professions That Suit You', ['count' => count($result['professions'])]) }}
    </h2>

    @if (empty($result['professions']))
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-8 text-center">
            <p class="text-yellow-900">{{ __('No matches found.') }} <a href="{{ route('tools.career-compass') }}" class="font-semibold underline">{{ __('Try again') }}</a></p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($result['professions'] as $i => $p)
                <article class="bg-white border-2 {{ $i === 0 ? 'border-rose-400 shadow-lg' : 'border-gray-200' }} rounded-2xl p-5 md:p-6 hover:border-rose-300 hover:shadow-md transition">
                    <div class="flex flex-col md:flex-row md:items-start gap-5">
                        {{-- Skor --}}
                        <div class="flex-shrink-0 flex md:flex-col items-center gap-3 md:w-28">
                            <div class="relative w-24 h-24 md:w-28 md:h-28 flex-shrink-0">
                                <svg viewBox="0 0 100 100" class="w-full h-full transform -rotate-90">
                                    <circle cx="50" cy="50" r="42" stroke="#e5e7eb" stroke-width="8" fill="none"/>
                                    <circle cx="50" cy="50" r="42" stroke="{{ $p['score'] >= 80 ? '#f43f5e' : ($p['score'] >= 60 ? '#f59e0b' : '#9ca3af') }}" stroke-width="8" fill="none" stroke-linecap="round" stroke-dasharray="263.89" stroke-dashoffset="{{ 263.89 * (1 - $p['score']/100) }}"/>
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-2xl md:text-3xl font-extrabold {{ $p['score'] >= 80 ? 'text-rose-600' : ($p['score'] >= 60 ? 'text-amber-600' : 'text-gray-500') }}">%{{ $p['score'] }}</span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500">{{ __('match') }}</span>
                                </div>
                            </div>
                            @if ($i === 0)
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded bg-rose-100 text-rose-700 whitespace-nowrap">
                                    <x-svg-icon name="star" class="w-3 h-3" />
                                    {{ __('Best Match') }}
                                </span>
                            @endif
                        </div>

                        {{-- Detay --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">{{ $p['field']?->name }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $p['type'] === 'studienberuf' ? 'bg-indigo-100 text-indigo-700' : ($p['type'] === 'ausbildung' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ ['studienberuf'=>__('University profession'),'ausbildung'=>'Ausbildung','weiterbildung'=>__('Advanced training'),'grundberuf'=>__('Basic profession')][$p['type']] ?? $p['type'] }}
                                </span>
                            </div>
                            <h3 class="text-lg md:text-xl font-bold text-gray-900 leading-tight mb-1">{{ $p['name'] }}</h3>
                            <p class="text-sm text-gray-500 mb-3">{{ $p['description'] }}</p>

                            {{-- Technical reasoning --}}
                            @if (! empty($p['reasons_tech']))
                                <div class="bg-blue-50 rounded-lg p-3 mb-2">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-blue-600 mb-1 inline-flex items-center gap-1.5">
                                        <x-svg-icon name="light-bulb" class="w-3.5 h-3.5" />
                                        {{ __('Technical match') }}
                                    </p>
                                    <ul class="space-y-1 text-sm text-gray-700">
                                        @foreach ($p['reasons_tech'] as $r)
                                            <li class="flex items-start gap-2"><span class="text-blue-500"><x-svg-icon name="check" class="w-3.5 h-3.5" /></span><span>{{ $r }}</span></li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Emotional reasoning --}}
                            @if (! empty($p['reasons_emo']))
                                <div class="bg-rose-50 rounded-lg p-3 mb-3">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-rose-600 mb-1 inline-flex items-center gap-1.5">
                                        <x-svg-icon name="heart" class="w-3.5 h-3.5" />
                                        {{ __('Value match') }}
                                    </p>
                                    <ul class="space-y-1 text-sm text-gray-700">
                                        @foreach ($p['reasons_emo'] as $r)
                                            <li class="flex items-start gap-2"><span class="text-rose-500"><x-svg-icon name="check" class="w-3.5 h-3.5" /></span><span>{{ $r }}</span></li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <a href="{{ route('professions.show', $p['slug']) }}" class="inline-flex items-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                                {{ __('Profession details') }} →
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    {{-- İLGİLİ PROGRAMLAR --}}
    @if ($result['programs']->isNotEmpty())
        <section class="mt-8 bg-white border border-gray-200 rounded-2xl p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4 inline-flex items-center gap-2">
                <x-svg-icon name="academic-cap" class="w-6 h-6" />
                {{ __('Programs you can study in Germany in these fields') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach ($result['programs'] as $prog)
                    <a href="{{ route('programs.show', $prog->slug) }}" class="flex items-start gap-3 p-3 border border-gray-100 rounded-lg hover:bg-gray-50 hover:border-rose-300 transition">
                        <span class="text-xl flex-shrink-0 flex items-center">
                            @if ($prog->language === 'en') 🇬🇧
                            @elseif ($prog->language === 'both') <x-svg-icon name="globe" class="w-5 h-5 text-rose-600" />
                            @else 🇩🇪
                            @endif
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-900 leading-tight">{{ $prog->name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $prog->university?->name_de }} · {{ ucfirst($prog->degree) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-3">
        <a href="{{ route('tools.career-compass') }}" class="inline-flex items-center justify-center gap-1.5 bg-white border-2 border-rose-200 hover:border-rose-400 text-rose-700 font-semibold py-3 rounded-lg transition">
            <x-svg-icon name="arrow-path" class="w-4 h-4" />
            {{ __('Try again') }}
        </a>
        <a href="{{ route('tools.recommendation') }}" class="inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg transition shadow-md">
            <x-svg-icon name="target" class="w-4 h-4" />
            {{ __('Find your university now') }} →
        </a>
        <button type="button" onclick="navigator.share ? navigator.share({title:'{{ __('My Career Compass Result') }}',text:'{{ __('I got:') }} {{ $result['profile']['title'] }}',url:location.href}) : (navigator.clipboard.writeText(location.href), alert('{{ __('Link copied') }}'))" class="inline-flex items-center justify-center gap-1.5 bg-white border-2 border-amber-200 hover:border-amber-400 text-amber-700 font-semibold py-3 rounded-lg transition">
            <x-svg-icon name="link" class="w-4 h-4" />
            {{ __('Share result') }}
        </button>
    </div>
@endif

</div>

{{-- Auto-FAQ (AIO + Featured Snippet) --}}
<x-faq-section
    :title="__('Frequently Asked Questions about Career Compass')"
    :subtitle="__('RIASEC methodology, BERUFENET data, and validity of the quiz')"
    :faqs="\App\Support\PageFaq::forCareerCompass()"
/>
@endsection
