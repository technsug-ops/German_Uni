<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Mükerrer temizliği — Studienkolleg rehberinin eski slug'lı TR/EN/DE kümesini yayından kaldırır.
 *
 * Canlı teşhis (2026-09-25): iki üç dilli küme yayında; olgular birebir aynı, eski EN/DE'nin
 * tabloları bozuk. İçerik kararı: TR/EN/DE = KEEP NEW (içerik taşınmaz).
 *   winner: studienkolleg-guide-2026-who-needs-it-which-course-which-school(-en|-de)
 *   loser : studienkolleg-rehberi-t-kurs-m-kurs-2026 · studienkolleg-guide-who-needs-it-which-course-school
 *           · studienkolleg-guide-pflicht-kurs-schule
 * tr/en yönlendirme satırları mevcut; de satırı YOK → eklenir. Her loser aynı dilde TEK 301 alır.
 *
 * Doğrulama: üç kazanan yayında ve AYNI çeviri grubunda; loser'lar (varsa) kazananın grubunda
 * DEĞİL; mevcut yönlendirme satırları kazanana gidiyor. Tutmazsa exception, hiçbir şey yazılmaz.
 * Silme yok, slug değişmez, updated_at'e dokunulmaz.
 */
return new class extends Migration
{
    private const PAIRS = [
        'tr' => ['studienkolleg-rehberi-t-kurs-m-kurs-2026', 'studienkolleg-guide-2026-who-needs-it-which-course-which-school'],
        'en' => ['studienkolleg-guide-who-needs-it-which-course-school', 'studienkolleg-guide-2026-who-needs-it-which-course-which-school-en'],
        'de' => ['studienkolleg-guide-pflicht-kurs-schule', 'studienkolleg-guide-2026-who-needs-it-which-course-which-school-de'],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('posts') || ! Schema::hasTable('blog_redirects')) {
            return;
        }

        $losers = [];
        $winners = [];
        foreach (self::PAIRS as $locale => [$old, $new]) {
            $losers[$locale] = DB::table('posts')->where('locale', $locale)->where('slug', $old)->first();
            $winners[$locale] = DB::table('posts')->where('locale', $locale)->where('slug', $new)->first();
        }

        if (array_filter($losers) === []) {
            Log::info('studienkolleg retire: eski küme yok, atlandı');

            return;
        }

        $errors = [];
        foreach ($winners as $locale => $w) {
            if (! $w || ! $w->is_published || $w->published_at === null || $w->published_at > now()) {
                $errors[] = "{$locale}: kazanan yok ya da yayında değil";
            }
        }
        $groups = array_unique(array_map(fn ($w) => $w?->translation_group_id, $winners));
        if (count($groups) !== 1 || reset($groups) === null) {
            $errors[] = 'kazananlar tek bir çeviri grubunda değil';
        }
        foreach ($losers as $locale => $l) {
            if ($l && $l->translation_group_id !== null && in_array($l->translation_group_id, $groups, true)) {
                $errors[] = "{$locale}: loser kazananın grubunda — eşleşme belirsiz";
            }
        }
        foreach (self::PAIRS as $locale => [$old, $new]) {
            $rows = DB::table('blog_redirects')->where('from_slug', $old)->where('locale', $locale)->get();
            if ($rows->count() > 1 || ($rows->count() === 1 && $rows->first()->to_slug !== $new)) {
                $errors[] = "{$locale}: mevcut yönlendirme kazanana gitmiyor";
            }
        }
        if ($errors !== []) {
            throw new RuntimeException("studienkolleg retire: HİÇBİR ŞEY YAZILMADI:\n  " . implode("\n  ", $errors));
        }

        $report = [];
        DB::transaction(function () use ($losers, &$report) {
            foreach (self::PAIRS as $locale => [$old, $new]) {
                if (! DB::table('blog_redirects')->where('from_slug', $old)->where('locale', $locale)->exists()) {
                    DB::table('blog_redirects')->insert([
                        'from_slug' => $old, 'to_slug' => $new, 'locale' => $locale,
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                    $report[] = "{$locale}: yönlendirme eklendi {$old} → {$new}";
                }
                $l = $losers[$locale];
                if ($l && $l->is_published) {
                    DB::table('posts')->where('id', $l->id)->update(['is_published' => false]);
                    $report[] = "{$locale}: #{$l->id} yayından kaldırıldı";
                }
            }
        });

        echo "studienkolleg retire:\n  " . (implode("\n  ", $report) ?: 'zaten temiz') . "\n";
    }

    public function down(): void
    {
        // Bilinçli olarak boş: eski kümeyi geri yayına almak mükerrer içeriği geri getirir.
    }
};
