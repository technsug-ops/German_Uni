<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 2026_10_09_001200/001300'ün devamı: KSH München kaydında kalan Hochschule München verisi.
 *
 * KSH'de Wikipedia linkleri (EN "Munich_University_of_Applied_Sciences", DE "Hochschule_für_angewandte_Wissenschaften_München"),
 * adres (Lothstr. 34, 80335) ve telefon (089 1265-0) HM'nindi; içerik yeniden üretilirken HM'nin Wikipedia'sı kaynak olurdu.
 * KSH'nin doğru bilgileri (2026-10-09): ksh-muenchen.de/impressum → Preysingstr. 95, 81667 München, +49 89 48092-900;
 * de.wikipedia "Katholische Stiftungshochschule München" (EN maddesi yok). HM verisi, bu alanları boş olan HM kaydına geçer.
 * Yalnız KSH hâlâ HM adresini taşıyorsa yazılır.
 */
return new class extends Migration
{
    private const KSH = 'katholische-stiftungshochschule-fur-angewandte-wissenschaften-munchen-partner-019ddbbb';

    private const HM = 'hochschule-munchen-university-of-applied-sciences-partner-019ddbba';

    private const MOVE = ['wikipedia_url_en', 'wikipedia_url_de', 'street', 'postal_code', 'phone'];

    public function up(): void
    {
        if (! Schema::hasTable('universities')) {
            return;
        }

        $ksh = DB::table('universities')->where('slug', self::KSH)->where('street', 'Lothstr. 34')->first();
        $hm = DB::table('universities')->where('slug', self::HM)->first();
        if (! $ksh || ! $hm) {
            return;
        }

        DB::transaction(function () use ($ksh, $hm) {
            $toHm = [];
            foreach (self::MOVE as $f) {
                if (blank($hm->{$f}) && filled($ksh->{$f})) {
                    $toHm[$f] = $ksh->{$f};
                }
            }
            if ($toHm) {
                DB::table('universities')->where('id', $hm->id)->update($toHm + ['updated_at' => now()]);
            }

            DB::table('universities')->where('id', $ksh->id)->update([
                'wikipedia_url_en' => null,
                'wikipedia_url_de' => 'https://de.wikipedia.org/wiki/Katholische_Stiftungshochschule_M%C3%BCnchen',
                'street' => 'Preysingstr. 95',
                'postal_code' => '81667',
                'phone' => '+49 89 48092-900',
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        // Veri düzeltmesi.
    }
};
