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
    public function bySubject(string $slug): View
    {
        $field = FieldOfStudy::where('slug', $slug)->active()->firstOrFail();

        $programs = Program::where('field_of_study_id', $field->id)
            ->where('is_active', true)
            ->where('admission_mode', 'zulassungsfrei')
            ->with(['university:id,slug,name_de,short_name,logo_url,city_id', 'university.city:id,name_tr,name_en,name_de','name_en','name_de'])
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
