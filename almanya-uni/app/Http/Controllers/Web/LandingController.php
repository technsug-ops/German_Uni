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
     * Örnek: /tr/programs/city/berlin-q64/field/bilisim
     */
    public function cityField(string $city, string $field, Request $request): View
    {
        $cityModel = City::where('slug', $city)->where('is_active', true)->firstOrFail();
        $fieldModel = FieldOfStudy::where('slug', $field)->firstOrFail();

        $degree = $this->validDegree($request->query('degree'));

        $query = $this->baseProgramQuery()
            ->whereHas('university.city', fn ($q) => $q->where('cities.id', $cityModel->id))
            ->where('field_of_study_id', $fieldModel->id);

        if ($degree) {
            $query->where('degree', $degree);
        }

        $programs = $query->paginate(self::PER_PAGE)->withQueryString();
        $totalCount = $query->count();

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

        $programs = $query->paginate(self::PER_PAGE)->withQueryString();
        $totalCount = $query->count();

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
     * Örnek: /tr/programs/field/bilisim/degree/master
     */
    public function fieldDegree(string $field, string $degree): View
    {
        abort_unless(in_array($degree, ['bachelor', 'master', 'phd'], true), 404);

        $fieldModel = FieldOfStudy::where('slug', $field)->firstOrFail();

        $query = $this->baseProgramQuery()
            ->where('field_of_study_id', $fieldModel->id)
            ->where('degree', $degree);

        $programs = $query->paginate(self::PER_PAGE)->withQueryString();
        $totalCount = $query->count();

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

    // ─────────────────────────────────────────────────────
    // Locale-aware H1 & meta builders (TR/EN/DE)
    // ─────────────────────────────────────────────────────

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
