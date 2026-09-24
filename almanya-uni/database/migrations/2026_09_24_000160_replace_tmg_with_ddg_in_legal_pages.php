<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Künye atfını güncelle: § 5 TMG → § 5 DDG. SADECE bu atıf.
 *
 * Telemediengesetz 14.05.2024'te yürürlükten kalktı. Ama "TMG'yi her yerde DDG
 * yap" YANLIŞ bir dönüşüm — bu migration'ın ilk hâli tam bu hatayı yapıyordu:
 *
 *   § 5 TMG → § 5 DDG        DOĞRULANDI. Genel bilgilendirme/künye yükümlülüğü
 *                            DDG § 5'e taşındı, konu ve numara örtüşüyor.
 *
 *   § 7 Abs. 1 TMG           DOKUNULMUYOR. Aynı numaralı DDG maddeleri eski
 *   §§ 8-10 TMG              TMG sorumluluk maddelerinin devamı DEĞİL; DDG § 9
 *   §§ 7-10 TMG              ve § 10 farklı konuları (audiovisual media /
 *                            video-sharing) düzenliyor. Eski §§ 8-10 TMG'deki
 *                            aracı sorumluluk rejimi bugün esas olarak DSA
 *                            (AB 2022/2065) m. 4-6 altında; DDG § 7/§ 8 yalnızca
 *                            tamamlayıcı ulusal hükümler içeriyor. Otomatik
 *                            numara eşleştirmesi bu metinleri yanlışlar.
 *
 * Sorumluluk (Haftung) metinlerinin akıbeti ayrı bir karar: silinecek mi, DSA
 * m. 4-6'ya göre yeniden mi yazılacak. O karar verilene kadar bu migration o
 * cümlelere DOKUNMAZ; `legal:audit` onları "karar bekliyor" bulgusu olarak
 * raporlamaya devam eder.
 *
 * § 18 MStV atıfları geçerliliğini koruyor, dokunulmaz.
 *
 * Canlıda (2026-09-24) etkilenen: impressum — gövde (3 dil) + meta açıklama (3 dil).
 */
return new class extends Migration
{
    /**
     * Yalnızca "§ 5 TMG" kalıbı. "§§ 8-10 TMG" ya da "§ 7 Abs. 1 TMG" bu kalıba
     * girmez: 5'ten hemen sonra boşluk + TMG gelmesi şart.
     */
    private const PATTERN = '~(§\s*5)\s+TMG\b~u';

    public function up(): void
    {
        if (! Schema::hasTable('legal_pages')) {
            Log::warning('§5 TMG→DDG: legal_pages tablosu yok, atlandı');

            return;
        }

        $report = [];
        $total = 0;

        foreach (DB::table('legal_pages')->orderBy('id')->get() as $page) {
            $update = [];

            foreach (['bodies', 'descriptions'] as $column) {
                $values = json_decode($page->{$column} ?? '', true);

                if (! is_array($values) || $values === []) {
                    continue;
                }

                $changed = false;

                foreach ($values as $locale => $text) {
                    if (! is_string($text)) {
                        continue;
                    }

                    $new = preg_replace(self::PATTERN, '$1 DDG', $text, -1, $count);

                    if ($count === 0) {
                        continue;
                    }

                    $values[$locale] = $new;
                    $changed = true;
                    $total += $count;
                    $report[] = "{$page->key}/{$locale} ({$column}): {$count} × § 5 TMG → § 5 DDG";
                }

                if ($changed) {
                    $update[$column] = json_encode($values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }

            if ($update !== []) {
                $update['updated_at'] = now();
                DB::table('legal_pages')->where('id', $page->id)->update($update);
            }
        }

        if ($total === 0) {
            // Idempotent: ikinci koşuda ya da zaten güncel veride normal.
            echo "§5 TMG→DDG: düzeltilecek atıf bulunamadı (muhtemelen zaten güncel)\n";

            return;
        }

        echo "§5 TMG→DDG: {$total} atıf güncellendi\n  " . implode("\n  ", $report) . "\n";
    }

    public function down(): void
    {
        // Bilinçli olarak boş: yürürlükten kalkmış bir kanuna atfı geri koymak
        // künyeyi tekrar yanlışlamak olurdu.
    }
};
