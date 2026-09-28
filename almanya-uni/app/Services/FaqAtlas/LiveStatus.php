<?php

namespace App\Services\FaqAtlas;

use App\Models\Faq;
use Carbon\CarbonInterface;

/**
 * SSS'nin CANLI yapısal durumu (doğruluk sınıflandırması DEĞİL):
 *   MISSING     — o dilde kayıt yok
 *   UNPUBLISHED — kayıt var ama yayında değil
 *   EMPTY       — sayfada gösterilen cevap (answer_html, etiketsiz) gerçekten boş
 *   THIN        — içerik var ama çok kısa (< THIN_THRESHOLD karakter)
 *   CONTENT     — anlamlı içerik var
 * Aynı kural hem PHP'de (classify) hem SQL'de (sql) uygulanır; liste/filtre/CSV SQL'i, detay PHP'yi kullanır.
 *
 * Canlı küme = ETKİN çeviri grubu: atlas'taki grup, yoksa denetimde bulunan TR SSS'nin (tr_faq_id) BUGÜNKÜ grubu
 * (TR sonradan bir gruba bağlanıp EN/DE kardeşleri açılabilir). Drift baz zamanı = LEAST(audited_at, created_at):
 * audited_at bir etiket olabilir ve gerçek taramadan sonrasını gösterebilir; import anı (created_at) taramadan
 * sonra, sonraki içerik düzeltmelerinden öncedir. Atlas satırı hiçbir zaman güncellenmez.
 */
class LiveStatus
{
    public const MISSING = 'MISSING';
    public const UNPUBLISHED = 'UNPUBLISHED';
    public const EMPTY = 'EMPTY';
    public const THIN = 'THIN';
    public const CONTENT = 'CONTENT';

    public const ALL = [self::MISSING, self::UNPUBLISHED, self::EMPTY, self::THIN, self::CONTENT];

    public const THIN_THRESHOLD = 80;

    /** Sayfanın gerçekten gösterdiği cevabın düz metin uzunluğu (etiket → boşluk, boşluklar tekilleşir). */
    public static function visibleLength(?string $html): int
    {
        $text = preg_replace('/\s+/u', ' ', preg_replace('/<[^>]*>/u', ' ', (string) $html));

        return mb_strlen(trim((string) $text));
    }

    public static function classify(?Faq $faq): string
    {
        if (! $faq) {
            return self::MISSING;
        }
        if (! $faq->is_published) {
            return self::UNPUBLISHED;
        }
        $len = $faq->has_answer ? self::visibleLength($faq->answer_html) : 0;

        return $len === 0 ? self::EMPTY : ($len < self::THIN_THRESHOLD ? self::THIN : self::CONTENT);
    }

    /** Etkin çeviri grubu (SQL): atlas grubu, yoksa denetimdeki TR SSS'nin şimdiki grubu. */
    public static function groupSql(string $atlasTable = 'faq_quality_atlas'): string
    {
        return "COALESCE({$atlasTable}.translation_group_id,"
            ." (SELECT g.translation_group_id FROM faqs g WHERE g.id = {$atlasTable}.tr_faq_id AND g.locale = 'tr'))";
    }

    /** Drift baz zamanı (SQL): denetim etiketi ile import anından erken olanı. */
    public static function baselineSql(string $atlasTable = 'faq_quality_atlas'): string
    {
        return "COALESCE(LEAST({$atlasTable}.audited_at, {$atlasTable}.created_at), {$atlasTable}.audited_at, {$atlasTable}.created_at)";
    }

    /** baselineSql() ile aynı kural (PHP). */
    public static function baseline(?CarbonInterface $auditedAt, ?CarbonInterface $createdAt): ?CarbonInterface
    {
        if ($auditedAt && $createdAt) {
            return $auditedAt->lt($createdAt) ? $auditedAt : $createdAt;
        }

        return $auditedAt ?? $createdAt;
    }

    /** classify() ile aynı kural — atlas satırı için ilgili dilin canlı durumunu veren SQL ifadesi (MySQL 8). */
    public static function sql(string $locale, string $atlasTable = 'faq_quality_atlas'): string
    {
        $len = "CHAR_LENGTH(TRIM(REGEXP_REPLACE(REGEXP_REPLACE(COALESCE(f.answer_html, ''), '<[^>]*>', ' '), '[[:space:]]+', ' ')))";

        return "COALESCE((SELECT CASE"
            ." WHEN f.is_published = 0 THEN '".self::UNPUBLISHED."'"
            ." WHEN f.has_answer = 0 OR {$len} = 0 THEN '".self::EMPTY."'"
            ." WHEN {$len} < ".self::THIN_THRESHOLD." THEN '".self::THIN."'"
            ." ELSE '".self::CONTENT."' END"
            ." FROM faqs f WHERE f.translation_group_id = ".self::groupSql($atlasTable)." AND f.locale = '{$locale}'"
            ." ORDER BY f.id LIMIT 1), '".self::MISSING."')";
    }

    /**
     * Atlas (denetim) durumu ile canlı durum arasında MADDİ fark var mı?
     *   - denetimde eksik olan dil artık var
     *   - denetimde boş olan dil artık anlamlı içerik taşıyor
     *   - denetimde içerik olan dil artık eksik / yayında değil / boş / çok kısa
     */
    public static function materiallyDiffers(string $atlasStatus, string $live): bool
    {
        return match (true) {
            $atlasStatus === 'MISSING' => $live !== self::MISSING,
            $atlasStatus === 'EMPTY' => $live === self::CONTENT,
            default => in_array($live, [self::MISSING, self::UNPUBLISHED, self::EMPTY, self::THIN], true),
        };
    }

    public static function materiallyDiffersSql(string $atlasColumn, string $liveSql): string
    {
        return "(({$atlasColumn} = 'MISSING' AND {$liveSql} <> 'MISSING')"
            ." OR ({$atlasColumn} = 'EMPTY' AND {$liveSql} = 'CONTENT')"
            ." OR ({$atlasColumn} NOT IN ('MISSING', 'EMPTY') AND {$liveSql} IN ('MISSING', 'UNPUBLISHED', 'EMPTY', 'THIN')))";
    }

    /**
     * REVIEW NEEDED: eşleşmiş kümede (etkin grup var) herhangi bir dildeki SSS drift baz zamanından sonra güncellendi
     * (BROKEN/STALE/PARTIAL dillerin düzeltilmesi dahil) ya da herhangi bir dilde maddi yapısal fark var.
     */
    public static function reviewSql(string $atlasTable = 'faq_quality_atlas'): string
    {
        $group = self::groupSql($atlasTable);
        $updated = "EXISTS (SELECT 1 FROM faqs f WHERE f.translation_group_id = {$group}"
            ." AND f.locale IN ('tr', 'en', 'de') AND f.updated_at > ".self::baselineSql($atlasTable).')';
        $diffs = [];
        foreach (['tr', 'en', 'de'] as $l) {
            $diffs[] = self::materiallyDiffersSql("{$atlasTable}.{$l}_status", self::sql($l, $atlasTable));
        }

        return "({$group} IS NOT NULL AND ({$updated} OR ".implode(' OR ', $diffs).'))';
    }
}
