<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentBrief extends Model
{
    protected $fillable = [
        'title', 'slug', 'audience', 'topic',
        'primary_keyword', 'secondary_keywords', 'pain_point',
        'source_questions', 'target_word_count', 'brand_tone',
        'status', 'author_id', 'notes',
    ];

    protected $casts = [
        'secondary_keywords' => 'array',
        'source_questions' => 'array',
    ];

    public const AUDIENCES = [
        'aday_ogrenci' => 'Aday Öğrenci (henüz başvurmamış)',
        'veli' => 'Veli',
        'mevcut_ogrenci' => 'Mevcut Öğrenci (Almanya\'da)',
        'phd_adayi' => 'PhD Adayı',
        'genel' => 'Genel',
    ];

    public const TONES = [
        'formal' => 'Resmi',
        'casual' => 'Arkadaşça',
        'instructive' => 'Öğretici',
        'inspirational' => 'İlham verici',
    ];

    public const STATUSES = [
        'draft' => 'Taslak',
        'in_progress' => 'Çalışılıyor',
        'ready' => 'Hazır',
        'published' => 'Yayında',
        'archived' => 'Arşiv',
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(ContentAsset::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function hasAsset(string $type): bool
    {
        return $this->assets()->where('asset_type', $type)->exists();
    }

    protected static function booted(): void
    {
        static::creating(function (self $brief) {
            if (blank($brief->slug) && filled($brief->title)) {
                $brief->slug = static::uniqueSlugFromTitle($brief->title);
            }
        });

        static::updating(function (self $brief) {
            if (blank($brief->slug) && filled($brief->title)) {
                $brief->slug = static::uniqueSlugFromTitle($brief->title, $brief->id);
            }
        });
    }

    public static function uniqueSlugFromTitle(string $title, ?int $ignoreId = null): string
    {
        $base = \Illuminate\Support\Str::slug($title);
        if (!$base) {
            $base = 'brief-' . substr(md5($title . microtime()), 0, 8);
        }
        $slug = mb_substr($base, 0, 200);
        $original = $slug;
        $i = 1;
        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }
}
