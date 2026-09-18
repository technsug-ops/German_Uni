<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Yazı gövdelerindeki ÖLÜ iç linkleri gerçek hedeflere yeniden bağlar.
 *
 * NEDEN AYRI BİR KOMUT: mevcut `content:resolve-post-links` (BlogPublisher::resolveInternalLinks)
 * yalnızca linkin ROUTE ŞEKLİNE bakar — "/tr/blog/<herhangi-bir-slug>" geçerli route önekiyle
 * başladığı için hedef yazı silinmiş/yeniden adlandırılmış olsa bile linke dokunmaz. Bu yüzden
 * canlıda yeniden adlandırılmış yazılara giden yüzlerce 404 link birikti. Bu komut hedefin
 * GERÇEKTEN var olup olmadığına bakar ve yoksa doğru kaydı bulmaya çalışır.
 *
 * Eşleştirme merdiveni (ilk tutan kazanır, hepsi muhafazakâr):
 *   1. Tam slug + locale eşleşmesi (yayınlanmış)
 *   2. Locale son eki düzeltmesi: temel slug + tr/'' · de/'-de' · en/'-en'
 *   3. translation_group üzerinden kardeş kayıt (slug başka locale'de duruyorsa)
 *   4. Slug token benzerliği (Jaccard ≥ 0.6 ve ≥ 3 ortak token) → en iyi aday
 *   5. Link METNİ ↔ yazı başlığı benzerliği (≥ %82)
 * Hiçbiri tutmazsa link OLDUĞU GİBİ bırakılır; `--demote` verilirse düz metne indirilir.
 * Varsayılanın "bırak" olması bilinçli: temiz/kısmi bir veritabanında (CI) çalışsa bile
 * içeriği bozmaz — yalnızca gerçekten çözebildiğini değiştirir.
 */
class RepairPostLinks extends Command
{
    protected $signature = 'content:repair-post-links
        {--dry-run : sadece raporla, yazma}
        {--demote : eşleşme bulunamayan ölü linkleri düz metne indir}
        {--limit=0 : 0 = tüm yazılar}';

    protected $description = 'Yazılardaki ölü iç linkleri (blog/şehir/üni/program) gerçek hedeflere yeniden bağlar';

    /** slug => locale (yayınlanmış yazılar) */
    private array $postSlugs = [];

    /** locale => [slug => title] */
    private array $byLocale = [];

    /** locale => [slug => başlıktan üretilmiş slug] — TR legacy slug'lar başlıktan türetilmişti */
    private array $titleSlug = [];

    /** locale => [slug => ['slug' => token[], 'title' => token[]]] — önceden hesaplanmış tokenlar */
    private array $tokenIndex = [];

    /** slug => translation_group_id (yayın durumundan bağımsız) */
    private array $groupOf = [];

    /** translation_group_id => [locale => slug] (yayınlanmış) */
    private array $groupSiblings = [];

    /** tip => [slug => true] */
    private array $entitySlugs = [];

    /**
     * Eski/Türkçe araç yolları → bugünkü gerçek route. Yalnızca ANLAMI AYNI olanlar;
     * benzer ama farklı araçlar (ör. /tools/language-courses ↔ language-certificates)
     * bilinçli olarak eşlenmedi, onlar düz metne iner.
     */
    private const ROUTE_ALIASES = [
        'araclar/yasam-maliyeti-hesaplayici' => 'tools/cost-of-living',
        'araclar/grade-converter'            => 'tools/grade-converter',
        'araclar/oneri'                      => 'tools/recommendation',
        'tools/blocked-account'              => 'tools/sperrkonto',
        'tools/blocked-account-providers'    => 'tools/sperrkonto',
    ];

    /** tam eşleşen statik GET route URI'leri (locale öneki atılmış) */
    private array $routeExact = [];

    /** parametreli route'ların statik önekleri ("tools/sperrkonto" gibi) */
    private array $routePrefix = [];

    /** "<ölü-slug>|<locale>" => çözülen slug | false — aynı ölü slug her yerde AYNI hedefe gitsin */
    private array $resolveCache = [];

    private array $stats = ['taranan' => 0, 'link' => 0, 'olu' => 0, 'onarilan' => 0, 'duz_metin' => 0, 'cozulemeyen' => 0];

    /** @var array<int,string> rapor satırları */
    private array $log = [];

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $demote = (bool) $this->option('demote');

        $this->loadIndex();

        $q = Post::query()->whereNotNull('content_md')->where('content_md', '!=', '');
        if (($limit = (int) $this->option('limit')) > 0) {
            $q->limit($limit);
        }

        foreach ($q->get() as $post) {
            $this->stats['taranan']++;
            $locale = $post->locale ?: 'tr';
            $md = (string) $post->content_md;

            $new = $this->rewrite($md, $locale, $demote, $post->slug);
            if ($new === $md) {
                continue;
            }

            if (! $dry) {
                $post->content_md = $new;
                $post->content_html = Str::markdown($new, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
                $post->save();
            }
        }

        foreach (array_slice($this->log, 0, 60) as $line) {
            $this->line($line);
        }
        if (count($this->log) > 60) {
            $this->line('  … ve ' . (count($this->log) - 60) . ' satır daha');
        }

        $this->newLine();
        $this->info(sprintf(
            '%s yazı tarandı · %d iç link · %d ölü → %d onarıldı, %d düz metne indirildi, %d çözülemedi%s',
            $this->stats['taranan'],
            $this->stats['link'],
            $this->stats['olu'],
            $this->stats['onarilan'],
            $this->stats['duz_metin'],
            $this->stats['cozulemeyen'],
            $dry ? ' (DRY-RUN — hiçbir şey yazılmadı)' : ''
        ));

        return self::SUCCESS;
    }

    /** Hedef kataloglarını tek seferde belleğe al (link başına sorgu atmamak için). */
    private function loadIndex(): void
    {
        foreach (DB::table('posts')->select('slug', 'locale', 'title', 'translation_group_id', 'is_published')->get() as $p) {
            $this->groupOf[$p->slug] = (string) $p->translation_group_id;
            if (! $p->is_published) {
                continue;
            }
            $loc = $p->locale ?: 'tr';
            $this->postSlugs[$p->slug] = $loc;
            $this->byLocale[$loc][$p->slug] = (string) $p->title;
            $this->titleSlug[$loc][$p->slug] = Str::slug((string) $p->title);
            $this->tokenIndex[$loc][$p->slug] = [
                'slug'  => $this->tokens(preg_replace('/-(de|en)$/', '', $p->slug)),
                'title' => $this->tokens($this->titleSlug[$loc][$p->slug]),
            ];
            if ($p->translation_group_id) {
                $this->groupSiblings[$p->translation_group_id][$loc] = $p->slug;
            }
        }

        foreach (app('router')->getRoutes() as $route) {
            if (! in_array('GET', $route->methods(), true)) {
                continue;
            }
            $uri = preg_replace('#^\{locale\??\}/?#', '', $route->uri());
            if ($uri === '' || str_starts_with($uri, 'admin') || str_starts_with($uri, 'api') || str_starts_with($uri, '_')) {
                continue;
            }
            if (! str_contains($uri, '{')) {
                $this->routeExact[trim($uri, '/')] = true;
                continue;
            }
            $prefix = trim(preg_replace('#/?\{.*$#', '', $uri), '/');
            if ($prefix !== '') {
                $this->routePrefix[$prefix] = true;
            }
        }

        foreach (['cities' => 'cities', 'universities' => 'universities', 'programs' => 'programs'] as $seg => $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'slug')) {
                continue;
            }
            $rows = DB::table($table)->when(Schema::hasColumn($table, 'is_active'), fn ($q) => $q->where('is_active', 1))
                ->pluck('slug');
            foreach ($rows as $s) {
                $this->entitySlugs[$seg][$s] = true;
            }
        }
    }

    private function rewrite(string $md, string $locale, bool $demote, string $sourceSlug): string
    {
        $out = preg_replace_callback(
            '/(?<!\!)\[([^\]]+)\]\(\s*([^)\s]+)(?:\s+"[^"]*")?\s*\)/u',
            function ($m) use ($locale, $demote, $sourceSlug) {
                $text = $m[1];
                $url = trim($m[2]);

                if (preg_match('#^https?://#i', $url)) {
                    $host = strtolower((string) parse_url($url, PHP_URL_HOST));
                    if (! str_contains($host, 'applytogerman.com') && ! str_contains($host, 'almanyauni.com')) {
                        return $m[0]; // dış link → dokunma
                    }
                    $url = (string) parse_url($url, PHP_URL_PATH);
                }

                if ($url === '' || $url[0] === '#' || str_starts_with($url, 'mailto:') || str_starts_with($url, 'tel:')) {
                    return $m[0];
                }

                $path = strtok($url, '?#') ?: $url;
                $segs = array_values(array_filter(explode('/', trim($path, '/'))));
                if ($segs === []) {
                    return $m[0];
                }

                $linkLocale = in_array($segs[0], ['tr', 'de', 'en'], true) ? array_shift($segs) : $locale;
                if ($segs === []) {
                    return $m[0];
                }

                $type = $segs[0];
                if (count($segs) === 1) {
                    return $m[0]; // /tr/blog, /tr/universities gibi indeks sayfaları
                }
                $slug = (string) end($segs);
                $this->stats['link']++;

                if ($type === 'blog') {
                    if (isset($this->postSlugs[$slug])) {
                        return '[' . $text . '](/' . $this->postSlugs[$slug] . '/blog/' . $slug . ')';
                    }

                    $this->stats['olu']++;
                    $hit = $this->resolveBlog($slug, $linkLocale, $text);
                    if ($hit) {
                        $this->stats['onarilan']++;
                        $this->log[] = "  ✅ [{$sourceSlug}] {$slug} → {$hit}";

                        return '[' . $text . '](/' . $this->postSlugs[$hit] . '/blog/' . $hit . ')';
                    }

                    if ($demote) {
                        $this->stats['duz_metin']++;
                        $this->log[] = "  ✂️  [{$sourceSlug}] {$slug} → düz metin";

                        return $text;
                    }

                    $this->stats['cozulemeyen']++;
                    $this->log[] = "  ❓ [{$sourceSlug}] {$slug} → eşleşme yok (dokunulmadı)";

                    return $m[0];
                }

                if (isset($this->entitySlugs[$type])) {
                    if (isset($this->entitySlugs[$type][$slug])) {
                        return $m[0];
                    }

                    $this->stats['olu']++;
                    $hit = $this->bestSlugMatch($slug, array_keys($this->entitySlugs[$type]));
                    if ($hit) {
                        $this->stats['onarilan']++;
                        $this->log[] = "  ✅ [{$sourceSlug}] {$type}/{$slug} → {$hit}";

                        return '[' . $text . '](/' . $linkLocale . '/' . $type . '/' . $hit . ')';
                    }

                    if ($demote) {
                        $this->stats['duz_metin']++;

                        return $text;
                    }

                    $this->stats['cozulemeyen']++;
                    $this->log[] = "  ❓ [{$sourceSlug}] {$type}/{$slug} → eşleşme yok (dokunulmadı)";

                    return $m[0];
                }

                // Kalan iç yollar (tools/, faq/, scholarships/ …): gerçek bir route'a denk
                // geliyor mu? Parametreli route'un alt yolu ise doğrulanamaz → dokunma.
                $normalized = implode('/', $segs);
                if (isset($this->routeExact[$normalized])) {
                    return $m[0];
                }
                if (isset(self::ROUTE_ALIASES[$normalized])) {
                    $this->stats['olu']++;
                    $this->stats['onarilan']++;
                    $this->log[] = "  ✅ [{$sourceSlug}] /{$normalized} → " . self::ROUTE_ALIASES[$normalized];

                    return '[' . $text . '](/' . $linkLocale . '/' . self::ROUTE_ALIASES[$normalized] . ')';
                }
                foreach (array_keys($this->routePrefix) as $prefix) {
                    if (str_starts_with($normalized, $prefix . '/')) {
                        return $m[0]; // ör. tools/sperrkonto/{slug}
                    }
                }

                $this->stats['olu']++;
                if ($demote) {
                    $this->stats['duz_metin']++;
                    $this->log[] = "  ✂️  [{$sourceSlug}] /{$normalized} → düz metin (route yok)";

                    return $text;
                }

                $this->stats['cozulemeyen']++;
                $this->log[] = "  ❓ [{$sourceSlug}] /{$normalized} → route yok (dokunulmadı)";

                return $m[0];
            },
            $md
        );

        return $out ?? $md;
    }

    /** Ölü blog slug'ı için doğru kaydı bul (merdiven: son ek → grup → slug benzerliği → başlık). */
    private function resolveBlog(string $slug, string $locale, string $text): ?string
    {
        $base = preg_replace('/-(de|en)$/', '', $slug);

        // 2. locale son eki düzeltmesi
        foreach ([$locale => $base, 'tr' => $base] as $loc => $b) {
            $cand = $b . match ($loc) { 'de' => '-de', 'en' => '-en', default => '' };
            if (isset($this->postSlugs[$cand])) {
                return $cand;
            }
        }

        // 3. translation_group kardeşi
        $group = $this->groupOf[$slug] ?? $this->groupOf[$base] ?? null;
        if ($group && isset($this->groupSiblings[$group])) {
            return $this->groupSiblings[$group][$locale]
                ?? $this->groupSiblings[$group]['tr']
                ?? reset($this->groupSiblings[$group]);
        }

        // 4-5. Aday puanlama: ölü slug ↔ (aday slug | başlıktan üretilmiş slug) ve link metni ↔ başlık.
        // TR legacy slug'lar ("uni-assist-nedir") İngilizce yeni slug'la token paylaşmaz ama
        // TR BAŞLIKLA paylaşır — bu yüzden başlık-slug'ı da havuza giriyor.
        return $this->bestPostMatch($base, $text, $locale);
    }

    /**
     * En iyi yazı adayını seç. Üç sinyalin en yükseği puandır; ikinci adaya belirgin fark
     * (≥ 0.08) yoksa eşleşme YAPILMAZ — yanlış yazıya bağlamak, kırık linkten kötüdür.
     */
    private function bestPostMatch(string $deadSlug, string $text, string $locale): ?string
    {
        $key = $deadSlug . '|' . $locale;
        if (array_key_exists($key, $this->resolveCache)) {
            return $this->resolveCache[$key] ?: null;
        }

        $hit = $this->searchAllLocales($deadSlug, $text, $locale);
        if ($hit !== null) {
            $this->resolveCache[$key] = $hit; // yalnızca BAŞARIYI önbellekle: aynı ölü slug
        }                                     // başka bir yazıda daha iyi link metniyle çözülebilir

        return $hit;
    }

    /**
     * Ölü slug hangi DİLDE yazılmışsa o dilin havuzunda bulunur (TR legacy slug bir EN yazının
     * içinde de geçebiliyor) → tüm havuzlarda ara, bulunanı translation_group ile hedef
     * locale'in kardeşine çevir.
     */
    private function searchAllLocales(string $deadSlug, string $text, string $locale): ?string
    {
        foreach ([$locale, 'tr', 'en', 'de'] as $pool) {
            $hit = $this->bestInPool($deadSlug, $text, $pool);
            if (! $hit) {
                continue;
            }
            if ($pool === $locale) {
                return $hit;
            }
            $group = $this->groupOf[$hit] ?? null;
            $sibling = $group ? ($this->groupSiblings[$group][$locale] ?? null) : null;

            return $sibling ?: $hit; // kardeşi yoksa bulunan kayıt (404'tan iyidir)
        }

        return null;
    }

    private function bestInPool(string $deadSlug, string $text, string $locale): ?string
    {
        $want = $this->tokens($deadSlug);
        $textTokens = $this->tokens(Str::slug($text));
        if (count($want) < 2 && count($textTokens) < 2) {
            return null;
        }

        $scores = [];
        foreach ($this->tokenIndex[$locale] ?? [] as $cand => $idx) {
            $slugTokens = $idx['slug'];
            $titleTokens = $idx['title'];

            $best = max(
                $this->score($want, $slugTokens),
                $this->score($want, $titleTokens),
                $this->score($textTokens, $titleTokens),
                $this->score($textTokens, $slugTokens),
            );
            if ($best > 0) {
                $scores[$cand] = $best;
            }
        }

        if ($scores === []) {
            return null;
        }

        arsort($scores);
        $top = array_key_first($scores);
        $topScore = $scores[$top];
        $second = count($scores) > 1 ? array_values($scores)[1] : 0.0;

        if ($topScore < 0.55 || ($topScore - $second) < 0.08) {
            return null;
        }

        return $top;
    }

    /** Jaccard ile kapsama (containment) karışımı; en az 2 ayırt edici ortak token şart. */
    private function score(array $a, array $b): float
    {
        if (count($a) < 2 || count($b) < 2) {
            return 0.0;
        }
        $shared = count(array_intersect($a, $b));
        if ($shared < 2) {
            return 0.0;
        }
        $jaccard = $shared / count(array_unique(array_merge($a, $b)));
        $containment = $shared / min(count($a), count($b));

        // Kapsama tek başına yanıltıcı olabilir (kısa slug uzun başlığın içinde erir) →
        // ikisinin ağırlıklı ortalaması alınır.
        return (0.45 * $jaccard) + (0.55 * ($containment >= 0.8 ? $containment : $containment * 0.7));
    }

    /** Token-Jaccard ile en iyi slug adayı; eşik altı → null (yanlış yazıya bağlamaktansa bırak). */
    private function bestSlugMatch(string $slug, array $pool): ?string
    {
        $want = $this->tokens($slug);
        if (count($want) < 3) {
            return null;
        }

        $best = null;
        $bestScore = 0.0;
        foreach ($pool as $cand) {
            $have = $this->tokens(preg_replace('/-(de|en)$/', '', $cand));
            $shared = count(array_intersect($want, $have));
            if ($shared < 3) {
                continue;
            }
            $score = $shared / max(1, count(array_unique(array_merge($want, $have))));
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $cand;
            }
        }

        return $bestScore >= 0.6 ? $best : null;
    }

    /** @return array<int,string> */
    private function tokens(string $slug): array
    {
        $stop = ['in', 'the', 'a', 'an', 'of', 'for', 'to', 'and', 'is', 'as', 'your', 'you', 'how', 'what', 'de', 'en'];

        return array_values(array_unique(array_diff(
            array_filter(explode('-', mb_strtolower($slug)), fn ($t) => mb_strlen($t) > 1),
            $stop
        )));
    }

    private function normalizeText(string $s): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', strip_tags($s))));
    }
}
