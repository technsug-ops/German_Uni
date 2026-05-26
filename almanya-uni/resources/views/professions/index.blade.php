@extends('layouts.app')

@section('title', __('Professions — What Jobs Can You Do in Germany?') . ' — ' . brand('name'))

<x-seo
    :title="__('Professions — What Jobs Can You Do in Germany?')"
    :description="__('Definition, education path and related university programs for :total professions in Germany. Bundesagentur für Arbeit data.', ['total' => number_format($totals['all'], 0, ',', '.')])"
/>

@php
    $typeLabels = [
        'ausbildung'    => ['Ausbildung', '🛠️', 'green'],
        'studienberuf'  => ['Studienberuf', '🎓', 'blue'],
        'weiterbildung' => ['Weiterbildung', '📈', 'purple'],
        'grundberuf'    => ['Grundberuf', '🧱', 'amber'],
        'other'         => [__('Other'), '📚', 'gray'],
    ];
    $hasFilter = (bool) ($filters['q'] || $filters['type'] || $filters['field']);
@endphp

@section('content')
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-accent-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Professions') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold mb-3 leading-tight">{{ __('Professions — Working Life in Germany') }}</h1>
        <p class="text-lg md:text-xl text-primary-100 max-w-3xl mb-6">
            {{ __('Definition, education path and tasks of :total professions in Germany — from Bundesagentur für Arbeit data.', ['total' => number_format($totals['all'], 0, ',', '.')]) }}
        </p>

        <form method="GET" action="{{ route('professions.index') }}" class="bg-white rounded-xl shadow-2xl p-3 text-gray-900">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 mb-2">
                <div class="md:col-span-7 flex items-center px-3 border border-gray-300 rounded-lg">
                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 0 0-15 7.5 7.5 0 0 0 0 15Z"/>
                    </svg>
                    <input type="text" name="q" value="{{ $filters['q'] }}"
                           placeholder="{{ __('Search Beruf, Tätigkeit, KldB code...') }}"
                           class="flex-1 px-3 py-2.5 placeholder-gray-400 focus:outline-none bg-transparent">
                </div>
                <div class="md:col-span-3">
                    <select name="type" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 bg-white">
                        <option value="">{{ __('All types') }}</option>
                        @foreach ($typeLabels as $k => [$label, $emoji, $color])
                            <option value="{{ $k }}" @selected($filters['type'] === $k)>{{ $emoji }} {{ $label }} ({{ $totals[$k] ?? 0 }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="w-full h-full bg-accent-500 hover:bg-accent-600 text-white font-bold px-4 py-2.5 rounded-lg transition">
                        {{ __('Search') }}
                    </button>
                </div>
            </div>

            {{-- Alan (field) filtresi --}}
            <div class="border-t border-gray-100 pt-2 px-1">
                <div class="flex items-center flex-wrap gap-1.5">
                    <span class="text-xs text-gray-500 mr-1">{{ __('Field:') }}</span>
                    <a href="{{ route('professions.index', array_filter(['q' => $filters['q'], 'type' => $filters['type']])) }}"
                       class="text-xs px-3 py-1 rounded-full border transition
                              {{ ! $filters['field'] ? 'bg-primary-600 text-white border-primary-600' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100' }}">
                        {{ __('All') }}
                    </a>
                    @foreach ($fields as $f)
                        <a href="{{ route('professions.index', array_filter(['q' => $filters['q'], 'type' => $filters['type'], 'field' => $f->slug])) }}"
                           class="inline-flex items-center gap-1 text-xs px-3 py-1 rounded-full border transition
                                  {{ $filters['field'] === $f->slug ? 'bg-primary-600 text-white border-primary-600' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                            <span>{{ $f->icon }}</span>
                            <span>{{ $f->name }}</span>
                            <span class="opacity-60">({{ $f->professions_count }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </form>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
        <p class="text-sm text-gray-700">
            <strong>{{ number_format($professions->total(), 0, ',', '.') }}</strong> {{ __('professions') }}
            @if ($hasFilter)
                <a href="{{ route('professions.index') }}" class="ml-3 text-accent-600 hover:text-accent-800">↻ {{ __('Clear') }}</a>
            @endif
        </p>
        <p class="text-sm text-gray-500">{{ __('Page :current / :total', ['current' => $professions->currentPage(), 'total' => max(1, $professions->lastPage())]) }}</p>
    </div>

    @if ($professions->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
            <p class="text-yellow-900 font-semibold mb-2">{{ __('No profession found.') }}</p>
            <a href="{{ route('professions.index') }}" class="inline-block bg-primary-600 hover:bg-primary-700 text-white px-5 py-2 rounded font-semibold">
                {{ __('All Professions') }}
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($professions as $p)
                @php
                    [$tLabel, $tEmoji, $tColor] = $typeLabels[$p->type ?? 'other'];
                    // description_de en güvenilir prose; ama "Aufgaben und TätigkeitenTechniker..." gibi başlık-değer bitişikliği var.
                    if ($p->description_de) {
                        $raw = $p->description_de;
                        // Bilinen başlıkları temizle: "X Bla" veya "XBla" → "Bla"
                        $raw = preg_replace('/^(Studienfach|Aufgaben und Tätigkeiten|Aufgaben und Tatigkeiten|Beruf|Berufsbezeichnung)\s*/u', '', $raw);
                        // Türkçe varsa onu tercih et (description_tr boşsa name_tr fallback olabilir ileride)
                        $body = \Illuminate\Support\Str::limit(trim($raw), 180);
                    } elseif ($p->clean_steckbrief) {
                        $body = \Illuminate\Support\Str::limit($p->clean_steckbrief, 180);
                    } else {
                        $body = null;
                    }
                @endphp
                <a href="{{ route('professions.show', $p->slug) }}"
                   class="group flex flex-col bg-white border border-gray-200 hover:border-primary-400 hover:shadow-md transition rounded-xl p-5 min-h-[200px] overflow-hidden">
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full max-w-full
                            @switch($tColor)
                                @case('green')  bg-green-100 text-green-700 @break
                                @case('blue')   bg-blue-100 text-blue-700 @break
                                @case('purple') bg-purple-100 text-purple-700 @break
                                @case('amber')  bg-amber-100 text-amber-800 @break
                                @default        bg-gray-100 text-gray-700
                            @endswitch
                        ">
                            <span>{{ $tEmoji }}</span>
                            <span class="truncate">{{ $tLabel }}</span>
                        </span>
                        @if ($p->kldb_code)
                            <span class="text-[10px] text-gray-400 font-mono">KldB {{ $p->kldb_code }}</span>
                        @endif
                    </div>
                    <h3 class="font-bold text-gray-900 leading-snug group-hover:text-primary-700 transition mb-2 break-words">
                        {{ \Illuminate\Support\Str::limit($p->name_de, 70) }}
                    </h3>

                    @if ($body)
                        <p class="text-sm text-gray-700 line-clamp-3 flex-1">{{ $body }}</p>
                    @else
                        <div class="flex-1 flex items-center justify-center text-center py-3">
                            <p class="text-xs text-gray-400 italic">
                                @if ($p->field)
                                    {{ __(':field area', ['field' => $p->field->name]) }}
                                @else
                                    {{ __('Click for details') }}
                                @endif
                            </p>
                        </div>
                    @endif

                    <span class="mt-3 inline-flex items-center gap-1 text-xs text-primary-600 group-hover:text-primary-800 font-semibold">
                        {{ __('See details →') }}
                    </span>
                </a>
            @endforeach
        </div>

        <div class="mt-8">{{ $professions->links() }}</div>
    @endif
</div>
@endsection
