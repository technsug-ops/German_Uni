@extends('layouts.app')

@section('title', __('Turkish-German Grade Converter') . '  — ' . brand('name'))

<x-seo
    :title="__('Convert Turkish Grade to German Grade — Bavarian Formula')"
    :description="__('Convert your Turkish university grade (4-point or 100-point) to the German 1-5 system. Modifizierte bayerische Formel — the DAAD and uni-assist standard.')"
/>

<x-tool-schema tool="grade-converter" />

@section('content')
<div class="bg-gradient-to-r from-primary-500 to-primary-700 text-white py-10">
    <div class="max-w-[1400px] mx-auto px-4">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="{{ route('tools.index') }}" class="hover:text-white">{{ __('Tools') }}</a>
            <span class="mx-2">/</span>
            <span>{{ __('Grade Converter') }}</span>
        </nav>
        <h1 class="text-3xl md:text-4xl font-bold mb-3">📊 {{ __('Grade Converter (TR → DE)') }}</h1>
        <p class="text-primary-100 max-w-3xl">
            {!! __('Convert your Turkish university grade to the German system. The calculation uses the <strong>modifizierte bayerische Formel</strong> — the standard used by DAAD and uni-assist.') !!}
        </p>
    </div>
</div>

<div class="max-w-[1400px] mx-auto px-4 py-10">
    <form method="GET" action="{{ route('tools.grade-converter') }}" class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="system" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('System') }}</label>
                <select name="system" id="system" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-primary-500 focus:outline-none">
                    <option value="tr4"   @selected($system === 'tr4')>{{ __('Turkish 4-point (0-4)') }}</option>
                    <option value="tr100" @selected($system === 'tr100')>{{ __('Turkish 100-point (0-100)') }}</option>
                </select>
            </div>
            <div>
                <label for="grade" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Your Grade') }}</label>
                <input type="text" name="grade" id="grade"
                       value="{{ $grade }}"
                       placeholder="{{ $system === 'tr100' ? __('e.g. 85') : __('e.g. 3.20') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:border-primary-500 focus:outline-none">
            </div>
        </div>
        <div class="mt-5">
            <button type="submit" class="bg-accent-500 hover:bg-accent-600 text-white font-semibold px-6 py-2.5 rounded transition">
                {{ __('Convert') }}
            </button>
        </div>
    </form>

    @if ($result)
        @if (! ($result['valid'] ?? false))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-8 text-red-800">
                {{ $result['message'] ?? __('Invalid grade.') }}
            </div>
        @else
            <div class="bg-primary-50 border border-primary-200 rounded-lg p-6 mb-8">
                <p class="text-sm text-primary-700 mb-1">
                    {{ __('Your Turkish grade:') }} <strong>{{ $result['grade'] }}</strong>
                    ({{ $system === 'tr100' ? __('100-point') : __('4-point') }})
                </p>
                <p class="text-3xl font-bold text-primary-900 my-2">
                    {{ __('German equivalent:') }} {{ number_format($result['german'], 1, ',', '') }}
                </p>
                <p class="text-primary-800">{{ $result['german_text'] }}</p>
                @if ($result['note'])
                    <p class="text-sm text-orange-700 mt-3">⚠️ {{ $result['note'] }}</p>
                @endif
            </div>
        @endif
    @endif

    <section class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 px-6 py-3">
            <h2 class="font-bold text-gray-900">{{ __('Quick Table') }} — {{ $system === 'tr100' ? __('100-point system') : __('4-point system') }}</h2>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50 text-sm text-gray-600">
                <tr>
                    <th class="text-left px-6 py-2 font-semibold">{{ __('Turkish grade') }}</th>
                    <th class="text-left px-6 py-2 font-semibold">{{ __('German grade') }}</th>
                    <th class="text-left px-6 py-2 font-semibold">{{ __('Meaning') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($table as $row)
                    <tr class="border-t border-gray-100">
                        <td class="px-6 py-2 font-medium text-gray-900">{{ $row['tr'] }}</td>
                        <td class="px-6 py-2 font-semibold text-primary-700">{{ number_format($row['de'], 1, ',', '') }}</td>
                        <td class="px-6 py-2 text-gray-600 text-sm">{{ $row['text'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <div class="mt-8 bg-gray-50 border border-gray-200 rounded-lg p-6">
        <h3 class="font-bold text-gray-900 mb-2">Modifizierte bayerische Formel</h3>
        <pre class="bg-white border border-gray-200 rounded p-3 text-sm overflow-x-auto">N<sub>d</sub> = 1 + 3 × (N<sub>max</sub> − N<sub>x</sub>) / (N<sub>max</sub> − N<sub>min</sub>)</pre>
        <ul class="text-sm text-gray-700 mt-3 space-y-1">
            <li>{!! __('<strong>N<sub>max</sub></strong>: Highest grade obtainable in the Turkish system (4.0 or 100)') !!}</li>
            <li>{!! __('<strong>N<sub>min</sub></strong>: Passing grade (4-point: 2.0 / 100-point: 60)') !!}</li>
            <li>{!! __('<strong>N<sub>x</sub></strong>: Your grade') !!}</li>
            <li>{!! __('<strong>N<sub>d</sub></strong>: German equivalent (1.0 = sehr gut, 4.0 = ausreichend, 5.0 = fail)') !!}</li>
        </ul>
        <p class="text-xs text-gray-500 mt-3">
            {{ __('Note: This calculation is for general reference. The exact formula used by each university via Uni-Assist may vary slightly.') }}
        </p>
    </div>
</div>

{{-- Auto-FAQ (AIO + Featured Snippet) --}}
<x-faq-section
    :title="__('Frequently Asked Questions about Grade Conversion')"
    :subtitle="__('Modified Bavarian formula, university acceptance, and 4.0 → German scale')"
    :faqs="\App\Support\PageFaq::forGradeConverter()"
/>
@endsection
