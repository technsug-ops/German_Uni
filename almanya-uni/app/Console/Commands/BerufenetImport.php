<?php

namespace App\Console\Commands;

use App\Models\Profession;
use App\Services\BerufenetService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('berufenet:import
    {--limit=0 : Maks kaç meslek import edilsin (0 = sınırsız)}
    {--list-only : Sadece liste\'den temel veri ekle, detay çağrısı yapma (hızlı POC için)}
    {--delay=300 : İstekler arası gecikme (ms)}
    {--skip-existing : Mevcut berufenet_id varsa atla}
')]
#[Description('BERUFENET API\'den 3.569 mesleği DB\'ye import eder.')]
class BerufenetImport extends Command
{
    public function handle(BerufenetService $svc): int
    {
        $limit       = (int) $this->option('limit');
        $listOnly    = (bool) $this->option('list-only');
        $delay       = max(0, (int) $this->option('delay'));
        $skipExisting= (bool) $this->option('skip-existing');

        $this->info('Mod: ' . ($listOnly ? 'LIST-ONLY' : 'FULL (with detail)') . ', gecikme: ' . $delay . 'ms');

        $stats = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors'  => 0,
        ];

        // Existing IDs (skip için)
        $existing = $skipExisting
            ? Profession::pluck('berufenet_id')->all()
            : [];
        $existingSet = array_flip($existing);

        $bar = $this->output->createProgressBar($limit > 0 ? $limit : 3600);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s% — %message%');
        $bar->setMessage('listing...');
        $bar->start();

        $processed = 0;

        foreach ($svc->streamAll(100) as $listItem) {
            $berufenetId = (int) ($listItem['id'] ?? 0);

            if (! $berufenetId) {
                $stats['errors']++;
                continue;
            }

            if ($skipExisting && isset($existingSet[$berufenetId])) {
                $stats['skipped']++;
                $bar->advance();
                continue;
            }

            $bar->setMessage('#' . $berufenetId . ' ' . mb_substr($listItem['kurzBezeichnungNeutral'] ?? '', 0, 35));

            try {
                if ($listOnly) {
                    // Sadece liste verisinden minimal kayıt
                    $name = $listItem['kurzBezeichnungNeutral'] ?? 'Unbenannt';
                    Profession::updateOrCreate(
                        ['berufenet_id' => $berufenetId],
                        [
                            'name_de'        => $name,
                            'short_name'     => $name,
                            'slug'           => Str::slug($name) . '-' . $berufenetId,
                            'cluster'        => isset($listItem['bkgr']['id']) ? (string) $listItem['bkgr']['id'] : null,
                            'last_synced_at' => now(),
                            'is_active'      => true,
                        ]
                    );
                    $stats['created']++;
                } else {
                    $detail = $svc->getDetail($berufenetId);
                    if (! $detail) {
                        $stats['errors']++;
                        continue;
                    }

                    $payload = $svc->transformDetail($detail);
                    $payload['slug'] = Str::slug($payload['name_de']) . '-' . $berufenetId;
                    $payload['last_synced_at'] = now();
                    $payload['is_active'] = true;

                    $record = Profession::updateOrCreate(
                        ['berufenet_id' => $berufenetId],
                        $payload
                    );

                    if ($record->wasRecentlyCreated) {
                        $stats['created']++;
                    } else {
                        $stats['updated']++;
                    }

                    if ($delay > 0) {
                        usleep($delay * 1000);
                    }
                }
            } catch (\Throwable $e) {
                $stats['errors']++;
                $this->newLine();
                $this->warn("Profession #{$berufenetId} fail: " . mb_substr($e->getMessage(), 0, 200));
            }

            $bar->advance();
            $processed++;

            if ($limit > 0 && $processed >= $limit) {
                break;
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Created', 'Updated', 'Skipped', 'Errors'],
            [[$stats['created'], $stats['updated'], $stats['skipped'], $stats['errors']]]
        );

        $this->info('Total in DB: ' . Profession::count());

        return self::SUCCESS;
    }
}
