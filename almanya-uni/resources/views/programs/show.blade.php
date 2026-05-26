@extends('layouts.app')

@php
    $degreeLabels = [
        'bachelor' => 'Bachelor', 'master' => 'Master', 'phd' => __('PhD'),
        'staatsexamen' => 'Staatsexamen', 'diplom' => 'Diplom',
        'magister' => 'Magister', 'other' => __('Other'),
    ];
    $degreeLabel = $degreeLabels[$program->degree] ?? ucfirst($program->degree);

    $langLabels = ['en' => __('English'), 'de' => __('German'), 'both' => __('German + English')];
    $langLabel  = $langLabels[$program->language] ?? $program->language;

    $title = $program->name_de . ' — ' . $degreeLabel . ' @ ' . $program->university->name_de;
    $description = $program->description
        ? \Illuminate\Support\Str::limit($program->description, 160)
        : ($program->description_en
            ? \Illuminate\Support\Str::limit($program->description_en, 160)
            : __(':uni :program :degree program — application requirements, deadlines, tuition and detailed information.', [
                'uni' => $program->university->name_de,
                'program' => $program->name_de,
                'degree' => $degreeLabel,
            ]));
@endphp

@section('title', $title . '  — ' . brand('name'))

<x-seo :title="$title" :description="$description" :image="route('og.image', ['type' => 'program', 'slug' => $program->slug . '.png'])" />

<x-json-ld :data="\App\Support\Seo::courseSchema($program)" />
<x-json-ld :data="\App\Support\Seo::breadcrumbs([
    ['name' => __('Home'), 'url' => route('home')],
    ['name' => __('Programs'), 'url' => route('programs.index')],
    ['name' => $program->name_de, 'url' => route('programs.show', $program->slug)],
])" />

@section('content')

{{-- HERO --}}
<section class="relative bg-gradient-to-br from-primary-700 via-primary-600 to-accent-500 text-white overflow-hidden">
    @if(!empty($program->university->image_url))
        <img src="{{ $program->university->image_url }}" alt="{{ $program->university->display_name }}"
             class="absolute inset-0 w-full h-full object-cover opacity-25" loading="lazy"/>
        <div class="absolute inset-0 bg-gradient-to-t from-primary-900/80 via-primary-800/50 to-transparent"></div>
    @endif
    <div class="relative max-w-[1400px] mx-auto px-4 py-10">
        <nav class="text-sm text-primary-100 mb-3 flex flex-wrap items-center gap-2">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="opacity-60">›</span>
            <a href="{{ route('programs.index') }}" class="hover:text-white">{{ __('Programs') }}</a>
            <span class="opacity-60">›</span>
            <a href="{{ route('universities.show', $program->university->slug) }}" class="hover:text-white truncate max-w-xs">
                {{ $program->university->display_name }}
            </a>
            <span class="opacity-60">›</span>
            <span class="text-white truncate">{{ $program->name_de }}</span>
        </nav>

        <div class="flex flex-wrap items-center gap-2 mb-3">
            <span class="inline-block bg-accent-500 text-white text-xs font-bold uppercase tracking-wide px-3 py-1 rounded-full shadow">
                {{ $degreeLabel }}
            </span>
            @if ($program->language)
                <span class="inline-block bg-white/20 backdrop-blur ring-1 ring-white/30 text-white text-xs font-semibold px-3 py-1 rounded-full">
                    {{ $langLabel }}
                </span>
            @endif
            @if ($program->field)
                <span class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full"
                      style="background-color: {{ $program->field->color }}; color: white;">
                    {{ $program->field->icon }} {{ $program->field->name }}
                </span>
            @endif
        </div>

        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-3 drop-shadow">
            {{ $program->name_de }}
        </h1>

        @if ($program->degree_specification)
            <p class="text-primary-100 text-lg mb-3">{{ $program->degree_specification }}</p>
        @endif

        <div class="flex flex-wrap gap-2 mt-4">
            <a href="{{ route('universities.show', $program->university->slug) }}"
               class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25 hover:bg-white/25 text-sm">
                @if($program->university->logo_url)
                    <img src="{{ $program->university->logo_url }}" alt="" class="w-5 h-5 object-contain bg-white rounded-full p-0.5" loading="lazy" decoding="async"/>
                @endif
                <span>{{ $program->university->display_name }}</span>
            </a>
            @if($program->university->city)
                <a href="{{ route('cities.show', $program->university->city->slug) }}"
                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25 hover:bg-white/25 text-sm">
                    📍 {{ $program->university->city->name }}
                    @if($program->university->city->state) · {{ $program->university->city->state->name }}@endif
                </a>
            @endif
            @if ($program->location && $program->location !== $program->university->city?->name)
                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25 text-sm">
                    {{ __('Location:') }} {{ $program->location }}
                </span>
            @endif
        </div>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10 grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- ============ ANA İÇERİK ============ --}}
    <div class="lg:col-span-2 space-y-8">

        {{-- TR description --}}
        @if ($program->description)
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('About the Program') }}</h2>
                <div class="blog-content text-gray-800 leading-relaxed whitespace-pre-line">{!! app(\App\Services\Content\BlogAutoLinker::class)->process(nl2br(e($program->description))) !!}</div>
            </section>
        @endif

        {{-- EN description --}}
        @if ($program->description_en)
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                @if ($program->description)
                    <details class="group">
                        <summary class="cursor-pointer text-sm font-semibold uppercase tracking-wider text-gray-500 hover:text-gray-700 list-none flex items-center gap-2">
                            <span class="group-open:rotate-90 transition-transform">▶</span>
                            {{ __('Show the original English text') }}
                        </summary>
                        <div class="text-gray-700 leading-relaxed mt-4">{!! nl2br(e($program->description_en)) !!}</div>
                    </details>
                @else
                    <div class="flex items-start gap-3 mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-900">
                        <span class="text-lg">ℹ️</span>
                        <p>{{ __('A translated description for this program is not ready yet — the original English text is shown below.') }}</p>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">{{ __('Program Description') }} <span class="text-sm font-normal text-gray-500">({{ __('English') }})</span></h2>
                    <div class="text-gray-800 leading-relaxed">{!! nl2br(e($program->description_en)) !!}</div>
                @endif
            </section>
        @endif

        {{-- Başvuru şartları --}}
        @if ($program->qualification_requirements_tr)
            <section class="bg-accent-50 border border-accent-200 rounded-xl p-6">
                <h2 class="text-2xl font-bold text-accent-900 mb-3 flex items-center gap-2">
                    📋 {{ __('Application Requirements') }}
                </h2>
                <div class="text-accent-900 leading-relaxed whitespace-pre-line prose prose-sm max-w-none">{!! nl2br(e($program->qualification_requirements_tr)) !!}</div>
            </section>
        @endif

        {{-- Dil şartları --}}
        @if ($program->language_requirements_tr)
            <section class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h2 class="text-2xl font-bold text-blue-900 mb-3 flex items-center gap-2">
                    🗣️ {{ __('Language Requirements') }}
                </h2>
                <div class="text-blue-900 leading-relaxed whitespace-pre-line">{!! nl2br(e($program->language_requirements_tr)) !!}</div>
            </section>
        @endif

        {{-- Gerekli belgeler --}}
        @if ($program->required_documents_tr)
            <section class="bg-purple-50 border border-purple-200 rounded-xl p-6">
                <h2 class="text-2xl font-bold text-purple-900 mb-3 flex items-center gap-2">
                    📑 {{ __('Required Documents') }}
                </h2>
                <div class="text-purple-900 leading-relaxed whitespace-pre-line">{!! nl2br(e($program->required_documents_tr)) !!}</div>
            </section>
        @endif

        {{-- İlgili Meslekler (BERUFENET) --}}
        @if (! empty($program->subjects) || $program->field)
            <section class="bg-gradient-to-br from-emerald-50 to-white border border-emerald-200 rounded-xl p-6">
                <h2 class="text-2xl font-bold text-emerald-900 mb-2 flex items-center gap-2">
                    💼 {{ __('Which Professions Does This Program Open Up?') }}
                </h2>
                <p class="text-sm text-emerald-800 mb-4">
                    {{ __('Related profession searches from Bundesagentur für Arbeit (BERUFENET) data:') }}
                </p>
                <div class="flex flex-wrap gap-2">
                    @if ($program->field)
                        <a href="{{ route('professions.index', ['q' => $program->field->name_de]) }}"
                           class="inline-block bg-white border border-emerald-300 hover:border-emerald-500 hover:bg-emerald-50 text-emerald-900 px-3 py-1.5 rounded-full text-sm font-semibold transition">
                            🔎 {{ __('See :field professions', ['field' => $program->field->name]) }}
                        </a>
                    @endif
                    @foreach (array_slice($program->subjects ?? [], 0, 4) as $subj)
                        @if ($subj)
                            <a href="{{ route('professions.index', ['q' => $subj]) }}"
                               class="inline-block bg-white border border-gray-200 hover:border-emerald-400 hover:bg-emerald-50 text-gray-800 px-3 py-1.5 rounded-full text-sm transition">
                                "{{ $subj }}"
                            </a>
                        @endif
                    @endforeach
                </div>
                <a href="{{ route('professions.index') }}" class="inline-block mt-4 text-sm font-semibold text-emerald-700 hover:text-emerald-900">
                    {{ __('Explore all professions') }} →
                </a>
            </section>
        @endif

        {{-- Subjects --}}
        @if (! empty($program->subjects))
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3">{{ __('Subjects / Topic Areas') }}</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($program->subjects as $subj)
                        @if ($subj)
                            <span class="inline-block bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1.5 rounded-full">{{ $subj }}</span>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Related programs --}}
        @if ($related->isNotEmpty())
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Similar Programs') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($related as $r)
                        <a href="{{ route('programs.show', $r->slug) }}"
                           class="block bg-gray-50 hover:bg-primary-50 border border-gray-200 hover:border-primary-400 rounded-lg p-4 transition">
                            <p class="font-semibold text-gray-900 leading-tight mb-1">{{ $r->name_de }}</p>
                            <p class="text-xs text-gray-600">{{ $r->university->name_de }}</p>
                            @if ($r->degree_specification)
                                <p class="text-xs text-gray-500 mt-1">{{ $r->degree_specification }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

    </div>

    {{-- ============ SIDEBAR ============ --}}
    <div class="space-y-6">

        {{-- Quick facts --}}
        <aside class="bg-white border border-gray-200 rounded-xl p-6 sticky top-20">
            <h3 class="font-bold text-gray-900 mb-4 text-lg">{{ __('Quick Facts') }}</h3>
            <dl class="space-y-3 text-sm">

                <div>
                    <dt class="text-gray-500">{{ __('University') }}</dt>
                    <dd class="font-semibold text-gray-900">
                        <a href="{{ route('universities.show', $program->university->slug) }}" class="hover:text-primary-700">
                            {{ $program->university->display_name }}
                        </a>
                    </dd>
                </div>

                @if ($program->university->city)
                    <div>
                        <dt class="text-gray-500">{{ __('City / State') }}</dt>
                        <dd class="font-semibold text-gray-900">
                            {{ $program->university->city->name }}@if ($program->university->city->state), {{ $program->university->city->state->name }}@endif
                        </dd>
                    </div>
                @endif

                <div>
                    <dt class="text-gray-500">{{ __('Degree') }}</dt>
                    <dd class="font-semibold text-gray-900">
                        {{ $degreeLabel }}
                        @if ($program->degree_specification)
                            <span class="block text-xs text-gray-600 font-normal">{{ $program->degree_specification }}</span>
                        @endif
                    </dd>
                </div>

                @if ($program->language)
                    <div>
                        <dt class="text-gray-500">{{ __('Language of Instruction') }}</dt>
                        <dd class="font-semibold text-gray-900">{{ $langLabel }}</dd>
                    </div>
                @endif

                @if ($program->duration_semesters)
                    <div>
                        <dt class="text-gray-500">{{ __('Duration') }}</dt>
                        <dd class="font-semibold text-gray-900">{{ $program->duration_semesters }} {{ __('semesters') }}</dd>
                    </div>
                @endif

                @if (! is_null($program->tuition_fee_eur))
                    <div>
                        <dt class="text-gray-500">{{ __('Tuition Fee') }}</dt>
                        <dd class="font-semibold text-gray-900">
                            @if ($program->tuition_fee_eur == 0)
                                <span class="text-green-700">{{ __('Free') }}</span>
                            @else
                                {{ number_format($program->tuition_fee_eur, 0, ',', '.') }} € / {{ __('semester') }}
                            @endif
                        </dd>
                    </div>
                @endif

                @if (! is_null($program->cost_per_semester_eur))
                    <div>
                        <dt class="text-gray-500">Semester Beitrag</dt>
                        <dd class="font-semibold text-gray-900">{{ number_format($program->cost_per_semester_eur, 0, ',', '.') }} € / {{ __('semester') }}</dd>
                    </div>
                @endif

                @if (! is_null($program->application_fee_eur))
                    <div>
                        <dt class="text-gray-500">{{ __('Application Fee') }}</dt>
                        <dd class="font-semibold text-gray-900">
                            @if ($program->application_fee_eur == 0)
                                {{ __('None') }}
                            @else
                                {{ number_format($program->application_fee_eur, 0, ',', '.') }} €
                            @endif
                        </dd>
                    </div>
                @endif

                @if ($program->application_deadline_winter)
                    <div>
                        <dt class="text-gray-500">{{ __('Winter Semester Deadline') }}</dt>
                        <dd class="font-semibold text-gray-900">{{ $program->application_deadline_winter->format('d.m.Y') }}</dd>
                    </div>
                @endif

                @if ($program->application_deadline_summer)
                    <div>
                        <dt class="text-gray-500">{{ __('Summer Semester Deadline') }}</dt>
                        <dd class="font-semibold text-gray-900">{{ $program->application_deadline_summer->format('d.m.Y') }}</dd>
                    </div>
                @endif

                @if (! is_null($program->nc_value))
                    <div>
                        <dt class="text-gray-500">{{ __('NC (Numerus Clausus)') }}</dt>
                        <dd class="font-semibold text-gray-900">{{ number_format($program->nc_value, 2, ',', '') }}</dd>
                    </div>
                @endif

                @if ($program->admission_mode)
                    <div>
                        <dt class="text-gray-500">{{ __('Admission Type') }}</dt>
                        <dd class="font-semibold text-gray-900">{{ $program->admission_mode }}</dd>
                    </div>
                @endif

                @if ($program->university->is_uni_assist_member)
                    <div class="pt-2 mt-3 border-t border-gray-100">
                        <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-green-700">
                            ✓ {{ __('Uni-Assist member') }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Applications go through Uni-Assist.') }}</p>
                    </div>
                @endif

                {{-- NC durumu --}}
                <div class="pt-2 mt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-500 mb-1">🔓 {{ __('NC (Zulassungsmodus)') }}</p>
                    @if ($program->admission_mode === 'zulassungsfrei')
                        <p class="text-sm font-semibold text-emerald-700">{{ __('NC Frei (Zulassungsfrei)') }} ✓</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ __('You can apply to this program without an NC.') }}</p>
                    @elseif ($program->admission_mode === 'oertlich')
                        <p class="text-sm font-semibold text-orange-700">Örtlich zulassungsbeschränkt</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ __('This university has its own NC criteria.') }}</p>
                    @elseif ($program->admission_mode === 'bundesweit')
                        <p class="text-sm font-semibold text-red-700">Bundesweit zulassungsbeschränkt</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ __('Nationwide quota via Hochschulstart.') }}</p>
                    @else
                        <p class="text-xs text-gray-400 italic mb-1">{{ __('Information not in our database') }}</p>
                    @endif
                    @if ($program->admission_summary)
                        <p class="text-xs text-gray-600 mt-1">{{ $program->admission_summary }}</p>
                    @endif
                    @if (! is_null($program->nc_value))
                        <p class="text-xs text-gray-700 mt-1">{{ __('Last cut-off NC:') }} <strong>{{ number_format($program->nc_value, 2, ',', '') }}</strong></p>
                    @endif
                    <a href="{{ hochschulkompass_url($program->name_de) }}"
                       target="_blank" rel="noopener"
                       class="inline-flex items-center gap-1 text-xs text-blue-700 hover:text-blue-900 hover:underline mt-2">
                        {{ __('Up-to-date info on Hochschulkompass') }} ↗
                    </a>
                </div>
            </dl>

            <div class="mt-5 space-y-2">
                <x-favorite-button :model="$program" type="program" size="lg" :block="true" />
                <a href="{{ route('universities.show', $program->university->slug) }}"
                   class="block text-center bg-primary-600 hover:bg-primary-700 text-white font-semibold px-4 py-3 rounded-lg transition">
                    {{ __('Go to university page') }} →
                </a>
            </div>
        </aside>
    </div>
</div>

{{-- ════════════════════════════════════════════════════ --}}
{{-- ŞEHİR + ÜNİVERSİTE BAĞLAMI (content_blocks teaser) --}}
{{-- ════════════════════════════════════════════════════ --}}
@if (app()->getLocale() === 'tr' && $program->university->city && (!empty($city_cost) || !empty($city_places) || !empty($uni_faq)))
{{-- Content blocks (cost/places/faq) sadece TR — JSON içerikleri Türkçe yazılmış --}}
<section class="bg-gray-50 border-t border-gray-200 py-12">
    <div class="max-w-[1400px] mx-auto px-4 space-y-8">

        <header class="mb-2">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">{{ __('Understand this program in context') }}</h2>
            <p class="text-gray-600 mt-1">{{ __('Highlights about living in :city and :uni', ['city' => $program->university->city->name, 'uni' => $program->university->name_de]) }}</p>
        </header>

        {{-- ───── Şehirde yaşam maliyeti teaser ───── --}}
        @if (!empty($city_cost))
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-baseline justify-between flex-wrap gap-2 mb-3">
                    <h3 class="text-xl font-bold text-gray-900">💶 {{ $program->university->city->name }} — {{ $city_cost['h'] ?? __('Monthly cost of living') }}</h3>
                    <a href="{{ route('cities.show', $program->university->city->slug) }}"
                       class="text-sm text-primary-600 hover:underline font-semibold">{{ __('See city guide') }} →</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach (array_slice($city_cost['items'] ?? [], 0, 6) as $item)
                        <div class="bg-gray-50 rounded-lg p-3 ring-1 ring-gray-200">
                            <div class="text-xs text-gray-600 uppercase tracking-wider font-medium">{{ $item['label'] ?? '' }}</div>
                            <div class="text-lg font-bold mt-1 text-gray-900">{{ $item['amount'] ?? '—' }} <span class="text-sm text-gray-500">{{ $city_cost['currency'] ?? '€' }}</span></div>
                        </div>
                    @endforeach
                </div>
                @if (!empty($city_cost['total']))
                    <div class="mt-3 flex items-center justify-end gap-2 text-sm">
                        <span class="text-gray-700">{{ __('Average total:') }}</span>
                        <strong class="text-primary-700 text-lg">{{ $city_cost['total'] }} {{ $city_cost['currency'] ?? '€' }} / {{ __('month') }}</strong>
                    </div>
                @endif
            </div>
        @endif

        {{-- ───── Şehirde gezilecek/öğrenci yerleri ───── --}}
        @if (!empty($city_places) && !empty($city_places['items']))
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-baseline justify-between flex-wrap gap-2 mb-3">
                    <h3 class="text-xl font-bold text-gray-900">📍 {{ $program->university->city->name }} — {{ __('Places to Visit') }}</h3>
                    <a href="{{ route('cities.show', $program->university->city->slug) }}"
                       class="text-sm text-primary-600 hover:underline font-semibold">{{ __('See all') }} →</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @php
                        $icons = ['library' => '📚', 'museum' => '🏛️', 'square' => '🏙️', 'park' => '🌳', 'landmark' => '🗿', 'cafe' => '☕'];
                    @endphp
                    @foreach (array_slice($city_places['items'], 0, 4) as $place)
                        <div class="flex items-start gap-3 bg-gray-50 rounded-lg p-3 ring-1 ring-gray-200">
                            <div class="text-xl shrink-0">{{ $place['icon'] ?? $icons[$place['type'] ?? ''] ?? '📍' }}</div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-900 text-sm">{{ $place['name'] ?? '' }}</div>
                                @if (!empty($place['description']))
                                    <p class="text-xs text-gray-600 mt-0.5 line-clamp-2">{{ $place['description'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ───── Üniversite FAQ teaser ───── --}}
        @if (!empty($uni_faq) && !empty($uni_faq['items']))
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-baseline justify-between flex-wrap gap-2 mb-3">
                    <h3 class="text-xl font-bold text-gray-900">❓ {{ $program->university->display_name }} — {{ __('Frequently Asked') }}</h3>
                    <a href="{{ route('universities.show', $program->university->slug) }}"
                       class="text-sm text-primary-600 hover:underline font-semibold">{{ __('See all') }} →</a>
                </div>
                <div class="space-y-3">
                    @foreach ($uni_faq['items'] as $faq)
                        <details class="group bg-gray-50 ring-1 ring-gray-200 rounded-lg p-3 open:ring-primary-300">
                            <summary class="cursor-pointer font-semibold text-gray-900 flex items-center justify-between list-none">
                                <span>{{ $faq['q'] ?? '' }}</span>
                                <span class="text-primary-600 group-open:rotate-180 transition shrink-0 ml-3">▼</span>
                            </summary>
                            <div class="mt-2 text-sm text-gray-800 leading-relaxed">
                                {!! \Illuminate\Support\Str::markdown($faq['a'] ?? '') !!}
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endif

{{-- Featured-snippet box (AIO target: Q + concise answer + steps) --}}
<div class="max-w-4xl mx-auto px-4">
    <x-featured-snippet
        :question="__('How do I apply to :program at :uni?', ['program' => $program->name_de, 'uni' => $program->university->display_name])"
        :answer="__('Most international applicants apply via uni-assist (document verification + APS for some countries). Prepare your secondary/Bachelor diploma, transcripts, language certificate (DSH/TestDaF or IELTS/TOEFL), motivation letter and CV. Non-EU students also need a Sperrkonto (~11,904 EUR/year) for the visa.')"
        :steps="[
            ['title' => __('Check requirements'), 'description' => __('Verify your degree is recognised + meet language/grade thresholds.')],
            ['title' => __('Get language certificate'), 'description' => __('TestDaF / DSH for German programmes, IELTS / TOEFL for English.')],
            ['title' => __('Submit via uni-assist (non-EU)'), 'description' => __('Document review + APS if your country requires it (China, India, Pakistan, Vietnam, etc.).')],
            ['title' => __('Apply directly or via uni portal'), 'description' => __('Some universities accept direct applications — check the official programme page.')],
            ['title' => __('Open Sperrkonto + apply for visa'), 'description' => __('After admission, deposit 11,904 EUR, get health insurance, book embassy appointment.')],
        ]"
    />
</div>

{{-- Auto-generated FAQ (AIO + Featured Snippet eligibility) — multi-lang via __() --}}
<x-faq-section
    :title="__('Frequently Asked Questions')"
    :subtitle="__('Quick answers about :program at :uni', ['program' => $program->name_de, 'uni' => $program->university->display_name])"
    :faqs="\App\Support\PageFaq::forProgram($program)"
/>

@endsection
