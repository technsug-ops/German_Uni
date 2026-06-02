<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\FieldOfStudy;
use App\Models\Post;
use App\Models\Profession;
use App\Models\Program;
use App\Models\Scholarship;
use App\Models\State;
use App\Models\University;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SearchSuggestController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['results' => [], 'q' => $q]);
        }

        $key = 'search:suggest:v3:' . md5(mb_strtolower($q));
        $results = Cache::remember($key, 300, function () use ($q) {
            $like = '%' . $q . '%';
            // Kavram/sinonim eşleşmeleri en üstte (sperrkonto = blocked account = bloke hesap)
            $all = \App\Support\SearchTools::match($q);

            // Universities (top 4)
            University::query()
                ->where('is_active', 1)
                ->searchFulltext($q, ['name_de', 'name_en', 'name_tr', 'short_name'])
                ->orderByDesc('student_count')
                ->limit(4)
                ->get(['id', 'slug', 'name_de', 'short_name', 'image_url', 'logo_url', 'city_id'])
                ->each(function ($u) use (&$all) {
                    $all[] = [
                        'type' => 'university',
                        'type_label' => '🎓 Üniversite',
                        'title' => $u->name_de,
                        'subtitle' => $u->short_name,
                        'url' => "/universities/{$u->slug}",
                        'image' => $u->image_url ?: $u->logo_url,
                    ];
                });

            // Cities (top 3)
            City::query()
                ->searchFulltext($q, ['name_de', 'name_tr', 'name_en'])
                ->whereHas('universities', fn ($u) => $u->where('is_active', 1))
                ->withCount(['universities' => fn ($q) => $q->where('is_active', 1)])
                ->orderByDesc('universities_count')
                ->limit(3)
                ->get(['id', 'slug', 'name_de', 'name_tr', 'image_url','name_en','name_de'])
                ->each(function ($c) use (&$all) {
                    $all[] = [
                        'type' => 'city',
                        'type_label' => '🏙️ Şehir',
                        'title' => $c->name_de,
                        'subtitle' => "{$c->universities_count} üniversite",
                        'url' => "/cities/{$c->slug}",
                        'image' => $c->image_url,
                    ];
                });

            // Programs (top 3)
            Program::query()
                ->where('is_active', 1)
                ->searchFulltext($q, ['name_de', 'name_en', 'name_tr', 'description_tr', 'description_en'])
                ->with('university:id,slug,name_de,logo_url')
                ->orderBy('name_de')
                ->limit(3)
                ->get(['id', 'slug', 'name_de', 'degree', 'university_id'])
                ->each(function ($p) use (&$all) {
                    $all[] = [
                        'type' => 'program',
                        'type_label' => '📚 Program',
                        'title' => $p->name_de,
                        'subtitle' => ucfirst($p->degree) . ($p->university ? " · {$p->university->name_de}" : ''),
                        'url' => "/programs/{$p->slug}",
                        'image' => $p->university?->logo_url,
                    ];
                });

            // Fields (top 2)
            FieldOfStudy::query()
                ->active()
                ->where(fn ($w) => $w->where('name_tr', 'like', $like)
                    ->orWhere('name_de', 'like', $like))
                ->limit(2)
                ->get(['slug', 'name_tr', 'name_de', 'icon', 'image_url','name_en','name_de'])
                ->each(function ($f) use (&$all) {
                    $all[] = [
                        'type' => 'field',
                        'type_label' => '🎯 Alan',
                        'title' => $f->name_tr,
                        'subtitle' => $f->name_de,
                        'url' => "/fields/{$f->slug}",
                        'image' => $f->image_url,
                        'icon' => $f->icon,
                    ];
                });

            // States (top 2)
            State::query()
                ->where(fn ($w) => $w->where('name_de', 'like', $like)
                    ->orWhere('name_tr', 'like', $like))
                ->limit(2)
                ->get(['slug', 'name_de', 'name_tr', 'image_url','name_en','name_de'])
                ->each(function ($s) use (&$all) {
                    $all[] = [
                        'type' => 'state',
                        'type_label' => '🗺️ Eyalet',
                        'title' => $s->name_de,
                        'subtitle' => $s->name_tr,
                        'url' => "/states/{$s->slug}",
                        'image' => $s->image_url,
                    ];
                });

            // Professions (top 3) — Türk öğrenciler için kritik
            Profession::query()
                ->where('is_active', 1)
                ->searchFulltext($q, ['name_de', 'name_tr', 'description_tr', 'description_de'])
                ->with('field:id,icon')
                ->orderByRaw("CASE WHEN name_de LIKE ? OR name_tr LIKE ? THEN 0 ELSE 1 END", [$like, $like])
                ->limit(3)
                ->get(['id', 'slug', 'name_de', 'name_tr', 'kldb_code', 'field_of_study_id','name_en','name_de'])
                ->each(function ($p) use (&$all) {
                    $all[] = [
                        'type' => 'profession',
                        'type_label' => '💼 Meslek',
                        'title' => $p->name_tr ?: $p->name_de,
                        'subtitle' => $p->name_tr && $p->name_de !== $p->name_tr ? $p->name_de : ($p->kldb_code ? "KldB {$p->kldb_code}" : null),
                        'url' => "/professions/{$p->slug}",
                        'icon' => $p->field?->icon ?: '💼',
                        'image' => null,
                    ];
                });

            // Scholarships (top 2) — DAAD önce
            Scholarship::query()
                ->whereNull('removed_at')
                ->where(fn ($w) => $w->where('name_en', 'like', $like)
                    ->orWhere('name_de', 'like', $like)
                    ->orWhere('programmname_en', 'like', $like))
                ->orderByDesc('is_daad')
                ->limit(2)
                ->get(['id', 'slug', 'name_en', 'name_de', 'is_daad'])
                ->each(function ($s) use (&$all) {
                    $all[] = [
                        'type' => 'scholarship',
                        'type_label' => $s->is_daad ? '🎖️ DAAD Bursu' : '🎖️ Burs',
                        'title' => $s->name_en ?: $s->name_de,
                        'subtitle' => null,
                        'url' => "/scholarships/{$s->slug}",
                        'icon' => '🎖️',
                        'image' => null,
                    ];
                });

            // Studienkollegs (top 2)
            \App\Models\Studienkolleg::query()
                ->where('is_active', 1)
                ->where(fn ($w) => $w->where('name', 'like', $like)
                    ->orWhere('city_name_cache', 'like', $like))
                ->limit(2)
                ->get(['id', 'slug', 'name', 'city_name_cache', 'type'])
                ->each(function ($s) use (&$all) {
                    $all[] = [
                        'type' => 'studienkolleg',
                        'type_label' => '🎓 Studienkolleg',
                        'title' => $s->name,
                        'subtitle' => $s->city_name_cache . ($s->type === 'privat' ? ' · Özel' : ' · Devlet'),
                        'url' => "/tools/studienkolleg#" . $s->slug,
                        'icon' => '🎓',
                        'image' => null,
                    ];
                });

            // Housing providers (top 2)
            \App\Models\HousingProvider::query()
                ->where('name', 'like', $like)
                ->limit(2)
                ->get(['id', 'slug', 'name', 'type'])
                ->each(function ($h) use (&$all) {
                    $all[] = [
                        'type' => 'housing',
                        'type_label' => '🏠 Yurt sağlayıcı',
                        'title' => $h->name,
                        'subtitle' => $h->type ?: null,
                        'url' => "/housing/providers/{$h->slug}",
                        'icon' => '🏠',
                        'image' => null,
                    ];
                });

            // Blocked account providers (top 2)
            \App\Models\BlockedAccountProvider::query()
                ->where('is_published', 1)
                ->where('name', 'like', $like)
                ->limit(2)
                ->get(['id', 'slug', 'name', 'type'])
                ->each(function ($b) use (&$all) {
                    $all[] = [
                        'type' => 'sperrkonto',
                        'type_label' => '🏦 Sperrkonto',
                        'title' => $b->name,
                        'subtitle' => $b->type === 'fintech' ? 'FinTech' : 'Banka',
                        'url' => "/tools/sperrkonto/{$b->slug}",
                        'icon' => '🏦',
                        'image' => null,
                    ];
                });

            // Blog posts (top 2) — published() = aktif locale (TR'de DE/EN sızmaz)
            Post::published()
                ->where(fn ($w) => $w->where('title', 'like', $like)
                    ->orWhere('excerpt', 'like', $like))
                ->with('category:id,name,color')
                ->orderByDesc('published_at')
                ->limit(2)
                ->get(['id', 'slug', 'title', 'category_id', 'featured_image', 'reading_minutes'])
                ->each(function ($p) use (&$all) {
                    $all[] = [
                        'type' => 'post',
                        'type_label' => '📝 Blog · ' . ($p->category?->name ?? 'Yazı'),
                        'title' => $p->title,
                        'subtitle' => $p->reading_minutes . ' dk okuma',
                        'url' => "/blog/{$p->slug}",
                        'image' => $p->featured_image,
                        'icon' => '📝',
                    ];
                });

            return $all;
        });

        return response()->json([
            'q' => $q,
            'results' => $results,
            'all_url' => route('search.index', ['q' => $q]),
        ]);
    }
}
