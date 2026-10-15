<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\University;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UniversityWebController extends Controller
{
    private const ALLOWED_TYPES = ['public', 'private', 'applied_sciences', 'art', 'religion'];

    /**
     * Ana kuruma birleştirilip silinen alt-birim kayıtlarının slug'ları → kanonik slug (301).
     * [[duplicate-university-records]] · universities:subunits ile merge edildi (2026-08-04).
     *
     * NEDEN CONTROLLER'DA: routes/web.php'ye Route::redirect olarak eklenmişti ama HİÇ
     * çalışmadı — üni sayfaları Route::prefix('{locale}') grubunda ve /universities/{slugOrId}
     * route'u daha ÖNCE tanımlı olduğundan ilk eşleşen o oluyor, sonuç 404. Burada çözülünce
     * route sırasından bağımsız ve dört dilde birden çalışıyor.
     */
    public const MERGED_SLUGS = [
        'institut-fur-kunst-und-bildgeschichte-ikb-der-humboldt-universitat-zu-berlin-q75251125' => 'humboldt-universitat-zu-berlin',
        'technische-universitat-berlin-institut-fur-technische-akustik-q101385266' => 'technische-universitat-berlin',
        // 2026-10-09: aynı kurumun partner/DAAD kabuk kayıtları; programlar kanonik kayda taşındı, kabuk pasif kaldı
        // (database/migrations/2026_10_09_000700_merge_duplicate_university_records.php).
        'hochschule-fresenius-university-of-applied-sciences-partner-019de9f1' => 'hochschule-fresenius-partner-019ddbba',
        'anhalt-university-of-applied-sciences-partner-019de9f1' => 'hochschule-anhalt-q1622074',
        'paderborn-university-partner-019de9ee' => 'universitat-paderborn-q679134',
        'university-of-applied-sciences-emdenleer-partner-019de9f1' => 'hochschule-emdenleer-q1622096',
        'rheinmain-university-of-applied-sciences-partner-019de9f1' => 'hochschule-rheinmain-q542415',
        'ifs-internationale-filmschule-koln-partner-019de9f2' => 'ifs-internationale-filmschule-partner-019ddbba',
        'accadis-hochschule-bad-homburg-university-of-applied-sciences-partner-019de9f1' => 'accadis-hochschule-bad-homburg-partner-019ddbbb',
        'university-of-applied-sciences-ravensburg-weingarten-partner-019de9f1' => 'hochschule-ravensburg-weingarten-q2718933',
        'reutlingen-university-partner-019de9f1' => 'hochschule-reutlingen-q317125',
        'fachhochschule-wedel-university-of-applied-sciences-partner-019de9f2' => 'fachhochschule-wedel-partner-019ddbba',
        'furtwangen-university-partner-019de9f1' => 'hochschule-furtwangen-partner-019ddbba',
        'constructor-university' => 'constructor-university-bremen-partner-019ddbba',
        'dresden-international-university' => 'diu-dresden-international-university-gmbh-partner-019ddbba',
        'jade-university-of-applied-sciences-wilhelmshavenoldenburgelsfleth-partner-019de9f1' => 'jade-hochschule-partner-019ddbba',
        // 2026-10-09 ikinci tur (database/migrations/2026_10_09_001100_merge_more_duplicate_university_records.php).
        'hochschule-bochum-partner-019ddbba' => 'hochschule-fur-technik-wirtschaft-und-gesundheit-bochum-q1622078',
        'bochum-university-of-applied-sciences-partner-019de9f1' => 'hochschule-fur-technik-wirtschaft-und-gesundheit-bochum-q1622078',
        'fachhochschule-macromedia-q1346417' => 'macromedia-university-of-applied-sciences-hs366',
        'german-international-school-of-management-and-administration-gisma-partner-019ddbba' => 'gisma-university-of-applied-sciences-hs518',
        'brandenburg-university-of-technology-cottbus-senftenberg-partner-019de9f1' => 'brandenburgische-technische-universitat-cottbus-senftenberg-partner-019ddbba',
        'technical-university-of-applied-sciences-wildau-partner-019de9f1' => 'technische-hochschule-wildau-q686171',
        'hamburg-university-of-technology-partner-019de9f1' => 'technische-universitat-hamburg-q1060',
        'fau-erlangen-nurnberg-partner-019de9f1' => 'friedrich-alexander-universitat-erlangen-nurnberg-q40025',
        'freie-universitat-berlin-e-medien-q130548543' => 'freie-universitat-berlin',
        // 2026-10-14 SEO Recovery P0-4 (soft 404): programsız pasif kabuk kayıtlar → aynı kurumun aktif kaydı.
        // Elle doğrulandı (docs/SEO_RECOVERY_P0_4_RESULT.md §C3): aynı kurum, aynı şehir/web sitesi.
        'hochschule-kempten-partner-019ddbba' => 'hochschule-fur-angewandte-wissenschaften-kempten-q1622115',
        'baden-wuerttemberg-cooperative-state-university-heidenheim-partner-019de9f1' => 'duale-hochschule-baden-wurttemberg-heidenheim-q1262602',
        // HfPV Hessen 2022'de "Hessische Hochschule für öffentliches Management und Sicherheit" (HöMS) adını aldı;
        // eski kaydın web sitesi zaten hoems.hessen.de, yeni kayıt Wiesbaden'de ve aktif.
        'hesse-state-university-of-applied-sciences-for-police-and-public-administration-management-q1481465' => 'hessische-hochschule-fur-offentliches-management-und-sicherheit-partner-019ddbba',
    ];

    /**
     * Kalıcı olarak kaldırılmış, sitede karşılığı olmayan kayıtlar → 410 Gone (kayıt pasif kaldığı sürece).
     * SEO Recovery P0-4: 225 "Informationszentrum für Fremdsprachenforschung" bir yükseköğretim kurumu değil
     * (Marburg Üniversitesi'ne bağlı bilgi merkezi), programı yok; benzer isimli bir kuruma yönlendirmek yanlış sinyal olur.
     */
    public const GONE_SLUGS = [
        'informationszentrum-fur-fremdsprachenforschung-q820409',
    ];

    public function index(Request $request): View|\Illuminate\Http\Response
    {
        $query = University::query()->where('is_active', true);

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));
            // Kelime-bazlı arama: her kelime AYRI eşleşmeli (AND), ama herhangi bir alanda (OR).
            // Önceki tek-LIKE "TU münchen" gibi kısaltma+şehir aramalarında 0 sonuç veriyordu
            // ("Technische Universität München" tek bir alanda "TU münchen" substring'ini içermez).
            $tokens = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY) ?: [$search];
            foreach ($tokens as $token) {
                $query->where(function ($w) use ($token) {
                    $w->where('name_de', 'like', "%$token%")
                        ->orWhere('name_en', 'like', "%$token%")
                        ->orWhere('name_tr', 'like', "%$token%")
                        ->orWhere('short_name', 'like', "%$token%")
                        ->orWhere('description_de', 'like', "%$token%")
                        ->orWhereHas('city', fn ($c) => $c
                            ->where('name_de', 'like', "%$token%")
                            ->orWhere('name_tr', 'like', "%$token%")
                            ->orWhere('name_en', 'like', "%$token%"));
                });
            }
        }

        $type = $request->input('type');
        if ($type && in_array($type, self::ALLOWED_TYPES, true)) {
            $query->where('type', $type);
        }

        if ($request->filled('city')) {
            $city = $request->input('city');
            $query->whereHas('city', fn ($q) => $q->where('slug', $city)
                ->orWhere('name_de', 'like', "%$city%")
                ->orWhere('name_tr', 'like', "%$city%")
                ->orWhere('name_en', 'like', "%$city%"));
        }

        if ($request->filled('state')) {
            $state = $request->input('state');
            $query->whereHas('city.state', fn ($q) => $q->where('slug', $state)
                ->orWhere('name_de', 'like', "%$state%")
                ->orWhere('name_tr', 'like', "%$state%")
                ->orWhere('name_en', 'like', "%$state%"));
        }

        if ($request->filled('founded_min')) {
            $query->where('founded_year', '>=', $request->input('founded_min'));
        }

        if ($request->filled('founded_max')) {
            $query->where('founded_year', '<=', $request->input('founded_max'));
        }

        // İngilizce program filter — Türk öğrenciler için kritik
        if ($request->filled('english') && $request->input('english') === '1') {
            $query->whereHas('programs', fn ($p) => $p
                ->where('is_active', 1)
                ->whereIn('language', ['en', 'both']));
        }

        // NC-frei (admission_mode = zulassungsfrei) program sunan üniler
        if ($request->filled('nc_free') && $request->input('nc_free') === '1') {
            $query->whereHas('programs', fn ($p) => $p
                ->where('is_active', 1)
                ->where('admission_mode', 'zulassungsfrei'));
        }

        // Uni-assist üye — Türk öğrenciler için başvuru kolaylığı
        if ($request->filled('uni_assist') && $request->input('uni_assist') === '1') {
            $query->where('is_uni_assist_member', true);
        }

        // Üni boyutu (öğrenci sayısı)
        if ($request->filled('size')) {
            match ($request->input('size')) {
                'small'  => $query->where('student_count', '<', 5000),
                'medium' => $query->whereBetween('student_count', [5000, 20000]),
                'large'  => $query->where('student_count', '>', 20000),
                default  => null,
            };
        }

        $total = $query->count();
        $universities = $query->with('city.state')
            ->orderByDesc('student_count')
            ->orderBy('name_de')
            ->paginate(48)
            ->withQueryString()
            ->through(fn ($u) => $this->mapUniversityCard($u));

        // Dropdown'lar için gerçekten DB'de bulunan türler + tüm eyaletler
        $availableTypes = University::where('is_active', true)
            ->select('type')
            ->distinct()
            ->whereNotNull('type')
            ->pluck('type')
            ->toArray();

        $states = State::orderBy('name_de')->get(['slug', 'name_de', 'name_tr','name_en','name_de']);

        $closures = $this->typeClosures();

        $viewData = [
            'universities' => $universities,
            'total' => $total,
            'available_types' => $availableTypes,
            'states' => $states,
            'typeLabel' => $closures['typeLabel'],
            'typeBadgeColor' => $closures['typeBadgeColor'],
            // Curated category landing pages (cards rendered above the grid).
            'collections' => \App\Support\UniversityCollections::all(),
        ];

        // XHR — return only the grid partial (async filter UX)
        if ($request->ajax() || $request->wantsJson() || $request->boolean('partial')) {
            return response(view('universities._grid', $viewData)->render())
                ->header('Content-Type', 'text/html; charset=utf-8');
        }

        return view('universities.index', $viewData);
    }

    /**
     * Curated category landing page (e.g. "English-taught", "Most preferred public").
     * Reuses the universities._grid partial verbatim by paginating the curated set
     * (10 unis fit one page, so the paginator's links() render nothing).
     */
    public function collection(string $slug): View
    {
        $collection = \App\Support\UniversityCollections::find($slug);
        abort_unless($collection, 404);

        $query = University::query()
            ->where('is_active', true)
            ->whereIn('slug', $collection['uni_slugs'])
            ->with('city.state');

        $total = $query->count();
        $universities = $query
            ->orderByDesc('student_count')
            ->orderBy('name_de')
            ->paginate(48)
            ->through(fn ($u) => $this->mapUniversityCard($u));

        $closures = $this->typeClosures();

        return view('universities.collection', [
            'slug'           => $slug,
            'collection'     => $collection,
            'universities'   => $universities,
            'total'          => $total,
            'typeLabel'      => $closures['typeLabel'],
            'typeBadgeColor' => $closures['typeBadgeColor'],
        ]);
    }

    /**
     * Shared card-shaped projection of a University for the _grid partial.
     * Used by both index() and collection(). Raw own-image only — _grid resolves
     * the 3-layer fallback (own → city landmark pool → gradient) via CoverImage.
     */
    private function mapUniversityCard(University $u): array
    {
        return [
            'id' => $u->id,
            'slug' => $u->slug,
            'name_de' => $u->name_de,
            'logo_url' => $u->logo_url,
            'image_url' => $u->image_url,
            'city_slug' => $u->city?->slug,
            'city_name' => $u->city?->name,
            'state_name' => $u->city?->state?->name,
            'founded_year' => $u->founded_year,
            'type' => $u->type,
            'student_count' => $u->student_count,
        ];
    }

    /**
     * Type label + badge-color closures shared by index/collection views (and the
     * _grid partial when returned standalone for XHR async-filter responses).
     *
     * @return array{typeLabel: \Closure, typeBadgeColor: \Closure}
     */
    private function typeClosures(): array
    {
        return [
            'typeLabel' => fn ($t) => match ($t) {
                'public' => __('Public'),
                'private' => __('Private'),
                'applied_sciences' => __('Applied Sciences'),
                'art' => __('Art'),
                'religion' => __('Religion'),
                default => $t ? ucfirst($t) : '-',
            },
            'typeBadgeColor' => fn ($t) => match ($t) {
                'public' => 'bg-emerald-50 text-emerald-700',
                'private' => 'bg-amber-50 text-amber-700',
                'applied_sciences' => 'bg-blue-50 text-blue-700',
                'art' => 'bg-pink-50 text-pink-700',
                'religion' => 'bg-purple-50 text-purple-700',
                default => 'bg-gray-100 text-gray-700',
            },
        ];
    }

    /** Üniversite sayfasında sayfa başına program. 2 sütunlu ızgarada çift sayı; diğer liste sayfalarıyla (meslekler: 24) aynı. */
    public const PROGRAMS_PER_PAGE = 24;

    /** Filtre değerleri: degree = veritabanındaki derece değeri; lang = öğretim dili (both her iki filtrede sayılır). */
    private const LANG_FILTERS = ['en' => ['en', 'both'], 'de' => ['de', 'both']];

    /** Derece sırası: önce lisans ve yüksek lisans, sonra diğerleri. */
    private const DEGREE_ORDER = ['bachelor', 'master', 'staatsexamen', 'diplom', 'magister', 'phd', 'other', 'sprachkurs', 'unknown'];

    /**
     * Üniversite program listesi: sayaçlar, filtreler, sayfa.
     *
     * - ?degree=<derece> ve ?lang=en|de filtreleri; geçersiz değer → 404.
     * - ?page=N: N ≥ 2 ve ≤ son sayfa; ?page=1 → aynı URL'ye page'siz 301; geçersiz/aralık dışı → 404.
     * - Sıra: anlamlı metni olan programlar önce (Program::scopeIndexable ön filtresiyle aynı sütunlar), sonra derece,
     *   ad, id (deterministik → sayfalar arasında kayıp/tekrar yok). Noindex programlar gizlenmez, sona doğru kayar.
     *
     * @return array<string,mixed>|RedirectResponse
     */
    private function programListing(Request $request, University $university): array|RedirectResponse
    {
        $base = fn () => $university->programs()->where('is_active', true);

        $order = array_flip(self::DEGREE_ORDER);
        $degreeCounts = $base()->selectRaw('degree, COUNT(*) as n')->groupBy('degree')->pluck('n', 'degree')
            ->map(fn ($n) => (int) $n)
            ->sortBy(fn ($n, $d) => $order[$d] ?? 99)
            ->all();
        $total = array_sum($degreeCounts);
        $langCounts = [];
        foreach (self::LANG_FILTERS as $key => $values) {
            $langCounts[$key] = $base()->whereIn('language', $values)->count();
        }
        $ncFree = $base()->where('admission_mode', 'zulassungsfrei')->count();

        $degree = $request->query('degree');
        $lang = $request->query('lang');
        if ($degree !== null && (! is_string($degree) || ! isset($degreeCounts[$degree]))) {
            abort(404);
        }
        if ($lang !== null && (! is_string($lang) || ! isset(self::LANG_FILTERS[$lang]) || $langCounts[$lang] === 0)) {
            abort(404);
        }
        $filters = array_filter(['degree' => $degree, 'lang' => $lang], fn ($v) => $v !== null);

        $rawPage = $request->query('page');
        if ($rawPage !== null) {
            if (! is_string($rawPage) || ! preg_match('/^[1-9]\d{0,4}$/', $rawPage)) {
                abort(404);
            }
            if ($rawPage === '1') {
                $query = $filters ? '?' . http_build_query($filters) : '';

                return redirect()->to(url(request()->path()) . $query, 301);
            }
        }
        $pageNo = $rawPage === null ? 1 : (int) $rawPage;

        $richSql = '(' . implode(' OR ', array_map(fn ($c) => "COALESCE($c, '') <> ''", [
            'description_tr', 'description_en', 'qualification_requirements_tr', 'qualification_requirements_en',
            'language_requirements_tr', 'language_requirements_en',
        ])) . ')';
        $degreeSql = 'FIELD(degree, ' . implode(', ', array_map(fn ($d) => "'$d'", self::DEGREE_ORDER)) . ')';

        $page = $base()
            ->when($degree, fn ($q) => $q->where('degree', $degree))
            ->when($lang, fn ($q) => $q->whereIn('language', self::LANG_FILTERS[$lang]))
            ->orderByRaw("$richSql DESC")
            ->orderByRaw("$degreeSql = 0, $degreeSql")
            ->orderBy('name_de')
            ->orderBy('id')
            ->with('field:id,name_tr,name_en,name_de,icon,color,slug')
            ->paginate(self::PROGRAMS_PER_PAGE, [
                'id', 'university_id', 'field_of_study_id', 'name_de', 'slug',
                'degree', 'degree_specification', 'language', 'duration_semesters',
                'location', 'description_tr', 'description_en',
                'application_deadline_summer', 'application_deadline_winter',
                'tuition_fee_eur', 'subjects', 'admission_mode',
            ], 'page', $pageNo)
            ->withQueryString()
            ->fragment('programs');

        if ($rawPage !== null && $pageNo > $page->lastPage()) {
            abort(404);
        }
        // Geçerli değerlerin sonuçsuz kombinasyonu (ör. ?degree=bachelor&lang=en, 0 program) → 404 (soft-404 değil).
        if ($filters && $page->total() === 0) {
            abort(404);
        }

        // Çip sayıları diğer filtreye göre: dil çipleri seçili derece içinde, derece çipleri seçili dil içinde sayılır.
        // Görünüm sonuç vermeyen çipi göstermez → boş kombinasyona bağlantı verilmez.
        $langCountsInDegree = $langCounts;
        if ($degree) {
            foreach (self::LANG_FILTERS as $key => $values) {
                $langCountsInDegree[$key] = $base()->where('degree', $degree)->whereIn('language', $values)->count();
            }
        }
        $degreeCountsInLang = $degreeCounts;
        if ($lang) {
            $inLang = $base()->whereIn('language', self::LANG_FILTERS[$lang])
                ->selectRaw('degree, COUNT(*) as n')->groupBy('degree')->pluck('n', 'degree')->all();
            foreach ($degreeCounts as $d => $_) {
                $degreeCountsInLang[$d] = (int) ($inLang[$d] ?? 0);
            }
        }

        // Canonical / hreflang: sayfa 1 (filtresiz) → mevcut davranış (paylaşım yok). Filtresiz sayfa N≥2 farklı liste
        // taşır → self-canonical ?page=N ve aynı sayfanın dil kardeşleri. Filtreli URL'ler → filtresiz üniversite URL'si.
        if ($filters || $pageNo > 1) {
            $suffix = (! $filters && $pageNo > 1) ? '?page=' . $pageNo : '';
            $urls = [];
            foreach (\App\Support\Hreflang::activeLocales() as $loc) {
                $urls[$loc] = route('universities.show', ['locale' => $loc, 'slugOrId' => $university->slug]) . $suffix;
            }
            view()->share('localeUrls', $urls);
            view()->share('canonicalUrl', $urls[app()->getLocale()] ?? null);
        }

        return [
            'page' => $page,
            'total' => $total,
            'degree_counts' => $degreeCounts,
            'lang_counts' => $langCounts,
            'degree_counts_in_lang' => $degreeCountsInLang,
            'lang_counts_in_degree' => $langCountsInDegree,
            'nc_free' => $ncFree,
            'filters' => $filters,
            'open_tab' => $filters || $pageNo > 1,
        ];
    }

    /**
     * /universities/{id} → kanonik slug'a tek adım 301. Aktif kayıt: kendi slug'ı. Pasif kayıt: MERGED_SLUGS hedefi
     * (tek adım), GONE_SLUGS ise 410; eşlemesi olmayan pasif kayıt (araştırma enstitüleri vb., NEEDS_REVIEW) ve
     * geçersiz/bilinmeyen ID → 404. ID URL'lerinin sitede kaynağı yok; yalnız dışarıda kalmış eski URL'leri toplar.
     */
    private function redirectNumericId(string $id): RedirectResponse
    {
        abort_unless(preg_match('/^[1-9][0-9]{0,9}$/', $id) === 1, 404);

        $uni = University::query()->whereKey((int) $id)->first(['id', 'slug', 'is_active']);
        abort_unless($uni && $uni->slug, 404);

        $target = self::MERGED_SLUGS[$uni->slug] ?? ($uni->is_active ? $uni->slug : null);
        if ($target) {
            return redirect()->route('universities.show', ['slugOrId' => $target], 301);
        }
        abort_if(in_array($uni->slug, self::GONE_SLUGS, true), 410, 'university');
        abort(404);
    }

    public function show(Request $request, string $slugOrId): View|RedirectResponse
    {
        // Merge edilmiş kaydın slug'ı → kanonik sayfaya kalıcı yönlendir. Kabuk kayıt DB'de (pasif) dursa bile.
        if ($canonical = self::MERGED_SLUGS[$slugOrId] ?? null) {
            return redirect()->route('universities.show', ['slugOrId' => $canonical], 301);
        }

        // SEO Recovery P0-4: sayısal ID URL'si (/universities/3) artık kopya sayfa açmıyor.
        // Yalnız ^[1-9][0-9]*$ ve gerçek bir kayıt → tek adımda kanonik slug'a 301 (sorgu dizgisi taşınmaz).
        // 003, 3-xyz, bilinmeyen ID → 404 (eskiden MySQL tip zorlamasıyla id=3 sayfası 200 + self-canonical açılıyordu).
        if (ctype_digit($slugOrId)) {
            return $this->redirectNumericId($slugOrId);
        }

        $university = University::where('slug', $slugOrId)
            ->with(['city.state'])
            ->first();

        if (! $university) {
            abort(404);
        }

        if (! $university->is_active && in_array($university->slug, self::GONE_SLUGS, true)) {
            abort(410, 'university');
        }

        \App\Support\ActivityLogger::log($university, $university->name_de);

        $data = [
            'id' => $university->id,
            'slug' => $university->slug,
            'name_de' => $university->name_de,
            'name_en' => $university->name_en,
            'description_tr' => $university->description_tr,
            'description_de' => $university->description_de,
            'description_en' => $university->description_en,
            'type' => $university->type,
            'founded_year' => $university->founded_year,
            'student_count' => $university->student_count,
            'website_url' => $university->website_url,
            'logo_url' => $university->logo_url,
            'wikipedia_url_de' => $university->wikipedia_url_de,
            'wikipedia_url_en' => $university->wikipedia_url_en,
            'city_name' => $university->city?->name,
            'city_slug' => $university->city?->slug,
            'state_name' => $university->city?->state?->name,
            'coordinates' => $university->coordinates,
            // Locale-aware: TR=content_blocks, EN/DE=content_blocks_{locale} (yoksa null → blade gizler)
            'content_blocks' => $university->localizedBlocks(),
            'image_url' => $university->image_url,
        ];

        // Programlar: sayfalı + filtreli liste (SEO Recovery Faz 1).
        // Önceden tüm aktif programlar tek HTML'de listeleniyordu (Mainz: 399 bağlantı, 1,65 MB). Artık sayfa başına
        // PROGRAMS_PER_PAGE kayıt; sonraki sayfalara gerçek <a href> bağlantıları; kullanıcı her programa erişebilir.
        $listing = $this->programListing($request, $university);
        if ($listing instanceof RedirectResponse) {
            return $listing;
        }
        $programs = $listing['page'];
        $programsByDegree = $programs->getCollection()->groupBy('degree');

        // Benzer üniler — önce aynı şehirde, sonra aynı tipte
        $similarUnis = University::query()
            ->where('id', '!=', $university->id)
            ->where('is_active', 1)
            ->where(function ($q) use ($university) {
                if ($university->city_id) {
                    $q->where('city_id', $university->city_id);
                }
            })
            ->orderByDesc('student_count')
            ->take(6)
            ->get(['id', 'slug', 'name_de', 'logo_url', 'image_url', 'type', 'city_id', 'student_count']);

        if ($similarUnis->count() < 4 && $university->type) {
            $extra = University::query()
                ->where('id', '!=', $university->id)
                ->where('is_active', 1)
                ->where('type', $university->type)
                ->whereNotIn('id', $similarUnis->pluck('id'))
                ->orderByDesc('student_count')
                ->take(6 - $similarUnis->count())
                ->get(['id', 'slug', 'name_de', 'logo_url', 'image_url', 'type', 'city_id', 'student_count']);
            $similarUnis = $similarUnis->merge($extra);
        }

        $similarUnis->load('city:id,name_tr,name_en,name_de,slug');

        // Onaylı öğrenci deneyimleri (Topluluk Katkıcısı)
        $shortName = $university->short_name;
        $experiences = \App\Models\Contribution::approved()
            ->where('target_type', 'university')
            ->where(function ($q) use ($university, $shortName) {
                $q->where('target_label', 'like', '%' . $university->name_de . '%');
                if ($shortName && mb_strlen($shortName) >= 3) {
                    $q->orWhere('target_label', 'like', '%' . $shortName . '%');
                }
            })
            ->with('user:id,name,avatar_url,is_contributor')
            ->latest('approved_at')
            ->take(6)
            ->get();

        $cityUniCount = 0;
        $cityStudents = 0;
        $cityPrograms = 0;
        if ($university->city_id) {
            $cityUniIds = University::where('city_id', $university->city_id)
                ->where('is_active', 1)->pluck('id');
            $cityUniCount = $cityUniIds->count();
            $cityStudents = (int) University::whereIn('id', $cityUniIds)->sum('student_count');
            $cityPrograms = \App\Models\Program::whereIn('university_id', $cityUniIds)
                ->where('is_active', 1)->count();
        }

        // UGC reviews — approved listesi + ortalama rating
        $reviews = \App\Models\UniversityReview::approved()
            ->where('university_id', $university->id)
            ->latest()
            ->limit(10)
            ->get();
        $reviewsStats = \App\Models\UniversityReview::approved()
            ->where('university_id', $university->id)
            ->selectRaw('COUNT(*) as total, AVG(rating) as avg_rating')
            ->selectRaw('SUM(CASE WHEN rating=5 THEN 1 ELSE 0 END) as r5')
            ->selectRaw('SUM(CASE WHEN rating=4 THEN 1 ELSE 0 END) as r4')
            ->selectRaw('SUM(CASE WHEN rating=3 THEN 1 ELSE 0 END) as r3')
            ->selectRaw('SUM(CASE WHEN rating=2 THEN 1 ELSE 0 END) as r2')
            ->selectRaw('SUM(CASE WHEN rating=1 THEN 1 ELSE 0 END) as r1')
            ->first();

        return view('universities.show', [
            'university'         => $data,
            'university_model'   => $university,
            'programs'           => $programs,
            'programs_by_degree' => $programsByDegree,
            'programListing'     => $listing,
            'similarUnis'        => $similarUnis,
            'experiences'        => $experiences,
            'cityUniCount'       => $cityUniCount,
            'cityStudents'       => $cityStudents,
            'cityPrograms'       => $cityPrograms,
            'reviews'            => $reviews,
            'reviewsStats'       => $reviewsStats,
        ]);
    }
}
