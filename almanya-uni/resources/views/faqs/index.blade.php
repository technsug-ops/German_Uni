@extends('layouts.app')

@section('title', __('Frequently Asked Questions about Studying in Germany') . ' — ' . brand('name'))

<x-seo
    :title="__('Study in Germany FAQ')"
    :description="__('Visa, language exams, Uni-Assist, Studienkolleg, blocked account and more — answers to the most asked questions about studying in Germany.')"
/>

@section('content')
<div class="bg-gradient-to-r from-primary-500 to-primary-700 text-white py-12">
    <div class="max-w-[1400px] mx-auto px-4">
        <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">{{ __('Frequently Asked Questions') }}</h1>
        <p class="text-lg text-primary-100 max-w-3xl mb-6">
            {!! __('For international students who want to study in Germany: <strong>:q questions</strong>, <strong>:t topics</strong>. Distilled from 1.5M+ community messages.', ['q' => $total_questions, 't' => count($topics)]) !!}
        </p>

        {{-- Arama --}}
        <form method="GET" action="{{ route('faqs.index') }}" class="max-w-2xl">
            <div class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ $q }}"
                       placeholder="{{ __('Sperrkonto, Anmeldung, visa, language test…') }}"
                       class="w-full pl-12 pr-4 py-3 rounded-full text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-white/30 shadow-lg">
            </div>
        </form>
    </div>
</div>

<div class="max-w-[1400px] mx-auto px-4 py-10">

    {{-- Arama sonuçları --}}
    @if ($q !== '' && $searchResults !== null)
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-4">
                {{ __(':n result(s) for ":q"', ['n' => $searchResults->count(), 'q' => $q]) }}
                @if (request()->has('q'))
                    <a href="{{ route('faqs.index') }}" class="ml-3 text-sm text-accent-600 hover:text-accent-800 font-normal">↻ {{ __('Clear search') }}</a>
                @endif
            </h2>
            @if ($searchResults->isEmpty())
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <p class="text-yellow-800">{{ __('No question matches this search. Try a different keyword or browse the topics below.') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($searchResults as $r)
                        <a href="{{ route('faqs.show', [$r->topic->slug, $r->slug]) }}"
                           class="block bg-white border border-gray-200 hover:border-primary-500 hover:shadow-md transition rounded-lg p-4">
                            <div class="flex items-baseline justify-between mb-1.5">
                                <span class="inline-block text-xs font-semibold uppercase tracking-wide"
                                      style="color: {{ $r->topic->color ?? '#1E40AF' }}">
                                    {{ $r->topic->name }}
                                </span>
                                @if (! $r->has_answer)
                                    <span class="text-xs text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded">{{ __('No answer') }}</span>
                                @endif
                            </div>
                            <p class="font-semibold text-gray-900 leading-snug">{{ $r->question }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    {{-- 2-column: Popüler + Recent --}}
    @if ($q === '')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12">
            @if (! empty($popular) && $popular->isNotEmpty())
                <section class="bg-white border border-gray-200 rounded-xl p-5">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">{{ __('Most popular questions') }}</h2>
                    <ol class="space-y-2.5">
                        @foreach ($popular as $i => $f)
                            <li>
                                <a href="{{ route('faqs.show', [$f->topic->slug, $f->slug]) }}"
                                   class="flex items-start gap-2 group hover:bg-gray-50 -mx-2 px-2 py-1 rounded transition">
                                    <span class="text-xs font-bold text-primary-600 shrink-0 w-5">{{ $i + 1 }}.</span>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 group-hover:text-primary-600 leading-snug">{{ $f->question }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            <span style="color: {{ $f->topic->color ?? '#1E40AF' }}">{{ $f->topic->name }}</span>
                                            · {{ __(':n views', ['n' => number_format($f->view_count)]) }}
                                        </p>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif

            @if (! empty($recent) && $recent->isNotEmpty())
                <section class="bg-white border border-gray-200 rounded-xl p-5">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">🆕 {{ __('Recently updated') }}</h2>
                    <ol class="space-y-2.5">
                        @foreach ($recent as $f)
                            <li>
                                <a href="{{ route('faqs.show', [$f->topic->slug, $f->slug]) }}"
                                   class="flex items-start gap-2 group hover:bg-gray-50 -mx-2 px-2 py-1 rounded transition">
                                    <span class="text-xs text-gray-400 shrink-0 w-12">{{ $f->updated_at->diffForHumans() }}</span>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 group-hover:text-primary-600 leading-snug">{{ $f->question }}</p>
                                        <p class="text-xs mt-0.5" style="color: {{ $f->topic->color ?? '#1E40AF' }}">{{ $f->topic->name }}</p>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif
        </div>
    @endif

    @if ($featured->isNotEmpty() && $q === '')
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-4">{{ __('Featured Questions') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach ($featured as $f)
                    <a href="{{ route('faqs.show', [$f->topic->slug, $f->slug]) }}"
                       class="block bg-white border border-gray-200 hover:border-primary-500 hover:shadow-md transition rounded-lg p-4">
                        <span class="inline-block text-xs font-semibold uppercase tracking-wide mb-2"
                              style="color: {{ $f->topic->color ?? '#1E40AF' }}">
                            {{ $f->topic->name }}
                        </span>
                        <p class="font-semibold text-gray-900 leading-snug">{{ $f->question }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <h2 class="text-2xl font-bold mb-4">{{ __('Questions by Topic') }}</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($topics as $topic)
            <a href="{{ route('faqs.topic', $topic->slug) }}"
               class="block bg-white border border-gray-200 hover:border-primary-500 hover:shadow-md transition rounded-lg p-5">
                <div class="flex items-start gap-3 mb-3">
                    @php $tColor = $topic->color ?? '#1E40AF'; @endphp
                    <span class="inline-flex items-center justify-center w-11 h-11 rounded-xl shrink-0"
                          style="background-color: {{ $tColor }}14; color: {{ $tColor }};">
                        {!! e_icon($topic->icon, 'w-6 h-6') !!}
                    </span>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-lg leading-tight"
                            style="color: {{ $tColor }}">
                            {{ $topic->name }}
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ __(':n questions', ['n' => $topic->faqs_count]) }}
                            @if ($topic->answered_count > 0)
                                · <span class="text-green-600 font-semibold">{{ __(':n answered', ['n' => $topic->answered_count]) }}</span>
                            @endif
                        </p>
                    </div>
                </div>
                @if ($topic->description)
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $topic->description }}</p>
                @endif
                <p class="text-primary-600 font-semibold text-sm mt-3">{{ __('See questions') }} →</p>
            </a>
        @endforeach
    </div>
</div>
@endsection
