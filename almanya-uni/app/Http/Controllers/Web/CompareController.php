<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompareController extends Controller
{
    private const MAX_ITEMS = 4;

    public function index(Request $request): View
    {
        $selectedSlugs = $this->parseSlugs($request->input('slugs'), $request);
        $selected = $this->loadSelected($selectedSlugs);

        $candidates = [];
        if ($request->filled('q')) {
            $term = $request->input('q');
            $candidates = University::query()
                ->where('is_active', true)
                ->with('city.state')
                ->where(function ($w) use ($term) {
                    $w->where('name_de', 'like', "%$term%")
                        ->orWhere('name_tr', 'like', "%$term%")
                        ->orWhere('name_en', 'like', "%$term%")
                        ->orWhere('short_name', 'like', "%$term%");
                })
                ->whereNotIn('slug', $selectedSlugs)
                ->orderBy('name_de')
                ->limit(15)
                ->get()
                ->map(fn ($u) => [
                    'slug' => $u->slug,
                    'name_de' => $u->name_de,
                    'logo_url' => $u->logo_url,
                    'image_url' => $u->image_url,
                    'city_name' => $u->city?->name,
                    'state_name' => $u->city?->state?->name,
                ])
                ->toArray();
        }

        return view('compare.index', [
            'selected' => $selected,
            'candidates' => $candidates,
            'q' => $request->input('q', ''),
            'max_items' => self::MAX_ITEMS,
            'can_add_more' => count($selected) < self::MAX_ITEMS,
        ]);
    }

    public function show(Request $request): View
    {
        $selectedSlugs = $this->parseSlugs($request->input('slugs'), $request);
        $universities = $this->loadSelected($selectedSlugs, fullDetail: true);

        return view('compare.show', [
            'universities' => $universities,
            'slug_csv' => implode(',', $selectedSlugs),
            'too_few' => count($universities) < 2,
        ]);
    }

    /**
     * Seçili üniler: önce ?slugs= (slug CSV), yoksa ?ids= (id CSV → slug çözümü).
     * Öneri testi gibi yerler id geçebilir; ikisini de destekle (önceden 0/4 boş kalıyordu).
     */
    private function parseSlugs(?string $raw, ?Request $request = null): array
    {
        if (! $raw && $request && $request->filled('ids')) {
            $ids = collect(explode(',', (string) $request->input('ids')))
                ->map(fn ($s) => (int) trim($s))
                ->filter()
                ->unique()
                ->take(self::MAX_ITEMS)
                ->all();
            if ($ids) {
                // id sırasını koru
                $bySlug = University::whereIn('id', $ids)->pluck('slug', 'id');
                return collect($ids)->map(fn ($id) => $bySlug[$id] ?? null)->filter()->values()->all();
            }
        }

        if (!$raw) {
            return [];
        }
        return collect(explode(',', $raw))
            ->map(fn ($s) => trim($s))
            ->filter()
            ->unique()
            ->take(self::MAX_ITEMS)
            ->values()
            ->all();
    }

    private function loadSelected(array $slugs, bool $fullDetail = false): array
    {
        if (empty($slugs)) {
            return [];
        }

        $unis = University::query()
            ->with(['city.state'])
            ->whereIn('slug', $slugs)
            ->get()
            ->keyBy('slug');

        $ordered = [];
        foreach ($slugs as $slug) {
            $u = $unis->get($slug);
            if (!$u) {
                continue;
            }

            $base = [
                'slug' => $u->slug,
                'name_de' => $u->name_de,
                'logo_url' => $u->logo_url,
                'image_url' => $u->image_url,
                'city_name' => $u->city?->name,
                'city_slug' => $u->city?->slug,
                'state_name' => $u->city?->state?->name,
                'type' => $u->type,
                'founded_year' => $u->founded_year,
                'student_count' => $u->student_count,
            ];

            if ($fullDetail) {
                // Programs breakdown
                $progStats = Program::where('university_id', $u->id)
                    ->where('is_active', 1)
                    ->selectRaw("
                        COUNT(*) AS total,
                        SUM(CASE WHEN degree = 'bachelor' THEN 1 ELSE 0 END) AS bachelor,
                        SUM(CASE WHEN degree = 'master' THEN 1 ELSE 0 END) AS master,
                        SUM(CASE WHEN degree = 'phd' THEN 1 ELSE 0 END) AS phd,
                        SUM(CASE WHEN language IN ('en', 'both') THEN 1 ELSE 0 END) AS english
                    ")
                    ->first();

                $progTotal = (int) ($progStats->total ?? 0);
                $progEnglish = (int) ($progStats->english ?? 0);

                // Top 3 alan — bu üni'nin program sayısına göre güçlü olduğu alanlar
                $topFields = Program::where('programs.university_id', $u->id)
                    ->where('programs.is_active', 1)
                    ->whereNotNull('programs.field_of_study_id')
                    ->join('fields_of_study', 'programs.field_of_study_id', '=', 'fields_of_study.id')
                    ->selectRaw('fields_of_study.id, fields_of_study.name_tr, fields_of_study.slug, fields_of_study.icon, COUNT(*) as cnt')
                    ->groupBy('fields_of_study.id', 'fields_of_study.name_tr', 'fields_of_study.slug', 'fields_of_study.icon')
                    ->orderByDesc('cnt')
                    ->limit(3)
                    ->get()
                    ->map(fn ($f) => [
                        'name' => $f->name_tr,
                        'slug' => $f->slug,
                        'icon' => $f->icon,
                        'count' => (int) $f->cnt,
                    ])
                    ->all();

                // Şehir cost_of_living teaser
                $cityBlocks = $u->city?->content_blocks ?? [];
                $cityCost = collect($cityBlocks)->firstWhere('type', 'cost_of_living');

                // Şehir nüfusu + boyut kategorisi
                $cityPop = $u->city?->population;
                $citySize = null;
                if ($cityPop) {
                    $citySize = $cityPop > 1_000_000 ? 'metropol'
                        : ($cityPop > 200_000 ? 'orta' : 'küçük');
                }

                // intro snippet (content_blocks'tan)
                $intro = collect($u->content_blocks ?? [])->firstWhere('type', 'intro');

                $base += [
                    'name_en' => $u->name_en,
                    'short_name' => $u->short_name,
                    'website_url' => $u->website_url,
                    'description_de' => $u->description_de,
                    'description_en' => $u->description_en,
                    'wikipedia_url_de' => $u->wikipedia_url_de,
                    'wikipedia_url_en' => $u->wikipedia_url_en,
                    'latitude' => $u->latitude,
                    'longitude' => $u->longitude,
                    'is_uni_assist_member' => (bool) $u->is_uni_assist_member,
                    'hrk_member' => (bool) ($u->hrk_member ?? false),
                    'has_content' => !empty($u->content_blocks),
                    'intro_snippet' => $intro ? mb_substr(strip_tags($intro['body_md'] ?? ''), 0, 200) . '…' : null,
                    'programs' => [
                        'total' => $progTotal,
                        'bachelor' => (int) ($progStats->bachelor ?? 0),
                        'master' => (int) ($progStats->master ?? 0),
                        'phd' => (int) ($progStats->phd ?? 0),
                        'english' => $progEnglish,
                        'english_pct' => $progTotal > 0 ? round(($progEnglish / $progTotal) * 100) : 0,
                    ],
                    'top_fields' => $topFields,
                    'city_population' => $cityPop,
                    'city_size' => $citySize,
                    'city_cost' => $cityCost,
                ];
            }

            $ordered[] = $base;
        }

        return $ordered;
    }
}
