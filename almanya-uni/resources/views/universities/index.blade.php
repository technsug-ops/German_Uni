@extends('layouts.app')

@section('title', __('German Universities — Guide for International Students') . '  — ' . brand('name'))
@section('meta_description', __('603 official German universities (Hochschule): programs, applications, cost of living and a complete guide for international students.'))

@php
    $typeLabel = fn ($t) => match ($t) {
        'public' => __('Public'),
        'private' => __('Private'),
        'applied_sciences' => __('Applied Sciences'),
        'art' => __('Art'),
        'religion' => __('Religion'),
        default => $t ? ucfirst($t) : '-',
    };
    $typeBadgeColor = fn ($t) => match ($t) {
        'public' => 'bg-emerald-50 text-emerald-700',
        'private' => 'bg-amber-50 text-amber-700',
        'applied_sciences' => 'bg-blue-50 text-blue-700',
        'art' => 'bg-pink-50 text-pink-700',
        'religion' => 'bg-purple-50 text-purple-700',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp

@section('content')

{{-- ─────────────── HERO ─────────────── --}}
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-accent-500 text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-16">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ __('Universities') }}</span>
        </nav>
        <h1 class="text-3xl md:text-5xl font-extrabold mb-3 leading-tight">
            {{ __('German Universities') }}
        </h1>
        <p class="text-lg md:text-xl text-primary-100 max-w-3xl mb-6">
            {{ __('Germany\'s 603 official higher education institutions (Hochschule) — public, private, applied sciences and art universities. Programs, cost of living and application guide.') }}
        </p>
        <div class="flex flex-wrap gap-4 text-sm">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/20">
                <span class="text-2xl">🎓</span>
                <span><strong class="text-lg">{{ $total }}</strong> {{ __('universities') }}</span>
            </div>
            @if(!empty($states))
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/20">
                    <span class="text-2xl">🗺️</span>
                    <span><strong class="text-lg">{{ count($states) }}</strong> {{ __('states') }}</span>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- ─────────────── FILTER BAR ─────────────── --}}
<section class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-[1400px] mx-auto px-4 py-4">
        <form method="GET" action="/universities" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-[1fr_180px_180px_140px_auto_auto] gap-3">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="{{ __('University name (TU München, Heidelberg...)') }}"
                           class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"/>
                </div>
                <select name="type" class="px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-900">
                    <option value="">🏛️ {{ __('All types') }}</option>
                    @foreach (($available_types ?? []) as $t)
                        <option value="{{ $t }}" @selected(request('type') === $t)>{{ $typeLabel($t) }}</option>
                    @endforeach
                </select>
                <select name="state" class="px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-900">
                    <option value="">🗺️ {{ __('All states') }}</option>
                    @foreach (($states ?? []) as $s)
                        <option value="{{ $s->slug }}" @selected(request('state') === $s->slug)>{{ $s->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="city" value="{{ request('city') }}"
                       placeholder="{{ __('City') }}"
                       class="px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-900"/>
                <button class="px-6 py-2.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold transition">
                    {{ __('Filter') }}
                </button>
                @if(request()->hasAny(['q', 'type', 'state', 'city', 'english', 'uni_assist', 'size']))
                    <a href="{{ route('universities.index') }}"
                       class="px-6 py-2.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-center transition">
                        {{ __('Reset') }}
                    </a>
                @endif
            </div>

            {{-- Quick toggle chips (kritik filtreler) --}}
            <div class="flex items-center flex-wrap gap-2 pt-2 border-t border-gray-100">
                <span class="text-xs text-gray-500 mr-1">{{ __('Quick filter:') }}</span>

                <label class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-full border cursor-pointer transition
                    {{ request('english') === '1' ? 'bg-blue-600 text-white border-blue-600' : 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100' }}">
                    <input type="checkbox" name="english" value="1" @checked(request('english') === '1') class="hidden" onchange="this.form.submit()">
                    🇬🇧 {{ __('Has English programs') }}
                </label>

                <label class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-full border cursor-pointer transition
                    {{ request('uni_assist') === '1' ? 'bg-purple-600 text-white border-purple-600' : 'bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100' }}">
                    <input type="checkbox" name="uni_assist" value="1" @checked(request('uni_assist') === '1') class="hidden" onchange="this.form.submit()">
                    🤝 {{ __('Uni-Assist member') }}
                </label>

                <span class="text-xs text-gray-400 mx-2">·</span>
                <span class="text-xs text-gray-500">{{ __('Size:') }}</span>

                @php
                    $sizeChips = [
                        'small'  => [__('Small'), __('< 5K students'),
                            'bg-amber-600 text-white border-amber-600',
                            'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100'],
                        'medium' => [__('Medium'), '5K–20K',
                            'bg-orange-600 text-white border-orange-600',
                            'bg-orange-50 text-orange-700 border-orange-200 hover:bg-orange-100'],
                        'large'  => [__('Large'), '> 20K',
                            'bg-rose-600 text-white border-rose-600',
                            'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100'],
                    ];
                @endphp

                @foreach ($sizeChips as $val => [$lbl, $rng, $activeCls, $idleCls])
                    <a href="{{ request()->fullUrlWithQuery(['size' => request('size') === $val ? null : $val]) }}"
                       class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full border transition
                              {{ request('size') === $val ? $activeCls : $idleCls }}">
                        {{ $lbl }} <span class="opacity-75">({{ $rng }})</span>
                    </a>
                @endforeach
            </div>
        </form>
    </div>
</section>

{{-- ─────────────── RESULTS ─────────────── --}}
<section class="bg-gray-50 py-10">
    <div class="max-w-[1400px] mx-auto px-4">
        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-gray-600">
                {!! __('<strong class="text-gray-900">:count</strong> universities found', ['count' => $total]) !!}
            </p>
            <p class="text-xs text-gray-500">{{ __('Sorted by student count') }}</p>
        </div>

        @if ($universities && count($universities) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach ($universities as $uni)
                    @php
                        $seed = crc32($uni['name_de']);
                        $palettes = [
                            'from-blue-500 to-cyan-400',
                            'from-purple-500 to-pink-500',
                            'from-amber-500 to-orange-400',
                            'from-emerald-500 to-teal-400',
                            'from-rose-500 to-fuchsia-500',
                            'from-indigo-500 to-violet-500',
                        ];
                        $palette = $palettes[$seed % count($palettes)];
                    @endphp
                    <a href="{{ route('universities.show', $uni['slug']) }}"
                       class="group bg-white rounded-xl overflow-hidden border border-gray-200 hover:border-primary-500 hover:shadow-lg hover:-translate-y-0.5 transition-all flex flex-col">

                        {{-- Cover image --}}
                        <div class="aspect-[16/9] overflow-hidden bg-gray-100 relative">
                            @if(!empty($uni['image_url']))
                                <img src="{{ $uni['image_url'] }}" alt="{{ $uni['name_de'] }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 {{ !empty($uni['image_is_fallback']) ? 'opacity-90' : '' }}"/>
                                @if(!empty($uni['image_is_fallback']) && $uni['city_name'])
                                    {{-- Şehir görseli fallback — kart'ta küçük overlay ile bildir --}}
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent pointer-events-none"></div>
                                    <span class="absolute bottom-2 right-2 inline-flex items-center gap-1 text-[10px] text-white/90 bg-black/40 backdrop-blur px-1.5 py-0.5 rounded">
                                        📍 {{ $uni['city_name'] }}
                                    </span>
                                @endif
                            @else
                                <div class="w-full h-full bg-gradient-to-br {{ $palette }} flex items-center justify-center">
                                    <span class="text-4xl font-extrabold text-white/90 drop-shadow text-center px-4">
                                        {{ mb_substr($uni['name_de'], 0, 2) }}
                                    </span>
                                </div>
                            @endif

                            {{-- Type badge top-left --}}
                            @if($uni['type'])
                                <span class="absolute top-2 left-2 inline-block px-2 py-0.5 rounded {{ $typeBadgeColor($uni['type']) }} text-xs font-semibold ring-1 ring-white/40 shadow-sm">
                                    {{ $typeLabel($uni['type']) }}
                                </span>
                            @endif

                            {{-- Logo overlay bottom-left (varsa) --}}
                            @if($uni['logo_url'] && !empty($uni['image_url']))
                                <div class="absolute bottom-2 left-2 w-12 h-12 bg-white rounded-lg ring-1 ring-white/60 shadow-md p-1 flex items-center justify-center">
                                    <img src="{{ $uni['logo_url'] }}" alt="" class="max-w-full max-h-full object-contain" loading="lazy" decoding="async"/>
                                </div>
                            @endif
                        </div>

                        {{-- Text content --}}
                        <div class="p-4 flex-1 flex flex-col">
                            <h3 class="font-bold text-gray-900 group-hover:text-primary-600 transition leading-tight line-clamp-2 mb-1">
                                {{ $uni['name_de'] }}
                            </h3>
                            <p class="text-xs text-gray-500 mb-3">
                                📍 {{ $uni['city_name'] ?? __('Unknown') }}@if (!empty($uni['state_name'])) · {{ $uni['state_name'] }}@endif
                            </p>
                            <div class="mt-auto flex items-center justify-between pt-2 border-t border-gray-100 text-xs">
                                @if($uni['student_count'])
                                    <span class="text-accent-600 font-bold">
                                        {{ number_format($uni['student_count']) }}
                                        <span class="font-normal text-gray-500">{{ __('students') }}</span>
                                    </span>
                                @else
                                    <span></span>
                                @endif
                                @if($uni['founded_year'])
                                    <span class="text-gray-500">est. {{ $uni['founded_year'] }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">{{ $universities->links() }}</div>
        @else
            <div class="bg-white rounded-2xl border border-gray-200 p-8 md:p-12 text-center shadow-sm max-w-2xl mx-auto">
                <div class="text-5xl mb-4">🎓</div>
                <p class="text-xl font-bold text-gray-900 mb-2">{{ __('No universities match these criteria.') }}</p>
                <p class="text-sm text-gray-500 mb-6">{{ __('Try loosening one of the filters or browse all universities below.') }}</p>

                <div class="flex flex-wrap gap-3 justify-center mb-6">
                    <a href="{{ route('universities.index') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition">
                        ↺ {{ __('Reset all filters') }}
                    </a>
                    <a href="{{ route('cities.index') }}" class="px-4 py-2 bg-white border border-gray-300 hover:border-primary-400 text-gray-700 font-semibold rounded-lg transition">
                        🏙️ {{ __('Browse by city') }}
                    </a>
                    <a href="{{ route('fields.index') }}" class="px-4 py-2 bg-white border border-gray-300 hover:border-primary-400 text-gray-700 font-semibold rounded-lg transition">
                        🎯 {{ __('Browse by field') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>

@endsection
