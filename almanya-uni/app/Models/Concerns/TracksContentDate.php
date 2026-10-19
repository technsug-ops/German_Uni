<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Schema;

/**
 * İçerik güncelleme tarihi (docs/SEO_CONTENT_UPDATED_AT_DESIGN.md, Faz 1).
 *
 * updated_at her kayıt değişikliğinde ilerler (slug, görsel, HTML yeniden üretimi…); SEO'nun "son içerik
 * değişikliği" için güvenilir değil. Bu trait yalnız editoryal içerik alanları gerçekten değiştiğinde
 * content_updated_at'i ilerletir:
 *  - Eski ve yeni içerik alanlarının özeti karşılaştırılır; aynı metni yeniden kaydetmek tarihi oynatmaz.
 *  - Yeni kayıt (oluşturma) bir içerik değişikliğidir.
 *  - Bakım işleri (bağlantı onarımı vb.) TracksContentDate::maintenance() içinde kaydedilirse tarih ilerlemez.
 *  - Model olayı çalışmayan doğrudan sorgular (DB::table, Builder::update, toBase) tarihe dokunmaz.
 *
 * Kullanan model `contentDateFields()` tanımlar. Sütunlar migration 2026_10_18_000100 ile eklenir.
 */
trait TracksContentDate
{
    private static bool $contentDateSuspended = false;

    /** @return list<string> */
    abstract public static function contentDateFields(): array;

    public static function bootTracksContentDate(): void
    {
        static::saving(fn ($model) => $model->recordContentDate());
    }

    public function initializeTracksContentDate(): void
    {
        $this->mergeCasts(['content_updated_at' => 'datetime', 'content_updated_floor' => 'datetime']);
    }

    /** Bakım işini içerik tarihini ilerletmeden çalıştırır (ör. iç bağlantı onarımı). */
    public static function maintenance(callable $callback): mixed
    {
        $previous = self::$contentDateSuspended;
        self::$contentDateSuspended = true;
        try {
            return $callback();
        } finally {
            self::$contentDateSuspended = $previous;
        }
    }

    /** İçerik alanlarının sha256 özeti (satır sonu ve kenar boşluğu farkı içerik değişikliği sayılmaz). */
    public static function contentHashOf(array $values): string
    {
        $norm = [];
        foreach (static::contentDateFields() as $field) {
            $norm[$field] = trim(str_replace("\r\n", "\n", (string) ($values[$field] ?? '')));
        }

        return hash('sha256', json_encode($norm, JSON_UNESCAPED_UNICODE));
    }

    protected function recordContentDate(): void
    {
        // "Sütun var" bilgisi uygulama container'ında tutulur (statik değil): testlerde her test yeni bir
        // uygulama ve gerekirse yeniden kurulan bir DB kullanır; eski veri migration'ları sütun eklenmeden
        // önce model kaydedebilir. Yalnız "var" sonucu saklanır; "yok" ise her kayıtta yeniden sorulur.
        $key = 'tracks-content-date.columns.' . $this->getConnectionName() . '.' . $this->getTable();
        if (! app()->bound($key)) {
            if (! Schema::connection($this->getConnectionName())->hasColumn($this->getTable(), 'content_updated_at')) {
                return; // migration henüz çalışmadı (deploy sırası) — sessizce atla
            }
            app()->instance($key, true);
        }

        $new = static::contentHashOf($this->getAttributes());
        $changed = ! $this->exists || static::contentHashOf($this->getOriginal()) !== $new;

        if ($changed && ! self::$contentDateSuspended) {
            $this->content_updated_at = $this->freshTimestamp();
            $this->content_date_source = 'recorded';
        }
        if ($this->content_hash !== $new) {
            $this->content_hash = $new;
        }
    }
}
