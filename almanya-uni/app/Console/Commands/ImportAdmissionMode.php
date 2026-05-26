<?php

namespace App\Console\Commands;

use App\Models\Program;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('programs:import-admission {file : CSV dosyası yolu} {--dry-run : DB\'ye yazma, sadece simüle et}')]
#[Description('CSV ile programs.admission_mode kolonunu toplu doldur. Header: program_slug,admission_mode')]
class ImportAdmissionMode extends Command
{
    private const ALLOWED = ['zulassungsfrei', 'oertlich', 'bundesweit', 'auswahl'];

    public function handle(): int
    {
        $file = $this->argument('file');
        $dryRun = (bool) $this->option('dry-run');

        if (! file_exists($file)) {
            $this->error("Dosya bulunamadı: $file");
            return self::FAILURE;
        }

        $handle = fopen($file, 'r');
        if (! $handle) {
            $this->error("Dosya açılamadı: $file");
            return self::FAILURE;
        }

        // Header satırını oku ve doğrula
        $header = fgetcsv($handle);
        if (! $header || count(array_intersect(['program_slug', 'admission_mode'], $header)) < 2) {
            $this->error('Geçersiz header. Beklenen: program_slug,admission_mode');
            $this->line('Bulunan: ' . implode(',', $header ?: []));
            fclose($handle);
            return self::FAILURE;
        }

        $slugIdx = array_search('program_slug', $header);
        $modeIdx = array_search('admission_mode', $header);

        // Optional: nc_value, admission_summary
        $ncIdx       = array_search('nc_value', $header);
        $summaryIdx  = array_search('admission_summary', $header);

        $this->info("CSV dosyası: $file");
        $this->info("Mod: " . ($dryRun ? 'DRY-RUN' : 'LIVE'));
        $this->newLine();

        $stats = ['updated' => 0, 'not_found' => 0, 'invalid_mode' => 0, 'skipped' => 0];
        $row = 1;

        while (($line = fgetcsv($handle)) !== false) {
                $row++;

                $slug = trim($line[$slugIdx] ?? '');
                $mode = trim($line[$modeIdx] ?? '');
                $nc   = $ncIdx !== false ? trim($line[$ncIdx] ?? '') : null;
                $sum  = $summaryIdx !== false ? trim($line[$summaryIdx] ?? '') : null;

                if ($slug === '') {
                    $stats['skipped']++;
                    continue;
                }

                if ($mode !== '' && ! in_array($mode, self::ALLOWED, true)) {
                    $this->warn("Satır $row: geçersiz mode '$mode' (allowed: " . implode(', ', self::ALLOWED) . ")");
                    $stats['invalid_mode']++;
                    continue;
                }

                $program = Program::where('slug', $slug)->first();
                if (! $program) {
                    $stats['not_found']++;
                    continue;
                }

                $payload = ['admission_mode' => $mode ?: null];
                if ($nc !== null && $nc !== '') {
                    $payload['nc_value'] = (float) str_replace(',', '.', $nc);
                }
                if ($sum !== null && $sum !== '') {
                    $payload['admission_summary'] = $sum;
                }

                if (! $dryRun) {
                    $program->update($payload);
                }
                $stats['updated']++;
        }

        fclose($handle);

        $this->table(
            ['Updated', 'Not Found', 'Invalid Mode', 'Skipped'],
            [[$stats['updated'], $stats['not_found'], $stats['invalid_mode'], $stats['skipped']]]
        );

        if ($dryRun) {
            $this->warn('DRY-RUN — DB değişmedi. Gerçek çalıştırma için --dry-run\'ı kaldır.');
        }

        return self::SUCCESS;
    }
}
