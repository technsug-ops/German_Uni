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
     * Localized label for the intent badge. The `intent` column stores a
     * Turkish-derived slug (seed-time classification); the badge must never
     * leak that slug — it is rendered through __() per active locale.
     * 'bilgi' (generic) returns null → no badge shown.
     */
    public function intentLabel(): ?string
    {
        return match ($this->intent) {
            'nasil'    => __('How-to'),
            'ne-kadar' => __('How much'),
            'ne-zaman' => __('When'),
            'hangi'    => __('Which'),
            'var-mi'   => __('Recommendations'),
            'neden'    => __('Why'),
            'community' => __('Community'),
            default    => null,
        };
    }

    /**
     * Türkçe fonksiyon-kelimeleri — bir metnin "hâlâ Türkçe" olduğunun GÜVENİLİR
     * sinyali. Tek diakritik (ş/ğ/ı) yetmez: EN/DE metinler meşru Türkçe özel ad
     * içerebilir (TEV = Türk Eğitim Vakfı, İzmir, Doğukan...). Bu kelimeler ise
     * yalnız Türkçe cümlede geçer, özel adda değil.
     */
    public static function looksTurkish(?string $text): bool
    {
        if (! $text) return false;
        // Slug/URL gürültüsünü at: iç bağlantı URL'leri TR slug içerir
        // (ör. ...goethe-online-kursu-nasil-...) → prose İngilizce olsa da eşleşir.
        $t = preg_replace('/\]\([^)]*\)/u', '] ', $text);            // markdown link hedefi (url)
        $t = preg_replace('/https?:\/\/\S+/u', ' ', (string) $t);     // çıplak url
        $t = preg_replace('/`[^`]*`/u', ' ', (string) $t);            // kod span
        $t = preg_replace('/[a-z0-9]+(?:-[a-z0-9]+)+/iu', ' ', (string) $t); // hyphen-slug
        return (bool) preg_match('/(için|nedir|nas[ıi]l|nelerdir|gerekli|gerekir|de[ğg]il|yap[ıi]l|al[ıi]n[ıi]r|edilir|kullan[ıi]l|sa[ğg]lar|m[ıi]d[ıi]r|şunlard[ıi]r|kadard[ıi]r|vard[ıi]r|yoktur|çünkü|ayr[ıi]ca|olarak|öğrenci|başvuru|gerekiyor|şeklinde)/u', (string) $t);
    }

    /** Satır bozuk mu: cevap soruya kaynamış (>200) ya da hâlâ Türkçe prose. */
    public function contentIsBroken(): bool
    {
        if (mb_strlen((string) $this->question) > 200) return true;
        return self::looksTurkish($this->question) || self::looksTurkish($this->answer_md);
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
