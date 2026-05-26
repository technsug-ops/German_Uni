<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\University;
use App\Services\RankingService;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RankingController extends Controller
{
    public function __construct(private RankingService $rankings)
    {
    }

    public function index(): View
    {
        $all = $this->rankings->all();

        $grouped = [];
        foreach ($all as $r) {
            $grouped[$r['category']][] = $r;
        }

        // Hero stats — gerçek DB rakamları
        $stats = [
            'total_rankings' => count($all),
            'largest_uni'    => University::where('is_active', 1)->orderByDesc('student_count')->first(['name_de', 'student_count', 'slug']),
            'oldest_uni'     => University::where('is_active', 1)->whereNotNull('founded_year')->where('founded_year', '>', 1000)->orderBy('founded_year')->first(['name_de', 'founded_year', 'slug']),
            'newest_uni'     => University::where('is_active', 1)->whereNotNull('founded_year')->orderByDesc('founded_year')->first(['name_de', 'founded_year', 'slug']),
        ];

        return view('rankings.index', [
            'grouped' => $grouped,
            'total' => count($all),
            'stats' => $stats,
        ]);
    }

    public function show(string $slug)
    {
        // Legacy TR slug → 301 redirect to new EN slug
        if (isset(\App\Services\RankingService::SLUG_REDIRECTS[$slug])) {
            return redirect()->route('rankings.show', \App\Services\RankingService::SLUG_REDIRECTS[$slug], 301);
        }

        // Eski eyalet TR slug ({state}-universiteleri) → yeni EN ({state}-universities)
        if (str_ends_with($slug, '-universiteleri')) {
            $newSlug = substr($slug, 0, -strlen('-universiteleri')) . '-universities';
            return redirect()->route('rankings.show', $newSlug, 301);
        }

        // Eski alan TR slug (en-iyi-{field}-universiteleri) → yeni EN (best-{field}-universities)
        if (str_starts_with($slug, 'en-iyi-') && str_ends_with($slug, '-universiteleri')) {
            $field = substr($slug, strlen('en-iyi-'), -strlen('-universiteleri'));
            return redirect()->route('rankings.show', 'best-' . $field . '-universities', 301);
        }

        $config = $this->rankings->resolve($slug);
        if (!$config) {
            throw new NotFoundHttpException("Ranking not found: {$slug}");
        }

        $universities = $this->rankings->fetchTop($config, 50);

        return view('rankings.show', [
            'config' => $config,
            'universities' => $universities,
        ]);
    }
}
