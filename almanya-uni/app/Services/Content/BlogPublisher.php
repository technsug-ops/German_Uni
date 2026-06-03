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
        // AI uydurma İÇ link üretiyor → 404. İç/relatif linkleri düz metne çevir
        // (dış kaynak linkleri kalır). content_md kaydı + html ikisi de temiz olsun.
        $body = $this->neutralizeInternalLinks($parsed['body']);
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
     * AI'ın ürettiği İÇ (kendi domain / relatif) markdown linklerini düz metne çevirir
     * → uydurma slug'lar 404 vermesin. Dış kaynak linkleri ve resimler ( ![]() ) korunur.
     */
    public function neutralizeInternalLinks(string $md): string
    {
        $internalHosts = ['applytogerman.com', 'almanyauni.com'];

        $out = preg_replace_callback(
            '/(?<!\!)\[([^\]]+)\]\(\s*([^)\s]+)(?:\s+"[^"]*")?\s*\)/u',
            function ($m) use ($internalHosts) {
                $text = $m[1];
                $url = $m[2];
                if (preg_match('#^https?://#i', $url)) {
                    $host = strtolower((string) parse_url($url, PHP_URL_HOST));
                    foreach ($internalHosts as $h) {
                        if (str_contains($host, $h)) {
                            return $text; // iç link → düz metin
                        }
                    }
                    return $m[0]; // dış kaynak linki kalır
                }
                // Relatif / #anchor / scheme'siz → iç link → düz metin
                return $text;
            },
            $md
        );

        return $out ?? $md; // preg null dönerse orijinali koru (güvenli)
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
