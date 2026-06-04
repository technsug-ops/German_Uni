<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\Content\BlogPublisher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Blog iç linklerini + resimlerini denetler ve düzeltir (kullanıcı 5 aksiyon):
 *  1) TÜM iç link/resimleri kataloglar → storage/app/blog-link-catalog.json (tabela, muhafaza)
 *  2) Kırık linkleri çözer: geçerli route → locale-prefix'li canonical; post → post linki
 *  3) Çözülemeyeni linkten çıkarır (metni korur, 404 biter)
 *  4) Doğru hedef + doğru locale
 *  5) Bozuk banner/resimleri (almanyauni.com/images vb.) kaldırır
 *
 * Varsayılan DRY-RUN (sadece katalog + ne yapılacağını raporlar). --apply ile uygular.
 */
class FixBlogLinks extends Command
{
    protected $signature = 'content:fix-blog-links {--apply : değişiklikleri uygula (yoksa dry-run)}';
    protected $description = 'Blog iç linklerini/resimlerini katalogla + kırıkları çöz/kaldır + bozuk resimleri sil';

    public function handle(BlogPublisher $publisher): int
    {
        $apply = (bool) $this->option('apply');

        $posts = Post::query()
            ->whereNotNull('content_md')->where('content_md', '!=', '')
            ->get(['id', 'locale', 'slug', 'title', 'content_md']);

        $catalog = [];
        $postsChanged = 0;
        $linksBefore = 0;
        $linksAfter = 0;
        $imagesBefore = 0;
        $imagesAfter = 0;

        foreach ($posts as $post) {
            $orig = (string) $post->content_md;
            $loc = $post->locale ?: 'tr';

            $before = $publisher->collectInternalLinks($orig);
            $linksBefore += count(array_filter($before, fn ($x) => $x['type'] === 'link'));
            $imagesBefore += count(array_filter($before, fn ($x) => $x['type'] === 'image'));

            // 2-4: linkleri çöz, 5: bozuk resimleri kaldır
            $md = $publisher->resolveInternalLinks($orig, $loc);
            $md = $publisher->stripBrokenImages($md);

            $after = $publisher->collectInternalLinks($md);
            $linksAfter += count(array_filter($after, fn ($x) => $x['type'] === 'link'));
            $imagesAfter += count(array_filter($after, fn ($x) => $x['type'] === 'image'));

            // 1: katalog (tabela) — bu postta verilmiş tüm iç link/resimler
            if ($before) {
                $catalog[] = [
                    'id' => $post->id,
                    'locale' => $loc,
                    'slug' => $post->slug,
                    'title' => mb_substr((string) $post->title, 0, 80),
                    'changed' => $md !== $orig,
                    'links' => $before,
                ];
            }

            if ($md !== $orig) {
                $postsChanged++;
                if ($apply) {
                    $html = Str::markdown($md, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
                    $post->update(['content_md' => $md, 'content_html' => $html]);
                }
            }
        }

        // 1: tabelayı dosyaya yaz (muhafaza)
        Storage::put('blog-link-catalog.json', json_encode([
            'generated_at' => now()->toIso8601String(),
            'mode' => $apply ? 'applied' : 'dry-run',
            'totals' => [
                'posts_scanned' => $posts->count(),
                'posts_with_links' => count($catalog),
                'posts_changed' => $postsChanged,
                'links_before' => $linksBefore, 'links_after' => $linksAfter,
                'links_removed_or_fixed' => $linksBefore - $linksAfter,
                'images_before' => $imagesBefore, 'images_after' => $imagesAfter,
                'images_removed' => $imagesBefore - $imagesAfter,
            ],
            'catalog' => $catalog,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        $this->newLine();
        $this->info(($apply ? '✅ UYGULANDI' : '🔍 DRY-RUN') . " — tabela: storage/app/blog-link-catalog.json");
        $this->table(['Metrik', 'Değer'], [
            ['Taranan post', $posts->count()],
            ['Link içeren post', count($catalog)],
            ['Değişen post', $postsChanged],
            ['Link (önce→sonra)', "{$linksBefore} → {$linksAfter}  (−" . ($linksBefore - $linksAfter) . ')'],
            ['Resim (önce→sonra)', "{$imagesBefore} → {$imagesAfter}  (−" . ($imagesBefore - $imagesAfter) . ')'],
        ]);

        return self::SUCCESS;
    }
}
