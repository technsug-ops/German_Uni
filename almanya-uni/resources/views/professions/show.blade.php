@extends('layouts.app')

@php
    $typeLabels = [
        'ausbildung'    => ['Ausbildung', 'wrench-screwdriver'],
        'studienberuf'  => ['Studienberuf', 'academic-cap'],
        'weiterbildung' => ['Weiterbildung', 'chart-bar'],
        'grundberuf'    => ['Grundberuf', 'briefcase'],
        'other'         => [__('Other'), 'book-open'],
    ];
    [$typeLabel, $typeIcon] = $typeLabels[$profession->type ?? 'other'];

    $title = ($profession->name_tr ?: $profession->name) . ' — ' . __('Profession Description') . '  — ' . brand('name');
    $description = $profession->description
        ? \Illuminate\Support\Str::limit($profession->description, 160)
        : ($profession->clean_steckbrief
            ? \Illuminate\Support\Str::limit($profession->clean_steckbrief, 160)
            : ($profession->description
                ? \Illuminate\Support\Str::limit($profession->description, 160)
                : __('Description, education path, and tasks for the profession :name in Germany.', ['name' => $profession->name])));
@endphp

@section('title', $title)
<x-seo :title="$profession->name" :description="$description" :image="route('og.image', ['type' => 'profession', 'slug' => $profession->slug . '.png'])" />

@php
    $occupationName = $profession->name_tr
        ? $profession->name_tr . ' (' . $profession->name . ')'
        : $profession->name;

    $occupationDesc = $profession->description
        ?: ($profession->clean_steckbrief
            ?: ($profession->description
                ?: __('The profession :name in Germany', ['name' => $profession->name])));

    $jsonLd = [
        '@context'          => 'https://schema.org',
        '@type'             => 'Occupation',
        'name'              => $occupationName,
        'description'       => \Illuminate\Support\Str::limit($occupationDesc, 500),
        'occupationLocation' => [
            '@type'       => 'Country',
            'name'        => 'Germany',
            'addressCountry' => 'DE',
        ],
        'inLanguage'        => ['de', 'tr'],
        'url'               => url()->current(),
    ];

    if ($profession->kldb_code) {
        $jsonLd['occupationalCategory'] = [
            '@type'     => 'CategoryCode',
            'codeValue' => $profession->kldb_code,
            'inCodeSet' => [
                '@type' => 'CategoryCodeSet',
                'name'  => 'Klassifikation der Berufe (KldB 2010)',
                'url'   => 'https://statistik.arbeitsagentur.de/Statistikdaten/Detail/Aktuell/iiia6/berichte-broschueren/klassifikation-der-berufe/kldb-2010.pdf',
            ],
        ];
    }

    if ($profession->field?->name_tr) {
        $jsonLd['skills'] = $profession->field->name;
    }

    if (! empty($pathwayPrograms) && $pathwayPrograms->isNotEmpty()) {
        $jsonLd['educationRequirements'] = $pathwayPrograms->map(fn ($p) => [
            '@type'       => 'EducationalOccupationalProgram',
            'name'        => $p->name,
            'programType' => $p->degree,
            'url'         => route('programs.show', $p->slug),
        ])->take(5)->values()->all();
    }

    $breadcrumbItems = [
        ['name' => __('Home'),  'url' => route('home')],
        ['name' => __('Professions'),  'url' => route('professions.index')],
    ];
    if ($profession->field) {
        $breadcrumbItems[] = ['name' => $profession->field->name, 'url' => route('fields.show', $profession->field->slug)];
    }
    $breadcrumbItems[] = ['name' => $profession->name, 'url' => url()->current()];

    $breadcrumbJsonLd = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => array_map(
            fn ($i, $b) => [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => $b['name'],
                'item'     => $b['url'],
            ],
            array_keys($breadcrumbItems),
            $breadcrumbItems
        ),
    ];
@endphp

@push('head')
<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumbJsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
<section class="relative text-white overflow-hidden"
         style="background: linear-gradient(135deg, {{ $profession->field?->color ?? '#1E40AF' }}, {{ $profession->field?->color ?? '#1E40AF' }}cc);">
    <div class="relative max-w-[1400px] mx-auto px-4 py-10 md:py-14">
        <nav class="text-sm text-white/80 mb-3">
            <a href="/" class="hover:text-white">{{ __('Home') }}</a>
            <span class="mx-2 opacity-60">›</span>
            <a href="{{ route('professions.index') }}" class="hover:text-white">{{ __('Professions') }}</a>
            @if ($profession->field)
                <span class="mx-2 opacity-60">›</span>
                <a href="{{ route('fields.show', $profession->field->slug) }}" class="hover:text-white">{{ $profession->field->name }}</a>
            @endif
            <span class="mx-2 opacity-60">›</span>
            <span class="text-white">{{ $profession->name }}</span>
        </nav>

        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight drop-shadow mb-2">{{ $profession->name }}</h1>
        @if ($profession->short_name && $profession->short_name !== $profession->name)
            <p class="text-white/85 text-lg mb-3">{{ $profession->short_name }}</p>
        @endif

        <div class="flex flex-wrap gap-2 text-sm mt-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25">
                <x-svg-icon :name="$typeIcon" class="w-4 h-4" /> {{ $typeLabel }}
            </span>
            @if ($profession->field)
                <a href="{{ route('fields.show', $profession->field->slug) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur ring-1 ring-white/25 hover:bg-white/25">
                    {!! e_icon($profession->field->icon, 'w-4 h-4') !!} {{ $profession->field->name }}
                </a>
            @endif
            @if ($profession->kldb_code)
                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-white/10 text-xs font-mono">
                    KldB {{ $profession->kldb_code }}
                </span>
            @endif
        </div>
    </div>
</section>

<div class="max-w-[1400px] mx-auto px-4 py-10 grid grid-cols-1 lg:grid-cols-3 gap-8">
    <main class="lg:col-span-2 space-y-6">
        @if ($profession->description)
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('What is :name?', ['name' => $profession->name_tr ?: $profession->name]) }}</h2>
                <div class="blog-content text-gray-800 leading-relaxed whitespace-pre-line prose prose-sm max-w-none">{!! app(\App\Services\Content\BlogAutoLinker::class)->process(nl2br(e($profession->description))) !!}</div>
            </section>
        @endif

        @if ($profession->clean_steckbrief)
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-3">Steckbrief</h2>
                <p class="text-gray-800 leading-relaxed">{{ $profession->clean_steckbrief }}</p>
            </section>
        @endif

        @if ($profession->description)
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('Beschreibung (German)') }}</h2>
                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $profession->description }}</p>
            </section>
        @endif

        {{-- Bu mesleğe götüren programlar (alan üzerinden) --}}
        @if (!empty($pathwayPrograms) && $pathwayPrograms->isNotEmpty())
            <section class="bg-gradient-to-br from-amber-50 to-orange-50 ring-1 ring-amber-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-baseline justify-between mb-4 flex-wrap gap-2">
                    <h2 class="text-xl font-bold text-gray-900 inline-flex items-center gap-2"><x-svg-icon name="academic-cap" class="w-5 h-5 text-amber-700" /> {{ __('Programs that lead to this profession') }}</h2>
                    @if($profession->field)
                        <a href="{{ route('fields.show', $profession->field->slug) }}" class="text-sm text-primary-600 hover:underline">
                            {{ __(':field field', ['field' => $profession->field->name]) }} →
                        </a>
                    @endif
                </div>
                <p class="text-sm text-gray-700 mb-4">
                    {!! __('The profession <strong>:name</strong> in Germany is generally reached through', ['name' => $profession->name]) !!}
                    @if($profession->field) {!! __('programs in the <strong>:field</strong> field:', ['field' => $profession->field->name]) !!} @else {{ __('these programs:') }} @endif
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($pathwayPrograms as $p)
                        <a href="{{ route('programs.show', $p->slug) }}"
                           class="group flex items-start gap-3 bg-white rounded-lg p-3 ring-1 ring-amber-100 hover:ring-amber-300 hover:shadow-md transition">
                            @if($p->university?->logo_url)
                                <img src="{{ $p->university->logo_url }}" alt="" class="w-10 h-10 object-contain bg-white rounded shrink-0 p-0.5" loading="lazy" decoding="async"/>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 group-hover:text-amber-700 leading-snug text-sm">{{ $p->name }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ ucfirst($p->degree) }}@if($p->university) · {{ $p->university->display_name }}@endif
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if (! empty($profession->info_fields))
            <section class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Detailed Information') }}</h2>
                <div class="space-y-4">
                    @foreach ($profession->info_fields as $heading => $content)
                        @if ($content && mb_strlen($content) > 5)
                            <details class="group border-b border-gray-100 pb-3 last:border-0">
                                <summary class="cursor-pointer font-semibold text-gray-900 hover:text-primary-700 transition flex items-center gap-2 list-none">
                                    <span class="group-open:rotate-90 transition-transform text-gray-400">▶</span>
                                    {{ $heading }}
                                </summary>
                                <p class="mt-2 text-gray-700 text-sm leading-relaxed pl-6">{{ \Illuminate\Support\Str::limit($content, 1500) }}</p>
                            </details>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        <p class="text-xs text-gray-500 px-1">
            {{ __('Source:') }} <a href="https://web.arbeitsagentur.de/berufenet/beruf/{{ $profession->berufenet_id }}" target="_blank" rel="noopener" class="underline">BERUFENET</a> · Bundesagentur für Arbeit
        </p>
    </main>

    <aside class="space-y-6">
        <div class="bg-white border border-gray-200 rounded-xl p-6 sticky top-20">
            <h3 class="font-bold text-gray-900 mb-3">{{ __('Quick Info') }}</h3>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500">{{ __('Type') }}</dt>
                    <dd class="font-semibold inline-flex items-center gap-1.5"><x-svg-icon :name="$typeIcon" class="w-4 h-4 text-gray-600" /> {{ $typeLabel }}</dd>
                </div>
                @if ($profession->kldb_code)
                    <div>
                        <dt class="text-gray-500">KldB-Kod</dt>
                        <dd class="font-mono font-semibold">{{ $profession->kldb_code }}</dd>
                    </div>
                @endif
                @if ($profession->cluster)
                    <div>
                        <dt class="text-gray-500">Berufskurzgruppe</dt>
                        <dd class="font-semibold">{{ $profession->cluster }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-gray-500">BERUFENET ID</dt>
                    <dd class="font-mono text-xs text-gray-700">{{ $profession->berufenet_id }}</dd>
                </div>
            </dl>

            <div class="mt-5 space-y-2">
                <x-favorite-button :model="$profession" type="profession" size="lg" :block="true" />
                <a href="https://web.arbeitsagentur.de/berufenet/beruf/{{ $profession->berufenet_id }}"
                   target="_blank" rel="noopener"
                   class="block text-center bg-primary-600 hover:bg-primary-700 text-white font-semibold px-4 py-2.5 rounded-lg transition">
                    {{ __('Open on BERUFENET') }} →
                </a>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h3 class="font-bold text-gray-900 mb-3 text-sm uppercase tracking-wider">{{ __('Related Professions') }}</h3>
                <ul class="space-y-2 text-sm">
                    @foreach ($related as $r)
                        <li>
                            <a href="{{ route('professions.show', $r->slug) }}" class="text-primary-700 hover:text-primary-900 hover:underline">
                                {{ $r->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </aside>
</div>

{{-- Auto-FAQ (AIO + Featured Snippet) — type-aware per profession --}}
<x-faq-section
    :title="__('Frequently Asked Questions about :name', ['name' => $profession->name_tr ?: $profession->name])"
    :subtitle="__('Education path, salary, recognition, and entry routes for foreigners')"
    :faqs="\App\Support\PageFaq::forProfession($profession)"
/>
@endsection
