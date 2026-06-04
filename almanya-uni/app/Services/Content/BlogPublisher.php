<?php

namespace App\Services\Content;

use App\Models\Category;
use App\Models\ContentAsset;
use App\Models\Post;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

/**
 * ContentAsset(blog) → Post yayın mantığı — TEK kaynak. Hem tekil "📤 Blog'a Aktar"
 * (AssetsRelationManager) hem Yayın Merkezi toplu yayın bunu kullanır.
 *
 * - Markdown frontmatter (title/slug/excerpt) parse + HTML entity decode (&quot; vb.)
 * - Kategori: brief.topic → blog kategori id (kültür konuları yeni üst kategoriye)
 * - Yazar: opts.author_id ?? brief.author_id ?? admin
 * - Çeviri (opsiyonel): content:translate-posts ile EN+DE, aynı translation_group
 */
class BlogPublisher
{
    /**
     * @param  array{go_live?:bool,author_id?:?int,translate?:bool}  $opts
     * @return array{ok:bool,created?:bool,post?:Post,translated?:array<int,string>,message?:string,warn?:string}
     */
    public function publish(ContentAsset $asset, array $opts = []): array
    {
        $parsed = $this->parse((string) $asset->body_md);
        if (! $parsed) {
            return ['ok' => false, 'message' => 'Markdown frontmatter (title) eksik — "Yeniden üret" ile tazele.'];
        }

        $decode = fn (?string $s) => $s === null ? '' : html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $title = $decode($parsed['title']);
        $slug = Str::limit($parsed['slug'] ?: Str::slug($title), 250, '');
        // AI iç linkleri çözümle: gerçek yayınlanmış yazıya bağla (doğru URL), yoksa
        // düz metne indir. Dış kaynak linkleri + resimler korunur. (404 üretmez.)
        $body = $this->resolveInternalLinks($parsed['body'], $asset->language ?: 'tr');
        $contentHtml = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $excerpt = Str::limit($decode($parsed['excerpt']) ?: strip_tags($contentHtml), 250, '...');

        $brief = $asset->brief;
        $goLive = $opts['go_live'] ?? true;
        $authorId = (int) ($opts['author_id'] ?? $brief?->author_id ?? auth()->id() ?? 1);

        $existing = Post::where('slug', $slug)->first();
        $payload = [
            'locale'               => $asset->language ?: 'tr',
            'translation_group_id' => $existing?->translation_group_id ?? (string) Str::uuid(),
            'user_id'              => $authorId,
            'category_id'          => $this->resolveCategoryId($brief?->topic),
            'title'                => Str::limit($title, 250, ''),
            'slug'                 => $slug,
            'excerpt'              => $excerpt,
            'content_md'           => $body,
            'content_html'         => $contentHtml,
            'meta_title'           => Str::limit($title, 250, ''),
            'meta_description'     => $excerpt,
            'reading_minutes'      => max(1, (int) round(str_word_count(strip_tags($contentHtml)) / 220)),
            'is_published'         => $goLive,
            'published_at'         => $existing?->published_at ?? now(),
        ];

        $post = $existing ? tap($existing)->update($payload) : Post::create($payload);
        $asset->update(['status' => $goLive ? 'published' : 'ready']);

        // Haber paritesi: yayınlanırken TR → EN + DE çevir (aynı grup).
        $translated = [];
        if (($opts['translate'] ?? false) && $goLive && ($asset->language ?: 'tr') === 'tr') {
            try {
                Artisan::call('content:translate-posts', ['--post' => $post->id, '--force' => true, '--sleep' => 0]);
                $out = Artisan::output();
                foreach (['en' => 'EN', 'de' => 'DE'] as $loc => $lbl) {
                    if (str_contains($out, $loc)) {
                        $translated[] = $lbl;
                    }
                }
            } catch (\Throwable $e) {
                return ['ok' => true, 'created' => ! $existing, 'post' => $post, 'translated' => [], 'warn' => 'çeviri başarısız: ' . mb_substr($e->getMessage(), 0, 100)];
            }
        }

        return ['ok' => true, 'created' => ! $existing, 'post' => $post, 'translated' => $translated];
    }

    /** brief.topic → blog kategori id. Kültür konuları yeni üst kategoriye gider. */
    public function resolveCategoryId(?string $topic): int
    {
        $map = [
            'vize' => 6, 'randevu' => 6, 'uni_assist' => 8, 'dil' => 7, 'sinav' => 7,
            'yurt' => 9, 'sehir' => 9, 'studienkolleg' => 1, 'master' => 1,
            'sigorta' => 5, 'para' => 5, 'sperrkonto' => 5,
        ];
        if ($topic && isset($map[$topic])) {
            return $map[$topic];
        }
        if (in_array($topic, ['yasam', 'konut', 'kultur'], true)) {
            $id = Category::where('slug', 'german-life-culture')->value('id');
            if ($id) {
                return (int) $id;
            }
        }
        return 1;
    }

    /**
     * AI'ın ürettiği İÇ markdown linklerini ÇÖZÜMLER (route-farkında):
     *  - dış kaynak linki → korunur
     *  - iç link → (1) geçerli bir ROUTE'a denk geliyorsa locale-prefix'li canonical
     *    URL'ye yeniden yazılır; (2) yayınlanmış bir POST'a denk geliyorsa ona bağlanır
     *    (hedef dildeki kardeşi yeğlenir); (3) hiçbiri değilse düz metne indirilir (404 yok).
     */
    public function resolveInternalLinks(string $md, string $locale = 'tr'): string
    {
        $internalHosts = ['applytogerman.com', 'almanyauni.com'];

        $out = preg_replace_callback(
            '/(?<!\!)\[([^\]]+)\]\(\s*([^)\s]+)(?:\s+"[^"]*")?\s*\)/u',
            function ($m) use ($internalHosts, $locale) {
                $text = $m[1];
                $url = trim($m[2]);

                if (preg_match('#^https?://#i', $url)) {
                    $host = strtolower((string) parse_url($url, PHP_URL_HOST));
                    $isInternal = false;
                    foreach ($internalHosts as $h) {
                        if (str_contains($host, $h)) { $isInternal = true; break; }
                    }
                    if (! $isInternal) {
                        return $m[0]; // dış kaynak linki → korunur
                    }
                    $url = (string) parse_url($url, PHP_URL_PATH); // kendi domain → path'i iç link gibi işle
                }

                // Anchor / mailto / tel → dokunma
                if ($url === '' || $url[0] === '#' || str_starts_with($url, 'mailto:') || str_starts_with($url, 'tel:')) {
                    return $m[0];
                }

                $path = strtok($url, '?#') ?: $url;
                $normalized = $this->normalizePath($path); // locale + blog öneki atılmış, trimli

                // (1) Geçerli route mu? → locale-prefix'li canonical link
                if ($normalized !== '' && $this->isValidRoutePath($normalized)) {
                    return '[' . $text . '](/' . $locale . '/' . $normalized . ')';
                }

                // (2) Yayınlanmış post mu? (slug veya başlık eşleşmesi)
                $candidate = (string) end(array_values(array_filter(explode('/', $normalized))) ?: ['']);
                $post = $this->findPostForLink($candidate, $text, $locale);
                if ($post) {
                    return '[' . $text . '](/' . $post->locale . '/blog/' . $post->slug . ')';
                }

                return $text; // gerçek hedef yok → düz metin (link kaldırılır)
            },
            $md
        );

        return $out ?? $md; // preg null dönerse orijinali koru (güvenli)
    }

    /** Path'i normalize et: locale öneki ({tr,en,de}) + sondaki / atılır, trimlenir. */
    private function normalizePath(string $path): string
    {
        $path = trim($path, '/');
        $segments = array_values(array_filter(explode('/', $path)));
        if ($segments && in_array($segments[0], ['tr', 'en', 'de', 'fr'], true)) {
            array_shift($segments);
        }
        return implode('/', $segments);
    }

    /**
     * Normalize edilmiş path geçerli bir public GET route'a denk geliyor mu?
     * Tüm kayıtlı route'ların STATİK öneklerini (param öncesi) bir kez toplar.
     */
    private function isValidRoutePath(string $normalized): bool
    {
        static $statics = null;
        if ($statics === null) {
            $statics = [];
            foreach (app('router')->getRoutes() as $route) {
                if (! in_array('GET', $route->methods(), true)) {
                    continue;
                }
                $uri = preg_replace('#^\{locale\??\}/?#', '', $route->uri()); // {locale}/ öneki at
                if (str_starts_with($uri, 'admin') || str_starts_with($uri, 'api') || str_starts_with($uri, '_')) {
                    continue; // admin/api/system route'ları iç-link hedefi değil
                }
                $static = trim(preg_replace('#\{.*$#', '', $uri), '/'); // ilk {param} öncesi
                if ($static !== '') {
                    $statics[$static] = true;
                }
            }
        }

        if (isset($statics[$normalized])) {
            return true; // tam eşleşme (index route)
        }
        foreach ($statics as $s => $_) {
            if (str_starts_with($normalized, $s . '/')) {
                return true; // param route alt-yolu (ör. tools/sperrkonto/{slug})
            }
        }
        return false;
    }

    /**
     * Bozuk/dış-barındırılan markdown RESİMLERİNİ kaldırır:
     *  - almanyauni.com / applytogerman.com altında /images, /img gibi var olmayan yollar
     *  - mutlak olmayan (relative) ama dosya sistemi/route karşılığı olmayan resimler
     * Geçerli (asset/CDN) resimler korunur.
     */
    public function stripBrokenImages(string $md): string
    {
        $out = preg_replace_callback(
            '/!\[[^\]]*\]\(\s*([^)\s]+)(?:\s+"[^"]*")?\s*\)/u',
            function ($m) {
                $url = trim($m[1]);
                $host = strtolower((string) parse_url($url, PHP_URL_HOST));
                // Kendi domainimizde /images veya /img altındaki placeholder/bozuk yollar
                if (($host === '' || str_contains($host, 'almanyauni.com') || str_contains($host, 'applytogerman.com'))
                    && preg_match('#/(images|img)/#i', (string) parse_url($url, PHP_URL_PATH) ?: $url)) {
                    return ''; // bozuk resim → kaldır
                }
                return $m[0]; // diğer resimler korunur
            },
            $md
        );
        return $out ?? $md;
    }

    /**
     * AI üretim artefaktlarını temizler:
     *  - İçeriğe sızmış görünür JSON-LD blokları (`<script ld+json>` veya kod-fence içinde)
     *    — bunlar bozuk almanyauni.com/images linklerini de içerir
     *  - "Sonuç + CTA" gibi şablon-placeholder başlıkları → "Sonuç"
     */
    public function stripContentArtifacts(string $md): string
    {
        // Kod-fence içine sarılmış JSON-LD (görünür çöp)
        $md = preg_replace('/```[a-zA-Z]*\s*\R?<script[^>]*application\/ld\+json.*?<\/script>\s*\R?```/su', '', $md) ?? $md;
        // Çıplak JSON-LD <script> blokları
        $md = preg_replace('/<script[^>]*application\/ld\+json.*?<\/script>/su', '', $md) ?? $md;
        // Şablon-placeholder başlık: "## Sonuç + CTA" → "## Sonuç"
        $md = preg_replace('/^(#{1,6}\s*)Sonu[çc]\s*\+\s*CTA\s*$/um', '$1Sonuç', $md) ?? $md;
        return $md;
    }

    /**
     * İçerikteki tüm İÇ link + resimleri kataloglar (read-only) — "tabela".
     * @return array<int,array{type:string,text:string,url:string}>
     */
    public function collectInternalLinks(string $md): array
    {
        $found = [];
        if (preg_match_all('/(!?)\[([^\]]*)\]\(\s*([^)\s]+)(?:\s+"[^"]*")?\s*\)/u', $md, $mm, PREG_SET_ORDER)) {
            foreach ($mm as $m) {
                $isImg = $m[1] === '!';
                $url = trim($m[3]);
                $host = strtolower((string) parse_url($url, PHP_URL_HOST));
                $external = preg_match('#^https?://#i', $url)
                    && ! str_contains($host, 'almanyauni.com')
                    && ! str_contains($host, 'applytogerman.com');
                if ($external) {
                    continue; // dış linkler katalog dışı
                }
                $found[] = ['type' => $isImg ? 'image' : 'link', 'text' => $m[2], 'url' => $url];
            }
        }
        return $found;
    }

    /** İç link için gerçek yayınlanmış Post bul (slug/başlık tam eşleşme); hedef dildeki kardeşi yeğle. */
    private function findPostForLink(string $candidateSlug, string $text, string $locale): ?Post
    {
        $textSlug = Str::slug($text);
        if ($candidateSlug === '' && $textSlug === '') {
            return null;
        }

        $post = Post::query()
            ->where('is_published', true)
            ->where(function ($q) use ($candidateSlug, $textSlug) {
                if ($candidateSlug !== '') { $q->where('slug', $candidateSlug); }
                if ($textSlug !== '') { $q->orWhere('slug', $textSlug); }
            })
            ->first();

        if (! $post) {
            return null;
        }

        // Hedef dilde kardeş varsa ona bağla (aynı translation_group)
        if ($post->translation_group_id && $post->locale !== $locale) {
            $sibling = Post::query()
                ->where('translation_group_id', $post->translation_group_id)
                ->where('locale', $locale)
                ->where('is_published', true)
                ->first();
            if ($sibling) {
                return $sibling;
            }
        }

        return $post;
    }

    /**
     * Asset body_md'sinden YAML frontmatter + body ayıklar.
     * @return array{title:string,slug:?string,excerpt:?string,body:string}|null
     */
    public function parse(string $md): ?array
    {
        $md = trim($md);
        if (preg_match('/^```(?:markdown|md)?\s*\n(.+)\n```\s*$/s', $md, $w)) {
            $md = trim($w[1]);
        }
        if (! preg_match('/^---\s*\n(.+?)\n---\s*\n(.+)$/s', $md, $m)) {
            return null;
        }
        $body = trim($m[2]);
        $meta = [];
        foreach (preg_split('/\n/', $m[1]) as $line) {
            if (preg_match('/^(\w+):\s*(.+)$/', trim($line), $kv)) {
                $val = trim($kv[2]);
                if (preg_match('/^"(.+)"$/', $val, $q) || preg_match("/^'(.+)'$/", $val, $q)) {
                    $val = $q[1];
                }
                $meta[$kv[1]] = $val;
            }
        }
        if (empty($meta['title'])) {
            return null;
        }
        return [
            'title'   => $meta['title'],
            'slug'    => $meta['slug'] ?? null,
            'excerpt' => $meta['excerpt'] ?? null,
            'body'    => $body,
        ];
    }
}
