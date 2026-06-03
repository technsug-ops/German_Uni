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
     * AI'ın ürettiği İÇ markdown linklerini ÇÖZÜMLER:
     *  - dış kaynak linki / resim → korunur
     *  - iç link → gerçek yayınlanmış yazı (slug veya başlık tam eşleşme) bulunursa
     *    doğru yerel URL'ye yeniden yazılır; bulunmazsa düz metne indirilir (404 yok).
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

                // Aday slug = path'in son segmenti (locale/blog/ önekleri atılır)
                $path = strtok($url, '?#') ?: $url;
                $segments = array_values(array_filter(explode('/', trim($path, '/'))));
                $candidate = (string) end($segments);

                $post = $this->findPostForLink($candidate, $text, $locale);
                if ($post) {
                    return '[' . $text . '](' . url($post->locale . '/blog/' . $post->slug) . ')';
                }
                return $text; // gerçek hedef yok → düz metin
            },
            $md
        );

        return $out ?? $md; // preg null dönerse orijinali koru (güvenli)
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
