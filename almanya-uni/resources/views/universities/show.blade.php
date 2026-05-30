@extends('layouts.app')

@section('title', (isset($university) ? $university['name_de'] : __('University')) . '  — ' . brand('name'))

@if (isset($university))
    @php
        $autoDesc = __(':name is a German :type located in :city.', [
            'name' => $university['name_de'],
            'city' => $university['city_name'] ?? __('Germany'),
            'type' => match ($university['type']) {
                'public' => __('public university'),
                'private' => __('private university'),
                'applied_sciences' => __('university of applied sciences'),
                'art' => __('art university'),
                default => __('university'),
            },
        ]);

        // Locale-aware description fallback (önce current locale, sonra en→de→tr)
        $locale = app()->getLocale();
        $descFallback = $university['description_' . $locale]
            ?? $university['description_en']
            ?? $university['description_de']
            ?? $university['description_tr']
            ?? $autoDesc;
        $uniDesc = \App\Support\Seo::descriptionFromBlocks($university['content_blocks'] ?? null, $descFallback);

        $uniOgImage = $university_model->image_url
            ?? $university['logo_url']
            ?? route('og.image', ['type' => 'university', 'slug' => $university['slug'] . '.png']);
    @endphp

    <x-seo
        :title="$university['name_de']"
        :description="$uniDesc"
        :image="$uniOgImage"
    />

    <x-json-ld :data="\App\Support\Seo::clean(\App\Support\Seo::universityOrg($university_model))" />
    <x-json-ld :data="\App\Support\Seo::breadcrumbs([
        ['name' => __('Home'), 'url' => route('home')],
        ['name' => __('Universities'), 'url' => route('universities.index')],
        ['name' => $university['name_de'], 'url' => route('universities.show', $university['slug'])],
    ])" />

    @php $faqSchema = \App\Support\Seo::faqPageFromBlocks($university['content_blocks'] ?? null); @endphp
    @if ($faqSchema)
        <x-json-ld :data="$faqSchema" />
    @endif
@endif

@section('content')
@if ($university)

@php
    // ── Tab istatistikleri ──
    $progTotal  = $programs->count();
    $progNcFree = $programs->where('admission_mode', 'zulassungsfrei')->count();
    $progEn     = $programs->whereIn('language', ['en', 'both'])->count();
    $city       = $university_model->city;
    $typeLabel  = match ($university['type']) {
        'public' => __('Public University'),
        'private' => __('Private University'),
        'applied_sciences' => __('University of Applied Sciences (HAW)'),
        'art' => __('Art University'),
        'religion' => __('Religious University'),
        default => $university['type'] ? ucfirst($university['type']) : __('University'),
    };

    // Hero görselini içerik bloklarından çıkar → küçük olarak sidebar'a koy (üstte dev resim olmasın)
    $heroImg = null;
    $blocksNoHero = [];
    foreach (($university['content_blocks'] ?? []) as $b) {
        if (($b['type'] ?? '') === 'hero' && ! $heroImg && ! empty($b['image_url'])) {
            $heroImg = ['url' => $b['image_url'], 'alt' => $b['alt'] ?? $university['name_de']];
        } else {
            $blocksNoHero[] = $b;
        }
    }
@endphp

{{-- ─────────────── HERO ─────────────── --}}
<section class="relative bg-gradient-to-br from-primary-700 via-primary-600 to-accent-500 text-white overflow-hidden">
    @if(!empty($university_model->image_url))
        <img src="{{ $university_model->image_url }}" alt="{{ $university['name_de'] }}"
             class="absolute inset-0 w-full h-full object-cover opacity-30" loading="eager" fetchpriority="high"/>
        <div class="absolute inset-0 bg-gradient-to-t from-primary-900/80 via-primary-800/50 to-transparent"></div>
    @endif
    <div class="relative max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-primary-100 mb-3">
            <a href="/" class="hover:text-white">{{ __('Ana Sayfa') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <a href="{{ route('universities.index') }}" class="hover:text-white">{{ __('Universities') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ $university['name_de'] }}</span>
        </nav>

        <div class="flex flex-col md:flex-row gap-5 items-start">
            @if ($university['logo_url'])
                <div class="w-24 h-24 md:w-28 md:h-28 bg-white rounded-xl shadow-lg p-3 flex items-center justify-center shrink-0">
                    <img src="{{ $university['logo_url'] }}" alt="{{ $university['name_de'] }}" class="max-w-full max-h-full object-contain" loading="lazy" decoding="async">
                </div>
            @endif

            <div class="flex-1 min-w-0">
                <h1 class="text-3xl md:text-5xl font-extrabold mb-2 leading-tight drop-shadow">{{ $university['name_de'] }}</h1>
                @if (!empty($university['name_en']) && $university['name_en'] !== $university['name_de'])
                    <p class="text-primary-100 text-base mb-3">{{ $university['name_en'] }}</p>
                @endif

                <div class="flex flex-wrap gap-2 text-sm">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25">
                        <x-svg-icon name="building-office" class="w-4 h-4" />
                        {{ $typeLabel }}
                    </span>
                    @if ($university['city_name'])
                        <a href="{{ route('cities.show', $university['city_slug'] ?? '') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25 hover:bg-white/25">
                            <x-svg-icon name="map-pin" class="w-4 h-4" />
                            {{ $university['city_name'] }}@if ($university['state_name']) · {{ $university['state_name'] }}@endif
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────────── TABS ─────────────── --}}
<div x-data="{ tab: 'uni' }" class="max-w-[1400px] mx-auto px-4 py-8">

    {{-- Tab nav --}}
    <div class="flex flex-wrap gap-1 border-b border-gray-200 mb-6">
        @php
            $tabBtn = 'px-5 py-3 font-semibold text-sm border-b-2 -mb-px transition';
        @endphp
        <button @click="tab='uni'" :class="tab==='uni' ? 'border-primary-600 text-primary-700' : 'border-transparent text-gray-500 hover:text-gray-800'" class="{{ $tabBtn }}">{{ __('Üniversite') }}</button>
        @if ($progTotal > 0)
            <button @click="tab='prog'" :class="tab==='prog' ? 'border-primary-600 text-primary-700' : 'border-transparent text-gray-500 hover:text-gray-800'" class="{{ $tabBtn }}">{{ __('Programlar') }} <span class="text-gray-400">({{ $progTotal }})</span></button>
        @endif
        <button @click="tab='sehir'" :class="tab==='sehir' ? 'border-primary-600 text-primary-700' : 'border-transparent text-gray-500 hover:text-gray-800'" class="{{ $tabBtn }}">{{ __('Şehir') }}</button>
    </div>

    {{-- ═══════════ TAB: ÜNİVERSİTE ═══════════ --}}
    <div x-show="tab==='uni'" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">

            {{-- Fact panel --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-5">
                    <div>
                        <div class="text-2xl font-extrabold text-gray-900">{{ $university['founded_year'] ?? '—' }}</div>
                        <div class="text-xs text-gray-500 mt-0.5 inline-flex items-center gap-1"><x-svg-icon name="flag" class="w-3.5 h-3.5" /> {{ __('Kuruluş yılı') }}</div>
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-gray-900">{{ $university['student_count'] ? number_format($university['student_count'], 0, ',', '.') : '—' }}</div>
                        <div class="text-xs text-gray-500 mt-0.5 inline-flex items-center gap-1"><x-svg-icon name="users" class="w-3.5 h-3.5" /> {{ __('Öğrenci sayısı') }}</div>
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-gray-900">{{ $typeLabel }}</div>
                        <div class="text-xs text-gray-500 mt-0.5 inline-flex items-center gap-1"><x-svg-icon name="building-office" class="w-3.5 h-3.5" /> {{ __('Tür') }}</div>
                    </div>
                </div>

                {{-- Renkli stat kartları (MGU tarzı) — sadece programı olan üniler. Verisi 0 olan kart gizlenir. --}}
                @if ($progTotal > 0)
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button @click="tab='prog'" class="flex-1 text-left rounded-lg bg-primary-600 hover:bg-primary-700 text-white p-4 transition">
                            <div class="text-2xl font-extrabold">{{ $progTotal }}</div>
                            <div class="text-xs opacity-90 mt-0.5">{{ __('Program') }} →</div>
                        </button>
                        @if ($progEn > 0)
                            <button @click="tab='prog'" class="flex-1 text-left rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white p-4 transition">
                                <div class="text-2xl font-extrabold">{{ $progEn }}</div>
                                <div class="text-xs opacity-90 mt-0.5">🇬🇧 {{ __('İngilizce program') }} →</div>
                            </button>
                        @endif
                        @if ($progNcFree > 0)
                            <a href="{{ route('admission-free.by-university', $university['slug']) }}" class="flex-1 rounded-lg bg-amber-500 hover:bg-amber-600 text-white p-4 transition">
                                <div class="text-2xl font-extrabold">{{ $progNcFree }}</div>
                                <div class="text-xs opacity-90 mt-0.5 inline-flex items-center gap-1"><x-svg-icon name="check-circle" class="w-3.5 h-3.5" /> {{ __('NC-frei (sınavsız) program') }} ↗</div>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            {{-- AI içerik blokları / açıklama (hero görseli sidebar'a taşındı) --}}
            {{-- content_blocks TR locale-only (single-language). EN/DE'de description_* fallback --}}
            @if (!empty($blocksNoHero) && app()->getLocale() === 'tr')
                <div class="bg-white p-6 rounded-xl border border-gray-200">
                    <x-content-blocks :blocks="$blocksNoHero" :exclude-url="'/universities/' . $university['slug']" />
                </div>
            @elseif ($university['description_tr'] || $university['description_de'] || $university['description_en'])
                @php
                    // Locale-aware description: tr → en → de → tr (her dilin önce kendi)
                    $locale = app()->getLocale();
                    $primary = $university['description_' . $locale] ?? null;
                    if (! $primary) {
                        $primary = $university['description_en'] ?? $university['description_de'] ?? $university['description_tr'];
                    }
                @endphp
                <div class="bg-white p-6 rounded-xl border border-gray-200">
                    <h2 class="text-2xl font-bold mb-4">{{ __('About') }}</h2>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $primary }}</p>
                </div>
            @endif

            {{-- Konum haritası --}}
            @if (isset($university['coordinates']) && $university['coordinates']['latitude'])
                @push('head')
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
                          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
                @endpush
                <div class="bg-white p-6 rounded-xl border border-gray-200">
                    <h2 class="text-xl font-bold mb-3 inline-flex items-center gap-2">
                        <x-svg-icon name="map-pin" class="w-5 h-5" />
                        {{ __('Konum') }}
                    </h2>
                    <div id="uniMiniMap"
                         data-lat="{{ $university['coordinates']['latitude'] }}"
                         data-lng="{{ $university['coordinates']['longitude'] }}"
                         data-name="{{ e($university['name_de']) }}"
                         class="w-full h-64 rounded-lg border border-gray-200 bg-gray-100"></div>
                    <p class="text-xs text-gray-500 mt-2">
                        <a href="https://www.openstreetmap.org/?mlat={{ $university['coordinates']['latitude'] }}&mlon={{ $university['coordinates']['longitude'] }}#map=16/{{ $university['coordinates']['latitude'] }}/{{ $university['coordinates']['longitude'] }}"
                           target="_blank" rel="noopener" class="text-primary-600 hover:underline">{{ __("OpenStreetMap'te aç") }} ↗</a>
                    </p>
                </div>
                @push('scripts')
                    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
                    <script>
                    (function () {
                        const el = document.getElementById('uniMiniMap');
                        if (!el) return;
                        const lat = parseFloat(el.dataset.lat), lng = parseFloat(el.dataset.lng);
                        if (isNaN(lat) || isNaN(lng)) return;
                        const render = () => {
                            if (el._leaflet_id) return;
                            const map = L.map(el, { scrollWheelZoom: false }).setView([lat, lng], 14);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>', maxZoom: 19,
                            }).addTo(map);
                            L.marker([lat, lng]).addTo(map).bindPopup(el.dataset.name);
                            setTimeout(() => map.invalidateSize(), 200);
                        };
                        render();
                    })();
                    </script>
                @endpush
            @endif

            {{-- Kaynaklar --}}
            @if ($university['website_url'] || $university['wikipedia_url_de'] || $university['wikipedia_url_en'])
                <div class="bg-white p-6 rounded-xl border border-gray-200">
                    <h2 class="text-xl font-bold mb-3">{{ __('Kaynaklar') }}</h2>
                    <div class="flex flex-wrap gap-3 text-sm">
                        @if ($university['website_url'])
                            <a href="{{ $university['website_url'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 font-semibold text-gray-800"><x-svg-icon name="globe" class="w-4 h-4" /> {{ __('Resmi website') }} ↗</a>
                        @endif
                        @if ($university['wikipedia_url_de'])
                            <a href="{{ $university['wikipedia_url_de'] }}" target="_blank" rel="noopener" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 font-semibold text-gray-800">Wikipedia (DE) ↗</a>
                        @endif
                        @if ($university['wikipedia_url_en'])
                            <a href="{{ $university['wikipedia_url_en'] }}" target="_blank" rel="noopener" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 font-semibold text-gray-800">Wikipedia (EN) ↗</a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar (işlemler) --}}
        <div>
            <div class="bg-white p-6 rounded-xl border border-gray-200 mb-6 sticky top-20 space-y-3">
                <x-favorite-button :model="$university_model" type="university" size="lg" :block="true" />
                <a href="{{ route('admission-free.by-university', $university['slug']) }}"
                   class="inline-flex items-center justify-center gap-1.5 w-full bg-emerald-600 text-white py-3 rounded-lg font-semibold hover:bg-emerald-700 transition">
                    <x-svg-icon name="check-circle" class="w-4 h-4" />
                    {{ __('NC-frei Programlar') }}
                </a>
                <a href="{{ route('compare.index', ['slugs' => $university['slug']]) }}"
                   class="inline-flex items-center justify-center gap-1.5 w-full bg-primary-500 text-white py-3 rounded-lg font-semibold hover:bg-primary-600 transition">
                    <x-svg-icon name="scale" class="w-4 h-4" />
                    {{ __('Karşılaştırmaya Ekle') }}
                </a>
                @if ($university['website_url'])
                    <a href="{{ $university['website_url'] }}" target="_blank" rel="noopener"
                       class="block text-center w-full bg-accent-500 text-white py-3 rounded-lg font-semibold hover:bg-accent-600 transition">{{ __('Üniversite Sitesi') }} ↗</a>
                @endif
            </div>

            {{-- Hero görseli (içerikten alındı, küçük) --}}
            @if ($heroImg)
                <figure class="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-center h-48">
                    <img src="{{ $heroImg['url'] }}" alt="{{ $heroImg['alt'] }}"
                         class="max-w-full max-h-full w-auto object-contain" loading="lazy">
                </figure>
            @endif
        </div>
    </div>

    {{-- ═══════════ TAB: PROGRAMLAR ═══════════ --}}
    <div x-show="tab==='prog'" x-cloak>
        @if ($programs->isNotEmpty())
            @php
                $degreeLabels = [
                    'bachelor' => ['Bachelor', 'academic-cap'], 'master' => ['Master', 'target'],
                    'phd' => ['PhD', 'beaker'], 'staatsexamen' => ['Staatsexamen', 'scale'],
                    'diplom' => ['Diplom', 'document-text'], 'magister' => ['Magister', 'document-text'], 'other' => [__('Diğer'), 'book-open'],
                ];
            @endphp
            <div class="bg-white p-6 rounded-xl border border-gray-200">
                <div class="flex items-end justify-between mb-5 flex-wrap gap-3">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ __('Programlar & Bölümler') }}</h2>
                        <p class="text-sm text-gray-600">
                            {{ $progTotal }} {{ __('aktif program') }} ·
                            {{ $progEn }} {{ __('İngilizce') }} ·
                            {{ $progNcFree }} NC-frei
                        </p>
                    </div>
                </div>

                @foreach ($programs_by_degree as $degree => $items)
                    @php [$label, $iconName] = $degreeLabels[$degree] ?? [ucfirst($degree), 'book-open']; @endphp
                    <h3 class="text-lg font-bold text-gray-900 mt-6 mb-3 flex items-center gap-2">
                        <x-svg-icon name="{{ $iconName }}" class="w-5 h-5" /> {{ $label }}
                        <span class="text-sm font-normal text-gray-500">({{ $items->count() }})</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach ($items as $p)
                            <a href="{{ route('programs.show', $p->slug) }}"
                               class="group block bg-gray-50 border border-gray-200 hover:border-primary-500 hover:shadow-md hover:bg-white transition rounded-lg p-4">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <h4 class="font-semibold text-gray-900 leading-snug group-hover:text-primary-700 transition">{{ $p->name_de }}</h4>
                                    @if ($p->language)
                                        <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full whitespace-nowrap
                                            @if ($p->language === 'en') bg-blue-100 text-blue-700
                                            @elseif ($p->language === 'de') bg-green-100 text-green-700
                                            @elseif ($p->language === 'both') bg-purple-100 text-purple-700
                                            @else bg-gray-100 text-gray-600 @endif">
                                            @switch($p->language)
                                                @case('en') EN @break
                                                @case('de') DE @break
                                                @case('both') DE+EN @break
                                                @default {{ strtoupper($p->language) }}
                                            @endswitch
                                        </span>
                                    @endif
                                </div>
                                @if ($p->degree_specification)
                                    <p class="text-xs text-gray-500 mb-2">{{ $p->degree_specification }}</p>
                                @endif
                                <div class="flex flex-wrap gap-2 text-xs">
                                    @if ($p->field)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-white" style="background-color: {{ $p->field->color }}">
                                            {{ $p->field->icon }} {{ $p->field->name_tr }}
                                        </span>
                                    @endif
                                    @if ($p->duration_semesters)
                                        <span class="inline-flex items-center gap-1 text-gray-600"><x-svg-icon name="clock" class="w-3.5 h-3.5" /> {{ $p->duration_semesters }} {{ __('sem') }}</span>
                                    @endif
                                    @if ($p->admission_mode === 'zulassungsfrei')
                                        <span class="inline-flex items-center gap-1 text-amber-700 font-semibold"><x-svg-icon name="check-circle" class="w-3.5 h-3.5" /> NC-frei</span>
                                    @endif
                                    @if (! is_null($p->tuition_fee_eur))
                                        <span class="inline-flex items-center gap-1 text-gray-600"><x-svg-icon name="currency-euro" class="w-3.5 h-3.5" /> {{ $p->tuition_fee_eur == 0 ? __('Ücretsiz') : number_format($p->tuition_fee_eur, 0, ',', '.') . ' € / ' . __('sem') }}</span>
                                    @endif
                                </div>
                                @if ($p->description)
                                    <p class="text-sm text-gray-700 mt-3 line-clamp-3">{{ \Illuminate\Support\Str::limit($p->description, 200) }}</p>
                                @endif
                                <span class="inline-block mt-3 text-xs font-semibold text-primary-600 group-hover:text-primary-800">{{ __('Detayını gör') }} →</span>
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white p-10 rounded-xl border border-gray-200 text-center text-gray-500">{{ __('Bu üniversite için kayıtlı program bulunmuyor.') }}</div>
        @endif
    </div>

    {{-- ═══════════ TAB: ŞEHİR ═══════════ --}}
    <div x-show="tab==='sehir'" x-cloak>
        @if ($city)
            @php
                $cityIntro = $city->content_blocks
                    ? \App\Support\Seo::descriptionFromBlocks($city->content_blocks, '')
                    : '';
                // MGU "Highlight" tarzı: şehir içerik bloklarından 3 öne çıkan (yaşam/barınma/kültür)
                $cityHighlights = collect($city->content_blocks ?? [])
                    ->filter(fn ($b) => ! empty($b['h']) && ! empty($b['body_md']))
                    ->take(3)
                    ->map(fn ($b) => [
                        'h' => $b['h'],
                        'text' => \Illuminate\Support\Str::limit(strip_tags(\Illuminate\Support\Str::markdown($b['body_md'])), 150),
                    ])->values();
                // Sadece verisi olan stat kartlarını topla — boş kutu olmasın
                $cityStats = [];
                $cityStats[] = ['v' => $cityUniCount, 'l' => __('Üniversite'), 'i' => 'building-office', 'c' => 'bg-primary-600 text-white'];
                if (($cityPrograms ?? 0) > 0) {
                    $cityStats[] = ['v' => number_format($cityPrograms, 0, ',', '.'), 'l' => __('Program'), 'i' => 'book-open', 'c' => 'bg-indigo-600 text-white'];
                }
                if (($cityStudents ?? 0) > 0) {
                    $cityStats[] = ['v' => number_format($cityStudents, 0, ',', '.'), 'l' => __('Öğrenci'), 'i' => 'academic-cap', 'c' => 'bg-sky-600 text-white'];
                }
                if ($city->population) {
                    $cityStats[] = ['v' => number_format($city->population, 0, ',', '.'), 'l' => __('Nüfus'), 'i' => 'users', 'c' => 'bg-gray-50 border border-gray-200 text-gray-900'];
                }
                if ($city->avg_rent_min) {
                    $rent = number_format($city->avg_rent_min, 0, ',', '.') . '€' . ($city->avg_rent_max ? '–' . number_format($city->avg_rent_max, 0, ',', '.') . '€' : '');
                    $cityStats[] = ['v' => $rent, 'l' => __('WG kira / ay'), 'i' => 'home', 'c' => 'bg-emerald-600 text-white'];
                }
                if ($city->stw_capacity) {
                    $cityStats[] = ['v' => number_format($city->stw_capacity, 0, ',', '.'), 'l' => __('Yurt kapasitesi'), 'i' => 'home', 'c' => 'bg-amber-500 text-white'];
                }
            @endphp
            <div class="bg-white p-6 rounded-xl border border-gray-200">
                <div class="flex items-center gap-4 mb-5">
                    @if ($city->image_url)
                        <img src="{{ $city->image_url }}" alt="{{ $city->name }}" class="w-20 h-20 rounded-lg object-cover shrink-0" loading="lazy">
                    @endif
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $city->name }}</h2>
                        @if ($university['state_name'])
                            <p class="text-sm text-gray-500 inline-flex items-center gap-1"><x-svg-icon name="map-pin" class="w-3.5 h-3.5" /> {{ __('Eyalet') }}: {{ $university['state_name'] }}</p>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                    @foreach ($cityStats as $s)
                        <div class="rounded-lg p-4 {{ $s['c'] }}">
                            <div class="text-2xl font-extrabold">{{ $s['v'] }}</div>
                            <div class="text-xs {{ str_contains($s['c'], 'text-white') ? 'opacity-90' : 'text-gray-500' }} mt-0.5 inline-flex items-center gap-1">
                                <x-svg-icon name="{{ $s['i'] }}" class="w-3.5 h-3.5" />
                                {{ $s['l'] }}
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($cityIntro)
                    <p class="text-gray-700 leading-relaxed mb-5">{{ \Illuminate\Support\Str::limit($cityIntro, 480) }}</p>
                @endif

                {{-- Öne çıkanlar (yaşam, barınma, kültür) — şehir rehberinden --}}
                @if ($cityHighlights->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
                        @foreach ($cityHighlights as $hl)
                            @php $anchor = \Illuminate\Support\Str::slug($hl['h']); @endphp
                            <a href="{{ route('cities.show', $city->slug) }}#{{ $anchor }}" class="block rounded-lg border border-gray-200 hover:border-primary-400 hover:shadow-sm bg-gray-50 hover:bg-white transition p-4">
                                <h3 class="font-bold text-gray-900 text-sm mb-1 leading-snug">{{ $hl['h'] }}</h3>
                                <p class="text-xs text-gray-600 leading-relaxed">{{ $hl['text'] }}</p>
                                <span class="inline-block mt-2 text-xs font-semibold text-primary-600">{{ __('devamı') }} →</span>
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="rounded-xl bg-primary-50 border border-primary-100 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <p class="font-bold text-primary-900">{{ $city->name }} {{ __('hakkında daha fazlası') }}</p>
                        <p class="text-sm text-primary-700">{{ __('Yaşam maliyeti, barınma, ulaşım, öğrenci hayatı ve tüm üniversiteler.') }}</p>
                    </div>
                    <a href="{{ route('cities.show', $city->slug) }}"
                       class="shrink-0 inline-block bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-lg font-semibold transition whitespace-nowrap">
                        {{ $city->name }} {{ __('şehir rehberi') }} →
                    </a>
                </div>
            </div>
        @else
            <div class="bg-white p-10 rounded-xl border border-gray-200 text-center text-gray-500">{{ __('Şehir bilgisi bulunmuyor.') }}</div>
        @endif
    </div>
</div>

{{-- ÖĞRENCİ DENEYİMLERİ --}}
<section class="bg-white border-t border-gray-200 py-2">
    <div class="max-w-[1400px] mx-auto px-4">
        @include('partials._community_experiences', ['experiences' => $experiences ?? collect(), 'shareLabel' => $university['name_de'] ?? ''])
    </div>
</section>

{{-- BENZER ÜNİVERSİTELER --}}
@if (!empty($similarUnis) && $similarUnis->isNotEmpty())
<section class="bg-gray-50 border-t border-gray-200 py-10">
    <div class="max-w-[1400px] mx-auto px-4">
        <div class="flex items-baseline justify-between mb-5 flex-wrap gap-2">
            <h2 class="text-2xl font-bold text-gray-900 inline-flex items-center gap-2">
                <x-svg-icon name="academic-cap" class="w-6 h-6" />
                @if ($university['city_name'])
                    {{ $university['city_name'] }} {{ __('şehrindeki diğer üniversiteler') }}
                @else
                    {{ __('Benzer üniversiteler') }}
                @endif
            </h2>
            <a href="{{ route('universities.index') }}" class="text-sm text-primary-600 hover:underline font-semibold">{{ __('Tümü') }} →</a>
        </div>
        @php
            $typeLabelFn = fn ($t) => match ($t) {
                'public' => __('Devlet'), 'private' => __('Özel'), 'applied_sciences' => 'HAW',
                'art' => __('Sanat'), 'religion' => __('Dini'), default => $t ? ucfirst($t) : '—',
            };
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach ($similarUnis as $u)
                <a href="{{ route('universities.show', $u->slug) }}"
                   class="group bg-white rounded-xl overflow-hidden border border-gray-200 hover:border-primary-500 hover:shadow-md transition flex flex-col">
                    <div class="aspect-[16/9] overflow-hidden bg-gray-100 relative">
                        @if ($u->image_url)
                            <img src="{{ $u->image_url }}" alt="{{ $u->display_name }}" loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @elseif ($u->logo_url)
                            <div class="w-full h-full bg-white flex items-center justify-center p-3">
                                <img src="{{ $u->logo_url }}" alt="{{ $u->display_name }}" loading="lazy" class="max-w-full max-h-full object-contain"/>
                            </div>
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center">
                                <span class="text-2xl font-extrabold text-white">{{ mb_substr($u->name_de, 0, 2) }}</span>
                            </div>
                        @endif
                        @if ($u->type)
                            <span class="absolute top-1.5 left-1.5 inline-block px-1.5 py-0.5 rounded bg-black/60 backdrop-blur text-white text-[10px] font-semibold">{{ $typeLabelFn($u->type) }}</span>
                        @endif
                    </div>
                    <div class="p-2.5">
                        <h3 class="font-bold text-gray-900 group-hover:text-primary-600 transition text-xs leading-tight line-clamp-2">{{ $u->display_name }}</h3>
                        @if ($u->student_count)
                            <p class="text-[10px] text-gray-500 mt-1">{{ number_format($u->student_count) }} {{ __('öğrenci') }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- UGC Student Reviews — i18n, schema.org AggregateRating + Review microdata --}}
@isset($university_model)
    <x-university-reviews
        :university="$university_model"
        :reviews="$reviews ?? collect()"
        :stats="$reviewsStats ?? null"
    />
@endisset

@isset($university_model)
<div class="max-w-4xl mx-auto px-4">
    <x-featured-snippet
        :question="__('How do I apply to :uni as an international student?', ['uni' => $university_model->display_name])"
        :answer="__('Non-EU applicants apply via uni-assist (document verification + APS certificate for some countries). EU applicants apply directly. You need a recognised degree, language certificate, motivation letter and CV. After admission, open a Sperrkonto and book a visa appointment at your local German embassy.')"
        :steps="[
            ['title' => __('Pick a programme'), 'description' => __('Browse the programme list above — filter by language, degree, field.')],
            ['title' => __('Verify your eligibility'), 'description' => __('Check Anabin/APS for your country and degree-equivalence rules.')],
            ['title' => __('Get language certificate'), 'description' => __('TestDaF/DSH for German, IELTS/TOEFL for English-taught programmes.')],
            ['title' => __('Apply via uni-assist or directly'), 'description' => __('Submit by 15 July (winter) / 15 January (summer) deadlines.')],
            ['title' => __('After admission: Sperrkonto + visa'), 'description' => __('Open the blocked account, get health insurance, attend embassy interview.')],
        ]"
    />
</div>

{{-- Generic FAQ — sadece content_blocks içinde FAQ blok YOKSA göster (çift FAQ önleme) --}}
@php
    $hasContentBlockFaq = collect($university['content_blocks'] ?? [])->contains(fn($b) => ($b['type'] ?? null) === 'faq');
@endphp
@if (! $hasContentBlockFaq)
    <x-faq-section
        :title="__('Frequently Asked Questions')"
        :subtitle="__('Quick answers about :uni', ['uni' => $university_model->display_name])"
        :faqs="\App\Support\PageFaq::forUniversity($university_model)"
    />
@endif
@endisset

@else
<div class="max-w-[1400px] mx-auto px-4 py-16 text-center">
    <h1 class="text-3xl font-bold mb-4">{{ __('Üniversite Bulunamadı') }}</h1>
    <a href="{{ route('universities.index') }}" class="text-primary-500 hover:text-primary-600 font-semibold">← {{ __('Üniversitelere Geri Dön') }}</a>
</div>
@endif
@endsection
