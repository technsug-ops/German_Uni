{{--
    Şartlı kabul koleksiyonu karar bölümü (UniversityCollections → 'paths').
    Her metin İngilizce __() anahtarıdır (TR/DE: lang/*.json). Kurum bilgileri resmî sayfalardan elle doğrulandı;
    kaynak URL ve kontrol tarihi her kartta görünür. Yeni kurum eklerken önce resmî kaynağı kontrol et.
--}}
@php
    $paths = $collection['paths'];
    $bySlug = collect($universities->items())->keyBy('slug');
    $locale = app()->getLocale();
    $checkedLabel = fn (string $date) => \Illuminate\Support\Carbon::parse($date)->locale($locale)->isoFormat('LL');

    // Rehber linkleri: yazı adresi her zaman Post::publicUrl() ile (dil başına ayrı slug). Yayında değilse link basılmaz.
    $guideSlugs = collect($paths['guides'] ?? [])->map(fn ($g) => $g['slugs'][$locale] ?? null)->filter()->values()->all();
    $guidePosts = $guideSlugs
        ? \App\Models\Post::published()->where('locale', $locale)->whereIn('slug', $guideSlugs)->get()->keyBy('slug')
        : collect();

    $sectionTone = [
        'conditional'     => ['chip' => 'bg-emerald-100 text-emerald-800', 'ring' => 'ring-emerald-200', 'head' => 'text-emerald-800'],
        'language-course' => ['chip' => 'bg-sky-100 text-sky-800',         'ring' => 'ring-sky-200',     'head' => 'text-sky-800'],
        'confused'        => ['chip' => 'bg-gray-200 text-gray-800',       'ring' => 'ring-gray-200',    'head' => 'text-gray-800'],
    ];
    $fieldLabels = [
        'scope'       => __('Who and which programmes'),
        'certificate' => __('German certificate due'),
        'place'       => __('Degree place reserved?'),
        'reapply'     => __('New application for the degree?'),
        'portal'      => __('Where to apply'),
    ];
@endphp

{{-- Kısa cevap + dört yolun tanımı --}}
<section class="bg-white border-b border-gray-200">
    <div class="max-w-[1400px] mx-auto px-4 py-8 grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
        <div class="min-w-0">
            <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-3">{{ __('Four routes that are often confused') }}</h2>
            <dl class="grid gap-3">
                @foreach ($paths['explainer'] as $e)
                    <div class="rounded-lg ring-1 ring-gray-200 p-4">
                        <dt class="font-semibold text-gray-900">{{ __($e['term']) }}</dt>
                        <dd class="text-sm text-gray-700 mt-1 leading-relaxed">{{ __($e['text']) }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
        <div class="min-w-0 grid gap-4 content-start">
            <div class="rounded-xl bg-amber-50 ring-1 ring-amber-200 p-5">
                <h2 class="font-bold text-amber-900 mb-2">{{ __('Language-course admission is not degree admission') }}</h2>
                <p class="text-sm text-amber-900 leading-relaxed">{{ __('A letter admitting you to a German course does not give you a place in the degree programme and does not guarantee a visa. Where a university reserves the place, it says so explicitly; this page only states it where the official page does.') }}</p>
            </div>
            <div class="rounded-xl ring-1 ring-gray-200 p-5">
                <h2 class="font-bold text-gray-900 mb-2">{{ __('How to use this comparison') }}</h2>
                <ul class="text-sm text-gray-700 space-y-2 list-disc pl-5">
                    <li>{{ __('Check whether your programme is admission-restricted (NC). Most routes below exclude NC programmes.') }}</li>
                    <li>{{ __('Note when the German certificate is due: with the application, at enrolment, or after a preparatory phase.') }}</li>
                    <li>{{ __('Confirm the current rules on the official page before you apply. Rules change between semesters.') }}</li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- Karşılaştırma tablosu (geniş ekranda tek bakış; mobilde yatay kaydırılır, aynı bilgiler aşağıdaki kartlarda da var) --}}
<section class="bg-gray-50 pt-10">
    <div class="max-w-[1400px] mx-auto px-4">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">{{ __('Comparison of the 9 universities') }}</h2>
        <p class="text-sm text-gray-600 mb-4">{{ __('Based on the official university pages linked in each row.') }}</p>
        <div class="overflow-x-auto rounded-xl ring-1 ring-gray-200 bg-white">
            <table class="w-full min-w-[960px] text-sm">
                <thead class="bg-gray-100 text-left text-gray-900">
                    <tr>
                        <th class="px-4 py-3 font-semibold">{{ __('University') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('Route') }}</th>
                        @foreach ($fieldLabels as $label)
                            <th class="px-4 py-3 font-semibold">{{ $label }}</th>
                        @endforeach
                        <th class="px-4 py-3 font-semibold">{{ __('Official source') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($paths['sections'] as $section)
                        @php $tone = $sectionTone[$section['key']] ?? $sectionTone['confused']; @endphp
                        @foreach ($section['items'] as $slug => $item)
                            @php $u = $bySlug[$slug] ?? null; @endphp
                            @continue(! $u)
                            <tr class="align-top">
                                <td class="px-4 py-3 font-semibold text-gray-900"><a href="{{ route('universities.show', $slug) }}" class="hover:text-primary-700 underline-offset-2 hover:underline">{{ $u['name_de'] }}</a></td>
                                <td class="px-4 py-3"><span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full {{ $tone['chip'] }}">{{ __($item['path']) }}</span></td>
                                @foreach (array_keys($fieldLabels) as $f)
                                    <td class="px-4 py-3 text-gray-800">{{ __($item[$f]) }}</td>
                                @endforeach
                                <td class="px-4 py-3 text-xs text-gray-600"><a href="{{ $item['source'] }}" target="_blank" rel="noopener" class="underline hover:text-primary-700">{{ parse_url($item['source'], PHP_URL_HOST) }}</a><br>{{ __('Checked :date', ['date' => $checkedLabel($item['checked'])]) }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- Yol gruplarına göre kurum kartları --}}
<section class="bg-gray-50 py-10">
    <div class="max-w-[1400px] mx-auto px-4 grid gap-10">
        @foreach ($paths['sections'] as $section)
            @php $tone = $sectionTone[$section['key']] ?? $sectionTone['confused']; @endphp
            <div id="{{ $section['key'] }}" class="scroll-mt-24">
                <h2 class="text-xl md:text-2xl font-bold mb-1 {{ $tone['head'] }}">{{ __($section['title']) }}</h2>
                <p class="text-sm text-gray-700 max-w-3xl mb-4 leading-relaxed">{{ __($section['lead']) }}</p>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($section['items'] as $slug => $item)
                        @php $u = $bySlug[$slug] ?? null; @endphp
                        @continue(! $u)
                        <article class="bg-white rounded-xl ring-1 {{ $tone['ring'] }} p-5 grid gap-3 min-w-0">
                            <div class="flex items-start gap-3">
                                @if (! empty($u['logo_url']))
                                    <img src="{{ $u['logo_url'] }}" alt="{{ $u['name_de'] }}" class="w-12 h-12 object-contain bg-gray-50 rounded p-1 shrink-0" loading="lazy" decoding="async">
                                @else
                                    <span class="w-12 h-12 rounded bg-primary-100 text-primary-700 flex items-center justify-center font-bold shrink-0">{{ mb_substr($u['name_de'], 0, 2) }}</span>
                                @endif
                                <div class="min-w-0">
                                    <h3 class="font-bold text-gray-900 leading-snug">{{ $u['name_de'] }}</h3>
                                    @if (! empty($u['city_name']))
                                        <div class="text-xs text-gray-500 inline-flex items-center gap-1"><x-svg-icon name="map-pin" class="w-3 h-3" /> {{ $u['city_name'] }}</div>
                                    @endif
                                    <div class="mt-1"><span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full {{ $tone['chip'] }}">{{ __($item['path']) }}</span></div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-800 leading-relaxed">{{ __($item['summary']) }}</p>
                            <dl class="grid gap-2 text-sm">
                                @foreach ($fieldLabels as $f => $label)
                                    <div class="grid grid-cols-1 sm:grid-cols-[11rem_minmax(0,1fr)] gap-x-3">
                                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500 pt-0.5">{{ $label }}</dt>
                                        <dd class="text-gray-800 min-w-0">{{ __($item[$f]) }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                            <div class="flex flex-wrap gap-2 pt-1">
                                <a href="{{ route('universities.show', $slug) }}" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold transition">{{ __('University profile and programmes') }} →</a>
                                <a href="{{ $item['source'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg ring-1 ring-gray-300 hover:ring-primary-400 text-sm text-gray-900 transition">{{ __('Official page') }} ↗</a>
                            </div>
                            <p class="text-xs text-gray-500">{{ __('Checked :date', ['date' => $checkedLabel($item['checked'])]) }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- İlgili rehberler (aynı dilde, yayındaysa) --}}
        @if ($guidePosts->isNotEmpty() || \Illuminate\Support\Facades\Route::has('tools.language-certificates'))
            <div class="rounded-xl bg-white ring-1 ring-gray-200 p-5">
                <h2 class="font-bold text-gray-900 mb-3">{{ __('Related guides') }}</h2>
                <ul class="grid gap-2 text-sm">
                    @foreach ($paths['guides'] ?? [] as $g)
                        @php $post = $guidePosts[$g['slugs'][$locale] ?? ''] ?? null; @endphp
                        @if ($post)
                            <li><a href="{{ $post->publicUrl() }}" class="text-primary-700 underline hover:text-primary-900">{{ __($g['title']) }}</a></li>
                        @endif
                    @endforeach
                    @if (\Illuminate\Support\Facades\Route::has('tools.language-certificates'))
                        <li><a href="{{ route('tools.language-certificates') }}" class="text-primary-700 underline hover:text-primary-900">{{ __('German language certificates for university: TestDaF, DSH, telc') }}</a></li>
                    @endif
                </ul>
            </div>
        @endif
    </div>
</section>
