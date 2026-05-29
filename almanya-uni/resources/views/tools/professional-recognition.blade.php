@extends('layouts.app')

@section('title', __('Professional Recognition — Anerkennung in Germany') . ' — ' . brand('name'))

<x-seo
    :title="__('Professional Recognition — Anerkennung in Germany')"
    :description="__('Is your profession regulated in Germany? Find out which authority handles your recognition, the expected timeline and cost. 6 popular professions covered.')"
/>

<x-tool-schema tool="professional-recognition" />

@section('content')

<section class="bg-gradient-to-br from-amber-700 via-orange-600 to-rose-600 text-white">
    <div class="max-w-[1100px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-amber-100 mb-3">
            <a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('tools.index') }}" class="hover:text-white">{{ __('Tools') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Professional Recognition') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-3">
            🛡️ {{ __('Is your profession recognised in Germany?') }}
        </h1>
        <p class="text-lg md:text-xl text-amber-100 max-w-3xl">
            {{ __('Check whether your job is regulated (Anerkennung required) or free, which authority handles it, expected timeline and cost. 6 most common professions, real data.') }}
        </p>
    </div>
</section>

<div class="max-w-[1100px] mx-auto px-4 py-10">

    {{-- FORM --}}
    <form method="POST" action="{{ route('tools.professional-recognition') }}" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 md:p-8 mb-10">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Your profession') }}</label>
                <select name="profession_key" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    <option value="">{{ __('Select profession...') }}</option>
                    @foreach ($professions as $key => $p)
                        <option value="{{ $key }}" @selected(($old['profession_key'] ?? '') === $key)>
                            {{ $p['icon'] }} {{ __($p['name_en']) }}
                            {{ $p['regulated'] ? ' · ' . __('Regulated') : ' · ' . __('Free') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Years of experience') }}</label>
                <input type="number" name="work_experience_years" min="0" max="50" value="{{ $old['work_experience_years'] ?? 0 }}"
                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            </div>
        </div>

        <input type="hidden" name="country_origin" value="TR">

        <button type="submit"
                class="mt-5 w-full md:w-auto bg-amber-600 hover:bg-amber-700 text-white font-bold px-8 py-3 rounded-lg shadow-md transition">
            🔍 {{ __('Check recognition') }}
        </button>
    </form>

    {{-- RESULT --}}
    @if ($result)
        @php $p = $result['profession']; @endphp
        <section class="space-y-6">
            <div class="bg-white border-2 border-amber-300 rounded-2xl p-6 md:p-8 shadow-md">
                <div class="flex items-start gap-4 flex-wrap mb-4">
                    <div class="text-5xl">{{ $p['icon'] }}</div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ __($p['name_en']) }}</h2>
                        @if ($p['regulated'])
                            <span class="inline-flex items-center gap-1 mt-2 px-3 py-1 rounded-full bg-rose-100 text-rose-800 text-sm font-bold">🛡️ {{ __('Regulated profession — Anerkennung required') }}</span>
                        @else
                            <span class="inline-flex items-center gap-1 mt-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-sm font-bold">✅ {{ __('Free profession — no Anerkennung needed') }}</span>
                        @endif
                    </div>
                </div>

                {{-- Quick stats --}}
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-6">
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 text-center">
                        <div class="text-xs uppercase tracking-wider text-gray-500 mb-1">{{ __('Timeline') }}</div>
                        <div class="font-extrabold text-gray-900">{{ $p['estimated_months'] }} {{ __('months') }}</div>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 text-center">
                        <div class="text-xs uppercase tracking-wider text-gray-500 mb-1">{{ __('Cost') }}</div>
                        <div class="font-extrabold text-gray-900">{{ $p['estimated_cost_eur'] }}</div>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 text-center col-span-2 md:col-span-1">
                        <div class="text-xs uppercase tracking-wider text-gray-500 mb-1">{{ __('Language') }}</div>
                        <div class="font-extrabold text-gray-900 text-sm">{{ $p['language_required'] }}</div>
                    </div>
                </div>

                {{-- Authority --}}
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <div class="text-2xl shrink-0">🏛️</div>
                        <div>
                            <p class="text-xs font-bold text-amber-900 uppercase tracking-wider mb-1">{{ __('Responsible authority') }}</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $p['authority'] }}</p>
                            <a href="{{ $p['authority_url'] }}" target="_blank" rel="noopener noreferrer nofollow"
                               class="inline-flex items-center gap-1 text-xs text-amber-700 hover:text-amber-900 underline mt-1">
                                {{ $p['authority_url'] }} ↗
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Process steps --}}
                <h3 class="text-lg font-bold text-gray-900 mb-3">📋 {{ __('Process steps') }}</h3>
                <ol class="space-y-2 mb-6">
                    @foreach ($p['process_steps'] as $i => $step)
                        <li class="flex gap-3 items-start">
                            <span class="shrink-0 w-7 h-7 rounded-full bg-amber-100 text-amber-800 font-extrabold text-sm flex items-center justify-center">{{ $i + 1 }}</span>
                            <span class="text-sm text-gray-700 leading-relaxed">{{ $step }}</span>
                        </li>
                    @endforeach
                </ol>

                {{-- Pitfalls --}}
                @if (! empty($p['pitfalls']))
                    <h3 class="text-lg font-bold text-gray-900 mb-3">⚠️ {{ __('Common pitfalls') }}</h3>
                    <ul class="space-y-2 mb-6">
                        @foreach ($p['pitfalls'] as $pitfall)
                            <li class="flex gap-2 items-start text-sm text-gray-700">
                                <span class="text-rose-500 mt-0.5">•</span>
                                <span class="leading-relaxed">{{ $pitfall }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- Personal notes --}}
                @if (! empty($result['notes']))
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 space-y-2">
                        <p class="font-bold text-gray-900 text-sm mb-1">💡 {{ __('Personal notes based on your input') }}</p>
                        @foreach ($result['notes'] as $note)
                            <p class="text-sm text-gray-700">{{ $note }}</p>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- CTA --}}
            <div class="bg-gradient-to-r from-amber-100 to-orange-100 border-2 border-amber-300 rounded-xl p-6 text-center">
                <h3 class="text-xl font-bold text-gray-900 mb-2">📖 {{ __('Official authority resource') }}</h3>
                <p class="text-sm text-gray-700 mb-4">{{ __('For exact paperwork checklist + your state\'s specific authority, the official Anerkennung portal is the canonical source.') }}</p>
                <a href="{{ $result['next_step_url'] }}" target="_blank" rel="noopener noreferrer nofollow"
                   class="inline-flex items-center gap-2 bg-amber-700 hover:bg-amber-800 text-white font-bold px-6 py-3 rounded-lg shadow-md transition">
                    {{ __('Open anerkennung-in-deutschland.de') }} ↗
                </a>
            </div>
        </section>
    @endif

    {{-- All professions index (always visible) --}}
    <section class="mt-14">
        <h2 class="text-2xl font-bold text-gray-900 mb-5">📚 {{ __('All covered professions') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach ($professions as $key => $p)
                <article class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition">
                    <div class="flex items-start gap-3">
                        <div class="text-3xl shrink-0">{{ $p['icon'] }}</div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900">{{ __($p['name_en']) }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                @if ($p['regulated'])
                                    🛡️ {{ __('Regulated') }} · {{ $p['estimated_months'] }} {{ __('months') }} · {{ $p['estimated_cost_eur'] }}
                                @else
                                    ✅ {{ __('Free') }} · {{ __('No Anerkennung needed') }}
                                @endif
                            </p>
                            <p class="text-xs text-gray-600 mt-1">{{ \Illuminate\Support\Str::limit($p['authority'], 60) }}</p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        <p class="text-xs text-gray-500 mt-4 italic">
            ℹ️ {{ __('Data sources: anerkennung-in-deutschland.de · BERUFENET · Bundesärztekammer · IHK FOSA. Verify on the official authority page before formal application.') }}
        </p>
    </section>

</div>

@endsection
