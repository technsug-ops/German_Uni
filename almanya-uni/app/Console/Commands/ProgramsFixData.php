<?php

namespace App\Console\Commands;

use App\Models\Program;
use Illuminate\Console\Command;

/**
 * Program verisindeki GÜVENLİ, deterministik temizlikler. Sadece objektif olarak
 * geçersiz değerleri düzeltir; yorum/tahmin gerektiren alanlara (harç, alan, derece)
 * DOKUNMAZ — onlar ayrı, denetimli işlerdir.
 *
 * Şu an:
 *  - duration_semesters ≤0 → NULL  (0 yarıyıl imkânsız; NULL = "bilinmiyor")
 *  - application_fee_eur >500 → NULL  (Almanya'da başvuru harcı ≤~€75; >€500 yanlış
 *    partner verisi. Yanlış değer göstermek "bilinmiyor"dan kötü → temizle.)
 *
 *   php artisan programs:fix-data            → DRY-RUN
 *   php artisan programs:fix-data --apply    → uygula
 */
class ProgramsFixData extends Command
{
    protected $signature = 'programs:fix-data {--apply : Değişiklikleri yaz (varsayılan dry-run)}';

    protected $description = 'Program verisinde güvenli/deterministik temizlikler (geçersiz süre → null).';

    public function handle(): int
    {
        $apply = $this->option('apply');
        $this->info($apply ? '🔥 APPLY — değişiklikler yazılacak' : '🔍 DRY-RUN — hiçbir şey yazılmayacak');
        $this->newLine();

        // duration_semesters ≤ 0 → NULL
        $durQ = Program::where('is_active', 1)->whereNotNull('duration_semesters')->where('duration_semesters', '<=', 0);
        $durCount = $durQ->count();
        $this->line(sprintf('  Geçersiz süre (≤0) → NULL:            %d', $durCount));
        if ($apply && $durCount) {
            $durQ->update(['duration_semesters' => null]);
        }

        // application_fee_eur > 500 → NULL (gerçekçi olmayan partner harcı)
        $feeQ = Program::where('is_active', 1)->where('application_fee_eur', '>', 500);
        $feeCount = $feeQ->count();
        $this->line(sprintf('  Absürt başvuru harcı (>€500) → NULL:  %d', $feeCount));
        if ($apply && $feeCount) {
            $feeQ->update(['application_fee_eur' => null]);
        }

        $this->newLine();
        if (! $apply && ($durCount || $feeCount)) {
            $this->warn('Uygulamak için --apply ile çalıştırın.');
        } elseif ($apply) {
            $this->info('✓ Tamamlandı.');
        }

        return self::SUCCESS;
    }
}
