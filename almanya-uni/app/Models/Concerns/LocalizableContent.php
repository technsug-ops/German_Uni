<?php

namespace App\Models\Concerns;

/**
 * Çok dilli DB içeriği için trait.
 * $model->localized('description') current locale'a göre doğru kolonu döndürür.
 * Fallback zinciri: config/locale.php → content_fallback
 */
trait LocalizableContent
{
    public function localized(string $field, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $chain = config("locale.content_fallback.$locale", [$locale, 'en', 'de', 'tr']);

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
        return $this->localized('description');
    }
}
