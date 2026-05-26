<?php

namespace App\Console\Commands;

use App\Services\PartnerApiClient;
use App\Services\PartnerImporter;
use App\Models\University;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PartnerSync extends Command
{
    protected $signature = 'partner:sync
        {--only=both : universities | programs | both | states | study-fields | all}
        {--full : Delta yerine tam sync (updated_since gönderme)}
        {--since= : ISO 8601 timestamp (örn: 2026-05-01T00:00:00Z), boşsa son sync zamanı kullanılır}
        {--dry-run : DB\'ye yazmadan say}';

    protected $description = 'Partner REST API\'sinden incremental (delta) veya tam sync.';

    private const LAST_SYNC_CACHE_KEY = 'partner.api.last_sync_at';

    public function handle(PartnerApiClient $api, PartnerImporter $importer): int
    {
        if (! $api->isConfigured()) {
            $this->error('Partner API yapılandırılmamış.');
            $this->line('  .env\'e şunları ekle:');
            $this->line('    PARTNER_API_BASE_URL=https://api.partner.com/v1');
            $this->line('    PARTNER_API_KEY=sk-...');
            $this->line('  Sonra: php artisan config:clear');
            return self::FAILURE;
        }

        $only   = $this->option('only');
        $full   = (bool) $this->option('full');
        $dryRun = (bool) $this->option('dry-run');
        $since  = $this->option('since');

        if (! $full && ! $since) {
            $since = Cache::get(self::LAST_SYNC_CACHE_KEY);
        }

        $this->info('Partner sync başlıyor');
        $this->line('  Mod: ' . ($full ? 'FULL' : ($since ? "DELTA (since {$since})" : 'FULL (ilk çalışma)')));
        $this->line('  Sadece: ' . $only);
        if ($dryRun) $this->warn('  DRY-RUN: DB değişmeyecek');
        $this->newLine();

        $startedAt = Carbon::now()->toIso8601String();
        $stats = [
            'universities' => ['fetched' => 0, 'imported' => 0, 'updated' => 0, 'errors' => 0],
            'programs'     => ['fetched' => 0, 'imported' => 0, 'updated' => 0, 'errors' => 0],
        ];

        try {
            if (in_array($only, ['universities', 'both', 'all'], true)) {
                $stats['universities'] = $this->syncUniversities($api, $importer, $full ? null : $since, $dryRun);
            }

            if (in_array($only, ['programs', 'both', 'all'], true)) {
                $stats['programs'] = $this->syncPrograms($api, $importer, $full ? null : $since, $dryRun);
            }

            // states + study-fields opsiyonel (snapshot v1'de küçük dosyalar, nadiren değişir)
            // Bunlar için ayrı import logic yazılabilir, şimdilik atlandı

        } catch (\Throwable $e) {
            $this->error('Sync hatası: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('📊 Sonuç');
        foreach ($stats as $type => $s) {
            if ($s['fetched'] === 0) continue;
            $this->line(sprintf(
                '  %s: %d fetched · %d new · %d updated · %d errors',
                ucfirst($type), $s['fetched'], $s['imported'], $s['updated'], $s['errors']
            ));
        }

        if (! $dryRun) {
            Cache::forever(self::LAST_SYNC_CACHE_KEY, $startedAt);
            $this->info("Son sync zamanı kaydedildi: {$startedAt}");
        }

        return self::SUCCESS;
    }

    private function syncUniversities(PartnerApiClient $api, PartnerImporter $importer, ?string $since, bool $dryRun): array
    {
        $stats = ['fetched' => 0, 'imported' => 0, 'updated' => 0, 'errors' => 0];

        $this->info('▸ Üniler senkronize ediliyor...');

        foreach ($api->fetchUniversities($since) as $page) {
            foreach ($page as $row) {
                $stats['fetched']++;
                if ($dryRun) continue;

                try {
                    [$created, $updated] = $this->upsertUniversity($row);
                    if ($created) $stats['imported']++;
                    if ($updated) $stats['updated']++;
                } catch (\Throwable $e) {
                    $stats['errors']++;
                    $this->warn("  Üni hata (partner_id={$row['id']}): " . $e->getMessage());
                }
            }
            $this->line("  Toplam fetched: {$stats['fetched']}");
        }

        return $stats;
    }

    private function syncPrograms(PartnerApiClient $api, PartnerImporter $importer, ?string $since, bool $dryRun): array
    {
        $stats = ['fetched' => 0, 'imported' => 0, 'updated' => 0, 'errors' => 0];

        $this->info('▸ Programlar senkronize ediliyor...');

        foreach ($api->fetchPrograms($since) as $page) {
            foreach ($page as $row) {
                $stats['fetched']++;
                if ($dryRun) continue;

                try {
                    $uniId = University::where('partner_id', $row['university_id'])->value('id');
                    if (! $uniId) {
                        $stats['errors']++;
                        continue;
                    }

                    $existed = Program::where('partner_id', $row['id'])->exists();
                    Program::withoutSyncingToSearch(function () use ($importer, $row, $uniId, &$stats) {
                        $statBucket = ['imported' => 0, 'updated' => 0, 'skipped_no_uni' => 0, 'errors' => 0];
                        // PartnerImporter'ın private upsertProgram'a doğrudan erişimimiz yok,
                        // bu yüzden public wrapper kullanırız (importer'a eklenecek)
                        $importer->upsertProgramFromApi($row, $uniId, $statBucket);
                        $stats['imported'] += $statBucket['imported'];
                        $stats['updated']  += $statBucket['updated'];
                        $stats['errors']   += $statBucket['errors'];
                    });
                } catch (\Throwable $e) {
                    $stats['errors']++;
                    $this->warn("  Program hata (partner_id={$row['id']}): " . $e->getMessage());
                }
            }
            $this->line("  Toplam fetched: {$stats['fetched']}");
        }

        return $stats;
    }

    private function upsertUniversity(array $row): array
    {
        $existing = University::where('partner_id', $row['id'])->first();
        $created = false;
        $updated = false;

        $attrs = [
            'partner_id' => $row['id'],
            'name_de'    => $row['name'] ?? $row['name_de'] ?? null,
            'last_synced_at' => now(),
        ];
        if (! empty($row['location'])) $attrs['name_de'] ??= $row['location'];

        // Sadece dolu alanları yaz, mevcut Wikidata/HRK verisini ezme
        $attrs = array_filter($attrs, fn ($v) => $v !== null && $v !== '');

        if ($existing) {
            $existing->fill($attrs);
            if ($existing->isDirty()) {
                $existing->saveQuietly();
                $updated = true;
            }
        } else {
            University::create(array_merge([
                'slug'        => \Illuminate\Support\Str::slug($attrs['name_de'] ?? 'partner-' . $row['id']) . '-partner-' . substr($row['id'], 0, 8),
                'name_tr'     => $attrs['name_de'] ?? '',
                'data_source' => 'partner',
                'is_active'   => true,
            ], $attrs));
            $created = true;
        }

        return [$created, $updated];
    }
}
