<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Models\ProgramVerification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Resmî kaynak doğrulama dosyasını (ör. database/data/program-verification/pilot-2026-10-02.json) uygular.
 *
 *  - Program, kaynak + dış kimlik + kimlik alanlarıyla tam olarak eşleşmeli (bkz. resolve()); eşleşmezse atlanır.
 *  - Program düzeyi doğrulama alanları (official_program_url, application_method, application_url, uni_assist_required,
 *    vpd_required) YALNIZ boşsa doldurulur; dolu ve farklıysa dokunulmaz, raporlanır. Mevcut program verisi
 *    (ad, dil, tarih, ücret…) bu komutla ASLA değiştirilmez — düzeltme önerileri ayrı onaya tabidir.
 *  - Doğrulama kayıtları (alan + aday grubu + dönem) anahtarıyla upsert edilir; aynı içerik tekrar gelirse değişiklik yok.
 *  - Deploy'da kendiliğinden çalışmaz; her veri dosyası ayrı bir data-migration ile bilinçli olarak yüklenir
 *    (pilot: 2026_10_09_000100_import_program_verification_pilot).
 */
class ProgramsVerificationImport extends Command
{
    protected $signature = 'programs:verification-import {file : JSON dosya yolu (proje köküne göre)} {--dry-run : Yazmadan raporla}';

    protected $description = 'Program doğrulama kayıtlarını (resmî kaynak) dosyadan idempotent olarak içe aktarır';

    private const PROGRAM_FIELDS = ['official_program_url', 'application_method', 'application_url', 'uni_assist_required', 'vpd_required'];

    public function handle(): int
    {
        $path = base_path($this->argument('file'));
        if (! is_file($path)) {
            $this->error("Dosya yok: {$path}");

            return self::FAILURE;
        }
        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $dry = (bool) $this->option('dry-run');
        $stats = ['programs' => 0, 'skipped' => 0, 'fields_set' => 0, 'fields_kept' => 0, 'created' => 0, 'updated' => 0, 'unchanged' => 0];

        $run = function () use ($data, $dry, &$stats) {
            foreach ($data['programs'] as $row) {
                $p = $this->resolve($row);
                if (! $p) {
                    $stats['skipped']++;
                    continue;
                }
                $stats['programs']++;

                $set = [];
                foreach (array_intersect_key($row['set'] ?? [], array_flip(self::PROGRAM_FIELDS)) as $col => $val) {
                    if (blank($p->getAttribute($col))) {
                        $set[$col] = $val;
                    } elseif ((string) $p->getAttribute($col) !== (string) $val) {
                        $this->line("  korundu: #{$p->id} {$col} mevcut değer farklı");
                        $stats['fields_kept']++;
                    }
                }
                if ($set) {
                    $stats['fields_set'] += count($set);
                    if (! $dry) {
                        $p->forceFill($set)->save();
                    }
                }

                foreach ($row['verifications'] as $v) {
                    $key = ['program_id' => $p->id, 'field' => $v['field'], 'applicant_group' => (string) ($v['applicant_group'] ?? ''), 'term' => (string) ($v['term'] ?? '')];
                    $attrs = [
                        'status' => $v['status'], 'source_value' => $v['source_value'] ?? null, 'source_url' => $v['source_url'] ?? null,
                        'evidence' => $v['evidence'] ?? null, 'checked_at' => $v['checked_at'] ?? null, 'review_reason' => $v['review_reason'] ?? null,
                        'verified_via' => 'pilot',
                    ];
                    $m = ProgramVerification::firstOrNew($key);
                    // CONFLICT'e modelce çekilmiş kayıt tekrar "verified" diye gelirse durum aynı kabul edilir (gereksiz yazım yok)
                    if ($m->exists && $m->status === ProgramVerification::CONFLICT && $attrs['status'] === ProgramVerification::VERIFIED) {
                        $attrs['status'] = ProgramVerification::CONFLICT;
                        $attrs['review_reason'] = $m->review_reason;
                    }
                    $m->fill($attrs);
                    if ($m->exists && ! $m->isDirty()) {
                        $stats['unchanged']++;
                        continue;
                    }
                    $m->exists ? $stats['updated']++ : $stats['created']++;
                    if (! $dry) {
                        $m->save();
                    }
                }
            }
        };

        $dry ? $run() : DB::transaction($run);

        $this->table(array_keys($stats), [array_values($stats)]);
        $this->info($dry ? 'DRY-RUN — hiçbir şey yazılmadı.' : 'Uygulandı.');

        return self::SUCCESS;
    }

    /**
     * Güvenli eşleştirme — yerel sayısal ID'ye ASLA güvenilmez (ortamlar arasında farklıdır):
     * import kaynağı + dış kimlik (partner_id | source_id) TAM OLARAK bir aktif programa eşleşmeli VE name_de + derece +
     * üniversite adı birebir aynı olmalı. Eşleşme yoksa/belirsizse kayda yazılmaz.
     */
    private function resolve(array $row): ?Program
    {
        $m = $row['match'] ?? null;
        $label = ($m['source'] ?? '?').':'.($m['external_id'] ?? '?').' '.($row['slug'] ?? '');
        if (! $m || ! in_array($m['external_id_field'] ?? '', ['partner_id', 'source_id'], true) || blank($m['external_id'] ?? null) || blank($m['source'] ?? null)) {
            $this->warn("Atlandı (eşleştirme anahtarı yok): {$label}");

            return null;
        }
        $hits = Program::with('university:id,name_de')->where('source', $m['source'])->where($m['external_id_field'], $m['external_id'])->get();
        if ($hits->count() !== 1) {
            $this->warn("Atlandı ({$hits->count()} eşleşme, tam 1 gerekli): {$label}");

            return null;
        }
        $p = $hits->first();
        foreach (['name_de' => $p->name_de, 'degree' => $p->degree, 'university' => $p->university?->name_de] as $k => $actual) {
            if ((string) $actual !== (string) ($m[$k] ?? '')) {
                $this->warn("Atlandı (kimlik farklı: {$k} '{$actual}' ≠ '".($m[$k] ?? '')."'): {$label}");

                return null;
            }
        }

        return $p;
    }
}
