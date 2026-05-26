<?php

namespace App\Models;

use App\Support\MarkdownRenderer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'faq_topic_id',
        'question',
        'slug',
        'answer_md',
        'answer_html',
        'intent',
        'answer_minutes',
        'has_answer',
        'is_featured',
        'view_count',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'has_answer' => 'boolean',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'view_count' => 'integer',
        'answer_minutes' => 'integer',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $faq) {
            if ($faq->isDirty('answer_md')) {
                $md = $faq->answer_md ?? '';
                if (trim($md) !== '') {
                    $faq->answer_html = app(MarkdownRenderer::class)->render($md);
                    $words = preg_match_all('/[\p{L}\p{N}]+/u', $md);
                    $faq->answer_minutes = max(1, (int) ceil($words / 220));
                    $faq->has_answer = true;
                } else {
                    $faq->answer_html = null;
                    $faq->answer_minutes = 0;
                    $faq->has_answer = false;
                }
            }
        });
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(FaqTopic::class, 'faq_topic_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where('locale', app()->getLocale());
    }

    public function scopeForLocale(Builder $query, ?string $locale = null): Builder
    {
        return $query->where('locale', $locale ?? app()->getLocale());
    }

    public function translations()
    {
        return $this->hasMany(self::class, 'translation_group_id', 'translation_group_id');
    }

    public function scopeAnswered(Builder $query): Builder
    {
        return $query->where('has_answer', true);
    }

    /**
     * Detect intent from the question text. Used at seed time to tag rows.
     */
    public static function detectIntent(string $question): string
    {
        $q = mb_strtolower($question);

        if (preg_match('/\bnas[ıi]l\b/u', $q)) return 'nasil';
        if (preg_match('/\bne\s*kadar|s[üü]re|kac\s|ka[çc]\s/u', $q)) return 'ne-kadar';
        if (preg_match('/\bne\s*zaman|deadline|tarih\b/u', $q)) return 'ne-zaman';
        if (preg_match('/\bhangi|kim\b/u', $q)) return 'hangi';
        if (preg_match('/\bvar\s*m[iı]\b|kimde\s*var|bilen\s*var/u', $q)) return 'var-mi';
        if (preg_match('/\bneden|ni[çc]in|niye\b/u', $q)) return 'neden';

        return 'bilgi';
    }
}
