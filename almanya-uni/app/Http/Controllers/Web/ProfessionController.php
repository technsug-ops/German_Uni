<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Profession;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProfessionController extends Controller
{
    private const PER_PAGE = 24;

    public function index(Request $request): View|\Illuminate\Http\Response
    {
        $filters = [
            'q'     => trim((string) $request->query('q', '')),
            'type'  => in_array($request->query('type'), ['ausbildung', 'studienberuf', 'weiterbildung', 'grundberuf', 'other'], true) ? $request->query('type') : null,
            'field' => (string) $request->query('field', '') ?: null,
        ];

        $query = Profession::query()->where('is_active', true);

        // Katmanlı arama: indexli alanlar FULLTEXT (alaka) + kod/kısa-ad LIKE.
        $q = $filters['q'];
        $like = $q ? '%' . str_replace(['%', '_'], ['\%', '\_'], $q) . '%' : null;
        $ftCols = ['name_de', 'name_tr', 'description_tr', 'description_de'];

        if ($q) {
            $query->where(function ($w) use ($q, $like, $ftCols) {
                $w->searchFulltext($q, $ftCols)
                  ->orWhere('short_name', 'like', $like)
                  ->orWhere('kldb_code', 'like', $like);
            });
        }

        if ($filters['type']) {
            $query->where('type', $filters['type']);
        }
        if ($filters['field']) {
            $query->whereHas('field', fn ($f) => $f->where('slug', $filters['field']));
        }

        $professions = $query
            ->when($q, fn ($qq) => $qq
                ->orderByRaw('CASE WHEN name_de LIKE ? OR name_tr LIKE ? THEN 0 ELSE 1 END', [$like, $like])
                ->orderByRelevance($q, $ftCols))
            ->orderBy('name_de')
            ->paginate(self::PER_PAGE)->withQueryString();

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

        $viewData = compact('professions', 'filters', 'totals', 'fields');

        if ($request->ajax() || $request->wantsJson() || $request->boolean('partial')) {
            return response(view('professions._grid', $viewData)->render())
                ->header('Content-Type', 'text/html; charset=utf-8');
        }

        return view('professions.index', $viewData);
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
