{{--
    İngilizce eğitim koleksiyonu: resmî sayfada doğrulanmış program örnekleri (UniversityCollections → 'examples').
    Kurum seçimi sayfasıdır; program listesi /english-taught'tadır. Örnekler kurumun bütün İngilizce programları değildir.
    Program adları resmî addır; diğer metinler İngilizce __() anahtarı (TR/DE: lang/*.json). Bağlantılar resmî program
    sayfasına gider (katalogda aynı programın dil etiketi çelişen çift kayıtları olabildiği için).
--}}
@php
    $bySlug = collect($universities->items())->keyBy('slug');
    $locale = app()->getLocale();
    $checkedLabel = fn (string $date) => \Illuminate\Support\Carbon::parse($date)->locale($locale)->isoFormat('LL');
    $teachingChip = [
        'english'   => ['label' => __('English-taught programme'), 'class' => 'bg-emerald-100 text-emerald-800'],
        'bilingual' => ['label' => __('Bilingual: German and English'), 'class' => 'bg-amber-100 text-amber-800'],
    ];
    $guideSlugs = [
        'master' => [
            'tr' => 'english-masters-in-germany-without-german-knowledge-finding-programs-and-application',
            'en' => 'english-masters-in-germany-without-german-knowledge-finding-programs-and-application-en',
            'de' => 'english-masters-in-germany-without-german-knowledge-finding-programs-and-application-de',
        ],
        'language' => [
            'tr' => ['goethe-telc-testdaf-dsh-difference-german-language-exam-comparison-for-turkish', 'goethe-telc-testdaf-dsh-differences-german-language-exam-comparison-for-turkish'],
            'en' => ['goethe-telc-testdaf-dsh-difference-german-language-exam-comparison-for-turkish-en', 'goethe-telc-testdaf-dsh-differences-german-language-exam-comparison-for-turkish-en'],
            'de' => ['goethe-telc-testdaf-dsh-difference-german-language-exam-comparison-for-turkish-de', 'goethe-telc-testdaf-dsh-differences-german-language-exam-comparison-for-turkish-de'],
        ],
    ];
    $guidePosts = \App\Models\Post::published()->where('locale', $locale)
        ->whereIn('slug', collect($guideSlugs)->flatMap(fn ($g) => (array) ($g[$locale] ?? []))->all())
        ->get()->keyBy('slug');
    // Prod ve lokal slug adayları (yazı prod'da yeniden adlandırıldı); ilk bulunan kullanılır.
    $guide = fn (string $key) => collect((array) ($guideSlugs[$key][$locale] ?? []))->map(fn ($s) => $guidePosts[$s] ?? null)->filter()->first();
@endphp

<section class="bg-gray-50 py-10">
    <div class="max-w-[1400px] mx-auto px-4">
        <div class="max-w-3xl mb-6">
            <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-2">{{ __('Checked examples of English-taught programmes') }}</h2>
            <p class="text-sm text-gray-600 leading-relaxed">{{ __('Language rules differ by programme, even within the same university. One example asks for German A2 at application, another accepts a previous degree taught in English instead of a test. Always read the requirements of the programme you choose.') }}</p>
        </div>

        <div class="grid gap-6">
            @foreach ($collection['examples'] as $slug => $programs)
                @php $u = $bySlug[$slug] ?? null; @endphp
                @if ($u)
                    <article class="rounded-2xl bg-white ring-1 ring-gray-200 p-5 md:p-6">
                        <header class="flex flex-wrap items-start justify-between gap-3 mb-4">
                            <div class="min-w-0">
                                <h3 class="text-lg font-bold text-gray-900"><a href="{{ route('universities.show', $slug) }}" class="hover:text-primary-700">{{ $u['name_de'] }}</a></h3>
                                @if (! empty($u['city_name']))
                                    <p class="text-xs text-gray-500 inline-flex items-center gap-1"><x-svg-icon name="map-pin" class="w-3 h-3" /> {{ $u['city_name'] }}</p>
                                @endif
                            </div>
                            <a href="{{ route('universities.show', $slug) }}" class="shrink-0 inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold transition">{{ __('University profile and programmes') }} →</a>
                        </header>

                        <div class="grid gap-4 lg:grid-cols-2">
                            @foreach ($programs as $p)
                                @php $chip = $teachingChip[$p['teaching']] ?? $teachingChip['english']; @endphp
                                <div class="rounded-xl ring-1 ring-gray-200 p-4 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="font-semibold text-gray-900">{{ $p['program'] }}</span>
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-gray-100 text-gray-700">{{ $p['degree'] }}</span>
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $chip['class'] }}">{{ $chip['label'] }}</span>
                                    </div>
                                    <dl class="text-sm grid gap-2">
                                        <div><dt class="font-semibold text-gray-700">{{ __('Language of instruction') }}</dt><dd class="text-gray-700">{{ __($p['teaching_note']) }}</dd></div>
                                        <div><dt class="font-semibold text-gray-700">{{ __('English proof for the application') }}</dt><dd class="text-gray-700">{{ __($p['english']) }}</dd></div>
                                        <div><dt class="font-semibold text-gray-700">{{ __('German requirement') }}</dt><dd class="text-gray-700">{{ __($p['german']) }}</dd></div>
                                    </dl>
                                    <p class="text-xs text-gray-500 mt-3 break-words">
                                        <a href="{{ $p['url'] }}" rel="noopener" target="_blank" class="text-primary-700 underline hover:text-primary-900">{{ __('Official programme page') }}</a>
                                        @if (! empty($p['language_url']))
                                            · <a href="{{ $p['language_url'] }}" rel="noopener" target="_blank" class="text-primary-700 underline hover:text-primary-900">{{ __('Official language requirements') }}</a>
                                        @endif
                                        · {{ __('Checked: :date', ['date' => $checkedLabel($p['checked'])]) }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endif
            @endforeach
        </div>

        <p class="text-xs text-gray-500 mt-5 max-w-3xl">{{ __('These are examples, not a complete list of each university\'s English-taught programmes. Requirements can change between intakes, so confirm them on the official page before you apply.') }}</p>

        <div class="mt-8 grid gap-4 md:grid-cols-2">
            <div class="rounded-xl bg-white ring-1 ring-gray-200 p-5">
                <h2 class="font-bold text-gray-900 mb-2">{{ __('Next step: find a programme') }}</h2>
                <p class="text-sm text-gray-600 mb-3">{{ __('Filter English-taught and bilingual Bachelor\'s and Master\'s programmes in our catalogue.') }}</p>
                <a href="{{ route('discover.english') }}" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold transition">{{ __('Explore English-taught programmes') }} →</a>
            </div>
            <div class="rounded-xl bg-white ring-1 ring-gray-200 p-5">
                <h2 class="font-bold text-gray-900 mb-2">{{ __('Related guides') }}</h2>
                <ul class="grid gap-2 text-sm">
                    @if ($g = $guide('master'))
                        <li><a href="{{ $g->publicUrl() }}" class="text-primary-700 underline hover:text-primary-900">{{ __('How to apply for an English-taught Master\'s') }}</a></li>
                    @endif
                    @if ($g = $guide('language'))
                        <li><a href="{{ $g->publicUrl() }}" class="text-primary-700 underline hover:text-primary-900">{{ __('Check language certificate requirements') }}</a></li>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('tools.language-certificates'))
                        <li><a href="{{ route('tools.language-certificates') }}" class="text-primary-700 underline hover:text-primary-900">{{ __('German language certificates for university: TestDaF, DSH, telc') }}</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</section>
