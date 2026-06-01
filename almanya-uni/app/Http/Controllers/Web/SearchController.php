<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\FieldOfStudy;
use App\Models\Post;
use App\Models\Profession;
use App\Models\Program;
use App\Models\Scholarship;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    private const PER_TYPE = 6;

    public function index(Request $request): View
    {
        $q = trim((string) $request->input('q', ''));

        if ($q === '') {
            return view('search.index', [
                'q' => '',
                'universities' => collect(),
                'cities' => collect(),
                'programs' => collect(),
                'posts' => collect(),
                'professions' => collect(),
                'scholarships' => collect(),
                'fields' => collect(),
                'studienkollegs' => collect(),
                'housing' => collect(),
                'sperrkonto' => collect(),
                'totals' => ['universities' => 0, 'cities' => 0, 'programs' => 0, 'posts' => 0, 'professions' => 0, 'scholarships' => 0, 'fields' => 0, 'studienkollegs' => 0, 'housing' => 0, 'sperrkonto' => 0],
                'tools' => [],
                'took_ms' => null,
            ]);
        }

        $started = microtime(true);
        $like = '%' . $q . '%';

        // Kavram/sinonim eşleşmeleri (sperrkonto = blocked account = bloke hesap → araç)
        $tools = \App\Support\SearchTools::match($q);

        // ─────────── UNIVERSITIES ───────────
        $uniBase = University::query()
            ->where('is_active', 1)
            ->searchFulltext($q, ['name_de', 'name_en', 'name_tr', 'short_name']);

        $uniTotal = (clone $uniBase)->count();
        $universities = $uniBase
            ->with('city:id,name_tr,name_en,name_de,slug,state_id', 'city.state:id,name_tr,name_en,name_de')
            ->orderByRaw("CASE WHEN name_de LIKE ? THEN 0 ELSE 1 END", [$like])
            ->orderByRelevance($q, ['name_de', 'name_en', 'name_tr', 'short_name'])
            ->orderByDesc('student_count')
            ->limit(self::PER_TYPE)
            ->get(['id', 'name_de', 'slug', 'logo_url', 'image_url', 'type', 'city_id', 'student_count', 'founded_year']);

        // ─────────── CITIES ───────────
        $cityBase = City::query()
            ->searchFulltext($q, ['name_de', 'name_tr', 'name_en'])
            ->whereHas('universities', fn ($u) => $u->where('is_active', 1));

        $cityTotal = (clone $cityBase)->count();
        $cities = $cityBase
            ->with('state:id,name_tr,name_en,name_de')
            ->withCount(['universities' => fn ($q) => $q->where('is_active', 1)])
            ->orderByRaw("CASE WHEN name_de LIKE ? OR name_tr LIKE ? THEN 0 ELSE 1 END", [$like, $like])
            ->orderByRelevance($q, ['name_de', 'name_tr', 'name_en'])
            ->orderByDesc('universities_count')
            ->limit(self::PER_TYPE)
            ->get(['id', 'name_de', 'name_tr', 'slug', 'image_url', 'state_id', 'population','name_en','name_de']);

        // ─────────── PROGRAMS ───────────
        $progBase = Program::query()
            ->where('is_active', 1)
            ->searchFulltext($q, ['name_de', 'name_en', 'name_tr', 'description_tr', 'description_en']);

        $progTotal = (clone $progBase)->count();
        $programs = $progBase
            ->with('university:id,name_de,slug,logo_url', 'field:id,name_tr,name_en,name_de,color,icon')
            ->orderByRaw("CASE WHEN name_de LIKE ? THEN 0 ELSE 1 END", [$like])
            ->orderByRelevance($q, ['name_de', 'name_en', 'name_tr', 'description_tr', 'description_en'])
            ->orderBy('name_de')
            ->limit(self::PER_TYPE)
            ->get(['id', 'name_de', 'name_en', 'name_tr', 'slug', 'university_id', 'degree', 'language', 'field_of_study_id']);

        // ─────────── BLOG POSTS ───────────
        $postBase = Post::query()
            ->where('is_published', 1)
            ->whereNotNull('published_at')
            ->where(function ($w) use ($like) {
                $w->where('title', 'like', $like)
                    ->orWhere('excerpt', 'like', $like)
                    ->orWhere('content_md', 'like', $like);
            });

        $postTotal = (clone $postBase)->count();
        $posts = $postBase
            ->with('category')
            ->orderByRaw("CASE WHEN title LIKE ? THEN 0 ELSE 1 END", [$like])
            ->orderByDesc('published_at')
            ->limit(self::PER_TYPE)
            ->get(['id', 'title', 'slug', 'excerpt', 'reading_minutes', 'featured_image', 'category_id', 'published_at']);

        // ─────────── PROFESSIONS ───────────
        $profBase = Profession::query()
            ->where('is_active', 1)
            ->searchFulltext($q, ['name_de', 'name_tr', 'description_tr', 'description_de']);

        $profTotal = (clone $profBase)->count();
        $professions = $profBase
            ->with('field:id,name_tr,name_en,name_de,icon,color')
            ->orderByRaw("CASE WHEN name_de LIKE ? OR name_tr LIKE ? THEN 0 ELSE 1 END", [$like, $like])
            ->orderByRelevance($q, ['name_de', 'name_tr', 'description_tr', 'description_de'])
            ->orderBy('name_de')
            ->limit(self::PER_TYPE)
            ->get(['id', 'name_de', 'name_tr', 'slug', 'kldb_code', 'type', 'field_of_study_id','name_en','name_de']);

        // ─────────── SCHOLARSHIPS ───────────
        $scholarBase = Scholarship::query()
            ->whereNull('removed_at')
            ->where(function ($w) use ($like) {
                $w->where('name_en', 'like', $like)
                    ->orWhere('name_de', 'like', $like)
                    ->orWhere('programmname_en', 'like', $like)
                    ->orWhere('programmname_de', 'like', $like);
            });

        $scholarTotal = (clone $scholarBase)->count();
        $scholarships = $scholarBase
            ->orderByDesc('is_daad')
            ->orderBy('name_en')
            ->limit(self::PER_TYPE)
            ->get(['id', 'name_en', 'name_de', 'programmname_en', 'slug', 'is_daad']);

        // ─────────── FIELDS (Eğitim Alanları) ───────────
        $fieldBase = FieldOfStudy::query()
            ->where('is_active', 1)
            ->where(function ($w) use ($like) {
                $w->where('name_tr', 'like', $like)
                    ->orWhere('name_de', 'like', $like)
                    ->orWhere('name_en', 'like', $like);
            });

        $fieldTotal = (clone $fieldBase)->count();
        $fields = $fieldBase
            ->orderBy('sort_order')
            ->orderBy('name_tr')
            ->limit(self::PER_TYPE)
            ->get(['id', 'name_tr', 'name_de', 'slug', 'icon', 'color','name_en','name_de']);

        // ─────────── STUDIENKOLLEGS ───────────
        $skBase = \App\Models\Studienkolleg::query()
            ->where('is_active', 1)
            ->where(function ($w) use ($like) {
                $w->where('name', 'like', $like)
                    ->orWhere('city_name_cache', 'like', $like);
            });
        $skTotal = (clone $skBase)->count();
        $studienkollegs = $skBase
            ->orderBy('name')
            ->limit(self::PER_TYPE)
            ->get(['id', 'slug', 'name', 'city_name_cache', 'type', 'tracks', 'semester_fee_eur']);

        // ─────────── HOUSING PROVIDERS ───────────
        $hpBase = \App\Models\HousingProvider::query()
            ->where('name', 'like', $like);
        $hpTotal = (clone $hpBase)->count();
        $housing = $hpBase
            ->orderBy('name')
            ->limit(self::PER_TYPE)
            ->get(['id', 'slug', 'name', 'type', 'logo_url']);

        // ─────────── SPERRKONTO PROVIDERS ───────────
        $baBase = \App\Models\BlockedAccountProvider::query()
            ->where('is_published', 1)
            ->where('name', 'like', $like);
        $baTotal = (clone $baBase)->count();
        $sperrkonto = $baBase
            ->orderBy('name')
            ->limit(self::PER_TYPE)
            ->get(['id', 'slug', 'name', 'type', 'yearly_fee_eur', 'logo_url']);

        $tookMs = (int) ((microtime(true) - $started) * 1000);

        // Arama logu — içerik fırsat analizi için (özellikle results_count=0 olanlar değerli).
        // Bot + çok kısa sorgular hariç.
        $ua = (string) $request->userAgent();
        if (mb_strlen($q) >= 2 && ! preg_match('/bot|crawl|spider|slurp/i', $ua)) {
            $breakdown = [
                'universities' => $uniTotal, 'cities' => $cityTotal, 'programs' => $progTotal,
                'posts' => $postTotal, 'professions' => $profTotal,
                'scholarships' => $scholarTotal, 'fields' => $fieldTotal,
                'studienkollegs' => $skTotal, 'housing' => $hpTotal, 'sperrkonto' => $baTotal,
            ];
            \App\Models\SearchQuery::create([
                'query'         => mb_strtolower($q),
                'query_raw'     => mb_substr($q, 0, 255),
                'results_count' => array_sum($breakdown),
                'breakdown'     => $breakdown,
                'session_id'    => substr(hash('sha256', $request->session()->getId() ?: $request->ip()), 0, 64),
                'took_ms'       => min(65535, $tookMs),
                'created_at'    => now(),
            ]);
        }

        return view('search.index', [
            'q' => $q,
            'universities' => $universities,
            'cities' => $cities,
            'programs' => $programs,
            'posts' => $posts,
            'professions' => $professions,
            'scholarships' => $scholarships,
            'fields' => $fields,
            'studienkollegs' => $studienkollegs,
            'housing' => $housing,
            'sperrkonto' => $sperrkonto,
            'totals' => [
                'universities' => $uniTotal,
                'cities' => $cityTotal,
                'programs' => $progTotal,
                'posts' => $postTotal,
                'professions' => $profTotal,
                'scholarships' => $scholarTotal,
                'fields' => $fieldTotal,
                'studienkollegs' => $skTotal,
                'housing' => $hpTotal,
                'sperrkonto' => $baTotal,
            ],
            'tools' => $tools,
            'took_ms' => $tookMs,
        ]);
    }
}
