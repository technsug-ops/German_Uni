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
    /**
     * Bir dosyadaki en fazla URL. Protokol sınırı 50.000; pay bırakılır. Bir dil bunu
     * aşarsa dosyası otomatik parçalanır (/sitemap-{lang}-2.xml …).
     */
    private const MAX_URLS_PER_FILE = 45000;

    /** Test için config'den küçültülebilir (sitemap.max_urls_per_file); 45.000'i geçemez. */
    private function maxUrls(): int
    {
        return max(1, min(self::MAX_URLS_PER_FILE, (int) config('sitemap.max_urls_per_file', self::MAX_URLS_PER_FILE)));
    }

    /**
     * Sitemap index (sitemap.xml) — her aktif dil için KENDİ dosyası.
     *
     * NEDEN DİL BAZLI (2026-09-25 kapsam denetimi): eski yapı host'un diline göre tek
     * bir dil üretiyordu (applytogerman.com → en). 23.858 kaydın tamamının <loc>'u EN'di;
     * TR/DE URL'leri yalnızca hreflang alternatifiydi (Search Console'da "gönderilmemiş")
     * ve EN'i olmayan TR yazı/SSS'ler sitemap'e hiç girmiyordu. Artık her dil sürümü
     * kendi <url>/<loc> kaydını alır; hreflang kümesi değişmez.
     */
    public function index(Request $request, RankingService $rankings): Response
    {
        $base = $request->getScheme() . '://' . $request->getHost();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($this->activeLocales() as $lang) {
            $files = max(1, (int) ceil($this->localeCount($lang, $rankings) / $this->maxUrls()));
            for ($page = 1; $page <= $files; $page++) {
                $loc = $base . '/sitemap-' . $lang . ($page > 1 ? '-' . $page : '') . '.xml';
                $xml .= "  <sitemap>\n";
                $xml .= '    <loc>' . htmlspecialchars($loc, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";
                $xml .= '    <lastmod>' . now()->format('Y-m-d') . "</lastmod>\n";
                $xml .= "  </sitemap>\n";
            }
        }
        $xml .= '</sitemapindex>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Tek dilin sitemap'i: <loc>'ların HEPSİ bu dilde. Hreflang alternatifleri kümenin
     * gerçek kardeşleri (Hreflang::xDefault ile aynı x-default) — sayfa <head>'iyle aynı.
     */
    public function locale(Request $request, RankingService $rankings, string $lang, ?string $page = null): Response
    {
        abort_unless(in_array($lang, $this->activeLocales(), true), 404);

        $page = (int) ($page ?? 1);
        $entries = $this->entriesFor($lang, $rankings);
        // Index kendi envanterini KURMAZ (üç dil ≈ 20 sn); dosya sayısını buradan okur.
        cache()->put($this->countKey($lang), count($entries), now()->addDays(2));

        $urls = array_slice($entries, ($page - 1) * $this->maxUrls(), $this->maxUrls());
        abort_if($page > 1 && $urls === [], 404);

        return response($this->buildXml($urls, $this->activeLocales()), 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Eski alt sitemap adresleri (content / landings / glossary). Search Console'da ayrıca
     * gönderilmiş olabilirler; 404 vermesinler diye yeni index'i sunarlar.
     */
    public function legacy(Request $request, RankingService $rankings): Response
    {
        return $this->index($request, $rankings);
    }

    /**
     * Bir dilin tüm sitemap kayıtları: içerik + programatik landing'ler + sözlük.
     *
     * route() bu dilin URL'ini üretsin diye uygulama dili ve URL varsayılanı geçici olarak
     * o dile ayarlanır (request/host dili DEĞİL — eski yapının tek-dil sebebi buydu).
     *
     * @return array<int, array<string, mixed>>
     */
    private function entriesFor(string $lang, RankingService $rankings): array
    {
        $prevLocale = App::getLocale();
        App::setLocale($lang);
        URL::defaults(['locale' => $lang]);

        try {
            return array_merge(
                $this->contentEntries($lang, $rankings),
                $this->landingEntries(),
                $this->glossaryEntries(),
            );
        } finally {
            App::setLocale($prevLocale);
            URL::defaults(['locale' => $prevLocale]);
        }
    }

    /**
     * Index'in dosya sayısı için dilin kayıt sayısı: dil dosyası her üretildiğinde önbelleğe
     * yazılır. Index envanteri KENDİSİ kurmaz — üç dil soğuk önbellekte ~20 sn sürer ve
     * paylaşımlı sunucuda zaman aşımı riski taşır. Sayı henüz yoksa tek dosya varsayılır; bir
     * dil 45.000'i ilk kez aşarsa ek dosya, o dilin sitemap'i bir kez üretildikten sonra görünür.
     */
    private function localeCount(string $lang, RankingService $rankings): int
    {
        return (int) cache()->get($this->countKey($lang), 0);
    }

    private function countKey(string $lang): string
    {
        return "sitemap_locale_count_v1_{$lang}";
    }

    /**
     * Sözlük (glossary) — semantic SEO entity sayfaları.
     *
     * @return array<int, array<string, mixed>>
     */
    private function glossaryEntries(): array
    {
        $urls = [];
        $urls[] = $this->entry(route('glossary.index'), now(), 'monthly', 0.7);
        foreach (array_keys(config('glossary', [])) as $slug) {
            $urls[] = $this->entry(route('glossary.show', $slug), now(), 'monthly', 0.7);
        }

        return $urls;
    }

    /**
     * Programmatic SEO landing pages — city × field, city × language, field × degree.
     *
     * @return array<int, array<string, mixed>>
     */
    private function landingEntries(): array
    {
        $urls = [];

        // City × Field
        $fieldSlugs = FieldOfStudy::pluck('slug', 'id')->all();
        \Illuminate\Support\Facades\DB::table('programs')
            ->join('universities', 'universities.id', '=', 'programs.university_id')
            ->join('cities', 'cities.id', '=', 'universities.city_id')
            ->where('programs.is_active', 1)
            ->where('cities.is_active', 1)
            ->whereNotNull('programs.field_of_study_id')
            ->select('cities.slug as city_slug', 'programs.field_of_study_id')
            ->groupBy('cities.slug', 'programs.field_of_study_id')
            ->get()
            ->each(function ($combo) use (&$urls, $fieldSlugs) {
                $fieldSlug = $fieldSlugs[$combo->field_of_study_id] ?? null;
                if (!$fieldSlug) return;
                $urls[] = $this->entry(
                    route('programs.city-field', [$combo->city_slug, $fieldSlug]),
                    now(),
                    'weekly',
                    0.6
                );
            });

        // City × Language (EN ve DE)
        foreach (['en', 'de'] as $lang) {
            $langFilter = $lang === 'en' ? ['en', 'both'] : ['de', 'both'];
            \Illuminate\Support\Facades\DB::table('programs')
                ->join('universities', 'universities.id', '=', 'programs.university_id')
                ->join('cities', 'cities.id', '=', 'universities.city_id')
                ->where('programs.is_active', 1)
                ->where('cities.is_active', 1)
                ->whereIn('programs.language', $langFilter)
                ->select('cities.slug')
                ->distinct()
                ->pluck('cities.slug')
                ->each(function ($citySlug) use (&$urls, $lang) {
                    $urls[] = $this->entry(
                        route('programs.city-language', [$citySlug, $lang]),
                        now(),
                        'weekly',
                        $lang === 'en' ? 0.65 : 0.55
                    );
                });
        }

        // Field × Degree
        foreach ($fieldSlugs as $fieldId => $fieldSlug) {
            foreach (['bachelor', 'master', 'phd'] as $degree) {
                $hasPrograms = \App\Models\Program::where('is_active', true)
                    ->where('field_of_study_id', $fieldId)
                    ->where('degree', $degree)
                    ->exists();
                if (!$hasPrograms) continue;
                $urls[] = $this->entry(
                    route('programs.field-degree', [$fieldSlug, $degree]),
                    now(),
                    'weekly',
                    0.6
                );
            }
        }

        return $urls;
    }

    /**
     * Aktif locale listesini döner — hreflang üretimi için.
     */
    private function activeLocales(): array
    {
        return \App\Support\Hreflang::activeLocales();
    }

    /**
     * İçerik — static pages + cities/unis/programs/blog/faq/scholarships (tek dil).
     *
     * @return array<int, array<string, mixed>>
     */
    private function contentEntries(string $lang, RankingService $rankings): array
    {
        $activeLocales = $this->activeLocales();

        $urls = [];

        $urls[] = $this->entry(route('home'), now(), 'daily', 1.0);

        $urls[] = $this->entry(route('universities.index'), now(), 'daily', 0.9);
        $urls[] = $this->entry(route('popular-universities'), now(), 'weekly', 0.85);
        $urls[] = $this->entry(route('cities.index'), now(), 'daily', 0.9);
        $urls[] = $this->entry(route('fields.index'), now(), 'weekly', 0.9);
        $urls[] = $this->entry(route('states.index'), now(), 'weekly', 0.9);
        $urls[] = $this->entry(route('scholarships.index'), now(), 'weekly', 0.9);
        $urls[] = $this->entry(route('scholarships.daad'), now(), 'weekly', 0.95);
        $urls[] = $this->entry(route('jobs.index'), now(), 'daily', 0.85);
        $urls[] = $this->entry(route('rankings.index'), now(), 'weekly', 0.8);
        $urls[] = $this->entry(route('compare.index'), now(), 'monthly', 0.5);
        $urls[] = $this->entry(route('blog.index'), now(), 'daily', 0.8);
        $urls[] = $this->entry(route('faqs.index'), now(), 'weekly', 0.9);
        $urls[] = $this->entry(route('about'), now(), 'monthly', 0.7);
        $urls[] = $this->entry(route('link-to-us'), now(), 'monthly', 0.6);

        $urls[] = $this->entry(route('map.index'), now(), 'weekly', 0.8);
        $urls[] = $this->entry(route('programs.index'), now(), 'daily', 0.9);
        $urls[] = $this->entry(route('admission-free.index'), now(), 'weekly', 0.85);
        $urls[] = $this->entry(route('discover.english'), now(), 'weekly', 0.85);
        $urls[] = $this->entry(route('discover.tuition-free'), now(), 'weekly', 0.85);
        $urls[] = $this->entry(route('professions.index'), now(), 'daily', 0.9);
        $urls[] = $this->entry(route('housing.index'), now(), 'weekly', 0.8);
        $urls[] = $this->entry(route('tools.index'), now(), 'weekly', 0.8);
        $urls[] = $this->entry(route('templates.index'), now(), 'weekly', 0.8);
        foreach (\App\Models\DocumentTemplate::where('is_active', true)->get(['slug', 'updated_at']) as $tpl) {
            $urls[] = $this->entry(route('templates.show', $tpl->slug), $tpl->updated_at, 'monthly', 0.7);
        }
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
                    // İnce şehir (zengin content_blocks yok) → noindex'li, sitemap'e KOYMA.
                    if (! is_array($c->content_blocks) || count($c->content_blocks) < 3) {
                        continue;
                    }
                    $urls[] = $this->entry(
                        route('cities.show', $c->slug),
                        $c->last_enriched_at ?: $c->updated_at,
                        'weekly',
                        0.8
                    );
                }
            });

        // Blog ve SSS slug'ları dile göre DEĞİŞİR ("...-en" / "...-de"). Bu yüzden
        // hreflang alternatifleri prefix değiştirerek değil, translation_group_id
        // üzerinden gerçek kardeş kayıttan üretilir.
        // Blog ve haber AYRI kümeler: haberin kanonik adresi /news/ ve kardeşleri yalnız haber
        // kayıtları (NewsController ile aynı). Eskiden haberler /blog/ altında, bütün türlerden
        // kurulan kümeyle listeleniyordu → sayfa <head>'iyle çelişen 42 kayıt.
        $postAlts = $this->postSlugsByGroup('blog');
        $newsAlts = $this->postSlugsByGroup('news');
        $faqAlts = $this->faqSlugsByGroup();

        // Yayın koşulları published() ile AYNI; ama dil request/host'tan değil kaydın kendi
        // locale alanından gelir. Eskiden published() host dilini (en) uyguluyordu → EN'i
        // olmayan TR yazılar sitemap'e hiç girmiyordu.
        //
        // blog_redirects'te AYNI DİLDE from_slug olan bir slug, sitenin kendi kaydına göre başka
        // bir yazıya devredilmiştir (ör. tr: uniassist-vpd-reddedilme-cozumler → yeni İngilizce
        // slug); <loc> olarak sunulmaz. Dil şart: "en" satırı yalnızca /en/<eski-slug> adresinin
        // taşındığını söyler, aynı slug'ı taşıyan TR kardeşini devre dışı bırakmaz.
        // Yönlendirme/canonical davranışı DEĞİŞMEZ.
        $superseded = \Illuminate\Support\Facades\Schema::hasTable('blog_redirects')
            ? \Illuminate\Support\Facades\DB::table('blog_redirects')->where('locale', $lang)->pluck('from_slug')->all()
            : [];

        Post::query()
            ->blogType()
            ->where('locale', $lang)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->when($superseded !== [], fn ($q) => $q->whereNotIn('slug', $superseded))
            ->select(['id', 'slug', 'updated_at', 'translation_group_id'])
            ->orderBy('id')
            ->chunk(500, function ($chunk) use (&$urls, $postAlts, $activeLocales, $lang) {
                foreach ($chunk as $p) {
                    $self = route('blog.show', $p->slug);
                    $alts = [];
                    foreach ($activeLocales as $loc) {
                        $slug = $postAlts[$p->translation_group_id][$loc] ?? null;
                        if ($slug) {
                            $alts[$loc] = route('blog.show', ['locale' => $loc, 'slug' => $slug]);
                        }
                    }

                    // translation_group_id boşsa kardeş doğrulanamaz; buildXml'in prefix-swap
                    // yedeği blog/SSS için var olmayan adres üretir. Sayfa (BlogController) gibi
                    // kaydın kendisi her zaman kümede.
                    $alts[$lang] ??= $self;

                    $urls[] = $this->entry($self, $p->updated_at, 'monthly', 0.7, $alts);
                }
            });

        // Haberler — yalnız modül açıksa (kapalıysa /news/ 404 verir).
        if (\App\Models\MenuPage::isKeyEnabled('news.index')) {
            Post::query()
                ->news()
                ->where('locale', $lang)
                ->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->select(['id', 'slug', 'updated_at', 'translation_group_id'])
                ->orderBy('id')
                ->chunk(500, function ($chunk) use (&$urls, $newsAlts, $activeLocales, $lang) {
                    foreach ($chunk as $p) {
                        $self = route('news.show', $p->slug);
                        $alts = [];
                        foreach ($activeLocales as $loc) {
                            $slug = $newsAlts[$p->translation_group_id][$loc] ?? null;
                            if ($slug) {
                                $alts[$loc] = route('news.show', ['locale' => $loc, 'slug' => $slug]);
                            }
                        }
                        $alts[$lang] ??= $self;

                        $urls[] = $this->entry($self, $p->updated_at, 'weekly', 0.6, $alts);
                    }
                });
        }

        Faq::query()
            ->where('locale', $lang)
            ->where('is_published', true)
            ->with('topic:id,slug')
            ->select(['id', 'slug', 'updated_at', 'has_answer', 'faq_topic_id', 'translation_group_id'])
            ->orderBy('id')
            ->chunk(500, function ($chunk) use (&$urls, $faqAlts, $activeLocales, $lang) {
                foreach ($chunk as $f) {
                    if (!$f->topic) continue;
                    $self = route('faqs.show', [$f->topic->slug, $f->slug]);
                    $alts = [];
                    foreach ($activeLocales as $loc) {
                        $sibling = $faqAlts[$f->translation_group_id][$loc] ?? null;
                        if ($sibling) {
                            $alts[$loc] = route('faqs.show', ['locale' => $loc, 'topic' => $sibling[0], 'slug' => $sibling[1]]);
                        }
                    }

                    // Kardeş doğrulanamıyorsa prefix-swap yedeğine düşmesin (bkz. blog).
                    $alts[$lang] ??= $self;

                    $urls[] = $this->entry(
                        $self,
                        $f->updated_at,
                        $f->has_answer ? 'monthly' : 'yearly',
                        $f->has_answer ? 0.7 : 0.4,
                        $alts
                    );
                }
            });

        // indexable(): yalnız ad+derece taşıyan (HK importu) programlar noindex olduğu için
        // sitemap'e de girmez — noindex URL'i sitemap'te sunmak çelişkili sinyaldir.
        Program::where('is_active', true)
            ->indexable()
            ->select(['slug', 'updated_at', 'description_tr', 'language'])
            ->orderBy('id')
            ->chunk(1000, function ($chunk) use (&$urls) {
                foreach ($chunk as $p) {
                    // Değer sinyali (crawl bütçesi yoğunlaştırma): uluslararası
                    // (İngilizce/both) programlar öncelikli; Almanca-only uzun-kuyruk
                    // daha düşük → Google önce marka-hedefi sayfaları tarasın.
                    $intl = in_array($p->language, ['en', 'both'], true);
                    $priority = ! $p->description_tr ? 0.4 : ($intl ? 0.7 : 0.5);
                    $urls[] = $this->entry(
                        route('programs.show', $p->slug),
                        $p->updated_at,
                        'monthly',
                        $priority
                    );
                }
            });

        // Programmatic SEO landing pages → sitemap-landings.xml
        // Glossary sözlük sayfaları → sitemap-glossary.xml

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

        // Akademik iş ilanları — sadece aktif + son başvuru geçmemiş
        \App\Models\JobPosting::active()
            ->select(['slug', 'updated_at', 'is_featured'])
            ->orderBy('id')
            ->chunk(500, function ($chunk) use (&$urls) {
                foreach ($chunk as $j) {
                    if (! $j->slug) continue;
                    $urls[] = $this->entry(
                        route('jobs.show', $j->slug),
                        $j->updated_at,
                        'daily',
                        $j->is_featured ? 0.85 : 0.7
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

        // Programmatic SEO: /programs/field/{field}/language/{de|en}
        // Sadece o dilde GERÇEKTEN programı olan alanlar; boş sayfa sitemap'e girmez.
        foreach (FieldOfStudy::active()->get(['id', 'slug', 'updated_at']) as $f) {
            foreach (['de', 'en'] as $lang) {
                $has = Program::where('is_active', true)
                    ->where('field_of_study_id', $f->id)
                    ->where(fn ($q) => $q->where('language', $lang)->orWhere('language', 'both'))
                    ->exists();
                if ($has) {
                    $urls[] = $this->entry(
                        route('programs.field-language', [$f->slug, $lang]),
                        $f->updated_at,
                        'weekly',
                        0.7
                    );
                }
            }
        }

        // Programmatic SEO: /subjects/{slug}/nc-free — sadece NC-frei programı OLAN alanlar
        // (boş sayfalar noindex'li → sitemap'e koyma, crawl bütçesini boşa harcama).
        foreach (FieldOfStudy::active()
            ->whereHas('programs', fn ($q) => $q->where('is_active', true)->where('admission_mode', 'zulassungsfrei'))
            ->get(['slug', 'updated_at']) as $f) {
            $urls[] = $this->entry(
                route('admission-free.by-subject', $f->slug),
                $f->updated_at,
                'weekly',
                0.7
            );
        }

        // Programmatic SEO: /cities/{slug}/nc-free — sadece NC-frei programı olan şehirler
        \App\Models\City::where('is_active', true)
            ->whereHas('universities.programs', fn ($q) => $q->where('is_active', true)->where('admission_mode', 'zulassungsfrei'))
            ->get(['slug', 'updated_at'])
            ->each(function ($c) use (&$urls) {
                $urls[] = $this->entry(route('programs.city-nc-free', $c->slug), $c->updated_at, 'weekly', 0.7);
            });

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

        // Programmatic SEO: /universities/{slug}/nc-free — sadece NC-frei programı OLAN üniler.
        // (463 aktif ünin 267'sinde hiç zulassungsfrei program yok → o sayfalar boş/ince,
        // noindex'li; sitemap'e koyma. Az ama indexli > çok ama yok sayılan.)
        University::where('is_active', true)
            ->whereHas('programs', fn ($q) => $q->where('is_active', true)->where('admission_mode', 'zulassungsfrei'))
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

        return $urls;
    }

    /**
     * @param  array<string,string>  $alternates  locale => GERÇEK URL. Boş bırakılırsa
     *   hreflang alternatifleri dil prefix'i değiştirilerek üretilir (bkz. buildXml).
     */
    private function entry(string $url, $lastmod, string $changefreq, float $priority, array $alternates = []): array
    {
        return [
            'loc' => $url,
            'lastmod' => $lastmod instanceof \DateTimeInterface
                ? $lastmod->format('Y-m-d')
                : (string) $lastmod,
            'changefreq' => $changefreq,
            'priority' => number_format($priority, 1),
            'alternates' => $alternates,
        ];
    }

    /**
     * translation_group_id → [locale => slug] haritası.
     *
     * published() scope'u locale filtreler (app locale), bu yüzden burada KULLANILMAZ:
     * amaç tam olarak diğer dillerdeki kardeş kayıtları bulmak.
     *
     * @param  'blog'|'news'  $kind  kümeler türe göre ayrılır (sayfa <head>'i ile aynı)
     * @return array<string,array<string,string>>
     */
    private function postSlugsByGroup(string $kind): array
    {
        $map = [];

        Post::query()
            ->when($kind === 'news', fn ($q) => $q->news(), fn ($q) => $q->blogType())
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->whereNotNull('translation_group_id')
            ->select(['translation_group_id', 'locale', 'slug'])
            ->chunk(1000, function ($chunk) use (&$map) {
                foreach ($chunk as $p) {
                    $map[$p->translation_group_id][$p->locale] = $p->slug;
                }
            });

        return $map;
    }

    /**
     * translation_group_id → [locale => [topic_slug, slug]] haritası (SSS).
     *
     * @return array<string,array<string,array{0:string,1:string}>>
     */
    private function faqSlugsByGroup(): array
    {
        $map = [];

        Faq::query()
            ->where('is_published', true)
            ->whereNotNull('translation_group_id')
            ->with('topic:id,slug')
            ->select(['id', 'translation_group_id', 'locale', 'slug', 'faq_topic_id'])
            ->chunk(1000, function ($chunk) use (&$map) {
                foreach ($chunk as $f) {
                    if (! $f->topic) {
                        continue;
                    }
                    $map[$f->translation_group_id][$f->locale] = [$f->topic->slug, $f->slug];
                }
            });

        return $map;
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

            /*
             * hreflang alternatifleri.
             *
             * İki kaynak var, sırayla:
             *  1. Girdi GERÇEK alternatif taşıyorsa (blog, SSS) onlar kullanılır.
             *     Bu içeriklerin slug'ı dile göre değişir ("...-en" / "...-de"), bu
             *     yüzden prefix değiştirmek olmayan URL üretir → 404.
             *  2. Taşımıyorsa (program, üniversite, şehir…) slug dilden bağımsızdır;
             *     prefix değiştirmek doğru sonucu verir.
             *
             * Çevirisi olmayan dil için alternatif YAZILMAZ — sahte URL üretmek,
             * hiç hreflang vermemekten daha zararlıdır (Google kümeyi tümden atar).
             */
            $alts = $u['alternates'] ?? [];

            if ($alts === []) {
                foreach ($activeLocales as $loc) {
                    $alts[$loc] = $this->swapLocale($u['loc'], $loc, $activeLocales);
                }
            }

            foreach ($alts as $loc => $altUrl) {
                $xml .= '    <xhtml:link rel="alternate" hreflang="' . $loc . '" href="' . htmlspecialchars($altUrl, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '"/>' . "\n";
            }

            // x-default sayfa <head>'iyle AYNI kaynaktan (Hreflang::xDefault): varsayılan
            // dil kümedeyse o, değilse kümede bulunan ilk aktif dil. Var olmayan dile işaret edilmez.
            $xDefault = \App\Support\Hreflang::xDefault($alts);
            if ($xDefault) {
                $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($xDefault, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '"/>' . "\n";
            }

            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';
        return $xml;
    }
}
