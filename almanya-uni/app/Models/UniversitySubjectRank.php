<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Bir üniversitenin belirli bir ALAN'daki sıralama konumu (CHE / GRAS / QS / THE).
 * Genel dünya sırasından farkı: konu bazlı olması ve CHE üzerinden Fachhochschule'leri
 * de kapsayabilmesi. Bkz. migration açıklaması ve doc/CHE-DATENANFRAGE.md.
 */
class UniversitySubjectRank extends Model
{
    use HasFactory;

    public const SOURCE_CHE = 'che';
    public const SOURCE_GRAS = 'gras';
    public const SOURCE_QS = 'qs';
    public const SOURCE_THE = 'the';

    public const TIER_TOP = 'top';
    public const TIER_MID = 'mid';
    public const TIER_BOTTOM = 'bottom';

    protected $fillable = [
        'university_id', 'field_of_study_id', 'source', 'source_subject', 'scope',
        'rank_low', 'rank_high', 'tier', 'year', 'source_url',
    ];

    protected $casts = [
        'rank_low' => 'integer',
        'rank_high' => 'integer',
        'year' => 'integer',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(FieldOfStudy::class, 'field_of_study_id');
    }

    /**
     * Sıralamada kullanılacak tekil değer. Bantlı sıralarda ("101-150") orta nokta alınır;
     * alt ucu almak dar bantları haksız biçimde öne çıkarırdı.
     */
    public function effectiveRank(): ?int
    {
        if ($this->rank_low === null) {
            return null;
        }

        return $this->rank_high === null
            ? $this->rank_low
            : (int) round(($this->rank_low + $this->rank_high) / 2);
    }

    /**
     * 0..1 arası kalite değeri — puanlamaya bu girer.
     * CHE grup verirse gruptan, sayısal sıra varsa logaritmik ölçekten türetilir
     * (RankingService'teki dünya sırası ölçeğiyle aynı mantık: baştaki farklar ağır basar).
     */
    public function qualityScore(): float
    {
        if ($this->tier !== null) {
            return match ($this->tier) {
                self::TIER_TOP => 0.90,
                self::TIER_MID => 0.55,
                self::TIER_BOTTOM => 0.20,
                default => 0.0,
            };
        }

        $rank = $this->effectiveRank();
        if ($rank === null || $rank < 1) {
            return 0.0;
        }

        // Konu sıralamaları dünya sıralamalarından kısadır (~800 kurum) → tavan 800.
        return max(0.0, 1 - (log($rank) / log(800)));
    }
}
