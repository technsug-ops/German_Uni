@extends('layouts.app')

@section('title', $provider->name . ' — ' . __('Student Health Insurance') . ' — ' . brand('name'))

<x-seo
    :title="$provider->name . ' — ' . __('Student Health Insurance (Germany)')"
    :description="$provider->description ?: __(':name health insurance for students in Germany — price, coverage, visa and enrolment validity.', ['name' => $provider->name])"
/>

@section('content')
<div class="max-w-[1000px] mx-auto px-4 py-10">
    <nav class="text-sm text-gray-500 mb-5">
        <a href="{{ route('tools.index') }}" class="hover:text-primary-600">{{ __('Tools') }}</a>
        <span class="mx-2 opacity-50">›</span>
        <a href="{{ route('tools.health-insurance') }}" class="hover:text-primary-600">{{ __('Health Insurance') }}</a>
        <span class="mx-2 opacity-50">›</span>
        <span class="text-gray-800">{{ $provider->name }}</span>
    </nav>

    <article class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
        {{-- Header --}}
        <div class="p-6 md:p-8 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-5">
            @if ($provider->logo_url)
                <img src="{{ $provider->logo_url }}" alt="{{ $provider->name }} logo" class="h-16 object-contain" loading="lazy">
            @else
                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white text-2xl font-extrabold shrink-0">
                    {{ mb_substr($provider->name, 0, 2) }}
                </div>
            @endif
            <div class="flex-1">
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ $provider->name }}</h1>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="inline-block px-2 py-0.5 text-xs font-medium rounded
                                 @switch($provider->type)
                                     @case('public') bg-emerald-50 text-emerald-700 @break
                                     @case('private') bg-indigo-50 text-indigo-700 @break
                                     @default bg-amber-50 text-amber-700
                                 @endswitch">
                        {{ __($provider->type_label) }}
                    </span>
                    @if ($provider->best_for_label)
                        <span class="text-sm text-gray-500">{{ __('Best for:') }} {{ $provider->best_for_label }}</span>
                    @endif
                </div>
            </div>
            <x-affiliate-link :provider="$provider" ctx="show"
                class="block text-center bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-5 rounded-lg transition shadow-sm">
                {{ __('Visit site') }} →
            </x-affiliate-link>
        </div>

        {{-- Key facts --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-gray-100">
            <div class="bg-white p-5">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ __('Monthly') }}</p>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ $provider->monthly_range }}</p>
            </div>
            <div class="bg-white p-5">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ __('Visa') }}</p>
                <p class="text-xl font-bold mt-1 {{ $provider->accepted_for_visa ? 'text-emerald-700' : 'text-gray-400' }}">
                    {{ $provider->accepted_for_visa ? __('Accepted') : '—' }}
                </p>
            </div>
            <div class="bg-white p-5">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ __('Enrolment') }}</p>
                <p class="text-xl font-bold mt-1 {{ $provider->accepted_for_enrollment ? 'text-emerald-700' : 'text-amber-600' }}">
                    {{ $provider->accepted_for_enrollment ? __('Valid') : __('No') }}
                </p>
            </div>
            <div class="bg-white p-5">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">{{ __('Age limit') }}</p>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ $provider->age_limit ? '≤ ' . $provider->age_limit : __('None') }}</p>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-6 md:p-8 space-y-6">
            @if ($provider->description)
                <p class="text-gray-700 leading-relaxed text-lg">{{ $provider->description }}</p>
            @endif

            @if ($provider->description_long)
                <div class="prose prose-sm max-w-none text-gray-700">{!! \Illuminate\Support\Str::markdown($provider->description_long) !!}</div>
            @endif

            {{-- Coverage --}}
            <div>
                <h2 class="text-lg font-bold text-gray-900 mb-3">{{ __('Coverage') }}</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ([
                        'covers_dental' => __('Dental'),
                        'covers_pregnancy' => __('Pregnancy'),
                        'covers_mental_health' => __('Mental health'),
                        'covers_repatriation' => __('Repatriation'),
                        'english_support' => __('English support'),
                        'digital_signup' => __('Online signup'),
                    ] as $field => $label)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-lg
                                     {{ $provider->$field ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-gray-50 text-gray-400 ring-1 ring-gray-100' }}">
                            <x-svg-icon name="{{ $provider->$field ? 'check' : 'x-mark' }}" class="w-4 h-4" />
                            {{ $label }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Pros / Cons (TR-only serbest metin) --}}
            @if (app()->getLocale() === 'tr' && (is_array($provider->pros) || is_array($provider->cons)))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @if (is_array($provider->pros) && count($provider->pros))
                        <div class="bg-emerald-50/50 border border-emerald-100 rounded-xl p-5">
                            <h3 class="font-bold text-emerald-900 mb-2">Avantajlar</h3>
                            <ul class="space-y-1.5 text-sm text-gray-700">
                                @foreach ($provider->pros as $pro)
                                    <li class="flex items-start gap-2"><span class="text-emerald-500 mt-0.5"><x-svg-icon name="check" class="w-4 h-4" /></span><span>{{ $pro }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (is_array($provider->cons) && count($provider->cons))
                        <div class="bg-amber-50/50 border border-amber-100 rounded-xl p-5">
                            <h3 class="font-bold text-amber-900 mb-2">Dezavantajlar</h3>
                            <ul class="space-y-1.5 text-sm text-gray-700">
                                @foreach ($provider->cons as $con)
                                    <li class="flex items-start gap-2"><span class="text-amber-500 mt-0.5">•</span><span>{{ $con }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            @if ($provider->last_verified_at)
                <p class="text-xs text-gray-400">{{ __('Last verified:') }} {{ $provider->last_verified_at->format('Y-m-d') }}</p>
            @endif
        </div>
    </article>

    {{-- Other providers --}}
    @if ($others->isNotEmpty())
        <section class="mt-10">
            <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Compare with other providers') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($others as $o)
                    <a href="{{ route('tools.health-insurance.show', $o->slug) }}"
                       class="block bg-white border border-gray-200 rounded-xl p-4 hover:border-primary-400 hover:shadow-sm transition">
                        <p class="font-bold text-gray-900">{{ $o->name }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">{{ __($o->type_label) }} · {{ $o->monthly_range }}/{{ __('mo') }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <div class="mt-8">
        <a href="{{ route('tools.health-insurance') }}" class="text-primary-600 font-semibold hover:underline">← {{ __('All health insurance options') }}</a>
    </div>
</div>
@endsection
