<?php

namespace App\Models\Concerns;

/**
 * Çok dilli DB içeriği için trait.
 * $model->localized('description') current locale'a göre doğru kolonu döndürür.
 * Fallback zinciri: config/locale.php → content_fallback
 */
trait LocalizableContent
{
    /**
     * @param bool $strict  Serbest-metin (prose) alanları için true ver: aktif dil TR
     *                      değilse fallback zincirinden 'tr' ÇIKARILIR → EN/DE sayfada
     *                      Türkçe sızmaz (çeviri yoksa null döner, blade gizler).
     *                      name/başlık gibi kimlik alanlarında false bırak (TR'ye
     *                      düşmek boş bırakmaktan iyidir).
     */
    public function localized(string $field, ?string $locale = null, bool $strict = false): ?string
    {
        $locale ??= app()->getLocale();
        $chain = config("locale.content_fallback.$locale", [$locale, 'en', 'de', 'tr']);

        // Aktif dilde sessiz TR-fallback yasağı (i18n konsolidasyon #3).
        if ($strict && $locale !== 'tr') {
            $chain = array_values(array_filter($chain, fn ($l) => $l !== 'tr'));
        }

        foreach ($chain as $loc) {
            $column = "{$field}_{$loc}";

            if (! array_key_exists($column, $this->getAttributes())
                && ! in_array($column, $this->getFillable(), true)) {
                continue;
            }

            $value = $this->getAttribute($column);
            if (! empty($value)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * $model->description → locale-aware (otomatik fallback chain).
     * View'lar artık direkt `$program->description` çağırabilir, `description_tr` değil.
     */
    public function getDescriptionAttribute(): ?string
    {
        // Serbest-metin → strict: EN/DE sayfada Türkçe açıklama sızmaz (gizlenir).
        return $this->localized('description', strict: true);
    }

    /**
     * $model->name → same fallback chain on name_{tr,en,de}.
     * Polymorphic relations (Favorite.favoriteable) can call $item->name
     * uniformly across Program, Profession, City, FieldOfStudy, etc.
     */
    public function getNameAttribute(): ?string
    {
        // Only override when explicit name_{locale} columns exist on the model.
        // Otherwise return the underlying raw 'name' attribute (if any).
        $localized = $this->localized('name');
        return $localized !== null ? $localized : ($this->attributes['name'] ?? null);
    }
}
