<?php

namespace App\Services\FaqAtlas;

use App\Models\FaqQualityAtlas;
use Illuminate\Database\Eloquent\Builder;

/**
 * Atlas filtrelerinin TEK kaynağı. Filament tablosu, KPI/konu/batch widget'ları ve CSV dışa aktarımı aynı durumu
 * (Filament'ın `filters[...]` biçimi + arama metni) buradan uygular; üçü birbirinden ayrışamaz.
 *   çoklu seçim: ['risk' => ['values' => ['P0']]]   tekli: ['topic' => ['value' => 'Visa']]
 *   evet/hayır:  ['guide' => ['value' => '1'|'0'|true|false|null]]
 * Denetim anlık görüntüsü seçilmemişse en güncel audit_label kullanılır.
 */
class AtlasFilters
{
    public const PRESETS = [
        'p0' => 'P0 only',
        'translation_ready' => 'Translation Ready',
        'fix_before_translation' => 'Fix Before Translation',
        'research_required' => 'Research Required',
        'merge_candidates' => 'Merge Candidates',
        'en_missing' => 'EN Missing/Empty',
        'de_missing' => 'DE Missing/Empty',
        'broken_locale' => 'Broken Locale',
        'batch_a' => 'Batch A',
        'chatbot_risk' => 'Chatbot Risk',
        'review_needed' => 'Review Needed',
    ];

    /** Çoklu seçim filtreleri → sütun. */
    private const MULTI = [
        'risk' => 'risk_level',
        'quality' => 'tr_quality',
        'en_status' => 'en_status',
        'de_status' => 'de_status',
        'parity' => 'parity_score',
        'strategy' => 'translation_strategy',
        'batch' => 'proposed_batch',
    ];

    public static function audit(array $filters): ?string
    {
        $label = $filters['audit']['value'] ?? null;

        return is_string($label) && $label !== '' ? $label : FaqQualityAtlas::latestAuditLabel();
    }

    public static function apply(Builder $query, array $filters = [], ?string $search = null): Builder
    {
        $t = $query->getModel()->getTable();
        $query->where("{$t}.audit_label", self::audit($filters));

        foreach (self::MULTI as $key => $column) {
            $values = array_values(array_filter((array) ($filters[$key]['values'] ?? []), fn ($v) => $v !== null && $v !== ''));
            if ($values) {
                $query->whereIn("{$t}.{$column}", $values);
            }
        }
        if (($topic = $filters['topic']['value'] ?? null) !== null && $topic !== '') {
            $query->where("{$t}.topic", $topic);
        }
        self::ternary($filters, 'guide', fn ($yes) => $yes ? $query->whereNotNull("{$t}.authoritative_internal_source") : $query->whereNull("{$t}.authoritative_internal_source"));
        self::ternary($filters, 'research', fn ($yes) => $query->where("{$t}.external_source_needed", $yes));
        self::ternary($filters, 'resolved', fn ($yes) => $query->where("{$t}.resolved", $yes));
        self::ternary($filters, 'chatbot', fn ($yes) => $query->where("{$t}.chatbot_risk", $yes));
        self::ternary($filters, 'review', fn ($yes) => $query->whereRaw(($yes ? '' : 'NOT ').LiveStatus::reviewSql($t)));

        if (($preset = $filters['preset']['value'] ?? null) && isset(self::PRESETS[$preset])) {
            self::preset($query, $preset, $t);
        }
        if ($search !== null && trim($search) !== '') {
            self::search($query, $search, $t);
        }

        return $query;
    }

    private static function ternary(array $filters, string $key, \Closure $apply): void
    {
        $v = $filters[$key]['value'] ?? null;
        if ($v === null || $v === '') {
            return;
        }
        $apply(in_array($v, [true, 1, '1', 'true'], true));
    }

    public static function preset(Builder $query, string $preset, string $t = 'faq_quality_atlas'): Builder
    {
        $gaps = FaqQualityAtlas::GAP_STATUSES;

        return match ($preset) {
            'p0' => $query->where("{$t}.risk_level", 'P0'),
            'translation_ready' => $query->where("{$t}.tr_quality", 'A')->where("{$t}.junk", false)
                ->where(fn ($q) => $q->whereIn("{$t}.en_status", $gaps)->orWhereIn("{$t}.de_status", $gaps)),
            'fix_before_translation' => $query->where("{$t}.tr_quality", 'B'),
            'research_required' => $query->where(fn ($q) => $q->where("{$t}.tr_quality", 'D')->orWhere("{$t}.external_source_needed", true)),
            'merge_candidates' => $query->where(fn ($q) => $q->where("{$t}.tr_quality", 'C')
                ->orWhereRaw("JSON_LENGTH(COALESCE({$t}.duplicate_of, JSON_ARRAY())) > 0")),
            'en_missing' => $query->whereIn("{$t}.en_status", ['MISSING', 'EMPTY']),
            'de_missing' => $query->whereIn("{$t}.de_status", ['MISSING', 'EMPTY']),
            'broken_locale' => $query->where(fn ($q) => $q->where("{$t}.en_status", 'BROKEN')->orWhere("{$t}.de_status", 'BROKEN')),
            'batch_a' => $query->where("{$t}.proposed_batch", 'A'),
            'chatbot_risk' => $query->where("{$t}.chatbot_risk", true),
            'review_needed' => $query->whereRaw(LiveStatus::reviewSql($t)),
            default => $query,
        };
    }

    /** Arama: soru (canlı + denetimdeki), slug ya da yapıştırılmış URL (son yol parçası slug olarak aranır). */
    public static function search(Builder $query, string $search, string $t = 'faq_quality_atlas'): Builder
    {
        $s = trim($search);
        $slug = str_contains($s, '/') ? basename(parse_url($s, PHP_URL_PATH) ?: $s) : $s;
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $s).'%';
        $slugLike = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $slug).'%';

        return $query->where(function ($q) use ($t, $like, $slugLike) {
            $q->where("{$t}.tr_slug", 'like', $slugLike)
                ->orWhere("{$t}.en_slug", 'like', $slugLike)
                ->orWhere("{$t}.de_slug", 'like', $slugLike)
                ->orWhere("{$t}.tr_question_at_audit", 'like', $like)
                ->orWhereExists(fn ($e) => $e->selectRaw('1')->from('faqs as sf')
                    ->whereRaw('sf.translation_group_id = '.LiveStatus::groupSql($t))
                    ->where(fn ($w) => $w->where('sf.question', 'like', $like)->orWhere('sf.slug', 'like', $slugLike)));
        });
    }

    /** CSV/URL için: yalnız dolu filtre değerleri. */
    public static function compact(array $filters): array
    {
        $out = [];
        foreach ($filters as $key => $state) {
            if (! is_array($state)) {
                continue;
            }
            $vals = array_filter($state, fn ($v) => $v !== null && $v !== '' && $v !== []);
            if ($vals) {
                $out[$key] = $vals;
            }
        }

        return $out;
    }
}
