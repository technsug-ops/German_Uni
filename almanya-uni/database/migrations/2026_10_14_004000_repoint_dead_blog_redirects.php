<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * SEO Recovery P0-4 (C4): yayında olmayan (taslak) yazıya giden blog_redirects satırları 301 → 404 zinciri
 * üretiyordu. Aynı dilde, aynı konuyu işleyen YAYINDAKİ yazıya (çoğunlukla aynı içeriğin bölünmüş 1. parçası)
 * doğrudan yönlendirilir. İçerik karşılaştırması: docs/SEO_RECOVERY_P0_4_RESULT.md §C4.
 *
 * Güvenli ve idempotent:
 * - Satır from_slug + locale ile bulunur (prod id'leri lokalden farklı).
 * - Yalnız satır hâlâ beklenen ESKİ hedefi gösteriyorsa, eski hedef bu dilde hâlâ yayında DEĞİLSE ve yeni hedef
 *   bu dilde YAYINDAYSA güncellenir. Aksi halde satıra dokunulmaz (sebep yazdırılır).
 * - Karşılığı olmayan 2 satır (bozuk haber, EN/DE) değişmez; BlogController hedef yayında değilse artık doğrudan 404 verir.
 */
return new class extends Migration
{
    /** @var array<int, array{0: string, 1: string, 2: string, 3: string}> [locale, from_slug, eski to_slug, yeni to_slug] */
    public const MAP = [
        ['tr', 'almanca-bilmeden-almanyada-ingilizce-master-yapmak-mumkun-mu-kapsamli-rehber', 'can-you-study-for-an-english-masters-in-germany-without-knowing', 'english-masters-in-germany-without-german-knowledge-finding-programs-and-application'],
        ['tr', 'aile-birlesimi-vizesi-almanya-es-ve-cocuklarinizi-yaniniza-getirme-rehberi', 'family-reunion-visa-germany-a-guide-to-bringing-your-spouse-and', 'germany-family-reunion-visa-2026-who-can-apply'],
        ['en', 'aile-birlesimi-vizesi-almanya-es-ve-cocuklarinizi-yaniniza-getirme-rehberi-en', 'family-reunion-visa-germany-a-guide-to-bringing-your-spouse-and-en', 'germany-family-reunion-visa-2026-who-can-apply-en'],
        ['de', 'aile-birlesimi-vizesi-almanya-es-ve-cocuklarinizi-yaniniza-getirme-rehberi-de', 'family-reunion-visa-germany-a-guide-to-bringing-your-spouse-and-de', 'germany-family-reunion-visa-2026-who-can-apply-de'],
        // Bozuk otomatik haber (2026_06_20_000100 ile kaldırıldı); o migration'ın kendi notu: gerçek içerik bu TR rehberde.
        ['tr', 'part-time-jobs-in-germany-the-news-youre-looking-for-isnt-there-26060621524305b7', 'part-time-jobs-in-germany-the-news-youre-looking-for-isnt', 'part-time-jobs-germany-international-students-guide'],
        ['en', 'sperrkonto-2025-tam-rehber-almanya-vizesi-icin-bloke-hesap-en', 'sperrkonto-2025-complete-guide-blocked-account-for-germany-visa-en', 'sperrkonto-for-a-german-visa-what-is-it-how-much-and-en'],
        ['de', 'sperrkonto-2025-tam-rehber-almanya-vizesi-icin-bloke-hesap-de', 'sperrkonto-2025-complete-guide-blocked-account-for-germany-visa-de', 'sperrkonto-for-a-german-visa-what-is-it-how-much-and-de'],
        ['en', 'almanyada-ogrenci-isleri-20-saat-kurali-vergi-ve-saglik-sigortasi-rehberi-en', 'student-matters-in-germany-the-20-hour-rule-tax-and-health-en', 'student-work-permit-in-germany-2026-20-hour-rule-and-types-en'],
        ['de', 'almanyada-ogrenci-isleri-20-saat-kurali-vergi-ve-saglik-sigortasi-rehberi-de', 'student-matters-in-germany-the-20-hour-rule-tax-and-health-de', 'student-work-permit-in-germany-2026-20-hour-rule-and-types-de'],
        ['tr', 'testdaf-mi-dsh-mi-karsilastirmasi', 'testdaf-or-dsh-2026-german-language-exam-comparison', 'testdaf-and-dsh-a-comprehensive-comparison-of-german-exams-for-2026'],
        ['en', 'testdaf-mi-dsh-mi-karsilastirmasi-en', 'testdaf-or-dsh-2026-german-language-exam-comparison-en', 'testdaf-and-dsh-a-comprehensive-comparison-of-german-exams-for-2026-en'],
        ['de', 'testdaf-mi-dsh-mi-karsilastirmasi-de', 'testdaf-or-dsh-2026-german-language-exam-comparison-de', 'testdaf-and-dsh-a-comprehensive-comparison-of-german-exams-for-2026-de'],
    ];

    private function published(string $slug, string $locale): bool
    {
        return DB::table('posts')
            ->where('slug', $slug)
            ->where('locale', $locale)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->exists();
    }

    public function up(): void
    {
        if (! Schema::hasTable('blog_redirects') || ! Schema::hasTable('posts')) {
            return;
        }

        foreach (self::MAP as [$locale, $from, $oldTo, $newTo]) {
            $row = DB::table('blog_redirects')->where('from_slug', $from)->where('locale', $locale)->first();
            $status = match (true) {
                $row === null => 'skip: satır yok',
                $row->to_slug === $newTo => 'ok: zaten güncel',
                $row->to_slug !== $oldTo => "skip: hedef beklenenden farklı ({$row->to_slug})",
                $this->published($oldTo, $locale) => 'skip: eski hedef yeniden yayında',
                ! $this->published($newTo, $locale) => 'skip: yeni hedef yayında değil',
                default => null,
            };

            if ($status === null) {
                DB::table('blog_redirects')->where('id', $row->id)->update(['to_slug' => $newTo, 'updated_at' => now()]);
                $status = 'updated';
            }

            if (! app()->runningUnitTests()) {
                echo "  blog_redirects [{$locale}] {$from}: {$status}\n";
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('blog_redirects')) {
            return;
        }

        foreach (self::MAP as [$locale, $from, $oldTo, $newTo]) {
            DB::table('blog_redirects')->where('from_slug', $from)->where('locale', $locale)->where('to_slug', $newTo)
                ->update(['to_slug' => $oldTo, 'updated_at' => now()]);
        }
    }
};
