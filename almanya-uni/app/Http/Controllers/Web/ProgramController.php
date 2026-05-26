<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FieldOfStudy;
use App\Models\Program;
use App\Models\State;
use App\Models\University;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    private const PER_PAGE = 20;

    public function index(Request $request): View
    {
        $filters = $this->cleanFilters($request);

        $query = Program::query()
            ->where('is_active', true)
            ->with([
                'university:id,slug,name_de,name_en,name_tr,short_name,logo_url,city_id',
                'university.city:id,name_tr,name_en,name_de,slug,state_id',
                'university.city.state:id,name_tr,name_en,name_de,slug',
                'field:id,slug,name_tr,name_en,name_de,icon,color',
            ]);

        // Arama
        if ($filters['q']) {
            $q = $filters['q'];
            $query->where(function ($w) use ($q) {
                $w->where('name_de',   'like', "%$q%")
                  ->orWhere('name_en', 'like', "%$q%")
                  ->orWhere('name_tr', 'like', "%$q%")
                  ->orWhereHas('university', fn ($u) => $u->where('name_de', 'like', "%$q%"));
            });
        }

        if ($filters['degree']) {
            $query->where('degree', $filters['degree']);
        }
        if ($filters['language']) {
            $query->where('language', $filters['language']);
        }
        if ($filters['field']) {
            $query->whereHas('field', fn ($f) => $f->where('slug', $filters['field']));
        }
        if ($filters['state']) {
            $query->whereHas('university.city.state', fn ($s) => $s->where('slug', $filters['state']));
        }
        if ($filters['uni']) {
            $query->whereHas('university', fn ($u) => $u->where('slug', $filters['uni']));
        }
        if ($filters['has_tr']) {
            $query->whereNotNull('description_tr');
        }
        if ($filters['free_only']) {
            $query->where(function ($w) {
                $w->whereNull('tuition_fee_eur')->orWhere('tuition_fee_eur', 0);
            });
        }
        if ($filters['no_app_fee']) {
            $query->where(function ($w) {
                $w->whereNull('application_fee_eur')->orWhere('application_fee_eur', 0);
            });
        }
        if ($filters['uni_assist']) {
            $query->whereHas('university', fn ($u) => $u->where('is_uni_assist_member', true));
        }
        if ($filters['semester'] === 'winter') {
            $query->whereNotNull('application_deadline_winter');
        } elseif ($filters['semester'] === 'summer') {
            $query->whereNotNull('application_deadline_summer');
        }
        if ($filters['deadline_open']) {
            // Bugünden sonraki deadline'ları olan programlar
            $query->where(function ($w) {
                $w->where('application_deadline_winter', '>=', now())
                  ->orWhere('application_deadline_summer', '>=', now());
            });
        }
        if ($filters['admission']) {
            // Partner snapshot v2 gelince aktif olacak — şimdilik DB boş
            $query->where('admission_mode', $filters['admission']);
        }
        if ($filters['duration_range']) {
            [$min, $max] = match ($filters['duration_range']) {
                'short' => [1, 3],
                'mid'   => [4, 6],
                'long'  => [7, 12],
                default => [1, 20],
            };
            $query->whereBetween('duration_semesters', [$min, $max]);
        }

        // Sıralama
        $query = match ($filters['sort']) {
            'name'        => $query->orderBy('name_de'),
            'duration'    => $query->orderBy('duration_semesters'),
            'deadline'    => $query->orderByRaw('LEAST(COALESCE(application_deadline_winter, "9999-12-31"), COALESCE(application_deadline_summer, "9999-12-31"))'),
            default       => $query->orderByRaw('CASE WHEN description_tr IS NULL THEN 1 ELSE 0 END')
                                   ->orderBy('name_de'),
        };

        $programs = $query->paginate(self::PER_PAGE)->withQueryString();

        // Lookup data filtre dropdownları için (cache'lenebilir, küçükler)
        // Locale-aware select — accessor name_tr/en/de tümünü ister
        $fields = FieldOfStudy::active()->orderBy('sort_order')->get(['slug', 'name_tr', 'name_en', 'name_de', 'icon']);
        $states = State::orderBy('name_de')->get(['slug', 'name_tr', 'name_en', 'name_de']);

        // Toplam stats sayfa head'inde
        $totalAll = Program::where('is_active', true)->count();
        $totalEn  = Program::where('is_active', true)->whereIn('language', ['en', 'both'])->count();

        // NC Frei verisi DB'de var mı? Partner snapshot v2 gelene kadar 0 olabilir.
        $admissionDataAvailable = Program::whereNotNull('admission_mode')
            ->where('admission_mode', '!=', '')
            ->exists();

        return view('programs.index', [
            'programs'  => $programs,
            'filters'   => $filters,
            'fields'    => $fields,
            'states'    => $states,
            'total_all' => $totalAll,
            'total_en'  => $totalEn,
            'admission_data_available' => $admissionDataAvailable,
        ]);
    }

    public function show(string $slug): View
    {
        $program = Program::where('slug', $slug)
            ->with([
                'university:id,slug,name_de,short_name,logo_url,image_url,city_id,type,is_uni_assist_member,content_blocks',
                'university.city:id,name_tr,name_en,name_de,slug,state_id,image_url,content_blocks',
                'university.city.state:id,name_tr,name_en,name_de,slug',
                'field:id,slug,name_tr,name_en,name_de,icon,color',
            ])
            ->firstOrFail();

        \App\Support\ActivityLogger::log($program, $program->name_de);

        $related = collect();
        if ($program->field_of_study_id) {
            $related = Program::where('field_of_study_id', $program->field_of_study_id)
                ->where('degree', $program->degree)
                ->where('id', '!=', $program->id)
                ->where('is_active', true)
                ->with('university:id,slug,name_de,short_name,logo_url')
                ->limit(6)
                ->get(['id', 'slug', 'name_de', 'degree', 'degree_specification', 'language', 'university_id']);
        }

        // İlgili bloklar — üniversite + şehir content_blocks'tan teaser
        $uniBlocks = $program->university->content_blocks ?? [];
        $cityBlocks = $program->university->city?->content_blocks ?? [];

        // Şehirden cost_of_living + places teaser
        $cityCost = collect($cityBlocks)->firstWhere('type', 'cost_of_living');
        $cityPlaces = collect($cityBlocks)->firstWhere('type', 'places');

        // Üniden FAQ teaser (ilk 3 soru)
        $uniFaq = collect($uniBlocks)->firstWhere('type', 'faq');
        if ($uniFaq && !empty($uniFaq['items'])) {
            $uniFaq['items'] = array_slice($uniFaq['items'], 0, 3);
        }

        return view('programs.show', [
            'program' => $program,
            'related' => $related,
            'city_cost' => $cityCost,
            'city_places' => $cityPlaces,
            'uni_faq' => $uniFaq,
        ]);
    }

    /**
     * Query parametrelerini temizle (XSS / invalid value koruması).
     */
    private function cleanFilters(Request $request): array
    {
        $allowedDegree    = ['bachelor', 'master', 'phd', 'staatsexamen', 'diplom', 'magister', 'other'];
        $allowedLanguage  = ['de', 'en', 'both'];
        $allowedSemester  = ['winter', 'summer'];
        $allowedDuration  = ['short', 'mid', 'long'];
        $allowedAdmission = ['zulassungsfrei', 'oertlich', 'bundesweit'];
        $allowedSort      = ['relevance', 'name', 'duration', 'deadline'];

        return [
            'q'              => trim((string) $request->query('q', '')),
            'degree'         => in_array($request->query('degree'), $allowedDegree, true) ? $request->query('degree') : null,
            'language'       => in_array($request->query('language'), $allowedLanguage, true) ? $request->query('language') : null,
            'field'          => (string) $request->query('field', '') ?: null,
            'state'          => (string) $request->query('state', '') ?: null,
            'uni'            => (string) $request->query('uni', '') ?: null,
            'has_tr'         => filter_var($request->query('has_tr'),   FILTER_VALIDATE_BOOLEAN),
            'free_only'      => filter_var($request->query('free_only'),FILTER_VALIDATE_BOOLEAN),
            'no_app_fee'     => filter_var($request->query('no_app_fee'), FILTER_VALIDATE_BOOLEAN),
            'uni_assist'     => filter_var($request->query('uni_assist'), FILTER_VALIDATE_BOOLEAN),
            'semester'       => in_array($request->query('semester'), $allowedSemester, true) ? $request->query('semester') : null,
            'deadline_open'  => filter_var($request->query('deadline_open'), FILTER_VALIDATE_BOOLEAN),
            'duration_range' => in_array($request->query('duration_range'), $allowedDuration, true) ? $request->query('duration_range') : null,
            'admission'      => in_array($request->query('admission'), $allowedAdmission, true) ? $request->query('admission') : null,
            'sort'           => in_array($request->query('sort'), $allowedSort, true) ? $request->query('sort') : 'relevance',
        ];
    }
}
