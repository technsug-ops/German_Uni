<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Faq;
use App\Models\FaqTopic;
use App\Models\FieldOfStudy;
use App\Models\Post;
use App\Models\Profession;
use App\Models\Program;
use App\Models\Scholarship;
use App\Models\State;
use App\Models\University;
use App\Services\RankingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    public function index(Request $request, RankingService $rankings): Response
    {
        // ── DOMAIN-AWARE LOCALE ────────────────────────────────────────────
        // Sitemap rotası middleware'siz tanımlandığı için manuel locale belirleme.
        // Host → brand → default_locale (almanyauni.com=tr, applytogerman.com=en)
        $host = strtolower(preg_replace('/^www\./', '', $request->getHost()));
        $domains = config('brand.domains', []);
        $brandKey = $domains[$host] ?? config('brand.fallback', 'almanyauni');
        $defaultLocale = config('brand.brands')[$brandKey]['default_locale'] ?? 'tr';
        App::setLocale($defaultLocale);
        URL::defaults(['locale' => $defaultLocale]);

        // Hreflang alternates: aktif tüm diller
        $activeLocales = collect(config('locale.locales', []))
            ->filter(fn ($c) => ! empty($c['active']) && empty($c['coming_soon']))
            ->keys()
            ->all();

        $urls = [];

        $urls[] = $this->entry(route('home'), now(), 'daily', 1.0);

        $urls[] = $this->entry(route('universities.index'), now(), 'daily', 0.9);
        $urls[] = $this->entry(route('cities.index'), now(), 'daily', 0.9);
        $urls[] = $this->entry(route('fields.index'), now(), 'weekly', 0.9);
        $urls[] = $this->entry(route('states.index'), now(), 'weekly', 0.9);
        $urls[] = $this->entry(route('scholarships.index'), now(), 'weekly', 0.9);
        $urls[] = $this->entry(route('scholarships.daad'), now(), 'weekly', 0.95);
        $urls[] = $this->entry(route('rankings.index'), now(), 'weekly', 0.8);
        $urls[] = $this->entry(route('compare.index'), now(), 'monthly', 0.5);
        $urls[] = $this->entry(route('blog.index'), now(), 'daily', 0.8);
        $urls[] = $this->entry(route('faqs.index'), now(), 'weekly', 0.9);
        $urls[] = $this->entry(route('about'), now(), 'monthly', 0.7);

        $urls[] = $this->entry(route('map.index'), now(), 'weekly', 0.8);
        $urls[] = $this->entry(route('programs.index'), now(), 'daily', 0.9);
        $urls[] = $this->entry(route('professions.index'), now(), 'daily', 0.9);
        $urls[] = $this->entry(route('housing.index'), now(), 'weekly', 0.8);
        $urls[] = $this->entry(route('tools.index'), now(), 'weekly', 0.8);
        $urls[] = $this->entry(route('tools.cost-of-living'), now(), 'monthly', 0.7);
        $urls[] = $this->entry(route('tools.grade-converter'), now(), 'monthly', 0.7);
        $urls[] = $this->entry(route('tools.recommendation'), now(), 'monthly', 0.6);
        $urls[] = $this->entry(route('tools.studienkolleg'), now(), 'monthly', 0.8);
        $urls[] = $this->entry(route('tools.eligibility-checker'), now(), 'monthly', 0.85);
        $urls[] = $this->entry(route('tools.blocked-account'), now(), 'monthly', 0.85);
        $urls[] = $this->entry(route('tools.visa-cost'), now(), 'monthly', 0.7);
        $urls[] = $this->entry(route('tools.budget-planner'), now(), 'monthly', 0.7);
        $urls[] = $this->entry(route('tools.deadlines'), now(), 'weekly', 0.75);
        $urls[] = $this->entry(route('tools.career-compass'), now(), 'monthly', 0.7);
        $urls[] = $this->entry(route('housing.providers'), now(), 'weekly', 0.8);
        $urls[] = $this->entry(route('pricing'), now(), 'monthly', 0.7);

        // Sperrkonto provider show pages (5 sağlayıcı)
        foreach (\App\Models\BlockedAccountProvider::where('is_published', 1)->get(['slug', 'updated_at']) as $p) {
            $urls[] = $this->entry(
                route('tools.blocked-account.show', $p->slug),
                $p->updated_at,
                'monthly',
                0.7
            );
        }

        foreach (FaqTopic::active()->orderBy('sort_order')->get(['slug', 'updated_at']) as $t) {
            $urls[] = $this->entry(
                route('faqs.topic', $t->slug),
                $t->updated_at,
                'weekly',
                0.8
            );
        }

        foreach ($rankings->all() as $r) {
            $urls[] = $this->entry(
                route('rankings.show', $r['slug']),
                now(),
                'weekly',
                0.7
            );
        }

        // Üniversite sayfaları — content_blocks varsa yüksek priority + last_enriched_at lastmod
        University::where('is_active', true)
            ->select(['slug', 'updated_at', 'last_enriched_at', 'content_blocks'])
            ->orderBy('id')
            ->chunk(500, function ($chunk) use (&$urls) {
                foreach ($chunk as $u) {
                    $enriched = !empty($u->content_blocks);
                    $urls[] = $this->entry(
                        route('universities.show', $u->slug),
                        $u->last_enriched_at ?: $u->updated_at,
                        $enriched ? 'weekly' : 'monthly',
                        $enriched ? 0.8 : 0.5
                    );
                }
            });

        // Şehir sayfaları — content_blocks olanlar yüksek priority (zengin içerik)
        City::whereHas('universities', fn ($q) => $q->where('is_active', 1))
            ->select(['slug', 'updated_at', 'last_enriched_at', 'content_blocks'])
            ->orderBy('id')
            ->chunk(500, function ($chunk) use (&$urls) {
                foreach ($chunk as $c) {
                    $enriched = !empty($c->content_blocks);
                    $urls[] = $this->entry(
                        route('cities.show', $c->slug),
                        $c->last_enriched_at ?: $c->updated_at,
                        $enriched ? 'weekly' : 'monthly',
                        $enriched ? 0.8 : 0.5
                    );
                }
            });

        Post::published()
            ->select(['slug', 'updated_at'])
            ->orderBy('id')
            ->chunk(500, function ($chunk) use (&$urls) {
                foreach ($chunk as $p) {
                    $urls[] = $this->entry(
                        route('blog.show', $p->slug),
                        $p->updated_at,
                        'monthly',
                        0.7
                    );
                }
            });

        Faq::published()
            ->with('topic:id,slug')
            ->select(['id', 'slug', 'updated_at', 'has_answer', 'faq_topic_id'])
            ->orderBy('id')
            ->chunk(500, function ($chunk) use (&$urls) {
                foreach ($chunk as $f) {
                    if (!$f->topic) continue;
                    $urls[] = $this->entry(
                        route('faqs.show', [$f->topic->slug, $f->slug]),
                        $f->updated_at,
                        $f->has_answer ? 'monthly' : 'yearly',
                        $f->has_answer ? 0.7 : 0.4
                    );
                }
            });

        Program::where('is_active', true)
            ->select(['slug', 'updated_at', 'description_tr'])
            ->orderBy('id')
            ->chunk(1000, function ($chunk) use (&$urls) {
                foreach ($chunk as $p) {
                    $urls[] = $this->entry(
                        route('programs.show', $p->slug),
                        $p->updated_at,
                        'monthly',
                        $p->description_tr ? 0.7 : 0.5
                    );
                }
            });

        Profession::where('is_active', true)
            ->select(['slug', 'updated_at', 'description_tr', 'description_de'])
            ->orderBy('id')
            ->chunk(1000, function ($chunk) use (&$urls) {
                foreach ($chunk as $p) {
                    $hasContent = ! empty($p->description_tr) || ! empty($p->description_de);
                    $urls[] = $this->entry(
                        route('professions.show', $p->slug),
                        $p->updated_at,
                        $hasContent ? 'monthly' : 'yearly',
                        $hasContent ? ($p->description_tr ? 0.75 : 0.6) : 0.4
                    );
                }
            });

        // DAAD bursları — her aktif burs için ayrı show sayfası
        Scholarship::whereNull('removed_at')
            ->select(['slug', 'updated_at', 'is_daad'])
            ->orderBy('id')
            ->chunk(500, function ($chunk) use (&$urls) {
                foreach ($chunk as $s) {
                    if (! $s->slug) continue;
                    $urls[] = $this->entry(
                        route('scholarships.show', $s->slug),
                        $s->updated_at,
                        'monthly',
                        $s->is_daad ? 0.8 : 0.7
                    );
                }
            });

        // Programmatic SEO: /subjects/{slug}/nc-free
        foreach (FieldOfStudy::active()->get(['slug', 'updated_at']) as $f) {
            $urls[] = $this->entry(
                route('admission-free.by-subject', $f->slug),
                $f->updated_at,
                'weekly',
                0.7
            );
        }

        // Yeni: /fields/{slug} — alan sayfaları (content_blocks varsa yüksek priority)
        foreach (FieldOfStudy::active()->get(['slug', 'updated_at', 'last_enriched_at', 'content_blocks']) as $f) {
            $enriched = !empty($f->content_blocks);
            $urls[] = $this->entry(
                route('fields.show', $f->slug),
                $f->last_enriched_at ?: $f->updated_at,
                $enriched ? 'weekly' : 'monthly',
                $enriched ? 0.85 : 0.6
            );
        }

        // Yeni: /states/{slug} — eyalet sayfaları
        foreach (State::all(['slug', 'updated_at', 'last_enriched_at', 'content_blocks']) as $s) {
            $enriched = !empty($s->content_blocks);
            $urls[] = $this->entry(
                route('states.show', $s->slug),
                $s->last_enriched_at ?: $s->updated_at,
                $enriched ? 'weekly' : 'monthly',
                $enriched ? 0.85 : 0.6
            );
        }

        // Programmatic SEO: /universities/{slug}/nc-free (~949 URL)
        University::where('is_active', true)
            ->select(['slug', 'updated_at'])
            ->orderBy('id')
            ->chunk(500, function ($chunk) use (&$urls) {
                foreach ($chunk as $u) {
                    $urls[] = $this->entry(
                        route('admission-free.by-university', $u->slug),
                        $u->updated_at,
                        'weekly',
                        0.6
                    );
                }
            });

        $xml = $this->buildXml($urls, $activeLocales);

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    private function entry(string $url, $lastmod, string $changefreq, float $priority): array
    {
        return [
            'loc' => $url,
            'lastmod' => $lastmod instanceof \DateTimeInterface
                ? $lastmod->format('Y-m-d')
                : (string) $lastmod,
            'changefreq' => $changefreq,
            'priority' => number_format($priority, 1),
        ];
    }

    /**
     * URL'in locale prefix'ini hedef locale ile değiştir (hreflang alternate üretimi için).
     * https://host.com/en/foo + locale='tr' → https://host.com/tr/foo
     */
    private function swapLocale(string $url, string $target, array $allLocales): string
    {
        $parts = parse_url($url);
        $path = $parts['path'] ?? '/';
        $segments = array_values(array_filter(explode('/', $path)));

        if (! empty($segments[0]) && in_array($segments[0], $allLocales, true)) {
            array_shift($segments);
        }

        $newPath = '/' . $target . ($segments ? '/' . implode('/', $segments) : '');
        return ($parts['scheme'] ?? 'https') . '://' . ($parts['host'] ?? '') . $newPath;
    }

    private function buildXml(array $urls, array $activeLocales): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>{$u['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$u['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$u['priority']}</priority>\n";

            // hreflang alternates — multilang SEO
            foreach ($activeLocales as $loc) {
                $altUrl = $this->swapLocale($u['loc'], $loc, $activeLocales);
                $xml .= '    <xhtml:link rel="alternate" hreflang="' . $loc . '" href="' . htmlspecialchars($altUrl, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '"/>' . "\n";
            }
            $xDefault = $this->swapLocale($u['loc'], $activeLocales[0] ?? 'tr', $activeLocales);
            $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($xDefault, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '"/>' . "\n";

            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';
        return $xml;
    }
}
