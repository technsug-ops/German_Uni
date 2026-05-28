<?php

namespace App\Console\Commands;

use App\Models\ContentAsset;
use App\Models\ContentBrief;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * ContentAsset (blog, status=ready) → Post tablosuna sync eder.
 *
 * Mevcut akış:
 *  1. /admin/content-briefs → Brief oluştur
 *  2. ⚡ AI ile Asset Üret → ContentAsset(blog, body_md) draft/ready
 *  3. Bu komut: ready durumdaki asset'leri Post'a publish hazır olarak sync eder
 *  4. Frontend'de görünmesi için Filament admin'den Post'u "is_published=true" yapmak gerek
 *
 * Idempotent: aynı brief_id → aynı slug → güncelleme, yeni Post yaratmaz.
 */
class PublishBlogAssets extends Command
{
    /**
     * brief.topic → blog_categories.id eşleştirmesi.
     */
    private const TOPIC_TO_CATEGORY = [
        'vize'         => 6,  // Vize
        'randevu'      => 6,  // Vize
        'uni_assist'   => 8,  // Başvuru
        'dil'          => 7,  // Dil & Sınavlar
        'sinav'        => 7,  // Dil & Sınavlar
        'yurt'         => 9,  // Öğrenci Hayatı
        'sehir'        => 9,  // Öğrenci Hayatı
        'studienkolleg'=> 1,  // Almanya'da Eğitim
        'master'       => 1,  // Almanya'da Eğitim
        'sigorta'      => 5,  // Finans
        'para'         => 5,  // Finans
        'sperrkonto'   => 5,  // Finans
    ];

    protected $signature = 'content:publish-blog-assets
        {--brief= : Tek bir brief_id (ör. --brief=7)}
        {--all : Tüm ready blog asset\'leri (default: sadece sync edilmemiş olanlar)}
        {--force : Mevcut Post varsa üzerine yaz}
        {--user=1 : Default user_id (admin)}
        {--dry-run : Önizleme, yazmadan}';

    protected $description = 'ContentAsset(blog, status=ready) → Post tablosuna sync eder';

    public function handle(): int
    {
        $query = ContentAsset::query()
            ->where('asset_type', 'blog')
            ->where('status', 'ready')
            ->whereNotNull('body_md');

        if ($briefId = $this->option('brief')) {
            $query->where('content_brief_id', (int) $briefId);
        } elseif (! $this->option('all')) {
            // Default: sync edilmemiş (slug Post tablosunda yoksa)
            $existingSlugs = Post::pluck('slug')->all();
            $query->whereDoesntHave('brief', function (Builder $q) use ($existingSlugs) {
                $q->whereIn('slug', $existingSlugs);
            });
        }

        $assets = $query->with('brief')->get();
        $total = $assets->count();

        if ($total === 0) {
            $this->info('Hedef asset yok — hepsi sync edilmiş olabilir.');
            return self::SUCCESS;
        }

        $this->info("📝 {$total} blog asset Post'a sync edilecek");
        $this->newLine();

        $userId = (int) $this->option('user');
        $dry = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $created = 0; $updated = 0; $skipped = 0;

        foreach ($assets as $asset) {
            $brief = $asset->brief;
            if (! $brief) {
                $this->warn('Asset #' . $asset->id . ' — brief bulunamadı, atlandı');
                $skipped++;
                continue;
            }

            $parsed = $this->parseMarkdown($asset->body_md);
            if (! $parsed) {
                $this->warn('Asset #' . $asset->id . ' (' . mb_substr($brief->title, 0, 40) . ') — parse fail');
                $skipped++;
                continue;
            }

            $slug = $parsed['slug'] ?: Str::slug($parsed['title']);
            $existing = Post::where('slug', $slug)->first();

            if ($existing && ! $force) {
                $this->line('⏭️ ' . mb_substr($parsed['title'], 0, 60) . ' — zaten var, atla (--force ile üzerine yaz)');
                $skipped++;
                continue;
            }

            $catId = self::TOPIC_TO_CATEGORY[$brief->topic] ?? 1; // default: Almanya'da Eğitim

            $contentHtml = Str::markdown($parsed['body'], [
                'html_input' => 'allow',
                'allow_unsafe_links' => false,
            ]);

            $excerpt = Str::limit($parsed['excerpt'] ?: strip_tags($contentHtml), 250, '...');

            $payload = [
                'locale'               => 'tr',
                'translation_group_id' => $existing?->translation_group_id ?? (string) Str::uuid(),
                'user_id'              => $userId,
                'category_id'          => $catId,
                'title'                => Str::limit($parsed['title'], 250, ''),
                'slug'                 => Str::limit($slug, 250, ''),
                'excerpt'              => $excerpt,
                'content_md'           => $parsed['body'],
                'content_html'         => $contentHtml,
                'meta_title'           => Str::limit($parsed['title'], 250, ''),
                'meta_description'    => Str::limit($parsed['excerpt'] ?: strip_tags($contentHtml), 250, '...'),
                'reading_minutes'      => max(1, (int) round(str_word_count(strip_tags($contentHtml)) / 220)),
                'is_published'         => false, // güvenli default — admin elle yayınlasın
                'published_at'         => $existing?->published_at ?? now(),
            ];

            if ($dry) {
                $this->info('🔍 [dry-run] ' . $parsed['title']);
                $this->line('     slug=' . $slug . ' · cat=' . $catId . ' · words=' . str_word_count(strip_tags($contentHtml)));
                continue;
            }

            if ($existing) {
                $existing->update($payload);
                $this->info('🔄 ' . mb_substr($parsed['title'], 0, 60) . ' (#' . $existing->id . ')');
                $updated++;
            } else {
                $post = Post::create($payload);
                $this->info('✅ #' . $post->id . ' ' . mb_substr($parsed['title'], 0, 55));
                $created++;
            }
        }

        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("Created: {$created}, Updated: {$updated}, Skipped: {$skipped}");
        $this->newLine();
        $this->warn('NOT: Yeni Post\'lar is_published=false ile geldi. Filament /admin/posts üzerinden incele + yayına al.');

        return self::SUCCESS;
    }

    /**
     * YAML frontmatter + body markdown parse eder.
     *  ---
     *  title: "..."
     *  slug: "..."
     *  excerpt: "..."
     *  ---
     *  body...
     */
    private function parseMarkdown(string $md): ?array
    {
        $md = trim($md);

        // Bazı asset'ler ```markdown ... ``` ile sarılmış — temizle
        if (preg_match('/^```(?:markdown|md)?\s*\n(.+)\n```\s*$/s', $md, $w)) {
            $md = trim($w[1]);
        }

        if (! preg_match('/^---\s*\n(.+?)\n---\s*\n(.+)$/s', $md, $m)) {
            // frontmatter yok, sadece body — title gerek
            return null;
        }

        $yaml = $m[1];
        $body = trim($m[2]);

        $meta = [];
        foreach (preg_split('/\n/', $yaml) as $line) {
            if (preg_match('/^(\w+):\s*(.+)$/', trim($line), $kv)) {
                $key = $kv[1];
                $val = trim($kv[2]);
                // Quoted string'i temizle
                if (preg_match('/^"(.+)"$/', $val, $q)) {
                    $val = $q[1];
                } elseif (preg_match("/^'(.+)'$/", $val, $q)) {
                    $val = $q[1];
                }
                $meta[$key] = $val;
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
