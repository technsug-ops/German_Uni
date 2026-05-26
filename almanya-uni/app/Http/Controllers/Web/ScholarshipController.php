<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\ScholarshipIntention;
use App\Models\ScholarshipOrigin;
use App\Models\ScholarshipStatus;
use App\Models\ScholarshipSubject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    public function index(Request $request): View
    {
        $query = Scholarship::query()
            ->whereNull('removed_at')
            ->with(['origins', 'statuses', 'subjects', 'intentions']);

        // Filters (deterministik DB filtreleme — Scout aramasi ayri parametre `q`)
        $filters = [
            'q'         => trim((string) $request->get('q', '')),
            'country'   => (int) $request->get('country', 0),       // origin_id
            'subject'   => (string) $request->get('subject', ''),   // subject code (A-G)
            'target'    => (int) $request->get('target', 0),        // status_id (target group)
            'intention' => (int) $request->get('intention', 0),
            'is_daad'   => $request->has('is_daad') ? (int) $request->get('is_daad') : null,
        ];

        if ($filters['q'] !== '') {
            // Scout/Meilisearch — fallback yoksa LIKE
            try {
                $ids = Scholarship::search($filters['q'])->keys()->all();
                $query->whereIn('id', $ids ?: [0]);
            } catch (\Throwable $e) {
                $like = '%' . $filters['q'] . '%';
                $query->where(function ($q) use ($like) {
                    $q->where('name_en', 'like', $like)
                      ->orWhere('name_de', 'like', $like)
                      ->orWhere('programmname_en', 'like', $like)
                      ->orWhere('programmname_de', 'like', $like);
                });
            }
        }

        if ($filters['country'] > 0) {
            $query->whereHas('origins', fn ($q) => $q->where('scholarship_origins_lookup.id', $filters['country']));
        }
        if ($filters['subject'] !== '') {
            $query->whereHas('subjects', fn ($q) => $q->where('scholarship_subject_groups.code', $filters['subject']));
        }
        if ($filters['target'] > 0) {
            $query->whereHas('statuses', fn ($q) => $q->where('scholarship_statuses.id', $filters['target']));
        }
        if ($filters['intention'] > 0) {
            $query->whereHas('intentions', fn ($q) => $q->where('scholarship_intentions.id', $filters['intention']));
        }
        if ($filters['is_daad'] !== null) {
            $query->where('is_daad', $filters['is_daad']);
        }

        $scholarships = $query->orderBy('is_daad', 'desc')
            ->orderBy('name_en')
            ->paginate(24)
            ->withQueryString();

        return view('scholarships.daad-list', [
            'scholarships' => $scholarships,
            'filters'      => $filters,
            'origins'      => ScholarshipOrigin::orderBy('name_en')->get(['id', 'name_en', 'name_de']),
            'subjects'     => ScholarshipSubject::orderBy('name_en')->get(['code', 'name_en', 'name_de']),
            'statuses'     => ScholarshipStatus::orderBy('sortierung')->get(['id', 'name_en', 'name_de']),
            'intentions'   => ScholarshipIntention::orderBy('id')->get(['id', 'name_en', 'name_de']),
            'totalActive'  => Scholarship::whereNull('removed_at')->count(),
        ]);
    }

    /**
     * Eski statik DAAD rehber sayfası — geçici olarak kalsın, yeni DB sayfasına yönlendirilebilir.
     */
    public function daad(): View
    {
        return view('scholarships.daad');
    }

    public function show(string $slug): View
    {
        $scholarship = Scholarship::query()
            ->where('slug', $slug)
            ->with(['origins', 'statuses', 'subjects', 'intentions', 'deadline'])
            ->firstOrFail();

        // removed_at varsa hâlâ render et (SEO friendly), ama notice göster.
        $related = Scholarship::query()
            ->whereNull('removed_at')
            ->where('id', '!=', $scholarship->id)
            ->when($scholarship->subjects->isNotEmpty(), function ($q) use ($scholarship) {
                $codes = $scholarship->subjects->pluck('code')->all();
                $q->whereHas('subjects', fn ($qq) => $qq->whereIn('scholarship_subject_groups.code', $codes));
            })
            ->orderBy('is_daad', 'desc')
            ->limit(6)
            ->get();

        return view('scholarships.show', [
            'scholarship' => $scholarship,
            'related'     => $related,
        ]);
    }

    /**
     * Burs liste sayfası — DB tabanlı, eski statik index.
     * (statik index pages yedek olarak `scholarships/static-index` view'da kalır)
     */
    public function staticIndex(): View
    {
        return view('scholarships.index');
    }
}
