@extends('layouts.app')

@section('title', __('Advisory Board') . ' — ' . brand('name'))

<x-seo
    :title="__('Advisory Board') . ' — ' . brand('name')"
    :description="__('Meet the advisors supporting :brand on its mission to make studying in Germany transparent for international students.', ['brand' => brand('name')])"
/>

@section('content')
<section class="bg-gradient-to-br from-primary-800 via-primary-700 to-primary-900 text-white">
    <div class="max-w-[900px] mx-auto px-4 py-12 md:py-16 text-center">
        <nav class="text-sm text-primary-200 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ __('Advisory Board') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-3">{{ __('Advisory Board') }}</h1>
        <p class="text-lg text-primary-50 max-w-2xl mx-auto">{{ __('A group of experienced advisors supports our mission to make studying in Germany transparent and accessible for international students.') }}</p>
    </div>
</section>

<div class="max-w-[1000px] mx-auto px-4 py-12">
    @if ($advisors->isEmpty())
        <p class="text-center text-gray-500">{{ __('Advisory board members will be announced soon.') }}</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach ($advisors as $a)
                @php $link = $a->linkedin_url ?: $a->profile_url; @endphp
                <div class="flex items-start gap-4 bg-white ring-1 ring-gray-200 rounded-2xl p-5 shadow-sm">
                    @if ($a->photo_url)
                        <img src="{{ \Illuminate\Support\Str::startsWith($a->photo_url, 'http') ? $a->photo_url : asset('storage/' . $a->photo_url) }}"
                             alt="{{ $a->name }}" loading="lazy"
                             class="w-20 h-20 rounded-full object-cover ring-2 ring-gray-100 shrink-0">
                    @else
                        <span class="w-20 h-20 rounded-full bg-primary-100 text-primary-700 text-2xl font-bold flex items-center justify-center shrink-0">
                            {{ mb_strtoupper(mb_substr($a->name, 0, 1)) }}
                        </span>
                    @endif
                    <div class="min-w-0">
                        <h2 class="text-lg font-bold text-gray-900">{{ $a->name }}</h2>
                        @if ($a->role_title)
                            <p class="text-sm text-primary-700 font-medium">{{ $a->role_title }}</p>
                        @endif
                        @if ($a->affiliation)
                            <p class="text-sm text-gray-500">{{ $a->affiliation }}</p>
                        @endif
                        @if ($a->bio)
                            <p class="text-sm text-gray-700 mt-2 leading-relaxed">{{ $a->bio }}</p>
                        @endif
                        @if ($link)
                            <a href="{{ $link }}" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-1 text-sm text-primary-600 font-semibold mt-2 hover:underline">
                                {{ __('Profile') }} ↗
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
