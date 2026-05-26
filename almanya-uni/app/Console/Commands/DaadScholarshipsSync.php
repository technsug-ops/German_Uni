<?php

namespace App\Console\Commands;

use App\Models\Scholarship;
use App\Models\ScholarshipDeadline;
use App\Models\ScholarshipIntention;
use App\Models\ScholarshipOrigin;
use App\Models\ScholarshipStatus;
use App\Models\ScholarshipSubject;
use App\Services\DaadScholarshipsClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DaadScholarshipsSync extends Command
{
    protected $signature = 'daad:scholarships:sync
        {--dry-run : Veriyi indir ve özet ver, DB\'ye yazma}
        {--no-scout : Scout/Meilisearch reindex adımını atla}';

    protected $description = 'DAAD scholarship database (166 burs) tek koşulu sync. Programlar (daad:import) için DEĞİL — burslar.';

    private int $created = 0;
    private int $updated = 0;
    private int $removed = 0;
    private int $deadlinesUpserted = 0;

    /** @var array<int>|null */
    private ?array $validOriginIds = null;
    /** @var array<int>|null */
    private ?array $validStatusIds = null;
    /** @var array<int>|null */
    private ?array $validIntentionIds = null;
    /** @var array<string>|null */
    private ?array $validSubjectCodes = null;

    public function handle(DaadScholarshipsClient $client): int
    {
        $dry = (bool) $this->option('dry-run');
        $skipScout = (bool) $this->option('no-scout');

        $this->info($dry ? '🔍 DRY-RUN — DB değişmez' : '▶ DAAD Scholarships Sync');
        $this->line('Kaynak: ' . DaadScholarshipsClient::BASE);
        $this->newLine();

        $this->info('1/6 Veri indiriliyor (6 dosya)…');
        $data = $client->fetchAll();

        foreach ($data as $key => $rows) {
            $this->line(sprintf('   %-14s %d kayıt', $key, count($rows)));
        }
        $this->newLine();

        if ($dry) {
            $this->showDryRunPreview($data);
            return self::SUCCESS;
        }

        // Scout reindex'i kapat — yazma sırasında her save'de queue'ya itme.
        if (method_exists(Scholarship::class, 'disableSearchSyncing')) {
            Scholarship::disableSearchSyncing();
        }

        try {
            DB::transaction(function () use ($data) {
                $this->info('2/6 Lookup tabloları upsert…');
                $this->upsertOrigins($data['origins'] ?? []);
                $this->upsertStatuses($data['statuses'] ?? []);
                $this->upsertSubjectGroups($data['subjectGroups'] ?? []);
                $this->upsertIntentions($data['intentions'] ?? []);

                // Pivot referansları için lookup whitelist'i — DAAD bazen -1 (any/all) gibi gerçek
                // lookup'ta olmayan ID'ler döner. Bunları sync'ten önce filtrelememiz gerekiyor.
                $this->validOriginIds    = ScholarshipOrigin::pluck('id')->map(fn ($i) => (int) $i)->all();
                $this->validStatusIds    = ScholarshipStatus::pluck('id')->map(fn ($i) => (int) $i)->all();
                $this->validIntentionIds = ScholarshipIntention::pluck('id')->map(fn ($i) => (int) $i)->all();
                $this->validSubjectCodes = ScholarshipSubject::pluck('code')->all();

                $this->info('3/6 Scholarship kayıtları upsert…');
                $seenSapObjids = $this->upsertScholarships($data['scholarships'] ?? []);

                $this->info('4/6 Soft-delete: bu sync\'te görünmeyen kayıtlar…');
                $this->markRemoved($seenSapObjids);

                $this->info('5/6 Deadlines upsert…');
                $this->upsertDeadlines($data['deadlines'] ?? []);
            });
        } finally {
            if (method_exists(Scholarship::class, 'enableSearchSyncing')) {
                Scholarship::enableSearchSyncing();
            }
        }

        if (!$skipScout) {
            $this->info('6/6 Scout reindex…');
            try {
                $this->call('scout:flush', ['model' => Scholarship::class]);
                $this->call('scout:import', ['model' => Scholarship::class]);
            } catch (\Throwable $e) {
                $this->warn('   Scout reindex atlandı: ' . $e->getMessage());
            }
        } else {
            $this->line('6/6 Scout reindex atlandı (--no-scout)');
        }

        $this->newLine();
        $this->info('═══ ÖZET ═══');
        $this->line("  Yeni:                {$this->created}");
        $this->line("  Güncellenen:         {$this->updated}");
        $this->line("  Kaldırılan (soft):   {$this->removed}");
        $this->line("  Deadline upsert:     {$this->deadlinesUpserted}");
        $this->line("  DB toplam aktif burs: " . Scholarship::query()->whereNull('removed_at')->count());

        return self::SUCCESS;
    }

    private function showDryRunPreview(array $data): void
    {
        $first = $data['scholarships'][0] ?? null;
        if ($first) {
            $this->line('İlk burs örneği:');
            $this->line('  sap_objid: ' . ($first['sapObjid'] ?? '?'));
            $this->line('  name_en:   ' . substr((string) ($first['nameEn'] ?? '?'), 0, 80));
            $this->line('  is_daad:   ' . ($first['isDaad'] ?? '?'));
            $this->line('  origin sayısı: ' . count($first['origin'] ?? []));
        }
    }

    private function upsertOrigins(array $rows): void
    {
        foreach (array_chunk($rows, 200) as $chunk) {
            $payload = array_map(fn ($r) => [
                'id'       => (int) $r['id'],
                'name_de'  => $r['nameDe'] ?? null,
                'name_en'  => $r['nameEn'] ?? null,
                'name_es'  => $r['nameEs'] ?? null,
                'sortname' => $r['sortname'] ?? ($r['nameEn'] ?? $r['nameDe'] ?? null),
            ], $chunk);
            ScholarshipOrigin::upsert($payload, ['id'], ['name_de', 'name_en', 'name_es', 'sortname']);
        }
    }

    private function upsertStatuses(array $rows): void
    {
        $payload = array_map(fn ($r) => [
            'id'         => (int) $r['id'],
            'name_de'    => $r['nameDe'] ?? null,
            'name_en'    => $r['nameEn'] ?? null,
            'name_es'    => $r['nameEs'] ?? null,
            'sortierung' => isset($r['sortierung']) ? (int) $r['sortierung'] : null,
        ], $rows);
        if ($payload) {
            ScholarshipStatus::upsert($payload, ['id'], ['name_de', 'name_en', 'name_es', 'sortierung']);
        }
    }

    private function upsertSubjectGroups(array $rows): void
    {
        $payload = array_map(fn ($r) => [
            'code'    => (string) ($r['code'] ?? $r['id'] ?? ''),
            'name_de' => $r['nameDe'] ?? null,
            'name_en' => $r['nameEn'] ?? null,
            'name_es' => $r['nameEs'] ?? null,
        ], $rows);
        $payload = array_values(array_filter($payload, fn ($r) => $r['code'] !== ''));
        if ($payload) {
            ScholarshipSubject::upsert($payload, ['code'], ['name_de', 'name_en', 'name_es']);
        }
    }

    private function upsertIntentions(array $rows): void
    {
        $payload = array_map(fn ($r) => [
            'id'      => (int) $r['id'],
            'name_de' => $r['nameDe'] ?? null,
            'name_en' => $r['nameEn'] ?? null,
        ], $rows);
        if ($payload) {
            ScholarshipIntention::upsert($payload, ['id'], ['name_de', 'name_en']);
        }
    }

    /**
     * @return array<int> seen sap_objids in this sync
     */
    private function upsertScholarships(array $rows): array
    {
        $now = now();
        $seen = [];

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $r) {
            $sapObjid = (int) ($r['sapObjid'] ?? 0);
            if ($sapObjid === 0) {
                $bar->advance();
                continue;
            }

            $name = $r['nameEn'] ?? $r['nameDe'] ?? ('daad-' . $sapObjid);
            $slug = $this->buildUniqueSlug($name, $sapObjid);

            $daadId = isset($r['id']) ? (int) $r['id'] : null;
            $detailUrl = $daadId
                ? 'https://www2.daad.de/deutschland/stipendium/datenbank/de/15371-finder/?detailid=' . $daadId
                : null;

            $payload = [
                'sap_objid'          => $sapObjid,
                'daad_id'            => $daadId,
                'sap_progid'         => isset($r['sapProgid']) ? (int) $r['sapProgid'] : null,
                'sap_target_system'  => $r['sapTargetSystem'] ?? null,
                'name_de'            => $r['nameDe'] ?? null,
                'name_en'            => $r['nameEn'] ?? null,
                'langname_de'        => $r['langnameDe'] ?? null,
                'langname_en'        => $r['langnameEn'] ?? null,
                'programmname_de'    => $r['programmnameDe'] ?? null,
                'programmname_en'    => $r['programmnameEn'] ?? null,
                'programmtyp_id'     => isset($r['programmtypId']) ? (int) $r['programmtypId'] : null,
                'slug'               => $slug,
                'introduction_json'  => $this->normalizePolymorphic($r['introduction'] ?? null),
                'q_de_json'          => $this->normalizePolymorphic($r['qDe'] ?? null),
                'q_en_json'          => $this->normalizePolymorphic($r['qEn'] ?? null),
                'is_daad'            => !empty($r['isDaad']),
                'is_move'            => !empty($r['isMove']),
                'sorting'            => isset($r['sorting']) ? (int) $r['sorting'] : null,
                'last_seen_at'       => $now,
                'removed_at'         => null,  // revive if previously soft-deleted
                'detail_url'         => $detailUrl,
            ];

            $existing = Scholarship::where('sap_objid', $sapObjid)->first();
            if ($existing) {
                // Slug çakışmasını engellemek için mevcut slug'ı koru
                $payload['slug'] = $existing->slug;
                $existing->update($payload);
                $this->updated++;
                $sch = $existing;
            } else {
                $sch = Scholarship::create($payload);
                $this->created++;
            }

            // M:M sync — pivot ID'lerini lookup whitelist'iyle filtrele (DAAD -1 vs.)
            $sch->origins()->sync(array_values(array_intersect($this->toIntList($r['origin'] ?? []), $this->validOriginIds ?? [])));
            $sch->statuses()->sync(array_values(array_intersect($this->toIntList($r['status'] ?? []), $this->validStatusIds ?? [])));
            $sch->subjects()->sync(array_values(array_intersect($this->toStringList($r['subjectGrps'] ?? []), $this->validSubjectCodes ?? [])));
            $sch->intentions()->sync(array_values(array_intersect($this->toIntList($r['intentions'] ?? []), $this->validIntentionIds ?? [])));

            $seen[] = $sapObjid;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return $seen;
    }

    private function markRemoved(array $seenSapObjids): void
    {
        if (empty($seenSapObjids)) return;

        $this->removed = Scholarship::query()
            ->whereNotIn('sap_objid', $seenSapObjids)
            ->whereNull('removed_at')
            ->update(['removed_at' => now()]);
    }

    private function upsertDeadlines(array $rows): void
    {
        $now = now();
        foreach (array_chunk($rows, 100) as $chunk) {
            $payload = [];
            foreach ($chunk as $r) {
                $sapObjid = (int) ($r['id'] ?? 0);
                if ($sapObjid === 0) continue;

                $general = $r['general'] ?? null;
                $generalDe = is_array($general) ? ($general['de'] ?? null) : (is_string($general) ? $general : null);
                $generalEn = is_array($general) ? ($general['en'] ?? null) : null;

                $payload[] = [
                    'sap_objid'      => $sapObjid,
                    'general_de'     => $generalDe,
                    'general_en'     => $generalEn,
                    'countries_json' => json_encode($r['countries'] ?? [], JSON_UNESCAPED_UNICODE),
                    'last_seen_at'   => $now,
                ];
            }
            if ($payload) {
                ScholarshipDeadline::upsert(
                    $payload,
                    ['sap_objid'],
                    ['general_de', 'general_en', 'countries_json', 'last_seen_at']
                );
                $this->deadlinesUpserted += count($payload);
            }
        }
    }

    /**
     * DAAD'in `introduction` / `qDe` / `qEn` field'ları kâh string kâh dict.
     * JSON column'a yazılacak şekilde normalize et — string ise {"_":string} sarmalama YAPMA,
     * string'i string olarak sakla (Eloquent JSON cast string'i de destekler).
     */
    private function normalizePolymorphic($v): array|string|null
    {
        if ($v === null || $v === '') return null;
        if (is_string($v)) return $v;
        if (is_array($v)) {
            $clean = array_filter($v, fn ($x) => is_string($x) && $x !== '');
            return $clean ?: null;
        }
        return null;
    }

    private function toIntList($v): array
    {
        if (!is_array($v)) return [];
        return array_values(array_unique(array_map('intval', array_filter($v, fn ($x) => is_numeric($x)))));
    }

    private function toStringList($v): array
    {
        if (!is_array($v)) return [];
        return array_values(array_unique(array_filter(array_map(fn ($x) => is_scalar($x) ? (string) $x : null, $v))));
    }

    private function buildUniqueSlug(string $name, int $sapObjid): string
    {
        // Deterministik: name + sap_objid → 191 char limiti (MySQL utf8mb4 index)
        $base = Str::slug($name);
        if ($base === '') $base = 'daad';
        $slug = substr($base . '-' . $sapObjid, 0, 191);

        // sap_objid match olan kayıt zaten preserve ediyor — burada nadir çakışma için fallback
        $i = 1;
        $candidate = $slug;
        while (
            Scholarship::query()
                ->where('slug', $candidate)
                ->where('sap_objid', '!=', $sapObjid)
                ->exists()
        ) {
            $candidate = substr($slug . '-' . $i++, 0, 191);
        }
        return $candidate;
    }
}
