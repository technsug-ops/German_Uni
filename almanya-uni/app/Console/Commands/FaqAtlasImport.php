<?php

namespace App\Console\Commands;

use App\Services\FaqAtlas\FaqAtlasImporter;
use Illuminate\Console\Command;

/**
 * SSS Kalite Atlası anlık görüntüsünü içe aktarır (idempotent; mevcut satırlar değişmez, SSS'lere yazılmaz).
 *   php artisan faq:atlas-import resources/data/faq-atlas/atlas-2026-09-28.json --dry-run
 * Yeni anlık görüntüler için beklenen checksum --sha256 ile ya da FaqAtlasImporter::KNOWN_CHECKSUMS ile verilir.
 */
class FaqAtlasImport extends Command
{
    protected $signature = 'faq:atlas-import {file} {--dry-run} {--sha256= : Beklenen SHA-256 (bilinen anlık görüntüler için gerekmez)}';

    protected $description = 'SSS Kalite Atlası anlık görüntüsünü (JSON) faq_quality_atlas tablosuna aktarır';

    public function handle(FaqAtlasImporter $importer): int
    {
        $path = $this->argument('file');
        if (! is_file($path) && is_file(base_path($path))) {
            $path = base_path($path);
        }
        try {
            $r = $importer->import($path, (bool) $this->option('dry-run'), $this->option('sha256') ?: null);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
        $this->info(($r['dry_run'] ? '[DRY RUN] ' : '')."Audit {$r['audit_label']}: toplam {$r['total']}, eklenen {$r['inserted']}, zaten var {$r['existing']}, eşleşen {$r['resolved']}, eşleşmeyen ".count($r['unresolved']));
        foreach ($r['unresolved'] as $u) {
            $this->warn("  eşleşmedi: {$u['tr_slug']} — {$u['reason']}");
        }
        foreach (array_slice($r['notes'], 0, 50) as $n) {
            $this->line("  not: {$n}");
        }

        return self::SUCCESS;
    }
}
