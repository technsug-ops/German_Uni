<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\Content\BlogAutoLinker;
use App\Support\MarkdownRenderer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * content_html boş ama content_md dolu olan yazıları backfill eder.
 *
 * Kök neden: çeviri/import pipeline'ı yazıları DB::table ile toplu insert ediyor
 * (Eloquent save bypass → Scout'tan kaçınmak için). Post modelinin saving()
 * mutator'ı yalnızca content_md "dirty" olunca content_html'i render ettiği için,
 * bu yazıların content_html'i hiç üretilmiyor → blog/show'da boş görünüyorlar
 * ve FAQ schema almıyorlar (FaqExtractor content_html okur).
 *
 * Bu komut mutator ile AYNI servisleri (MarkdownRenderer + BlogAutoLinker)
 * kullanıp eksik content_html'leri üretir ve DB::table ile yazar (Scout tetiklemez).
 * Varsayılan DRY-RUN; --apply ile uygular.
 */
class BlogRenderHtml extends Command
{
    protected $signature = 'blog:render-html {--apply : değişiklikleri uygula (yoksa dry-run)} {--id= : sadece tek bir post id} {--force : content_html dolu olsa bile yeniden render et (re-autolink — i18n temizliği için)}';

    protected $description = 'content_html boş yazıları content_md\'den render edip backfill et (mutator ile aynı pipeline)';

    public function handle(MarkdownRenderer $renderer, BlogAutoLinker $linker): int
    {
        $apply = (bool) $this->option('apply');
        $force = (bool) $this->option('force'); // dolu content_html'leri de yeniden render et (re-autolink)

        $query = Post::query()
            ->whereNotNull('content_md')->where('content_md', '!=', '');

        if ($id = $this->option('id')) {
            $query->where('id', (int) $id);
        }

        $posts = $query->get(['id', 'locale', 'slug', 'title', 'content_md', 'content_html']);

        $fixed = 0;
        $skipped = 0;
        $rows = [];

        foreach ($posts as $post) {
            // Varsayılan: yalnızca content_html'i boş olanları backfill et. --force ile dolu
            // olanlar da yeniden render edilir (i18n re-autolink: DE/EN'den Türkçe glossary söker).
            if (! $force && trim(strip_tags((string) $post->content_html)) !== '') {
                $skipped++;
                continue;
            }

            $html = $renderer->render((string) $post->content_md);
            // ÖNEMLİ: post'un kendi locale'ini geçir → TR'de glossary eklenir, EN/DE'de eklenmez.
            // (Bu olmadan endpoint context'inde app locale kullanılır = yanlış dil.)
            $html = $linker->process($html, excludeUrl: null, resetCounters: true, locale: $post->locale);

            if (trim(strip_tags($html)) === '') {
                // markdown render hâlâ boş → veri sorunlu, atla (sessizce gömme)
                $rows[] = [$post->id, $post->locale, '⚠️ render boş', mb_substr((string) $post->title, 0, 50)];

                continue;
            }

            $fixed++;
            $words = preg_match_all('/[\p{L}\p{N}]+/u', strip_tags($html));
            $rows[] = [$post->id, $post->locale, $words . ' kelime', mb_substr((string) $post->title, 0, 50)];

            if ($apply) {
                DB::table('posts')->where('id', $post->id)->update([
                    'content_html' => $html,
                    'reading_minutes' => Post::computeReadingMinutes((string) $post->content_md),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->newLine();
        if ($rows) {
            $this->table(['ID', 'Dil', 'Sonuç', 'Başlık'], $rows);
        }
        $this->info(($apply ? '✅ UYGULANDI' : '🔍 DRY-RUN (uygulamak için --apply)')
            . " — backfill edilecek: {$fixed}, dolu (atlandı): {$skipped}");

        return self::SUCCESS;
    }
}
