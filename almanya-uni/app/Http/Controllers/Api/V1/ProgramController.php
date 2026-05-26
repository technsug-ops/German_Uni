<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\ProgramSummaryResource;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    private const ALLOWED_DEGREES = ['bachelor', 'master', 'phd', 'studienkolleg', 'sprachkurs', 'other', 'unknown'];
    private const ALLOWED_LANGUAGES = ['de', 'en', 'both', 'other'];
    private const ALLOWED_ADMISSION_MODES = ['zulassungsfrei', 'oertlich', 'auswahl', 'eignung', 'nc', 'frei', 'unknown'];
    private const ALLOWED_SORTS = ['name_de', 'name_tr', 'degree', 'duration_semesters', 'nc_value', 'created_at','name_en','name_de'];

    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:120',
            'university_id' => 'nullable|integer|exists:universities,id',
            'university_slug' => 'nullable|string|max:120',
            'field_id' => 'nullable|integer|exists:fields_of_study,id',
            'field_slug' => 'nullable|string|max:120',
            'degree' => 'nullable|in:' . implode(',', self::ALLOWED_DEGREES),
            'language' => 'nullable|in:' . implode(',', self::ALLOWED_LANGUAGES),
            'admission_mode' => 'nullable|in:' . implode(',', self::ALLOWED_ADMISSION_MODES),
            'nc_max' => 'nullable|numeric|min:1|max:5',
            'tuition_max' => 'nullable|numeric|min:0|max:100000',
            'tuition_free' => 'nullable|boolean',
            'study_form' => 'nullable|string|max:40',
            'sort' => 'nullable|in:' . implode(',', self::ALLOWED_SORTS),
            'order' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Program::query()
            ->where('is_active', true)
            ->with([
                'university:id,slug,name_de,name_tr,name_en,type,logo_url',
                'field:id,slug,name_de',
            ]);

        if (!empty($validated['q'])) {
            $term = '%' . str_replace(['%', '_'], ['\%', '\_'], $validated['q']) . '%';
            $query->where(function ($w) use ($term) {
                $w->where('name_de', 'like', $term)
                    ->orWhere('name_tr', 'like', $term)
                    ->orWhere('name_en', 'like', $term);
            });
        }

        if (!empty($validated['university_id'])) {
            $query->where('university_id', $validated['university_id']);
        }
        if (!empty($validated['university_slug'])) {
            $query->whereHas('university', fn ($q) => $q->where('slug', $validated['university_slug']));
        }

        if (!empty($validated['field_id'])) {
            $query->where('field_of_study_id', $validated['field_id']);
        }
        if (!empty($validated['field_slug'])) {
            $query->whereHas('field', fn ($q) => $q->where('slug', $validated['field_slug']));
        }

        if (!empty($validated['degree'])) {
            $query->where('degree', $validated['degree']);
        }
        if (!empty($validated['language'])) {
            $query->where('language', $validated['language']);
        }
        if (!empty($validated['admission_mode'])) {
            $query->where('admission_mode', $validated['admission_mode']);
        }
        if (!empty($validated['study_form'])) {
            $query->where('study_form', $validated['study_form']);
        }
        if (array_key_exists('nc_max', $validated) && $validated['nc_max'] !== null) {
            $query->whereNotNull('nc_value')->where('nc_value', '<=', $validated['nc_max']);
        }
        if (array_key_exists('tuition_max', $validated) && $validated['tuition_max'] !== null) {
            $query->where(function ($w) use ($validated) {
                $w->whereNull('tuition_fee_eur')->orWhere('tuition_fee_eur', '<=', $validated['tuition_max']);
            });
        }
        if (array_key_exists('tuition_free', $validated) && $validated['tuition_free'] !== null) {
            $validated['tuition_free']
                ? $query->where(fn ($w) => $w->whereNull('tuition_fee_eur')->orWhere('tuition_fee_eur', 0))
                : $query->where('tuition_fee_eur', '>', 0);
        }

        $sort = $validated['sort'] ?? 'name_de';
        $order = $validated['order'] ?? 'asc';
        $query->orderBy($sort, $order);

        $perPage = (int) ($validated['per_page'] ?? 20);
        $paginator = $query->paginate($perPage)->withQueryString();

        return ProgramSummaryResource::collection($paginator);
    }

    public function show(string $slugOrId)
    {
        $query = Program::query()->with(['university.city', 'field']);

        $program = is_numeric($slugOrId)
            ? $query->findOrFail((int) $slugOrId)
            : $query->where('slug', $slugOrId)->firstOrFail();

        return new ProgramResource($program);
    }

    public function stats(): JsonResponse
    {
        $stats = [
            'total' => Program::where('is_active', true)->count(),
            'by_degree' => Program::where('is_active', true)
                ->selectRaw('degree, COUNT(*) as count')
                ->whereNotNull('degree')
                ->groupBy('degree')
                ->pluck('count', 'degree'),
            'by_language' => Program::where('is_active', true)
                ->selectRaw('language, COUNT(*) as count')
                ->whereNotNull('language')
                ->groupBy('language')
                ->pluck('count', 'language'),
            'by_admission_mode' => Program::where('is_active', true)
                ->selectRaw('admission_mode, COUNT(*) as count')
                ->whereNotNull('admission_mode')
                ->groupBy('admission_mode')
                ->pluck('count', 'admission_mode'),
        ];

        return response()->json(['data' => $stats]);
    }
}
