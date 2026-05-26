<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Profession;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProfessionController extends Controller
{
    private const PER_PAGE = 24;

    public function index(Request $request): View
    {
        $filters = [
            'q'     => trim((string) $request->query('q', '')),
            'type'  => in_array($request->query('type'), ['ausbildung', 'studienberuf', 'weiterbildung', 'grundberuf', 'other'], true) ? $request->query('type') : null,
            'field' => (string) $request->query('field', '') ?: null,
        ];

        $query = Profession::query()->where('is_active', true);

        if ($filters['q']) {
            $q = $filters['q'];
            $query->where(function ($w) use ($q) {
                $w->where('name_de', 'like', "%$q%")
                  ->orWhere('short_name', 'like', "%$q%")
                  ->orWhere('kldb_code', 'like', "%$q%")
                  ->orWhere('description_de', 'like', "%$q%");
            });
        }

        if ($filters['type']) {
            $query->where('type', $filters['type']);
        }
        if ($filters['field']) {
            $query->whereHas('field', fn ($f) => $f->where('slug', $filters['field']));
        }

        $professions = $query->orderBy('name_de')->paginate(self::PER_PAGE)->withQueryString();

        $totals = [
            'all'           => Profession::where('is_active', true)->count(),
            'ausbildung'    => Profession::where('type', 'ausbildung')->count(),
            'studienberuf'  => Profession::where('type', 'studienberuf')->count(),
            'weiterbildung' => Profession::where('type', 'weiterbildung')->count(),
            'grundberuf'    => Profession::where('type', 'grundberuf')->count(),
            'other'         => Profession::where('type', 'other')->count(),
        ];

        // Alan filtresi — sadece mesleği olan alanlar, mesleğ adedi ile sıralı
        $fields = \App\Models\FieldOfStudy::query()
            ->where('is_active', true)
            ->withCount(['professions' => fn ($q) => $q->where('is_active', true)])
            ->having('professions_count', '>', 0)
            ->orderByDesc('professions_count')
            ->get(['id', 'slug', 'name_tr', 'icon','name_en','name_de']);

        return view('professions.index', compact('professions', 'filters', 'totals', 'fields'));
    }

    public function show(string $slug): View
    {
        $profession = Profession::where('slug', $slug)
            ->with('field:id,slug,name_tr,name_en,name_de,icon,color')
            ->firstOrFail();

        \App\Support\ActivityLogger::log($profession, $profession->name_de);

        // İlgili meslekler — önce field bazlı, yoksa cluster bazlı
        $relatedQ = Profession::where('id', '!=', $profession->id)->where('is_active', true);
        if ($profession->field_of_study_id) {
            $relatedQ->where('field_of_study_id', $profession->field_of_study_id);
        } elseif ($profession->cluster) {
            $relatedQ->where('cluster', $profession->cluster);
        }
        $related = $relatedQ->orderBy('name_de')->limit(6)->get(['id', 'slug', 'name_de', 'type']);

        // Bu mesleğe götürebilen programlar (alanın programlarından örnek)
        $pathwayPrograms = collect();
        if ($profession->field_of_study_id) {
            $pathwayPrograms = \App\Models\Program::where('field_of_study_id', $profession->field_of_study_id)
                ->where('is_active', 1)
                ->whereIn('degree', ['bachelor', 'master'])
                ->with('university:id,slug,name_de,logo_url')
                ->orderBy('name_de')
                ->take(6)
                ->get(['id', 'slug', 'name_de', 'degree', 'university_id', 'field_of_study_id']);
        }

        return view('professions.show', compact('profession', 'related', 'pathwayPrograms'));
    }
}
