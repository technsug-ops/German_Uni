<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Kazınan HK JSON dosyalarını hk_catalog yedek tablosuna yükler (ham referans).
 * Idempotent: önce truncate, sonra topluca insert. Mevcut programlara DOKUNMAZ —
 * düzeltme ayrı (programs:import-hk-catalog --update-only).
 */
class LoadHkBackup extends Command
{
    protected $signature = 'programs:load-hk-backup {--glob=storage/app/hk-*.json : Yüklenecek HK mod dosyaları}';

    protected $description = 'HK ham kataloğunu hk_catalog yedek tablosuna yükle (referans/yedek; import yapmaz).';

    public function handle(): int
    {
        $files = glob(base_path($this->option('glob')));
        if (empty($files)) {
            $this->error('HK JSON bulunamadı: ' . $this->option('glob'));

            return self::FAILURE;
        }

        DB::table('hk_catalog')->truncate();
        $now = now();
        $total = 0;

        foreach ($files as $file) {
            $mode = preg_replace('/^hk-(.+)\.json$/', '$1', basename($file));
            $rows = json_decode(file_get_contents($file), true) ?: [];
            $this->info(basename($file) . ': ' . count($rows) . ' satır');

            foreach (array_chunk($rows, 1000) as $chunk) {
                $insert = [];
                foreach ($chunk as $r) {
                    $insert[] = [
                        'mode'       => $mode,
                        'hochschule' => mb_substr((string) ($r['hochschule'] ?? ''), 0, 400),
                        'fach'       => mb_substr((string) ($r['fach'] ?? ''), 0, 400),
                        'ort'        => $r['ort'] ?? null,
                        'abschluss'  => $r['abschluss'] ?? null,
                        'typ'        => $r['typ'] ?? null,
                        'studtyp'    => $r['studtyp'] ?? null,
                        'form'       => $r['form'] ?? null,
                        'zulassung'  => $r['admission_mode'] ?? $r['zulassung'] ?? $mode,
                        'raw'        => json_encode($r, JSON_UNESCAPED_UNICODE),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                DB::table('hk_catalog')->insert($insert);
                $total += count($insert);
            }
        }

        $this->info("✅ hk_catalog yedeğine {$total} satır yüklendi.");

        return self::SUCCESS;
    }
}
