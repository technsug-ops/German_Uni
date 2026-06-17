@extends('layouts.app')

@php
    $uniCount = $city->universities->count();
    $stateName = $city->state?->name;

    $autoDesc = __('Studying in :city, Germany: :n universities', ['city' => $city->name, 'n' => $uniCount])
        . ($stateName ? ', ' . __('state of :state', ['state' => $stateName]) : '')
        . '. ' . __('Cost of living, places to visit, student culture and application guide.');

    // Content blocks TR-only — EN/DE'de auto-generated description (yoksa TR sızar)
    $description = app()->getLocale() === 'tr'
        ? \App\Support\Seo::descriptionFromBlocks($city->content_blocks, $autoDesc)
        : $autoDesc;
    $title = $city->name . ' — ' . __('Student City Guide');
@endphp

<x-seo
    :title="$title"
    :description="$description"
    :image="$city->image_url ?: route('og.image', ['type' => 'city', 'slug' => $city->slug . '.png'])"
    type="website"
    :noindex="$city->isThinForLocale()"
/>

<x-json-ld :data="\App\Support\Seo::breadcrumbs([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('Cities'), 'url' => route('cities.index')],
    ['name' => $city->name, 'url' => route('cities.show', $city->slug)],
])" />

{{-- Schema.org City — yerel SEO (Berlin universities, Munich student life vb. aramalar için) --}}
<x-json-ld :data="\App\Support\Seo::clean(\App\Support\Seo::cityPlace($city))" />

@php $faqSchema = app()->getLocale() === 'tr' ? \App\Support\Seo::faqPageFromBlocks($city->content_blocks) : null; @endphp
@if ($faqSchema)
    <x-json-ld :data="$faqSchema" />
@endif

@section('title', $title . ' — ' . brand('name'))
@section('meta_description', $description)

@section('content')

{{-- ─────────────── HERO ─────────────── --}}
@php
    // Hero arka planı: admin'den yüklenen ilk galeri fotosu (güvenilir self-host).
    // Yoksa dekoratif gradient (bozuk dış görsele bağımlı DEĞİL).
    $heroPhoto = $city->hero_image_url;
    $cityProgramCount = \App\Models\Program::whereIn('university_id', $city->universities->pluck('id'))
        ->where('is_active', 1)->count();
@endphp
<section class="relative bg-gradient-to-br from-primary-700 via-primary-600 to-accent-500 text-white overflow-hidden">
    @if($heroPhoto)
        {{-- Yüklenen şehir fotoğrafı → cover hero. Hafif desatüre → daha sakin/tutarlı. --}}
        <img src="{{ $heroPhoto }}" alt="{{ __(':city — university and student life guide', ['city' => $city->name]) }}"
             class="absolute inset-0 w-full h-full object-cover [filter:saturate(0.85)]" loading="eager" fetchpriority="high"/>
        {{-- MARKA TINT: her fotoğrafı sitenin lacivert/accent karakterine çeker (multiply hue shift)
             → uymayan (sıcak/farklı) fotolar otomatik markayla uyumlanır. --}}
        <div class="absolute inset-0 bg-gradient-to-br from-primary-900 via-primary-800 to-accent-700 opacity-60 mix-blend-multiply"></div>
        {{-- Okunabilirlik + derinlik (alt koyu, üst şeffaf) --}}
        <div class="absolute inset-0 bg-gradient-to-t from-primary-900/90 via-primary-900/40 to-transparent"></div>
    @else
        {{-- Foto yoksa: dekoratif modern gradient — yumuşak ışık küreleri + nokta deseni --}}
        <div class="absolute -top-24 -right-16 w-96 h-96 rounded-full bg-white/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-28 -left-12 w-80 h-80 rounded-full bg-accent-300/20 blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 opacity-[0.07] pointer-events-none" style="background-image:radial-gradient(circle at 1px 1px, #fff 1px, transparent 0);background-size:22px 22px;"></div>
    @endif

    <div class="relative max-w-[1400px] mx-auto px-4 pt-8 pb-7 md:pt-12 md:pb-9">
        <nav class="text-sm text-white/80 mb-4">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <a href="{{ route('cities.index') }}" class="hover:text-white">{{ __('Cities') }}</a>
            <span class="mx-2 opacity-50">›</span>
            <span class="text-white">{{ $city->name }}</span>
        </nav>

        <div class="flex items-end gap-4 flex-wrap mb-6">
            @if($city->image_url)
                {{-- Arma → küçük rozet (404'te onerror ile kaybolur) --}}
                <div class="shrink-0 w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-white/95 ring-1 ring-white/40 shadow-lg p-2 flex items-center justify-center">
                    <img src="{{ $city->image_url }}" alt="" class="max-w-full max-h-full object-contain" loading="lazy" onerror="this.parentElement.remove()"/>
                </div>
            @endif
            <div>
                <h1 class="text-4xl md:text-6xl font-extrabold leading-none drop-shadow-lg">{{ $city->name }}</h1>
                @if($city->state)
                    <p class="text-white/90 mt-2.5 inline-flex items-center gap-1.5 text-sm md:text-base">
                        <x-svg-icon name="map" class="w-4 h-4" /> {{ $city->state->name }}
                        @if($citySize)
                            <span class="ml-1 px-2 py-0.5 rounded-full bg-white/20 text-xs font-semibold backdrop-blur">{{ $citySize[0] }} {{ $citySize[1] }}</span>
                        @endif
                    </p>
                @endif
            </div>
        </div>

        {{-- Entegre istatistikler (eski ayrı highlight-bar'ı kapsar) --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 max-w-3xl">
            <div class="bg-white/12 backdrop-blur rounded-xl px-4 py-3 ring-1 ring-white/20">
                <p class="text-2xl md:text-3xl font-extrabold leading-none">{{ $city->universities->count() }}</p>
                <p class="text-xs text-white/80 mt-1.5 inline-flex items-center gap-1"><x-svg-icon name="academic-cap" class="w-3.5 h-3.5" /> {{ __('University') }}</p>
            </div>
            @if($cityProgramCount > 0)
                <div class="bg-white/12 backdrop-blur rounded-xl px-4 py-3 ring-1 ring-white/20">
                    <p class="text-2xl md:text-3xl font-extrabold leading-none">{{ number_format($cityProgramCount, 0, ',', '.') }}</p>
                    <p class="text-xs text-white/80 mt-1.5 inline-flex items-center gap-1"><x-svg-icon name="book-open" class="w-3.5 h-3.5" /> {{ __('Program') }}</p>
                </div>
            @endif
            @if($city->population)
                <div class="bg-white/12 backdrop-blur rounded-xl px-4 py-3 ring-1 ring-white/20">
                    <p class="text-2xl md:text-3xl font-extrabold leading-none">{{ number_format($city->population, 0, ',', '.') }}</p>
                    <p class="text-xs text-white/80 mt-1.5 inline-flex items-center gap-1"><x-svg-icon name="users" class="w-3.5 h-3.5" /> {{ __('Population') }}</p>
                </div>
            @endif
            <a href="{{ route('tools.cost-of-living', ['city' => $city->id]) }}"
               class="bg-accent-500/90 hover:bg-accent-500 transition rounded-xl px-4 py-3 ring-1 ring-white/25">
                <p class="text-2xl md:text-3xl font-extrabold leading-none">€</p>
                <p class="text-xs text-white/90 mt-1.5 inline-flex items-center gap-1"><x-svg-icon name="banknotes" class="w-3.5 h-3.5" /> {{ __('Cost calculator') }} →</p>
            </a>
        </div>
    </div>
</section>

{{-- ─────────────── CONTENT ─────────────── --}}
<section class="bg-gray-50 py-10">
    <div class="max-w-[1400px] mx-auto px-4">

        {{-- Locale-aware enrichment: TR=content_blocks, EN/DE=content_blocks_{locale} (null → fallback paneli) --}}
        {{-- 'hero' bloğu (genelde şehir arması, beyaz üstte kocaman) gizlenir — yeni hero zaten kapsıyor --}}
        @php $cityBlocks = collect($city->localizedBlocks() ?? [])->reject(fn ($b) => ($b['type'] ?? null) === 'hero')->values()->all(); @endphp
        @if(!empty($cityBlocks))
            <x-content-blocks :blocks="$cityBlocks" :exclude-url="'/cities/' . $city->slug" />
        @else
            <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center shadow-sm">
                <div class="flex justify-center mb-3 text-gray-400"><x-svg-icon name="document-text" class="w-12 h-12" /></div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">{{ __('Content Not Ready Yet') }}</h2>
                <p class="text-gray-600 max-w-lg mx-auto">
                    {{ __('A detailed page for this city (cost of living, places to visit, student culture) will be added soon.') }}
                </p>
            </div>
        @endif

        {{-- (Eski highlight-bar kaldırıldı — istatistikler artık hero'da entegre) --}}

        {{-- ŞEHİR GALERİSİ & VİDEO (admin'den yönetilir) --}}
        @php $galleryUrls = $city->galleryUrls(); @endphp
        @if(count($galleryUrls) > 1 || $city->video_embed_url)
            <section class="mt-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4 inline-flex items-center gap-2">
                    <x-svg-icon name="photo" class="w-5 h-5 text-primary-600" /> {{ __(':city in photos & video', ['city' => $city->name]) }}
                </h2>

                @if($city->video_embed_url)
                    <div class="relative w-full rounded-2xl overflow-hidden ring-1 ring-gray-200 shadow-sm mb-4 bg-black" style="aspect-ratio:16/9;">
                        <iframe src="{{ $city->video_embed_url }}" title="{{ $city->name }}" loading="lazy"
                                class="absolute inset-0 w-full h-full" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                @endif

                @if(count($galleryUrls) > 1)
                    {{-- İlk foto hero'da; kalanlar galeri --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach(array_slice($galleryUrls, 1) as $img)
                            <a href="{{ $img }}" target="_blank" rel="noopener" class="group block aspect-[4/3] rounded-xl overflow-hidden ring-1 ring-gray-200 bg-gray-100">
                                <img src="{{ $img }}" alt="{{ $city->name }}" loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.closest('a').remove()"/>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        @endif

        {{-- YURT SEÇENEKLERİ --}}
        @if ($city->stw_name || (! empty($city->private_chain_slugs) && is_array($city->private_chain_slugs)))
            @php
                $cityChains = ! empty($city->private_chain_slugs)
                    ? \App\Models\HousingProvider::active()
                        ->whereIn('slug', $city->private_chain_slugs)
                        ->orderBy('sort_order')
                        ->get()
                    : collect();
            @endphp
            <section class="mt-10">
                <div class="flex items-baseline justify-between mb-4 flex-wrap gap-2">
                    <h2 class="text-2xl font-bold text-gray-900 inline-flex items-center gap-2"><x-svg-icon name="home" class="w-6 h-6" /> {{ __('Housing options in :city', ['city' => $city->name]) }}</h2>
                    <a href="{{ route('housing.providers') }}" class="text-sm text-primary-600 hover:underline">{{ __('All providers') }} →</a>
                </div>

                @if ($city->avg_rent_min)
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-lg p-4 mb-4 text-sm inline-flex flex-wrap items-center gap-1">
                        <x-svg-icon name="currency-euro" class="w-4 h-4 text-amber-700" />
                        <strong>{{ __('Average dorm price:') }}</strong> €{{ $city->avg_rent_min }}–€{{ $city->avg_rent_max }}/{{ __('mo') }}
                        @if ($city->stw_capacity)
                            <span>·</span>
                            <x-svg-icon name="home" class="w-4 h-4 text-amber-700" />
                            <strong>{{ __('STW capacity:') }}</strong> {{ __(':n beds', ['n' => number_format($city->stw_capacity, 0, ',', '.')]) }}
                        @endif
                        @if ($city->stw_waiting)
                            <span>·</span>
                            <x-svg-icon name="clock" class="w-4 h-4 text-amber-700" />
                            <strong>{{ __('Waiting time:') }}</strong> {{ $city->stw_waiting }}
                        @endif
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- STW kartı --}}
                    @if ($city->stw_name)
                        @php
                            $stw = \App\Models\HousingProvider::where('name', $city->stw_name)->first();
                        @endphp
                        @if ($stw)
                            <a href="{{ route('housing.provider-show', $stw->slug) }}"
                               class="group block bg-white border-2 border-emerald-300 hover:border-emerald-500 hover:shadow-lg transition rounded-xl p-5">
                                <div class="flex items-start gap-3 mb-2">
                                    <div class="w-12 h-12 rounded bg-gradient-to-br from-emerald-500 to-teal-500 text-white flex items-center justify-center"><x-svg-icon name="building-office" class="w-7 h-7" /></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">{{ __('Public (Studierendenwerk)') }}</p>
                                        <h3 class="font-bold text-gray-900 group-hover:text-emerald-700 leading-tight">{{ $stw->name }}</h3>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-2 text-xs mt-3 pt-3 border-t border-gray-100">
                                    @if ($stw->price_range)
                                        <div>
                                            <p class="text-gray-500">{{ __('Price') }}</p>
                                            <p class="font-bold text-amber-700">{{ $stw->price_range }}</p>
                                        </div>
                                    @endif
                                    @if ($stw->total_capacity)
                                        <div>
                                            <p class="text-gray-500">{{ __('Beds') }}</p>
                                            <p class="font-bold">{{ number_format($stw->total_capacity, 0, ',', '.') }}</p>
                                        </div>
                                    @endif
                                    @if ($stw->waiting_period)
                                        <div>
                                            <p class="text-gray-500">{{ __('Waiting') }}</p>
                                            <p class="font-bold">{{ $stw->waiting_period }}</p>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endif
                    @endif

                    {{-- Private chains özet kartı --}}
                    @if ($cityChains->isNotEmpty())
                        <div class="bg-white border-2 border-indigo-300 rounded-xl p-5">
                            <div class="flex items-start gap-3 mb-3">
                                <div class="w-12 h-12 rounded bg-gradient-to-br from-indigo-500 to-purple-500 text-white flex items-center justify-center"><x-svg-icon name="building-office" class="w-7 h-7" /></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">{{ __('Private operator') }} ({{ $cityChains->count() }})</p>
                                    <h3 class="font-bold text-gray-900 leading-tight">{{ __('Fast + Furnished + Pricey') }}</h3>
                                </div>
                            </div>
                            <div class="space-y-1.5 text-sm">
                                @foreach ($cityChains as $chain)
                                    <a href="{{ route('housing.provider-show', $chain->slug) }}"
                                       class="flex items-baseline justify-between p-1.5 rounded hover:bg-indigo-50 transition">
                                        <span class="font-semibold text-gray-900">{{ $chain->name }}</span>
                                        @if ($chain->price_range !== '—')
                                            <span class="text-xs text-amber-700">{{ $chain->price_range }}</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <p class="text-xs text-gray-500 mt-3 text-center inline-flex items-center gap-1 justify-center flex-wrap">
                    <x-svg-icon name="light-bulb" class="w-3.5 h-3.5" /> <strong>{{ __('Strategy:') }}</strong> {{ __('Apply to STW early (long waiting list); check private providers in parallel.') }}
                    <a href="{{ route('housing.index') }}" class="text-primary-600 hover:underline">{{ __('Housing guide') }} →</a>
                </p>
            </section>
        @endif

        {{-- TOP ALANLAR --}}
        @if (! empty($topFieldsInCity) && $topFieldsInCity->isNotEmpty())
            <section class="mt-10">
                <div class="flex items-baseline justify-between mb-4 flex-wrap gap-2">
                    <h2 class="text-2xl font-bold text-gray-900 inline-flex items-center gap-2"><x-svg-icon name="target" class="w-6 h-6" /> {{ __('Strongest fields in :city', ['city' => $city->name]) }}</h2>
                    <a href="{{ route('fields.index') }}" class="text-sm text-primary-600 hover:underline">{{ __('All fields') }} →</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    @foreach ($topFieldsInCity as $field)
                        <a href="{{ route('fields.show', $field->slug) }}"
                           class="group bg-white rounded-xl border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-4 text-center">
                            <div class="mb-1 flex justify-center" style="color: {{ $field->color ?? '#1e40af' }};">{!! e_icon($field->icon, 'w-10 h-10') !!}</div>
                            <h3 class="font-bold text-sm text-gray-900 group-hover:text-primary-600 leading-tight">{{ $field->name }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ __(':n programs', ['n' => $field->programs_count]) }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- TOP PROGRAMLAR --}}
        @if (! empty($topPrograms) && $topPrograms->isNotEmpty())
            <section class="mt-10">
                <div class="flex items-baseline justify-between mb-4 flex-wrap gap-2">
                    <h2 class="text-2xl font-bold text-gray-900 inline-flex items-center gap-2"><x-svg-icon name="book-open" class="w-6 h-6" /> {{ __('Popular programs in :city', ['city' => $city->name]) }}</h2>
                    <a href="{{ route('programs.index', ['q' => $city->name]) }}" class="text-sm text-primary-600 hover:underline">{{ __('All programs') }} →</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($topPrograms as $p)
                        <a href="{{ route('programs.show', $p->slug) }}"
                           class="group bg-white rounded-lg border border-gray-200 hover:border-primary-500 hover:shadow-md transition p-4">
                            <div class="flex items-start gap-2 mb-1">
                                @if ($p->field?->icon)
                                    <span class="shrink-0" style="color: {{ $p->field->color ?? '#1e40af' }};">{!! e_icon($p->field->icon, 'w-4 h-4') !!}</span>
                                @endif
                                <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 leading-snug flex-1">{{ $p->name }}</h3>
                            </div>
                            <div class="flex flex-wrap gap-1.5 mt-2 text-xs">
                                <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-700">{{ ucfirst($p->degree) }}</span>
                                @if ($p->language === 'en' || $p->language === 'both')
                                    <span class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-700">🇬🇧 {{ __('English') }}</span>
                                @endif
                            </div>
                            @if ($p->university)
                                <p class="text-xs text-gray-500 mt-2">@ {{ $p->university->display_name }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Üniversiteler bölümü — her zaman göster (content_blocks içinde olsa bile detaylı kart listesi faydalı) --}}
        @if($city->universities->isNotEmpty())
            <section class="mt-12">
                <h2 class="text-2xl font-bold mb-1 text-gray-900">{{ __('Universities in :city', ['city' => $city->name]) }}</h2>
                <p class="text-sm text-gray-500 mb-5">{{ __(':n universities in this city', ['n' => $city->universities->count()]) }}</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($city->universities as $u)
                        <a href="{{ route('universities.show', $u->slug) }}"
                           class="group bg-white rounded-xl p-4 border border-gray-200 hover:border-primary-500 hover:shadow-lg transition-all">
                            <div class="flex items-start gap-3">
                                @if($u->logo_url)
                                    <img src="{{ $u->logo_url }}" alt="{{ $u->display_name }}"
                                         class="w-14 h-14 object-contain shrink-0 bg-white rounded ring-1 ring-gray-100 p-1" loading="lazy" decoding="async"/>
                                @else
                                    <div class="w-14 h-14 shrink-0 rounded bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white font-bold text-xl">
                                        {{ mb_substr($u->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 transition leading-snug">
                                        {{ $u->display_name }}
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-1">
                                        @if($u->type === 'public')
                                            <span class="inline-block px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 font-medium">{{ __('Public') }}</span>
                                        @elseif($u->type === 'private')
                                            <span class="inline-block px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 font-medium">{{ __('Private') }}</span>
                                        @elseif($u->type)
                                            <span class="inline-block px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 font-medium">{{ ucfirst($u->type) }}</span>
                                        @endif
                                        @if($u->student_count) · {{ __(':n students', ['n' => number_format($u->student_count)]) }} @endif
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- İLGİLİ BLOG YAZILARI --}}
        @if (! empty($relatedPosts) && $relatedPosts->count() > 0)
            <section class="mt-10">
                <div class="flex items-baseline justify-between mb-4 flex-wrap gap-2">
                    <h2 class="text-2xl font-bold text-gray-900 inline-flex items-center gap-2"><x-svg-icon name="document-text" class="w-6 h-6" /> {{ __('Articles about :city', ['city' => $city->name]) }}</h2>
                    <a href="{{ route('blog.index') }}" class="text-sm text-primary-600 hover:underline">Blog →</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($relatedPosts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}"
                           class="group block bg-white rounded-xl border border-gray-200 hover:border-primary-400 hover:shadow-md transition p-5">
                            @if ($post->category)
                                <p class="text-xs font-semibold uppercase tracking-wide mb-2"
                                   style="color: {{ $post->category->color ?? '#1E40AF' }}">
                                    {{ __($post->category->name) }}
                                </p>
                            @endif
                            <h3 class="font-bold text-gray-900 group-hover:text-primary-600 leading-tight mb-2 line-clamp-2">{{ $post->title }}</h3>
                            @if ($post->excerpt)
                                <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ \Illuminate\Support\Str::limit($post->excerpt, 100) }}</p>
                            @endif
                            <p class="text-xs text-gray-500">{{ __(':n min read', ['n' => $post->reading_minutes]) }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ÖĞRENCİ DENEYİMLERİ (Topluluk Katkıcısı) --}}
        @include('partials._community_experiences', ['experiences' => $experiences ?? collect(), 'shareLabel' => $city->name])

    </div>
</section>

{{-- ════════════════════════════════════════════════════ --}}
{{-- POPÜLER ARAMALAR (Wiki-style cross-linking → programmatic landing pages) --}}
{{-- SEO: bu sayfadan landing'lere internal link → trafik akışı + topic clustering --}}
{{-- ════════════════════════════════════════════════════ --}}
@php
    // Bu şehirde program olan alanlar (cache'lenebilir, şimdilik 1 query)
    $cityTopFields = \App\Models\FieldOfStudy::query()
        ->whereHas('programs', function ($q) use ($city) {
            $q->where('is_active', true)
              ->whereHas('university', fn ($u) => $u->where('city_id', $city->id));
        })
        ->withCount(['programs as programs_in_city_count' => function ($q) use ($city) {
            $q->where('is_active', true)
              ->whereHas('university', fn ($u) => $u->where('city_id', $city->id));
        }])
        ->orderByDesc('programs_in_city_count')
        ->take(8)
        ->get(['id', 'slug', 'name_tr', 'name_en', 'name_de', 'icon', 'color']);

    // EN/DE program var mı?
    $hasEnPrograms = \App\Models\Program::query()
        ->where('is_active', true)
        ->whereHas('university', fn ($u) => $u->where('city_id', $city->id))
        ->whereIn('language', ['en', 'both'])
        ->exists();
@endphp

{{-- Şehir etkinlik bildirimi aboneliği --}}
<section class="bg-white border-t border-gray-200 py-10">
    <div class="max-w-2xl mx-auto px-4">
        @include('events._alert-subscribe', ['city' => $city])
    </div>
</section>

@if ($cityTopFields->isNotEmpty() || $hasEnPrograms)
<section class="bg-gradient-to-br from-gray-50 to-white border-t border-gray-200 py-10">
    <div class="max-w-[1400px] mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-2 inline-flex items-center gap-2"><x-svg-icon name="search" class="w-6 h-6" /> {{ __('Popular searches in :city', ['city' => $city->name]) }}</h2>
        <p class="text-sm text-gray-600 mb-6">{{ __('Filtered program lists for common queries') }}</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @if ($hasEnPrograms)
                <a href="{{ route('programs.city-language', [$city->slug, 'en']) }}"
                   title="{{ __('English-taught programs in :city', ['city' => $city->name]) }}"
                   class="group flex items-center gap-3 bg-white border border-blue-200 hover:border-blue-500 hover:shadow-md rounded-xl p-4 transition">
                    <span class="text-3xl shrink-0">🇬🇧</span>
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 group-hover:text-blue-700 leading-tight">{{ __('English-taught programs in :city', ['city' => $city->name]) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ __('All English Bachelor/Master programs') }}</p>
                    </div>
                </a>
            @endif

            @foreach ($cityTopFields as $f)
                <a href="{{ route('programs.city-field', [$city->slug, $f->slug]) }}"
                   title="{{ $f->name }} — {{ $city->name }}"
                   class="group flex items-center gap-3 bg-white border border-gray-200 hover:border-primary-500 hover:shadow-md rounded-xl p-4 transition">
                    <span class="shrink-0" style="color: {{ $f->color ?? '#1e40af' }};">{!! e_icon($f->icon, 'w-8 h-8') !!}</span>
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 group-hover:text-primary-700 leading-tight">{{ __(':field in :city', ['field' => $f->name, 'city' => $city->name]) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $f->programs_in_city_count }} {{ __('programs') }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ════════════════════════════════════════════════════ --}}
{{-- BENZER ŞEHİRLER --}}
{{-- ════════════════════════════════════════════════════ --}}
@if (!empty($similarCities) && $similarCities->isNotEmpty())
<section class="bg-white border-t border-gray-200 py-10">
    <div class="max-w-[1400px] mx-auto px-4">
        <div class="flex items-baseline justify-between mb-5 flex-wrap gap-2">
            <h2 class="text-2xl font-bold text-gray-900 inline-flex items-center gap-2">
                <x-svg-icon name="building-office" class="w-6 h-6" />
                @if ($city->state)
                    {{ __('Other cities in :state', ['state' => $city->state->name]) }}
                @else
                    {{ __('Similar cities') }}
                @endif
            </h2>
            <a href="{{ route('cities.index') }}" class="text-sm text-primary-600 hover:underline font-semibold">{{ __('All') }} →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach ($similarCities as $c)
                <a href="{{ route('cities.show', $c->slug) }}"
                   class="group bg-white rounded-xl overflow-hidden border border-gray-200 hover:border-primary-500 hover:shadow-md transition flex flex-col">
                    <div class="aspect-[4/3] overflow-hidden bg-gray-100 relative">
                        @if ($c->image_url)
                            <img src="{{ $c->image_url }}" alt="{{ $c->name }}" loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center">
                                <span class="text-3xl font-extrabold text-white/90">{{ mb_substr($c->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <span class="absolute bottom-2 right-2 px-2 py-0.5 rounded-full bg-black/60 backdrop-blur text-white text-xs font-semibold">
                            {{ __(':n unis', ['n' => $c->universities_count]) }}
                        </span>
                    </div>
                    <div class="p-3">
                        <h3 class="font-bold text-gray-900 group-hover:text-primary-600 transition leading-tight text-sm truncate">{{ $c->name }}</h3>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Generic FAQ — sadece content_blocks içinde FAQ blok YOKSA göster (çift FAQ önleme) --}}
@php $hasContentBlockFaq = collect($city->content_blocks ?? [])->contains(fn($b) => ($b['type'] ?? null) === 'faq'); @endphp
@if (! $hasContentBlockFaq)
<x-faq-section
    :title="__('Frequently Asked Questions')"
    :subtitle="__('Quick answers about studying in :city', ['city' => $city->name])"
    :faqs="\App\Support\PageFaq::forCity($city)"
/>
@endif

@endsection
