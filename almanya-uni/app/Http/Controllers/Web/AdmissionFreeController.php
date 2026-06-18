<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FieldOfStudy;
use App\Models\Program;
use App\Models\University;
use Illuminate\Contracts\View\View;

/**
 * Programmatic SEO landing sayfaları:
 *   /subjects/{slug}/nc-free     — bir alan grubunun NC Frei programları
 *   /universities/{slug}/nc-free — bir üni'nin NC Frei programları
 *
 * Partner snapshot v2 ile admission_mode='zulassungsfrei' dolduğunda
 * onbinlerce SEO URL'i otomatik aktifleşir. Şimdilik banner + Hochschulkompass.
 */
class AdmissionFreeController extends Controller
{
    /**
     * NC-frei üst-hub: /nc-free — "Almanya'da NC'siz (zulassungsfrei) bölümler".
     * Alanları + en çok NC-frei programı olan üniversiteleri listeler, alt
     * landing'lere (by-subject / by-university) iç-link verir.
     */
    public function index(): View
    {
        $fields = FieldOfStudy::active()
            ->withCount(['programs as nc_free_count' => fn ($q) => $q->where('is_active', true)->where('admission_mode', 'zulassungsfrei')])
            ->get()
            ->filter(fn ($f) => $f->nc_free_count > 0)
            ->sortByDesc('nc_free_count')
            ->values();

        $totalNcFree = Program::where('is_active', true)->where('admission_mode', 'zulassungsfrei')->count();

        $topUnis = University::where('is_active', true)
            ->withCount(['programs as nc_free_count' => fn ($q) => $q->where('is_active', true)->where('admission_mode', 'zulassungsfrei')])
            ->having('nc_free_count', '>', 0)
            ->orderByDesc('nc_free_count')
            ->take(12)
            ->get();

        $topCities = \App\Models\City::where('is_active', true)
            ->withCount(['universities as nc_free_count' => function ($q) {
                $q->join('programs', 'programs.university_id', '=', 'universities.id')
                  ->where('programs.is_active', true)
                  ->where('programs.admission_mode', 'zulassungsfrei');
            }])
            ->having('nc_free_count', '>', 0)
            ->orderByDesc('nc_free_count')
            ->take(12)
            ->get(['id', 'slug', 'name_tr', 'name_en', 'name_de']);

        return view('admission-free.index', compact('fields', 'totalNcFree', 'topUnis', 'topCities'));
    }

    public function bySubject(string $slug): View
    {
        $field = FieldOfStudy::where('slug', $slug)->active()->firstOrFail();

        $programs = Program::where('field_of_study_id', $field->id)
            ->where('is_active', true)
            ->where('admission_mode', 'zulassungsfrei')
            ->with(['university:id,slug,name_de,short_name,logo_url,city_id', 'university.city:id,name_tr,name_en,name_de'])
            ->orderBy('name_de')
            ->paginate(30)
            ->withQueryString();

        // Bu alanda hiç (admission_mode null bile olsa) program var mı?
        $totalInField = Program::where('field_of_study_id', $field->id)
            ->where('is_active', true)
            ->count();

        return view('admission-free.by-subject', [
            'field'           => $field,
            'programs'        => $programs,
            'total_in_field'  => $totalInField,
            'has_data'        => $programs->total() > 0,
        ]);
    }

    public function byUniversity(string $slug): View
    {
        $university = University::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $programs = Program::where('university_id', $university->id)
            ->where('is_active', true)
            ->where('admission_mode', 'zulassungsfrei')
            ->with('field:id,slug,name_tr,name_en,name_de,icon,color')
            ->orderBy('degree')
            ->orderBy('name_de')
            ->paginate(30)
            ->withQueryString();

        $totalAtUni = Program::where('university_id', $university->id)
            ->where('is_active', true)
            ->count();

        return view('admission-free.by-university', [
            'university'  => $university,
            'programs'    => $programs,
            'total_at_uni'=> $totalAtUni,
            'has_data'    => $programs->total() > 0,
        ]);
    }
}
