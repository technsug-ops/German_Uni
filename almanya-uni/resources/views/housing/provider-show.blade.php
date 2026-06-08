@extends('layouts.app')

@section('title', $provider->name . ' — ' . $provider->type_label . ' — ' . brand('name'))

<x-seo
    :title="$provider->name . ' — ' . $provider->type_label"
    :description="\Illuminate\Support\Str::limit($provider->description ?? __('Germany student housing provider: :name', ['name' => $provider->name]), 160)"
/>

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br
    @if ($provider->type === 'studierendenwerk') from-emerald-700 via-emerald-600 to-teal-500
    @elseif ($provider->type === 'private_chain') from-indigo-700 via-indigo-600 to-purple-500
    @else from-amber-600 via-amber-500 to-orange-500
    @endif
    text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-white/80 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <a href="{{ route('housing.index') }}" class="hover:text-white">{{ __('Housing') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <a href="{{ route('housing.providers') }}" class="hover:text-white">{{ __('Providers') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ $provider->name }}</span>
        </nav>

        <div class="flex items-start gap-5">
            <div class="w-20 h-20 md:w-28 md:h-28 rounded-lg bg-white/15 backdrop-blur flex items-center justify-center shadow-xl text-white">
                @if ($provider->logo_url)
                    <img src="{{ $provider->logo_url }}" alt="" class="w-full h-full object-contain p-2">
                @else
                    {!! e_icon($provider->type_emoji, 'w-12 h-12 md:w-14 md:h-14') !!}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-white/20 mb-2">
                    {!! e_icon($provider->type_emoji, 'w-3.5 h-3.5') !!} {{ $provider->type_label }}
                </span>
                <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-1">{{ $provider->name }}</h1>
                @if ($provider->price_min)
                    <p class="text-lg md:text-xl text-white/90">{{ $provider->price_range }} / {{ __('month') }}</p>
                @endif
            </div>
        </div>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10 grid grid-cols-1 lg:grid-cols-3 gap-8">
    <main class="lg:col-span-2 space-y-6">
        @if ($provider->description)
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3">{{ __('About') }}</h2>
                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $provider->description }}</p>
            </section>
        @endif

        {{-- ÖZELLİKLER --}}
        @if (! empty($provider->features))
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3 inline-flex items-center gap-2"><x-svg-icon name="sparkles" class="w-5 h-5 text-emerald-600" /> {{ __('Features') }}</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($provider->features as $f)
                        <span class="text-xs px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">{{ str_replace('_', ' ', $f) }}</span>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ŞEHİR LİSTESİ --}}
        @if ($citiesWithProvider->isNotEmpty())
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3 inline-flex items-center gap-2"><x-svg-icon name="map-pin" class="w-5 h-5 text-emerald-600" /> {{ __('Cities where this provider operates (:count)', ['count' => $citiesWithProvider->count()]) }}</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach ($citiesWithProvider as $c)
                        <a href="{{ route('cities.show', $c->slug) }}"
                           class="flex items-baseline justify-between p-2 rounded hover:bg-emerald-50 border border-gray-100 hover:border-emerald-300 transition text-sm">
                            <span class="font-semibold text-gray-900">{{ $c->name }}</span>
                            @if ($c->avg_rent_min)
                                <span class="text-xs text-gray-500">€{{ $c->avg_rent_min }}–{{ $c->avg_rent_max }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @elseif (! empty($provider->cities) && is_array($provider->cities))
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3 inline-flex items-center gap-2"><x-svg-icon name="map-pin" class="w-5 h-5 text-emerald-600" /> {{ __('Cities') }}</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($provider->cities as $c)
                        <span class="text-sm px-3 py-1 rounded-full bg-gray-100 text-gray-700">{{ $c }}</span>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- BENZER SAĞLAYICILAR --}}
        @if ($related->isNotEmpty())
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3">{{ __('Similar :type', ['type' => $provider->type_label]) }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($related as $r)
                        <a href="{{ route('housing.provider-show', $r->slug) }}"
                           class="flex items-center gap-3 p-3 border border-gray-100 rounded hover:bg-gray-50 transition">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600">{!! e_icon($r->type_emoji, 'w-5 h-5') !!}</span>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-gray-900 truncate">{{ $r->name }}</p>
                                @if ($r->price_min)
                                    <p class="text-xs text-gray-500">{{ $r->price_range }} / {{ __('month') }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    <aside class="space-y-4">
        <div class="bg-white border-2 border-emerald-500 rounded-xl p-6 sticky top-20 shadow-lg">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-4 inline-flex items-center gap-1.5"><x-svg-icon name="list-bullet" class="w-4 h-4" /> {{ __('Quick Info') }}</h3>

            <div class="space-y-3 text-sm">
                @if ($provider->price_min)
                    <div class="flex items-baseline justify-between">
                        <span class="text-gray-600">{{ __('Price') }}</span>
                        <strong class="text-amber-700 text-lg">{{ $provider->price_range }}<span class="text-xs text-gray-500 font-normal">/{{ __('month') }}</span></strong>
                    </div>
                @endif
                @if ($provider->total_capacity)
                    <div class="flex items-baseline justify-between">
                        <span class="text-gray-600">{{ __('Total Beds') }}</span>
                        <span class="font-semibold">{{ number_format($provider->total_capacity, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if ($provider->waiting_period)
                    <div class="flex items-baseline justify-between">
                        <span class="text-gray-600">{{ __('Waiting') }}</span>
                        <span class="font-semibold">{{ $provider->waiting_period }}</span>
                    </div>
                @endif
                @if ($provider->phone)
                    <div class="flex items-baseline justify-between">
                        <span class="text-gray-600">{{ __('Phone') }}</span>
                        <span class="font-semibold text-xs">{{ $provider->phone }}</span>
                    </div>
                @endif
                @if ($provider->email)
                    <div class="flex items-baseline justify-between">
                        <span class="text-gray-600">{{ __('Email') }}</span>
                        <a href="mailto:{{ $provider->email }}" class="font-semibold text-xs text-emerald-700 hover:underline truncate max-w-[60%]">{{ $provider->email }}</a>
                    </div>
                @endif
            </div>

            @if ($provider->website)
                <a href="{{ $provider->website }}"
                   target="_blank" rel="noopener nofollow"
                   class="flex items-center justify-center gap-2 mt-5 text-center py-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition shadow-md">
                    <x-svg-icon name="globe" class="w-5 h-5" /> {{ __('Go to Official Site') }}
                </a>
                <p class="text-[11px] text-gray-500 text-center mt-2">{{ __('Application is always made on the official site') }}</p>
            @endif
        </div>

        @if ($provider->type === 'studierendenwerk')
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 text-sm text-amber-900 flex items-start gap-2">
                <x-svg-icon name="light-bulb" class="w-5 h-5 flex-shrink-0 text-amber-600" />
                <span><strong>{{ __('Tip:') }}</strong> {{ __('Studierendenwerk applications have long waiting lists. Apply as soon as you receive your university admission — and review private companies in parallel.') }}</span>
            </div>
        @elseif ($provider->type === 'private_chain')
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5 text-sm text-indigo-900 flex items-start gap-2">
                <x-svg-icon name="light-bulb" class="w-5 h-5 flex-shrink-0 text-indigo-600" />
                <span><strong>{{ __('Tip:') }}</strong> {{ __('Private companies\' inventory changes by the minute. On their official sites, use the "Verfügbar / Available" filter to see open rooms.') }}</span>
            </div>
        @else
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 text-sm text-amber-900 flex items-start gap-2">
                <x-svg-icon name="light-bulb" class="w-5 h-5 flex-shrink-0 text-amber-600" />
                <span><strong>{{ __('Tip:') }}</strong> {{ __('On WG-Gesucht, a profile photo + short intro in German/English increases your reply rate 3x.') }}</span>
            </div>
        @endif
    </aside>
</div>
@endsection
