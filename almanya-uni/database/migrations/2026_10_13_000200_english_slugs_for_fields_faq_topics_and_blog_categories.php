<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * İngilizce URL kuralı — Faz 1: alan (fields_of_study), FAQ konusu (faq_topics) ve blog kategorisi (categories)
 * slug'ları İngilizceye çevrilir; eski slug'lar slug_redirects ile kalıcı 301 olur; kb_chunks URL'leri güncellenir.
 * Ön doğrulama: her eski slug ya mevcut ya da zaten yenisine dönmüş; yeni slug başka bir kayıtta kullanılmıyor.
 * Tek transaction; rerun no-op. Test ortamında olmayan satırlar atlanır (fixture'lar kendi satırını kurar).
 */
return new class extends Migration
{
    public const FIELDS = [
        'muhendislik'      => 'engineering',
        'bilisim'          => 'computer-science',
        'matematik-doga'   => 'mathematics-natural-sciences',
        'tip-saglik'       => 'medicine-health',
        'hukuk-ekonomi'    => 'law-economics',
        'sosyal-bilimler'  => 'social-sciences',
        'sanat-tasarim'    => 'arts-design',
        'dil-kultur'       => 'languages-culture',
        'tarim-ormancilik' => 'agriculture-forestry-nutrition',
        'veteriner-spor'   => 'veterinary-sport-sciences',
    ];

    public const FAQ_TOPICS = [
        'vize'    => 'visa',
        'dil'     => 'language',
        'yurt'    => 'housing',
        'para'    => 'money',
        'sehir'   => 'city-life',
        'is'      => 'work',
        'sigorta' => 'insurance',
        'randevu' => 'appointments',
        'burs'    => 'scholarships',
        'denklik' => 'recognition',
        // master, uni-assist, studienkolleg, anmeldung: zaten İngilizce/uluslararası
    ];

    public const CATEGORIES = [
        'almanyada-egitim' => 'studying-in-germany',
        'finans'           => 'finance',
        'vize'             => 'visa',
        'dil'              => 'language-exams',
        'basvuru'          => 'application',
        'yasam'            => 'campus-life', // 'student-life' haber kategorisinde kullanımda
    ];

    /** entity → [tablo, eşleme, kb_chunks URL deseni (sprintf, eski/yeni slug)] */
    private const PLAN = [
        'field'     => ['fields_of_study', self::FIELDS],
        'faq_topic' => ['faq_topics', self::FAQ_TOPICS],
        'category'  => ['categories', self::CATEGORIES],
    ];

    public function up(): void
    {
        foreach (['fields_of_study', 'faq_topics', 'categories', 'slug_redirects'] as $t) {
            if (! Schema::hasTable($t)) {
                return;
            }
        }
        $problems = [];
        $renames = [];   // [table, id, old, new, entity]
        foreach (self::PLAN as $entity => [$table, $map]) {
            foreach ($map as $old => $new) {
                $oldRow = DB::table($table)->where('slug', $old)->first(['id']);
                $newRow = DB::table($table)->where('slug', $new)->first(['id']);
                if ($oldRow && $newRow) {
                    $problems[] = "$table: hem '$old' hem '$new' var";
                    continue;
                }
                if (! $oldRow && ! $newRow) {
                    if (! app()->environment('testing')) {
                        $problems[] = "$table: '$old' bulunamadı";
                    }
                    continue;
                }
                if ($oldRow) {
                    $renames[] = [$table, $oldRow->id, $old, $new, $entity];
                }
                // $newRow: zaten uygulanmış → yönlendirme kaydı yine garanti edilir (aşağıda idempotent)
            }
        }
        if ($problems) {
            throw new RuntimeException('İngilizce slug (Faz 1) uygulanmadı, hiçbir şey yazılmadı: ' . implode('; ', $problems));
        }

        DB::transaction(function () use ($renames) {
            $now = now();
            foreach ($renames as [$table, $id, $old, $new]) {
                DB::table($table)->where('id', $id)->update(['slug' => $new, 'updated_at' => $now]);
            }
            foreach (self::PLAN as $entity => [$table, $map]) {
                foreach ($map as $old => $new) {
                    // Yalnız hedef satır gerçekten varsa yönlendirme yaz (test DB'sinde olmayanlar atlanır).
                    if (! DB::table($table)->where('slug', $new)->exists()) {
                        continue;
                    }
                    $existing = DB::table('slug_redirects')->where('entity', $entity)->where('from_slug', $old)->first(['id', 'to_slug']);
                    if (! $existing) {
                        DB::table('slug_redirects')->insert(['entity' => $entity, 'from_slug' => $old, 'to_slug' => $new, 'created_at' => $now, 'updated_at' => $now]);
                    } elseif ($existing->to_slug !== $new) {
                        DB::table('slug_redirects')->where('id', $existing->id)->update(['to_slug' => $new, 'updated_at' => $now]);
                    } // aynı kayıt → dokunma (rerun no-op)
                }
            }
            // KB: FAQ chunk URL'leri konu segmentini içerir (/{locale}/faq/{topic}/{slug}); yeniden embed gerekmez.
            if (Schema::hasTable('kb_chunks')) {
                foreach (self::FAQ_TOPICS as $old => $new) {
                    DB::table('kb_chunks')->where('source_type', 'faq')->where('url', 'like', "%/faq/$old/%")
                        ->update(['url' => DB::raw("REPLACE(url, '/faq/$old/', '/faq/$new/')")]);
                }
            }
        });
    }

    public function down(): void
    {
        // Geri alma yok — URL değişikliği kalıcı, yönlendirmeler korunur.
    }
};
