@extends('layouts.app')

@php
    $levels = $kind === 'language_course' ? ($item->levels ?? []) : [];
    $languages = $kind === 'translation_office' ? ($item->languages ?? []) : [];
    $features = $item->features ?? [];
    $bannerUrl = $item->image_path ? asset('storage/' . $item->image_path) : null;
@endphp

@section('title', $item->name . ' — ' . $indexTitle . ' — ' . brand('name'))

<x-seo :title="$item->name . ' — ' . $indexTitle"
       :description="\Illuminate\Support\Str::limit(strip_tags((string) $item->description) ?: $item->name, 155)" />

@section('content')

<div class="max-w-[1100px] mx-auto px-4 py-8">
    <nav class="text-sm text-gray-500 mb-4">
        <a href="/" class="hover:text-indigo-600">{{ __('Home') }}</a>
        <span class="mx-2 opacity-60">›</span>
        <a href="{{ route($indexRoute) }}" class="hover:text-indigo-600">{{ $indexTitle }}</a>
        <span class="mx-2 opacity-60">›</span>
        <span class="text-gray-700">{{ $item->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- ANA --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                @if ($bannerUrl)
                    <img src="{{ $bannerUrl }}" alt="{{ $item->name }}" class="w-full h-48 object-cover">
                @endif
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        @if ($item->logo_url)
                            <img src="{{ $item->logo_url }}" alt="{{ $item->name }}" class="w-16 h-16 rounded object-contain bg-gray-50 p-1 border">
                        @else
                            <div class="w-16 h-16 rounded bg-gradient-to-br from-indigo-500 to-violet-600 text-white flex items-center justify-center text-3xl">{{ $item->type_emoji }}</div>
                        @endif
                        <div>
                            <h1 class="text-2xl font-extrabold text-gray-900 leading-tight">{{ $item->name }}</h1>
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <span class="text-xs px-2 py-0.5 rounded bg-indigo-100 text-indigo-700">{{ $item->type_label }}</span>
                                @if ($kind === 'translation_office' && $item->is_sworn)
                                    <span class="text-xs px-2 py-0.5 rounded bg-emerald-100 text-emerald-700">{{ __('Sworn') }}</span>
                                @endif
                                @if ($item->is_featured)
                                    <span class="text-xs px-2 py-0.5 rounded bg-amber-100 text-amber-700">★ {{ __('Featured') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($item->description)
                        <div class="prose prose-sm max-w-none mt-5 text-gray-700">{!! nl2br(e($item->description)) !!}</div>
                    @endif

                    @if (! empty($levels))
                        <div class="mt-5">
                            <div class="text-xs font-semibold text-gray-500 uppercase mb-1">{{ __('Levels') }}</div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($levels as $lv)
                                    <span class="text-sm font-semibold px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-100">{{ $lv }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if (! empty($languages))
                        <div class="mt-5">
                            <div class="text-xs font-semibold text-gray-500 uppercase mb-1">{{ __('Language pairs') }}</div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($languages as $lg)
                                    <span class="text-sm font-semibold px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-100">{{ $lg }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if (! empty($features))
                        <div class="mt-5">
                            <div class="text-xs font-semibold text-gray-500 uppercase mb-1">{{ __('Services') }}</div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($features as $f)
                                    <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-700">{{ $f }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if (! empty($item->cities) && is_array($item->cities))
                        <div class="mt-5">
                            <div class="text-xs font-semibold text-gray-500 uppercase mb-1">{{ __('Cities') }}</div>
                            <div class="text-sm text-gray-700">{{ implode(' · ', $item->cities) }}</div>
                        </div>
                    @endif
                </div>
            </div>

            @if ($related->isNotEmpty())
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-3">{{ __('Similar providers') }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($related as $r)
                            @include('partners._card', ['item' => $r, 'kind' => $kind, 'showRoute' => $showRoute])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- YAN: CTA + lead form --}}
        <aside class="space-y-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-5 lg:sticky lg:top-24">
                @if ($item->outbound_url)
                    <a href="{{ route('partner.click', ['kind' => $kind, 'id' => $item->id]) }}" target="_blank" rel="nofollow noopener sponsored"
                       class="block text-center bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg px-4 py-2.5 mb-3">
                        {{ __('Visit website') }} ↗
                    </a>
                @endif
                @if ($item->phone)
                    <a href="tel:{{ $item->phone }}" class="block text-center border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg px-4 py-2 mb-3 text-sm">📞 {{ $item->phone }}</a>
                @endif

                @include('partners._lead_form', ['kind' => $kind, 'item' => $item])
            </div>
        </aside>
    </div>
</div>

@endsection
