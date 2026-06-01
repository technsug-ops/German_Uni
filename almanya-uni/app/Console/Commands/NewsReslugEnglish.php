<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Mevcut yayınlanmış haberlerin slug'larını İNGİLİZCE tabana çevirir
 * (linkler İngilizce olmalı). translation_group başına EN kardeşin başlığından
 * tek taban üretir; tr=taban, en=taban-en, de=taban-de (posts.slug global unique).
 *
 * UYARI: Eski URL'ler değişir (301 yok). Sadece taze haberler için güvenli.
 *
 *   php artisan news:reslug-english [--dry-run]
 */
class NewsReslugEnglish extends Command
{
    protected $signature = 'news:reslug-english {--dry-run}';
    protected $description = 'Mevcut haber slug\'larını İngilizce tabana çevirir (grup bazında)';

    public function handle(): int
    {
        $groups = Post::news()->whereNotNull('translation_group_id')
            ->get(['id', 'locale', 'slug', 'title', 'translation_group_id'])
            ->groupBy('translation_group_id');

        if ($groups->isEmpty()) {
            $this->info('Haber yok.');
            return self::SUCCESS;
        }

        $this->info("🔗 {$groups->count()} haber grubu yeniden slug'lanacak");
        $dry = (bool) $this->option('dry-run');
        $changed = 0;

        foreach ($groups as $gid => $posts) {
            // İngilizce başlık tercih et; yoksa ilk kardeşin başlığı (Str::slug ASCII'ye katlar)
            $enTitle = optional($posts->firstWhere('locale', 'en'))->title ?: $posts->first()->title;
            $base = Str::limit(Str::slug((string) $enTitle), 80, '') ?: 'news';
            $base .= '-' . substr(sha1((string) $gid), 0, 6);

            foreach ($posts as $p) {
                $new = $base . ($p->locale === 'tr' ? '' : '-' . $p->locale);
                if ($p->slug === $new) continue;
                $this->line("  {$p->locale}: {$p->slug}  →  {$new}");
                if (! $dry) {
                    // çakışma olursa atla (başka kayıt aynı slug'ı tutuyorsa)
                    $taken = Post::where('slug', $new)->where('id', '!=', $p->id)->exists();
                    if ($taken) { $this->warn("     atlandı (slug dolu)"); continue; }
                    Post::where('id', $p->id)->update(['slug' => $new]);
                    $changed++;
                }
            }
        }

        $this->newLine();
        $this->info($dry ? 'DRY — değişiklik yok.' : "✅ {$changed} slug güncellendi.");
        return self::SUCCESS;
    }
}
