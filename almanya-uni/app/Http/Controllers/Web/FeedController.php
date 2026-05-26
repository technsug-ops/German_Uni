<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\FieldOfStudy;
use App\Models\Post;
use App\Models\State;
use App\Models\University;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * RSS 2.0 feed — blog yazıları + son enrich edilen şehir/üni/alan/eyalet.
 * Cache: 30 dakika.
 */
class FeedController extends Controller
{
    public function rss(): Response
    {
        $xml = Cache::remember('feed:rss', now()->addMinutes(30), function () {
            return $this->buildRss();
        });

        return response($xml, 200)
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }

    private function buildRss(): string
    {
        $items = collect();

        // 1. Blog yazıları
        Post::published()
            ->orderByDesc('published_at')
            ->limit(20)
            ->get(['slug', 'title', 'excerpt', 'published_at', 'updated_at'])
            ->each(function ($p) use ($items) {
                $items->push([
                    'title' => $p->title,
                    'link' => route('blog.show', $p->slug),
                    'description' => $p->excerpt ?: '',
                    'category' => 'Blog',
                    'pub_date' => $p->published_at ?: $p->updated_at,
                ]);
            });

        // 2. Son enrich edilen şehirler
        City::whereNotNull('content_blocks')
            ->orderByDesc('last_enriched_at')
            ->limit(10)
            ->get(['slug', 'name_de', 'last_enriched_at'])
            ->each(function ($c) use ($items) {
                $items->push([
                    'title' => $c->name_de . ' — Şehir Rehberi',
                    'link' => route('cities.show', $c->slug),
                    'description' => "Türk öğrenciler için {$c->name_de} şehir rehberi: üniversiteler, yaşam maliyeti, kültür.",
                    'category' => 'Şehir',
                    'pub_date' => $c->last_enriched_at,
                ]);
            });

        // 3. Son enrich edilen üniversiteler
        University::where('is_active', 1)
            ->whereNotNull('content_blocks')
            ->orderByDesc('last_enriched_at')
            ->limit(10)
            ->get(['slug', 'name_de', 'last_enriched_at'])
            ->each(function ($u) use ($items) {
                $items->push([
                    'title' => $u->name_de . ' — Üniversite',
                    'link' => route('universities.show', $u->slug),
                    'description' => "{$u->name_de} hakkında: programlar, başvuru ve kampüs rehberi.",
                    'category' => 'Üniversite',
                    'pub_date' => $u->last_enriched_at,
                ]);
            });

        // 4. Alanlar
        FieldOfStudy::active()->whereNotNull('content_blocks')
            ->orderByDesc('last_enriched_at')
            ->limit(10)
            ->get(['slug', 'name_tr', 'last_enriched_at','name_en','name_de'])
            ->each(function ($f) use ($items) {
                $items->push([
                    'title' => $f->name . ' — ' . __('Study Field in Germany'),
                    'link' => route('fields.show', $f->slug),
                    'description' => __('Programs, universities and career paths in :field in Germany.', ['field' => $f->name]),
                    'category' => __('Field'),
                    'pub_date' => $f->last_enriched_at,
                ]);
            });

        // 5. Eyaletler
        State::whereNotNull('content_blocks')
            ->orderByDesc('last_enriched_at')
            ->limit(10)
            ->get(['slug', 'name_de', 'last_enriched_at'])
            ->each(function ($s) use ($items) {
                $items->push([
                    'title' => $s->name_de . ' — Eyalet Rehberi',
                    'link' => route('states.show', $s->slug),
                    'description' => "Almanya {$s->name_de} eyaletinin şehirleri, üniversiteleri ve özellikleri.",
                    'category' => 'Eyalet',
                    'pub_date' => $s->last_enriched_at,
                ]);
            });

        // Tarihe göre sırala, top 50
        $items = $items
            ->sortByDesc(fn ($i) => $i['pub_date'] instanceof \DateTimeInterface ? $i['pub_date']->timestamp : 0)
            ->take(50)
            ->values();

        $siteName = config('seo.site_name', 'AlmanyaUni');
        $siteUrl = config('seo.organization.url', url('/'));
        $description = config('seo.default.description', 'Türk öğrenciler için Almanya rehberi');
        $now = now()->toRfc2822String();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        $xml .= "<channel>\n";
        $xml .= '  <title>' . $this->esc($siteName) . " — Yeni İçerikler</title>\n";
        $xml .= '  <link>' . $this->esc($siteUrl) . "</link>\n";
        $xml .= '  <description>' . $this->esc($description) . "</description>\n";
        $xml .= "  <language>tr-TR</language>\n";
        $xml .= "  <lastBuildDate>{$now}</lastBuildDate>\n";
        $xml .= '  <atom:link href="' . url('/rss.xml') . '" rel="self" type="application/rss+xml" />' . "\n";

        foreach ($items as $i) {
            $pubDate = $i['pub_date'] instanceof \DateTimeInterface
                ? $i['pub_date']->toRfc2822String()
                : (string) $i['pub_date'];

            $xml .= "  <item>\n";
            $xml .= '    <title>' . $this->esc($i['title']) . "</title>\n";
            $xml .= '    <link>' . $this->esc($i['link']) . "</link>\n";
            $xml .= '    <guid isPermaLink="true">' . $this->esc($i['link']) . "</guid>\n";
            $xml .= '    <description>' . $this->esc($i['description']) . "</description>\n";
            $xml .= '    <category>' . $this->esc($i['category']) . "</category>\n";
            $xml .= "    <pubDate>{$pubDate}</pubDate>\n";
            $xml .= "  </item>\n";
        }

        $xml .= "</channel>\n";
        $xml .= '</rss>';
        return $xml;
    }

    private function esc(string $s): string
    {
        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
