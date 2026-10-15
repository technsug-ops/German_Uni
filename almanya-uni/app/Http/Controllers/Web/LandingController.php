<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\FieldOfStudy;
use App\Models\Program;
use App\Models\State;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Programmatic SEO landing pages.
 *
 * Filter kombinasyonları için özel SEO-optimize sayfalar üretir:
 * - City × Field: "Berlin'de Bilişim Programları"
 * - City × Language: "Berlin'de İngilizce Programlar"
 * - Field × Degree: "Master Bilgisayar Mühendisliği"
 * - Field × State: "Bavyera'da Mühendislik"
 *
 * Her sayfa: locale-aware H1/title/meta, schema (ItemList + BreadcrumbList),
 * filtered programs list, related links.
 */
class LandingController extends Controller
{
    private const PER_PAGE = 20;

    /**
     * Şehir × Alan filtreli landing.
     * URL: /{locale}/programs/city/{city}/field/{field}
     * Örnek: /tr/programs/city/berlin-q64/field/computer-science
     */
    public function cityField(string $city, string $field, Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $cityModel = City::where('slug', $city)->where('is_active', true)->firstOrFail();
        $fieldModel = FieldOfStudy::where('slug', $field)->first();
        if (! $fieldModel) {
            if ($r = \App\Support\SlugRedirects::response('field', 'field', $field)) {
                return $r;
            }
            abort(404);
        }

        $degree = $this->validDegree($request->query('degree'));

        $query = $this->baseProgramQuery()
            ->whereHas('university.city', fn ($q) => $q->where('cities.id', $cityModel->id))
            ->where('field_of_study_id', $fieldModel->id);

        // Derece çipleri yalnız sonucu olan dereceler için bağlantı verir (boş ?degree= varyantına link yok).
        $degreeCounts = $this->degreeCounts(clone $query);
        $englishCount = $this->baseCount()
            ->whereHas('university.city', fn ($q) => $q->where('cities.id', $cityModel->id))
            ->whereIn('language', ['en', 'both'])
            ->count();

        if ($degree) {
            $query->where('degree', $degree);
        }

        [$programs, $totalCount] = $this->paginateOr404($query, $request);

        // Diğer alanlardan örnekler (cross-link, internal linking)
        $otherFields = $this->relatedFields($cityModel->id, $fieldModel->id);
        $otherCities = $this->relatedCities($fieldModel->id, $cityModel->id);

        return view('programs.landing', [
            'context' => 'city-field',
            'city' => $cityModel,
            'field' => $fieldModel,
            'degree' => $degree,
            'programs' => $programs,
            'totalCount' => $totalCount,
            'otherFields' => $otherFields,
            'otherCities' => $otherCities,
            'degreeCounts' => $degreeCounts,
            'englishCount' => $englishCount,
            'h1' => $this->h1ForCityField($cityModel, $fieldModel, $degree),
            'metaDescription' => $this->metaDescForCityField($cityModel, $fieldModel, $totalCount),
        ]);
    }

    /**
     * Şehir × Dil filtreli landing.
     * URL: /{locale}/programs/city/{city}/language/{lang}
     * Örnek: /tr/programs/city/berlin-q64/language/en
     */
    public function cityLanguage(string $city, string $lang, Request $request): View
    {
        abort_unless(in_array($lang, ['en', 'de'], true), 404);

        $cityModel = City::where('slug', $city)->where('is_active', true)->firstOrFail();

        $degree = $this->validDegree($request->query('degree'));

        $query = $this->baseProgramQuery()
            ->whereHas('university.city', fn ($q) => $q->where('cities.id', $cityModel->id))
            ->where(function ($q) use ($lang) {
                $q->where('language', $lang)->orWhere('language', 'both');
            });

        if ($degree) {
            $query->where('degree', $degree);
        }

        [$programs, $totalCount] = $this->paginateOr404($query, $request);

        $otherCities = $this->relatedCitiesByLanguage($lang, $cityModel->id);

        return view('programs.landing', [
            'context' => 'city-language',
            'city' => $cityModel,
            'language' => $lang,
            'degree' => $degree,
            'programs' => $programs,
            'totalCount' => $totalCount,
            'otherCities' => $otherCities,
            'h1' => $this->h1ForCityLanguage($cityModel, $lang, $degree),
            'metaDescription' => $this->metaDescForCityLanguage($cityModel, $lang, $totalCount),
        ]);
    }

    /**
     * Alan × Derece filtreli landing.
     * URL: /{locale}/programs/field/{field}/degree/{degree}
     * Örnek: /tr/programs/field/computer-science/degree/master
     */
    public function fieldDegree(string $field, string $degree, Request $request): View|\Illuminate\Http\RedirectResponse
    {
        abort_unless(in_array($degree, ['bachelor', 'master', 'phd'], true), 404);

        $fieldModel = FieldOfStudy::where('slug', $field)->first();
        if (! $fieldModel) {
            if ($r = \App\Support\SlugRedirects::response('field', 'field', $field)) {
                return $r;
            }
            abort(404);
        }

        $query = $this->baseProgramQuery()
            ->where('field_of_study_id', $fieldModel->id)
            ->where('degree', $degree);

        [$programs, $totalCount] = $this->paginateOr404($query, $request);

        return view('programs.landing', [
            'context' => 'field-degree',
            'field' => $fieldModel,
            'degree' => $degree,
            'programs' => $programs,
            'totalCount' => $totalCount,
            'h1' => $this->h1ForFieldDegree($fieldModel, $degree),
            'metaDescription' => $this->metaDescForFieldDegree($fieldModel, $degree, $totalCount),
        ]);
    }

    /**
     * Alan × ÖĞRETİM DİLİ landing.
     * URL: /{locale}/programs/field/{field}/language/{lang}   (lang: de|en)
     * Örnek: /tr/programs/field/law-economics/language/de — "Almanca ekonomi/hukuk programları"
     *
     * NEDEN VAR: dil boyutu şimdiye kadar yalnızca ŞEHİR kırılımında vardı (city-language).
     * Oysa kullanıcı "Almanya'da Almanca ekonomi nerede okunur" diye ALAN + DİL soruyor;
     * bu sorunun karşılığı olan bir sayfa yoktu. Hochschulkompass importundan sonra Almanca
     * programlar katalogda gerçekten temsil edildiği için bu sayfa artık dolu.
     */
    public function fieldLanguage(string $field, string $lang, Request $request): View|\Illuminate\Http\RedirectResponse
    {
        abort_unless(in_array($lang, ['en', 'de'], true), 404);

        $fieldModel = FieldOfStudy::where('slug', $field)->first();
        if (! $fieldModel) {
            if ($r = \App\Support\SlugRedirects::response('field', 'field', $field)) {
                return $r;
            }
            abort(404);
        }
        $degree = $this->validDegree($request->query('degree'));

        $query = $this->baseProgramQuery()
            ->where('field_of_study_id', $fieldModel->id)
            ->where(function ($q) use ($lang) {
                $q->where('language', $lang)->orWhere('language', 'both');
            });

        // Derece ve diğer-dil çipleri yalnız sonucu varsa bağlantı verir.
        $degreeCounts = $this->degreeCounts(clone $query);
        $otherLang = $lang === 'de' ? 'en' : 'de';
        $otherLanguageCount = $this->baseCount()
            ->where('field_of_study_id', $fieldModel->id)
            ->whereIn('language', [$otherLang, 'both'])
            ->count();

        if ($degree) {
            $query->where('degree', $degree);
        }

        [$programs, $totalCount] = $this->paginateOr404($query, $request);

        // Aynı alanda NC'siz kaç program var? Sayfadaki en değerli çapraz link bu.
        $ncFreeCount = Program::where('is_active', true)
            ->where('field_of_study_id', $fieldModel->id)
            ->where('admission_mode', 'zulassungsfrei')
            ->count();

        return view('programs.landing', [
            'context' => 'field-language',
            'field' => $fieldModel,
            'language' => $lang,
            'degree' => $degree,
            'programs' => $programs,
            'totalCount' => $totalCount,
            'ncFreeCount' => $ncFreeCount,
            'degreeCounts' => $degreeCounts,
            'otherLanguage' => $otherLang,
            'otherLanguageCount' => $otherLanguageCount,
            'h1' => $this->h1ForFieldLanguage($fieldModel, $lang, $degree),
            'metaDescription' => $this->metaDescForFieldLanguage($fieldModel, $lang, $totalCount),
        ]);
    }

    /**
     * Şehir × NC-frei filtreli landing.
     * URL: /{locale}/cities/{city}/nc-free
     * Örnek: /tr/cities/berlin-q64/nc-free — "Berlin'de NC'siz bölümler"
     */
    public function cityNcFree(string $city, Request $request): View
    {
        $cityModel = City::where('slug', $city)->where('is_active', true)->firstOrFail();

        $query = $this->baseProgramQuery()
            ->whereHas('university.city', fn ($q) => $q->where('cities.id', $cityModel->id))
            ->where('admission_mode', 'zulassungsfrei');

        [$programs, $totalCount] = $this->paginateOr404($query, $request);

        $otherCities = $this->relatedCitiesByNcFree($cityModel->id);

        return view('programs.landing', [
            'context' => 'city-nc-free',
            'city' => $cityModel,
            'programs' => $programs,
            'totalCount' => $totalCount,
            'otherCities' => $otherCities,
            'h1' => $this->h1ForCityNcFree($cityModel),
            'metaDescription' => $this->metaDescForCityNcFree($cityModel, $totalCount),
        ]);
    }

    // ─────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────

    private function baseProgramQuery()
    {
        return Program::query()
            ->where('is_active', true)
            ->with([
                'university:id,slug,name_de,name_en,name_tr,short_name,logo_url,city_id,type',
                'university.city:id,name_tr,name_en,name_de,slug,state_id',
                'university.city.state:id,name_tr,name_en,name_de,slug',
                'field:id,slug,name_tr,name_en,name_de,icon,color',
            ])
            ->orderByRaw('CASE WHEN description_tr IS NULL THEN 1 ELSE 0 END')
            ->orderBy('name_de');
    }

    /**
     * SEO Recovery P0-4 (soft 404): bağımsız programatik landing'de sonuç yoksa gerçek 404.
     *
     * Koşul sitemap üretimiyle aynı: aktif program sayısı (SitemapController::landingEntries ve
     * şehir/alan NC-free blokları da yalnız ≥1 aktif programı olan kombinasyonları yazar). Bu yüzden
     * sitemap'teki her landing 200 + index kalır; sitemap dışındaki boş kombinasyonlar ("Bu filtreye
     * uygun program yok" + 200 + index) artık 404. `?degree=` varyantı da aynı kurala tabi.
     * Sayfa: `page` pozitif tam sayı olmalı ve son sayfayı aşmamalı (aksi halde 404; /programs ile aynı).
     *
     * Not: eski kod toplamı paginate()'ten SONRA $query->count() ile alıyordu; paginate() builder'a
     * offset eklediği için 2. ve sonraki sayfalarda toplam 0 görünüyordu. Toplam artık ayrı sayılıyor.
     *
     * @return array{0: \Illuminate\Contracts\Pagination\LengthAwarePaginator, 1: int}
     */
    private function paginateOr404($query, Request $request): array
    {
        $total = (clone $query)->count();
        abort_if($total === 0, 404);

        $page = 1;
        if ($request->query->has('page')) {
            $raw = $request->query('page');
            abort_unless(is_string($raw) && preg_match('/^[1-9][0-9]{0,5}$/', $raw), 404);
            $page = (int) $raw;
        }
        abort_if($page > (int) ceil($total / self::PER_PAGE), 404);

        $programs = $query->paginate(self::PER_PAGE, ['*'], 'page', $page, $total)->withQueryString();

        return [$programs, $total];
    }

    /** Eager-load ve sıralama olmadan aktif program sayımı için temel sorgu (çip sayıları). */
    private function baseCount()
    {
        return Program::query()->where('is_active', true);
    }

    /**
     * Derece çipleri için sayımlar (bachelor/master/phd), derece filtresi uygulanmadan önceki sorgudan.
     *
     * @return array<string, int>
     */
    private function degreeCounts($query): array
    {
        $counts = $query->reorder()->setEagerLoads([])
            ->whereIn('degree', ['bachelor', 'master', 'phd'])
            ->selectRaw('degree, COUNT(*) as n')
            ->groupBy('degree')
            ->pluck('n', 'degree')
            ->all();

        $out = [];
        foreach (['bachelor', 'master', 'phd'] as $d) {
            $out[$d] = (int) ($counts[$d] ?? 0);
        }

        return $out;
    }

    private function validDegree(?string $degree): ?string
    {
        return in_array($degree, ['bachelor', 'master', 'phd'], true) ? $degree : null;
    }

    private function relatedFields(int $cityId, int $excludeFieldId): \Illuminate\Support\Collection
    {
        return FieldOfStudy::query()
            ->where('id', '!=', $excludeFieldId)
            ->whereHas('programs', function ($q) use ($cityId) {
                $q->where('is_active', true)
                  ->whereHas('university', fn ($u) => $u->where('city_id', $cityId));
            })
            ->orderBy('sort_order')
            ->limit(6)
            ->get(['id', 'slug', 'name_tr', 'name_en', 'name_de', 'icon', 'color']);
    }

    private function relatedCities(int $fieldId, int $excludeCityId): \Illuminate\Support\Collection
    {
        return City::query()
            ->where('is_active', true)
            ->where('id', '!=', $excludeCityId)
            ->whereHas('universities.programs', function ($q) use ($fieldId) {
                $q->where('is_active', true)->where('field_of_study_id', $fieldId);
            })
            ->withCount(['universities as programs_count' => function ($q) use ($fieldId) {
                $q->join('programs', 'programs.university_id', '=', 'universities.id')
                  ->where('programs.field_of_study_id', $fieldId)
                  ->where('programs.is_active', true);
            }])
            ->orderByDesc('programs_count')
            ->limit(8)
            ->get(['id', 'slug', 'name_tr', 'name_en', 'name_de', 'image_url']);
    }

    private function relatedCitiesByLanguage(string $lang, int $excludeCityId): \Illuminate\Support\Collection
    {
        return City::query()
            ->where('is_active', true)
            ->where('id', '!=', $excludeCityId)
            ->whereHas('universities.programs', function ($q) use ($lang) {
                $q->where('is_active', true)
                  ->where(fn ($w) => $w->where('language', $lang)->orWhere('language', 'both'));
            })
            ->orderBy('name_de')
            ->limit(8)
            ->get(['id', 'slug', 'name_tr', 'name_en', 'name_de', 'image_url']);
    }

    private function relatedCitiesByNcFree(int $excludeCityId): \Illuminate\Support\Collection
    {
        return City::query()
            ->where('is_active', true)
            ->where('id', '!=', $excludeCityId)
            ->whereHas('universities.programs', function ($q) {
                $q->where('is_active', true)->where('admission_mode', 'zulassungsfrei');
            })
            ->orderBy('name_de')
            ->limit(8)
            ->get(['id', 'slug', 'name_tr', 'name_en', 'name_de', 'image_url']);
    }

    // ─────────────────────────────────────────────────────
    // Locale-aware H1 & meta builders (TR/EN/DE)
    // ─────────────────────────────────────────────────────

    private function h1ForFieldLanguage(FieldOfStudy $field, string $lang, ?string $degree): string
    {
        $degreeLabel = $degree ? ' ' . $this->degreeLabel($degree) : '';

        return __(':lang-taught:degree :field Programs in Germany', [
            'lang' => $this->languageName($lang),
            'degree' => $degreeLabel,
            'field' => $field->name,
        ]);
    }

    private function metaDescForFieldLanguage(FieldOfStudy $field, string $lang, int $count): string
    {
        return __(':count :lang-taught :field programs in Germany — filter by degree and admission mode (NC), see the university, city and application details.', [
            'count' => $count,
            'lang' => $this->languageName($lang),
            'field' => $field->name,
        ]);
    }

    private function h1ForCityNcFree(City $city): string
    {
        return __('NC-free (zulassungsfrei) programs in :city', ['city' => $city->name]);
    }

    private function metaDescForCityNcFree(City $city, int $count): string
    {
        return __(':count NC-free (zulassungsfrei) programs in :city — open admission, no grade restriction (NC). Filter by field and degree.', [
            'count' => $count,
            'city' => $city->name,
        ]);
    }

    private function h1ForCityField(City $city, FieldOfStudy $field, ?string $degree): string
    {
        $degreeLabel = $degree ? ' ' . $this->degreeLabel($degree) : '';
        return __(':field:degree Programs in :city', [
            'field' => $field->name,
            'degree' => $degreeLabel,
            'city' => $city->name,
        ]);
    }

    private function metaDescForCityField(City $city, FieldOfStudy $field, int $count): string
    {
        return __(':count :field programs at universities in :city. Filter by degree, language, deadline; see tuition fees and admission requirements.', [
            'count' => $count,
            'field' => $field->name,
            'city' => $city->name,
        ]);
    }

    private function h1ForCityLanguage(City $city, string $lang, ?string $degree): string
    {
        $langName = $this->languageName($lang);
        $degreeLabel = $degree ? ' ' . $this->degreeLabel($degree) : '';
        return __(':lang-taught:degree Programs in :city', [
            'lang' => $langName,
            'degree' => $degreeLabel,
            'city' => $city->name,
        ]);
    }

    private function metaDescForCityLanguage(City $city, string $lang, int $count): string
    {
        $langName = $this->languageName($lang);
        return __(':count :lang-taught programs at universities in :city. Filter by degree, deadline, field.', [
            'count' => $count,
            'lang' => $langName,
            'city' => $city->name,
        ]);
    }

    private function h1ForFieldDegree(FieldOfStudy $field, string $degree): string
    {
        $degreeLabel = $this->degreeLabel($degree);
        return __(':degree Programs in :field in Germany', [
            'degree' => $degreeLabel,
            'field' => $field->name,
        ]);
    }

    private function metaDescForFieldDegree(FieldOfStudy $field, string $degree, int $count): string
    {
        $degreeLabel = $this->degreeLabel($degree);
        return __(':count :degree programs in :field at German universities. Compare tuition, language, deadlines.', [
            'count' => $count,
            'degree' => $degreeLabel,
            'field' => $field->name,
        ]);
    }

    private function degreeLabel(string $degree): string
    {
        return match ($degree) {
            'bachelor' => __('Bachelor'),
            'master' => __('Master'),
            'phd' => __('PhD'),
            default => ucfirst($degree),
        };
    }

    private function languageName(string $lang): string
    {
        return match ($lang) {
            'en' => __('English-taught'),
            'de' => __('German-taught'),
            default => $lang,
        };
    }
}
