<?php

namespace App\Services\FaqAtlas;

use App\Models\Faq;
use App\Models\FaqQualityAtlas;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Değişmez atlas veri setini (resources/data/faq-atlas/atlas-YYYY-MM-DD.json) faq_quality_atlas'a aktarır.
 *
 *  1. SHA-256 checksum (satır sonları LF'e normalleştirilmiş içerik — git CRLF dönüşümünden bağımsız) doğrulanır;
 *     bilinen anlık görüntülerin beklenen checksum'u KNOWN_CHECKSUMS'ta sabittir.
 *  2. JSON şeması doğrulanır. Checksum/şema hatası → RuntimeException, HİÇBİR ŞEY yazılmaz.
 *  3. TR SSS slug + locale=tr ile bulunur → translation_group_id → EN/DE kardeşleri. TR bulunamazsa satır
 *     resolved=false + unresolved_reason ile yazılır (tahmin yok). EN/DE yoksa bu geçerli bir durumdur (MISSING).
 *  4. Anahtar (audit_label, tr_slug). Var olan satır DEĞİŞTİRİLMEZ (anlık görüntü değişmezdir) → ikinci içe aktarım no-op.
 *  5. Tek transaction. SSS kayıtlarına asla yazılmaz (yalnız okunur).
 */
class FaqAtlasImporter
{
    public const KNOWN_CHECKSUMS = [
        'atlas-2026-09-28.json' => '0de1dc461cf650bf9c718dbe677939b257751e9759c2db9679e2e72d58af9577',
    ];

    private const REQUIRED = ['tr_slug', 'topic', 'risk_level', 'tr_quality', 'tr_status', 'en_status', 'de_status', 'parity_score', 'proposed_batch'];

    public static function checksum(string $raw): string
    {
        return hash('sha256', str_replace("\r\n", "\n", $raw));
    }

    /**
     * @return array{audit_label:string,total:int,inserted:int,existing:int,resolved:int,unresolved:array,notes:array,dry_run:bool}
     */
    public function import(string $path, bool $dryRun = false, ?string $expectedSha = null): array
    {
        if (! is_file($path)) {
            throw new RuntimeException("Atlas veri seti bulunamadı: ".basename($path));
        }
        $raw = (string) file_get_contents($path);
        $expected = $expectedSha ?? (self::KNOWN_CHECKSUMS[basename($path)] ?? null);
        if (! $expected) {
            throw new RuntimeException('Bilinmeyen atlas anlık görüntüsü ('.basename($path).'): beklenen SHA-256 verilmeli.');
        }
        $actual = self::checksum($raw);
        if (! hash_equals(strtolower($expected), $actual)) {
            throw new RuntimeException('Atlas veri seti checksum uyuşmuyor ('.basename($path)."): beklenen {$expected}, bulunan {$actual}. Anlık görüntü dosyaları değiştirilemez.");
        }
        $doc = json_decode($raw, true);
        if (! is_array($doc)) {
            throw new RuntimeException('Atlas veri seti geçerli JSON değil: '.json_last_error_msg());
        }
        $rows = $this->validate($doc);

        $label = $doc['audit_label'];
        $auditedAt = Carbon::parse($doc['audited_at']);
        $report = ['audit_label' => $label, 'total' => count($rows), 'inserted' => 0, 'existing' => 0, 'resolved' => 0,
            'unresolved' => [], 'notes' => [], 'dry_run' => $dryRun];

        $existing = FaqQualityAtlas::query()->where('audit_label', $label)->pluck('tr_slug')->flip();
        $trFaqs = Faq::query()->where('locale', 'tr')->whereIn('slug', array_column($rows, 'tr_slug'))
            ->get(['id', 'slug', 'translation_group_id'])->groupBy('slug');
        $groups = $trFaqs->flatten()->pluck('translation_group_id')->filter()->unique()->values();
        $siblings = Faq::query()->whereIn('translation_group_id', $groups)->whereIn('locale', ['en', 'de'])
            ->orderBy('id')->get(['id', 'slug', 'locale', 'translation_group_id'])
            ->groupBy(fn ($f) => $f->translation_group_id.'|'.$f->locale);

        $inserts = [];
        $now = now();
        foreach ($rows as $c) {
            if (isset($existing[$c['tr_slug']])) {
                $report['existing']++;
                continue;
            }
            $reason = [];
            $matches = $trFaqs->get($c['tr_slug']);
            $tr = $matches && $matches->count() === 1 ? $matches->first() : null;
            if (! $tr) {
                $reason[] = $matches ? "TR slug birden fazla kayıtla eşleşti ({$matches->count()})" : 'TR SSS bulunamadı (slug + locale=tr)';
            }
            $group = $tr?->translation_group_id;
            if ($tr && ! $group) {
                $reason[] = 'TR SSS çeviri grubuna bağlı değil';
            }
            $ids = ['en' => null, 'de' => null];
            foreach (['en', 'de'] as $l) {
                $sib = $group ? $siblings->get($group.'|'.$l)?->first() : null;
                $ids[$l] = $sib?->id;
                $want = $c[$l.'_slug'] ?? null;
                if ($sib && $want && $sib->slug !== $want) {
                    $report['notes'][] = "{$c['tr_slug']}: {$l} slug farklı (veri seti {$want}, canlı {$sib->slug})";
                }
                if (! $sib && $want && $c[$l.'_status'] !== 'MISSING') {
                    $report['notes'][] = "{$c['tr_slug']}: {$l} kardeşi canlıda yok (denetim durumu {$c[$l.'_status']})";
                }
            }
            $resolved = $tr !== null;
            $resolved ? $report['resolved']++ : $report['unresolved'][] = ['tr_slug' => $c['tr_slug'], 'reason' => implode('; ', $reason)];

            $inserts[] = [
                'audit_label' => $label, 'translation_group_id' => $group,
                'tr_slug' => $c['tr_slug'], 'en_slug' => $c['en_slug'] ?? null, 'de_slug' => $c['de_slug'] ?? null,
                'tr_question_at_audit' => isset($c['tr_question']) ? mb_substr((string) $c['tr_question'], 0, 500) : null,
                'tr_faq_id' => $tr?->id, 'en_faq_id' => $ids['en'], 'de_faq_id' => $ids['de'],
                'topic' => $c['topic'], 'risk_level' => $c['risk_level'], 'tr_quality' => $c['tr_quality'],
                'tr_status' => $c['tr_status'], 'en_status' => $c['en_status'], 'de_status' => $c['de_status'],
                'tr_issues' => json_encode(array_values($c['tr_issues'] ?? []), JSON_UNESCAPED_UNICODE),
                'authoritative_internal_source' => $c['authoritative_internal_source'] ?? null,
                'external_source_needed' => (bool) ($c['external_source_needed'] ?? false),
                'translation_strategy' => $c['translation_strategy'] ?? null,
                'parity_score' => (int) $c['parity_score'],
                'quality_ready_locales' => json_encode(array_values($c['quality_ready_locales'] ?? [])),
                'recommended_action' => isset($c['recommended_action']) ? mb_substr((string) $c['recommended_action'], 0, 500) : null,
                'proposed_batch' => $c['proposed_batch'],
                'priority_score' => isset($c['priority_score']) ? (int) $c['priority_score'] : null,
                'duplicate_of' => json_encode(array_values($c['duplicate_of'] ?? []), JSON_UNESCAPED_UNICODE),
                'junk' => (bool) ($c['junk'] ?? false), 'notes' => $c['notes'] ?? null,
                'chatbot_risk' => (bool) ($c['chatbot_risk'] ?? false),
                'stale_markers' => isset($c['stale_markers']) ? json_encode($c['stale_markers'], JSON_UNESCAPED_UNICODE) : null,
                'audited_at' => $auditedAt, 'resolved' => $resolved,
                'unresolved_reason' => $reason ? mb_substr(implode('; ', $reason), 0, 500) : null,
                'created_at' => $now, 'updated_at' => $now,
            ];
        }
        $report['inserted'] = count($inserts);
        if (! $dryRun && $inserts) {
            DB::transaction(function () use ($inserts) {
                foreach (array_chunk($inserts, 200) as $chunk) {
                    FaqQualityAtlas::query()->insert($chunk);
                }
            });
        }

        return $report;
    }

    /** @return array<int, array> */
    private function validate(array $doc): array
    {
        $err = [];
        if (($doc['schema_version'] ?? null) !== 1) {
            $err[] = 'schema_version 1 olmalı';
        }
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($doc['audit_label'] ?? ''))) {
            $err[] = 'audit_label YYYY-MM-DD olmalı';
        }
        try {
            Carbon::parse((string) ($doc['audited_at'] ?? ''));
        } catch (\Throwable) {
            $err[] = 'audited_at geçersiz';
        }
        $rows = $doc['clusters'] ?? null;
        if (! is_array($rows) || $rows === []) {
            $err[] = 'clusters boş ya da dizi değil';
            $rows = [];
        }
        $enums = [
            'risk_level' => FaqQualityAtlas::RISKS, 'tr_quality' => FaqQualityAtlas::QUALITIES,
            'tr_status' => FaqQualityAtlas::STATUSES, 'en_status' => FaqQualityAtlas::STATUSES, 'de_status' => FaqQualityAtlas::STATUSES,
            'proposed_batch' => FaqQualityAtlas::BATCHES,
        ];
        $seen = [];
        foreach ($rows as $i => $c) {
            if (! is_array($c)) {
                $err[] = "#{$i}: nesne değil";
                continue;
            }
            foreach (self::REQUIRED as $k) {
                if (! array_key_exists($k, $c) || $c[$k] === null || $c[$k] === '') {
                    $err[] = "#{$i}: {$k} eksik";
                }
            }
            foreach ($enums as $k => $allowed) {
                if (isset($c[$k]) && ! in_array($c[$k], $allowed, true)) {
                    $err[] = "#{$i}: {$k} geçersiz ({$c[$k]})";
                }
            }
            if (isset($c['translation_strategy']) && ! in_array($c['translation_strategy'], FaqQualityAtlas::STRATEGIES, true)) {
                $err[] = "#{$i}: translation_strategy geçersiz";
            }
            if (isset($c['parity_score']) && (! is_int($c['parity_score']) || $c['parity_score'] < 0 || $c['parity_score'] > 3)) {
                $err[] = "#{$i}: parity_score 0-3 olmalı";
            }
            foreach (['tr_issues', 'quality_ready_locales', 'duplicate_of'] as $k) {
                if (isset($c[$k]) && ! is_array($c[$k])) {
                    $err[] = "#{$i}: {$k} dizi olmalı";
                }
            }
            if (isset($c['tr_slug'])) {
                if (isset($seen[$c['tr_slug']])) {
                    $err[] = "#{$i}: tr_slug tekrar ediyor ({$c['tr_slug']})";
                }
                $seen[$c['tr_slug']] = true;
            }
        }
        if ($err) {
            throw new RuntimeException('Atlas veri seti şeması geçersiz, hiçbir şey yazılmadı: '.implode(' | ', array_slice($err, 0, 15)));
        }

        return $rows;
    }
}
