<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Models\University;
use App\Services\PartnerImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('partner:import
    {--path= : Snapshot dizini (manifest.json içeren)}
    {--only=both : universities | programs | both}
    {--limit=0 : Programs için limit (test için)}
')]
#[Description('Kardeş kuruluştan gelen snapshot ZIP\'inin içeriğini import eder (universities + programs).')]
class PartnerImport extends Command
{
    public function handle(PartnerImporter $importer): int
    {
        $path = $this->option('path');
        if (! $path || ! is_dir($path)) {
            $this->error('--path geçersiz veya boş. Snapshot dizinini ver (manifest.json içeren).');
            return self::FAILURE;
        }

        $manifestFile = $path . DIRECTORY_SEPARATOR . 'manifest.json';
        if (! file_exists($manifestFile)) {
            $this->error("manifest.json bulunamadı: $manifestFile");
            return self::FAILURE;
        }

        $manifest = json_decode(file_get_contents($manifestFile), true);
        $this->info("Snapshot: {$manifest['snapshot_taken_at']} — counts: " . json_encode($manifest['counts']));

        $only = $this->option('only');

        if (in_array($only, ['universities', 'both'], true)) {
            $uniFile = $path . DIRECTORY_SEPARATOR . 'universities.json';
            if (! file_exists($uniFile)) {
                $this->error("universities.json bulunamadı");
                return self::FAILURE;
            }

            $this->info('Universities yükleniyor...');
            $stats = University::withoutSyncingToSearch(fn () => $importer->importUniversities($uniFile));
            $this->table(
                ['Linked (existing)', 'Created (new)', 'Unlinked'],
                [[$stats['linked'], $stats['created'], $stats['unlinked']]]
            );
        }

        if (in_array($only, ['programs', 'both'], true)) {
            $progFile = $path . DIRECTORY_SEPARATOR . 'programs.json';
            if (! file_exists($progFile)) {
                $this->error("programs.json bulunamadı");
                return self::FAILURE;
            }

            $limit = (int) $this->option('limit');
            $total = $limit > 0 ? $limit : $manifest['counts']['programs'];

            $this->info("Programs yükleniyor (hedef: $total)...");
            $bar = $this->output->createProgressBar($total);
            $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%');
            $bar->start();

            $stats = $importer->importPrograms($progFile, fn () => $bar->advance(), $limit);

            $bar->finish();
            $this->newLine(2);

            $this->table(
                ['Imported', 'Updated', 'Skipped (no uni)', 'Errors'],
                [[$stats['imported'], $stats['updated'], $stats['skipped_no_uni'], $stats['errors']]]
            );

            $this->info('Total programs in DB: ' . Program::count());
        }

        return self::SUCCESS;
    }
}
