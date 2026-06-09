@extends('layouts.app')

@section('title', __('University Match Quiz — 8 Questions to Find the Best German Universities for You') . ' — ' . brand('name'))

<x-seo
    :title="__('Germany University Match — 8-Question Smart Quiz')"
    :description="__('We match the best German universities for you based on budget, field, language preference and city type. Each university gets a %match score + reason explanation.')"
/>

<x-tool-schema tool="recommendation" />

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-indigo-700 via-purple-600 to-pink-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-indigo-100 mb-3">
            <a href="{{ route('tools.index') }}" class="hover:text-white">{{ __('Tools') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('University Match') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3 inline-flex items-center gap-3">
            <x-svg-icon name="target" class="w-8 h-8 md:w-10 md:h-10" />
            {{ __('Find the Best University for You') }}
        </h1>
        <p class="text-lg md:text-xl text-indigo-100 max-w-3xl">
            {{ __('8 quick questions. Not AI — based on real data (14,527 programs + 464 unis + 130 cities), each university gets a % score + match reason.') }}
        </p>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10">

@if (! $result)
    {{-- ================= QUIZ FORM (Alpine multi-step) ================= --}}
    <div x-data="quizApp()" x-init="init()" class="bg-white border border-gray-200 rounded-2xl shadow-lg overflow-hidden">

        {{-- Progress bar --}}
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-700">
                    {{ __('Question') }} <span x-text="currentStep + 1"></span> / <span x-text="totalSteps"></span>
                </span>
                <span class="text-xs text-gray-500" x-text="Math.round(((currentStep + 1) / totalSteps) * 100) + '%'"></span>
            </div>
            <div class="w-full bg-white rounded-full h-2 overflow-hidden">
                <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-500"
                     :style="`width: ${((currentStep + 1) / totalSteps) * 100}%`"></div>
            </div>
        </div>

        <form method="POST" action="{{ route('tools.recommendation') }}" id="quizForm" class="p-6 md:p-8">
            @csrf

            {{-- 1. BÜTÇE --}}
            <div x-show="currentStep === 0" x-transition.duration.300ms class="space-y-5">
                <div class="text-center mb-6">
                    <div class="flex justify-center mb-3 text-indigo-600"><x-svg-icon name="banknotes" class="w-12 h-12" /></div>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ __('Total monthly living budget?') }}</h2>
                    <p class="text-gray-500 text-sm mt-2">{{ __('Rent + food + transport + insurance + other · Sperrkonto min: €992/month (2025)') }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @php
                        $budgets = [
                            ['low',  'currency-euro', '< €1,000',         __('East Germany — Leipzig, Dresden, Chemnitz (rent ≤ €280)')],
                            ['mid',  'briefcase',     '€1,000–€1,400',    __('Berlin, Cologne, Hannover, Stuttgart range')],
                            ['high', 'sparkles',      '> €1,400',          __('Munich, Frankfurt, Hamburg, Düsseldorf')],
                        ];
                    @endphp
                    @foreach ($budgets as [$v, $icon, $label, $desc])
                        <label class="block cursor-pointer">
                            <input type="radio" name="budget" value="{{ $v }}" x-model="answers.budget" class="sr-only peer">
                            <div class="border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 rounded-xl p-5 text-center transition hover:border-indigo-300">
                                <div class="flex justify-center mb-2 text-indigo-600"><x-svg-icon name="{{ $icon }}" class="w-8 h-8" /></div>
                                <div class="font-bold text-gray-900">{{ $label }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $desc }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- 2. ŞEHİR BÜYÜKLÜĞÜ --}}
            <div x-show="currentStep === 1" x-transition.duration.300ms class="space-y-5">
                <div class="text-center mb-6">
                    <div class="flex justify-center mb-3 text-indigo-600"><x-svg-icon name="building-office" class="w-12 h-12" /></div>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ __('What kind of city do you want?') }}</h2>
                    <p class="text-gray-500 text-sm mt-2">{{ __('City size and daily rhythm') }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @php
                        $sizes = [
                            ['small',  'mountain',        __('Small University Town'), 'Tübingen, Göttingen, Heidelberg'],
                            ['medium', 'home',            __('Mid-Size'),               'Münster, Aachen, Bonn'],
                            ['large',  'building-office', __('Big Metropolis'),         'Berlin, Munich, Hamburg'],
                        ];
                    @endphp
                    @foreach ($sizes as [$v, $icon, $label, $desc])
                        <label class="block cursor-pointer">
                            <input type="radio" name="city_size" value="{{ $v }}" x-model="answers.city_size" class="sr-only peer">
                            <div class="border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 rounded-xl p-5 text-center transition hover:border-indigo-300">
                                <div class="flex justify-center mb-2 text-indigo-600"><x-svg-icon name="{{ $icon }}" class="w-8 h-8" /></div>
                                <div class="font-bold text-gray-900">{{ $label }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $desc }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- 3. BÖLGE --}}
            <div x-show="currentStep === 2" x-transition.duration.300ms class="space-y-5">
                <div class="text-center mb-6">
                    <div class="flex justify-center mb-3 text-indigo-600"><x-svg-icon name="map" class="w-12 h-12" /></div>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ __('Which region of Germany?') }}</h2>
                    <p class="text-gray-500 text-sm mt-2">{{ __('Differs in climate, culture and price') }}</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    @php
                        $regions = [
                            ['nord', 'map-pin',  __('North'),     'Hamburg, Bremen, Kiel'],
                            ['west', 'home',     __('West'),      'Cologne, Düsseldorf, Aachen'],
                            ['sued', 'mountain', __('South'),     'Munich, Stuttgart, Karlsruhe'],
                            ['ost',  'sparkles', __('East'),      'Berlin, Leipzig, Dresden'],
                            ['any',  'globe',    __('Any'),       __('All work')],
                        ];
                    @endphp
                    @foreach ($regions as [$v, $icon, $label, $desc])
                        <label class="block cursor-pointer">
                            <input type="radio" name="region" value="{{ $v }}" x-model="answers.region" class="sr-only peer">
                            <div class="border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 rounded-xl p-4 text-center transition hover:border-indigo-300">
                                <div class="flex justify-center mb-1 text-indigo-600"><x-svg-icon name="{{ $icon }}" class="w-6 h-6" /></div>
                                <div class="font-bold text-gray-900 text-sm">{{ $label }}</div>
                                <div class="text-[10px] text-gray-500 mt-1">{{ $desc }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- 4. ALAN --}}
            <div x-show="currentStep === 3" x-transition.duration.300ms class="space-y-5">
                <div class="text-center mb-6">
                    <div class="flex justify-center mb-3 text-indigo-600"><x-svg-icon name="book-open" class="w-12 h-12" /></div>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ __('What field do you want to study?') }}</h2>
                    <p class="text-gray-500 text-sm mt-2">{{ __('Let\'s find which universities have the strongest program for you') }}</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @php
                        $fields = [
                            ['muhendislik',     'cog',          __('Engineering')],
                            ['bilisim',         'cursor-arrow-rays', __('Computer Science / IT')],
                            ['matematik-doga',  'beaker',       __('Mathematics / Natural Sciences')],
                            ['tip-saglik',      'shield-check', __('Medicine / Health')],
                            ['hukuk-ekonomi',   'scale',        __('Law / Economics')],
                            ['sosyal-bilimler', 'users',        __('Social Sciences')],
                            ['sanat-tasarim',   'sparkles',     __('Art / Design')],
                            ['dil-kultur',      'language',     __('Language / Culture')],
                            ['tarim-ormancilik','globe',        __('Agriculture / Forestry')],
                        ];
                    @endphp
                    @foreach ($fields as [$v, $icon, $label])
                        <label class="block cursor-pointer">
                            <input type="radio" name="field" value="{{ $v }}" x-model="answers.field" class="sr-only peer">
                            <div class="border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 rounded-xl p-3 text-center transition hover:border-indigo-300">
                                <div class="flex justify-center text-indigo-600"><x-svg-icon name="{{ $icon }}" class="w-6 h-6" /></div>
                                <div class="font-semibold text-gray-900 text-xs md:text-sm mt-1">{{ $label }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- 5. DİL --}}
            <div x-show="currentStep === 4" x-transition.duration.300ms class="space-y-5">
                <div class="text-center mb-6">
                    <div class="flex justify-center mb-3 text-indigo-600"><x-svg-icon name="language" class="w-12 h-12" /></div>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ __('Which language do you want to study in?') }}</h2>
                    <p class="text-gray-500 text-sm mt-2">{{ __('Real data: 6,101 English + 8,498 German programs') }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @php
                        $langs = [
                            ['de',   'flag',          __('German'),       __('Classic, free, deep integration'), '🇩🇪'],
                            ['en',   'flag',          __('English'),      __('Fastest path if you don\'t speak German'), '🇬🇧'],
                            ['both', 'globe',         __('Either works'), __('Start in English, switch to German later'), null],
                        ];
                    @endphp
                    @foreach ($langs as [$v, $icon, $label, $desc, $flag])
                        <label class="block cursor-pointer">
                            <input type="radio" name="lang" value="{{ $v }}" x-model="answers.lang" class="sr-only peer">
                            <div class="border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 rounded-xl p-5 text-center transition hover:border-indigo-300">
                                @if ($flag)
                                    <div class="text-3xl mb-2">{{ $flag }}</div>
                                @else
                                    <div class="flex justify-center mb-2 text-indigo-600"><x-svg-icon name="{{ $icon }}" class="w-8 h-8" /></div>
                                @endif
                                <div class="font-bold text-gray-900">{{ $label }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $desc }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- 6. ÜNİ TİPİ --}}
            <div x-show="currentStep === 5" x-transition.duration.300ms class="space-y-5">
                <div class="text-center mb-6">
                    <div class="flex justify-center mb-3 text-indigo-600"><x-svg-icon name="academic-cap" class="w-12 h-12" /></div>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ __('University type preference?') }}</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @php
                        $types = [
                            ['public',  'building-office', __('Public'),  __('No / low tuition, more classical')],
                            ['private', 'sparkles',        __('Private'), __('Expensive but boutique, lots of English')],
                            ['any',     'puzzle',          __('Any'),     __('I\'ll consider both')],
                        ];
                    @endphp
                    @foreach ($types as [$v, $icon, $label, $desc])
                        <label class="block cursor-pointer">
                            <input type="radio" name="uni_type" value="{{ $v }}" x-model="answers.uni_type" class="sr-only peer">
                            <div class="border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 rounded-xl p-5 text-center transition hover:border-indigo-300">
                                <div class="flex justify-center mb-2 text-indigo-600"><x-svg-icon name="{{ $icon }}" class="w-8 h-8" /></div>
                                <div class="font-bold text-gray-900">{{ $label }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $desc }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- 7. LIFESTYLE --}}
            <div x-show="currentStep === 6" x-transition.duration.300ms class="space-y-5">
                <div class="text-center mb-6">
                    <div class="flex justify-center mb-3 text-indigo-600"><x-svg-icon name="sparkles" class="w-12 h-12" /></div>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ __('How do you want campus life to be?') }}</h2>
                    <p class="text-gray-500 text-sm mt-2">{{ __('Intensity + social life rhythm') }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @php
                        $lifestyles = [
                            ['quiet',    'book-open', __('Quiet'),    __('Small campus, academic focus, peaceful')],
                            ['balanced', 'scale',     __('Balanced'), __('Both classes and social life, average pace')],
                            ['vibrant',  'fire',      __('Vibrant'),  __('Large campus, crowded, constant events')],
                        ];
                    @endphp
                    @foreach ($lifestyles as [$v, $icon, $label, $desc])
                        <label class="block cursor-pointer">
                            <input type="radio" name="lifestyle" value="{{ $v }}" x-model="answers.lifestyle" class="sr-only peer">
                            <div class="border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 rounded-xl p-5 text-center transition hover:border-indigo-300">
                                <div class="flex justify-center mb-2 text-indigo-600"><x-svg-icon name="{{ $icon }}" class="w-8 h-8" /></div>
                                <div class="font-bold text-gray-900">{{ $label }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $desc }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- 8. TOPLULUK --}}
            <div x-show="currentStep === 7" x-transition.duration.300ms class="space-y-5">
                <div class="text-center mb-6">
                    <div class="flex justify-center mb-3 text-indigo-600"><x-svg-icon name="users" class="w-12 h-12" /></div>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ __('International student community?') }}</h2>
                    <p class="text-gray-500 text-sm mt-2">{{ __('How large an international community do you want in the city?') }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @php
                        $communities = [
                            ['large_intl', 'globe', __('Large + International'),  __('Berlin/Cologne style, many languages/cultures')],
                            ['medium',     'users', __('Medium + Local mix'),     __('Reasonable international community, balanced')],
                            ['local',      'home',  __('Small + Local'),          __('Few foreigners, German environment, fast integration')],
                        ];
                    @endphp
                    @foreach ($communities as [$v, $icon, $label, $desc])
                        <label class="block cursor-pointer">
                            <input type="radio" name="community" value="{{ $v }}" x-model="answers.community" class="sr-only peer">
                            <div class="border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 rounded-xl p-5 text-center transition hover:border-indigo-300">
                                <div class="flex justify-center mb-2 text-indigo-600"><x-svg-icon name="{{ $icon }}" class="w-8 h-8" /></div>
                                <div class="font-bold text-gray-900">{{ $label }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $desc }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded p-3 text-sm text-red-700 mt-6">
                    {{ __('An error occurred. Please answer all questions.') }}
                </div>
            @endif

            {{-- NAV BUTTONS --}}
            <div class="mt-8 flex items-center justify-between pt-5 border-t border-gray-100">
                <button type="button" @click="prev()" x-show="currentStep > 0"
                        class="text-sm font-semibold text-gray-500 hover:text-gray-700 inline-flex items-center gap-1.5">
                    ← {{ __('Back') }}
                </button>
                <div x-show="currentStep === 0"></div>

                <div class="flex items-center gap-3">
                    <span x-show="currentStep < totalSteps - 1" class="text-xs text-gray-400 hidden md:inline">{{ __('Press Enter to continue') }}</span>
                    <button type="button" @click="next()" x-show="currentStep < totalSteps - 1"
                            :disabled="! canProceed()"
                            :class="canProceed() ? 'bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                            class="font-bold px-6 py-3 rounded-lg transition shadow-md inline-flex items-center gap-2">
                        {{ __('Next') }} <span class="text-lg">→</span>
                    </button>
                    <button type="submit" x-show="currentStep === totalSteps - 1"
                            :disabled="! canProceed()"
                            :class="canProceed() ? 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                            class="font-bold px-6 py-3 rounded-lg transition shadow-md inline-flex items-center gap-2">
                        <x-svg-icon name="target" class="w-4 h-4" />
                        {{ __('Show Recommendations') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <p class="text-xs text-gray-400 text-center mt-4 inline-flex items-center gap-1.5 justify-center w-full">
        <x-svg-icon name="light-bulb" class="w-4 h-4" />
        {{ __('Your answers are only used for matching. Registered users can see their history in their profile.') }}
    </p>

    <script>
        function quizApp() {
            return {
                currentStep: 0,
                totalSteps: 8,
                answers: {
                    budget: '', city_size: '', region: '', field: '',
                    lang: '', uni_type: '', lifestyle: '', community: '',
                },
                init() {
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' && this.currentStep < this.totalSteps - 1 && this.canProceed()) {
                            e.preventDefault();
                            this.next();
                        }
                    });
                },
                canProceed() {
                    const keys = ['budget','city_size','region','field','lang','uni_type','lifestyle','community'];
                    return this.answers[keys[this.currentStep]] !== '';
                },
                next() { if (this.canProceed() && this.currentStep < this.totalSteps - 1) this.currentStep++; },
                prev() { if (this.currentStep > 0) this.currentStep--; },
            }
        }
    </script>

@else
    {{-- ================= SONUÇ EKRANI ================= --}}

    {{-- KİŞİLİK KARTI --}}
    <section class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 text-white rounded-2xl p-6 md:p-10 mb-6 shadow-xl text-center">
        <p class="text-xs font-bold uppercase tracking-widest text-indigo-200 mb-1">{{ __('Your type') }}</p>
        <h2 class="text-3xl md:text-5xl font-extrabold mb-3">{{ $result['personality']['title'] }}</h2>
        <p class="text-lg md:text-xl text-indigo-100 max-w-2xl mx-auto">{{ $result['personality']['description'] }}</p>

        <div class="mt-6 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur text-sm">
            <x-svg-icon name="academic-cap" class="w-4 h-4" />
            {{ __(':count university recommendations', ['count' => count($result['universities'])]) }}
            <span class="opacity-60">·</span>
            <x-svg-icon name="building-office" class="w-4 h-4" />
            {{ __('matched from :n pool', ['n' => $result['total_pool'] ?? 0]) }}
        </div>
    </section>

    {{-- SONUÇLAR --}}
    @if (empty($result['universities']))
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-8 text-center">
            <div class="flex justify-center mb-3 text-yellow-700"><x-svg-icon name="information-circle" class="w-12 h-12" /></div>
            <h3 class="text-xl font-bold text-yellow-900 mb-2">{{ __('No perfect match found') }}</h3>
            <p class="text-yellow-800 mb-4">{{ __('Your filters may be too narrow — especially the field + language + region combination.') }}</p>
            <a href="{{ route('tools.recommendation') }}"
               class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-semibold">
                ← {{ __('Try again (more flexible)') }}
            </a>
        </div>
    @else
        <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-6 inline-flex items-center gap-2">
            <x-svg-icon name="trophy" class="w-7 h-7 text-amber-500" />
            {{ __('Top :count universities for you', ['count' => count($result['universities'])]) }}
        </h2>

        <div class="space-y-4">
            @foreach ($result['universities'] as $i => $uni)
                <article class="bg-white border-2 {{ $i === 0 ? 'border-emerald-400 shadow-lg' : 'border-gray-200' }} rounded-2xl p-5 md:p-6 hover:border-indigo-400 hover:shadow-md transition">
                    <div class="flex flex-col md:flex-row md:items-start gap-5">
                        {{-- SCORE GAUGE — sabit yükseklik (rozet'le birlikte) --}}
                        <div class="flex-shrink-0 flex md:flex-col items-center md:items-center gap-3 md:w-28 md:min-h-[160px] md:justify-start">
                            <div class="relative w-24 h-24 md:w-28 md:h-28 flex-shrink-0">
                                <svg viewBox="0 0 100 100" class="w-full h-full transform -rotate-90">
                                    <circle cx="50" cy="50" r="42" stroke="#e5e7eb" stroke-width="8" fill="none"/>
                                    <circle cx="50" cy="50" r="42"
                                            stroke="url(#g{{ $uni['id'] }})" stroke-width="8" fill="none"
                                            stroke-linecap="round"
                                            stroke-dasharray="{{ 263.89 }}"
                                            stroke-dashoffset="{{ 263.89 * (1 - $uni['score']/100) }}"/>
                                    <defs>
                                        <linearGradient id="g{{ $uni['id'] }}" x1="0" y1="0" x2="1" y2="1">
                                            <stop offset="0%" stop-color="{{ $uni['score'] >= 80 ? '#10b981' : ($uni['score'] >= 60 ? '#6366f1' : '#f59e0b') }}"/>
                                            <stop offset="100%" stop-color="{{ $uni['score'] >= 80 ? '#14b8a6' : ($uni['score'] >= 60 ? '#a855f7' : '#f97316') }}"/>
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-2xl md:text-3xl font-extrabold {{ $uni['score'] >= 80 ? 'text-emerald-600' : ($uni['score'] >= 60 ? 'text-indigo-600' : 'text-amber-600') }}">%{{ $uni['score'] }}</span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500">{{ __('match') }}</span>
                                </div>
                            </div>
                            {{-- Rozet alanı: ilk kart için doldur, diğerlerinde aynı yükseklikte boş tut --}}
                            <div class="md:h-6 flex items-center">
                                @if ($i === 0)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded bg-emerald-100 text-emerald-700 whitespace-nowrap">
                                        <x-svg-icon name="star" class="w-3 h-3" />
                                        {{ __('Best Match') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- DETAY --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start gap-3 mb-3">
                                @if ($uni['logo_url'])
                                    <img src="{{ $uni['logo_url'] }}" alt="" class="w-12 h-12 object-contain flex-shrink-0">
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 text-white font-bold rounded flex items-center justify-center text-lg flex-shrink-0">
                                        {{ mb_substr($uni['name_de'], 0, 1) }}
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg md:text-xl font-bold text-gray-900 leading-tight">{{ $uni['name_de'] }}</h3>
                                    <p class="text-sm text-gray-500 mt-0.5 inline-flex flex-wrap items-center gap-1">
                                        <x-svg-icon name="map-pin" class="w-3.5 h-3.5" />
                                        {{ $uni['city']?->name }}
                                        @if ($uni['type'])
                                            <span>·</span>
                                            {{ $uni['type'] === 'public' ? __('Public') : __('Private') }}
                                        @endif
                                        @if ($uni['student_count'])
                                            <span>·</span>
                                            <x-svg-icon name="users" class="w-3.5 h-3.5" />
                                            {{ number_format($uni['student_count'], 0, ',', '.') }} {{ __('students') }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- NEDEN EŞLEŞTİ --}}
                            @if (! empty($uni['reasons']))
                                <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">{{ __('Why is it for you?') }}</p>
                                    <ul class="space-y-1.5 text-sm text-gray-700">
                                        @foreach ($uni['reasons'] as $r)
                                            <li class="flex items-start gap-2">
                                                <span>{{ $r['text'] }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('universities.show', $uni['slug']) }}"
                                   class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                                    {{ __('Details & Programs') }} →
                                </a>
                                @if ($uni['city']?->slug)
                                    <a href="{{ route('cities.show', $uni['city']->slug) }}"
                                       class="inline-flex items-center gap-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg transition">
                                        <x-svg-icon name="building-office" class="w-4 h-4" />
                                        {{ $uni['city']?->name }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- CTA --}}
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-3">
            <a href="{{ route('tools.recommendation') }}"
               class="inline-flex items-center justify-center gap-1.5 bg-white border-2 border-indigo-200 hover:border-indigo-400 text-indigo-700 font-semibold py-3 rounded-lg transition">
                <x-svg-icon name="arrow-path" class="w-4 h-4" />
                {{ __('Try again') }}
            </a>
            <a href="{{ route('compare.index') }}?slugs={{ collect($result['universities'])->pluck('slug')->take(3)->join(',') }}"
               class="inline-flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-lg transition shadow-md">
                <x-svg-icon name="scale" class="w-4 h-4" />
                {{ __('Compare top 3') }}
            </a>
            <button type="button"
                    onclick="navigator.share ? navigator.share({title:'{{ __('My AlmanyaUni Quiz Result') }}',text:'{{ $result['personality']['title'] }} — {{ __(':n uni recommendations received.', ['n' => count($result['universities'])]) }}',url:location.href}) : (navigator.clipboard.writeText(location.href), alert('{{ __('Link copied') }}'))"
                    class="inline-flex items-center justify-center gap-1.5 bg-white border-2 border-amber-200 hover:border-amber-400 text-amber-700 font-semibold py-3 rounded-lg transition">
                <x-svg-icon name="link" class="w-4 h-4" />
                {{ __('Share result') }}
            </button>
        </div>
    @endif
@endif

</div>

{{-- Auto-FAQ (AIO + Featured Snippet) --}}
<x-faq-section
    :title="__('Frequently Asked Questions about the University Match Quiz')"
    :subtitle="__('How the recommendation engine scores 600+ universities for your profile')"
    :faqs="\App\Support\PageFaq::forRecommendation()"
/>
@endsection
