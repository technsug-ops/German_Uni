<?php

namespace App\Models;

use App\Services\FaqAtlas\LiveStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * SSS Kalite Atlası — küme başına denetim anlık görüntüsü (değişmez sınıflandırma).
 * Canlı SSS'ler translation_group_id + locale ile çözülür; saklanan *_faq_id'ler yalnız teşhis içindir.
 */
class FaqQualityAtlas extends Model
{
    protected $table = 'faq_quality_atlas';

    protected $guarded = ['id'];

    public const LOCALES = ['tr', 'en', 'de'];
    public const RISKS = ['P0', 'P1', 'P2'];
    public const QUALITIES = ['A', 'B', 'C', 'D'];
    public const STATUSES = ['COMPLETE', 'EMPTY', 'PARTIAL', 'MISSING', 'BROKEN', 'STALE'];
    public const GAP_STATUSES = ['EMPTY', 'PARTIAL', 'MISSING', 'BROKEN', 'STALE'];
    public const STRATEGIES = ['TRANSLATE_DIRECTLY', 'LOCALIZE', 'REWRITE_FROM_VERIFIED_FACTS', 'MERGE', 'RESEARCH_FIRST'];
    public const BATCHES = ['A', 'B', 'C', 'D', 'E', 'OUTSIDE_BATCH'];

    protected $casts = [
        'tr_issues' => 'array',
        'quality_ready_locales' => 'array',
        'duplicate_of' => 'array',
        'stale_markers' => 'array',
        'external_source_needed' => 'boolean',
        'junk' => 'boolean',
        'chatbot_risk' => 'boolean',
        'resolved' => 'boolean',
        'parity_score' => 'integer',
        'priority_score' => 'integer',
        'audited_at' => 'datetime',
    ];

    private function localeFaq(string $locale): HasOne
    {
        return $this->hasOne(Faq::class, 'translation_group_id', 'translation_group_id')
            ->where('faqs.locale', $locale)
            ->orderBy('faqs.id');
    }

    public function tr(): HasOne
    {
        return $this->localeFaq('tr');
    }

    public function en(): HasOne
    {
        return $this->localeFaq('en');
    }

    public function de(): HasOne
    {
        return $this->localeFaq('de');
    }

    /** Liste için: canlı durumlar + review_needed SQL'de hesaplanır; SSS gövdeleri yüklenmez. */
    public function scopeWithLive(Builder $query): Builder
    {
        $t = $this->getTable();
        if (empty($query->getQuery()->columns)) {
            $query->select("{$t}.*");
        }
        foreach (self::LOCALES as $l) {
            $query->selectRaw(LiveStatus::sql($l, $t)." AS {$l}_live");
        }

        return $query->selectRaw('('.LiveStatus::reviewSql($t).') AS review_needed');
    }

    public function scopeReviewNeeded(Builder $query, bool $needed = true): Builder
    {
        $sql = LiveStatus::reviewSql($this->getTable());

        return $needed ? $query->whereRaw($sql) : $query->whereRaw("NOT {$sql}");
    }

    public static function latestAuditLabel(): ?string
    {
        return static::query()->max('audit_label');
    }

    /** Canlı sayfa yolu (/tr/faq/{topic}/{slug}) — topic ilişkisi yüklü olmalı. */
    public static function faqPath(?Faq $faq): ?string
    {
        if (! $faq || ! $faq->topic) {
            return null;
        }

        return '/'.$faq->locale.'/faq/'.$faq->topic->slug.'/'.$faq->slug;
    }
}
