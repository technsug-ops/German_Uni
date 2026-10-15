<?php

namespace App\Support;

use App\Models\City;
use App\Models\FieldOfStudy;
use App\Models\Post;
use App\Models\Profession;
use App\Models\Program;
use App\Models\Scholarship;
use App\Models\University;

/**
 * Dinamik paylaşım kartı (og:image) — dil kararı, cache yolu, URL ve metin girdileri.
 *
 * Kural: kartın TÜM metinleri (marka sloganı, rozet, alt başlık, okuma süresi…) sayfanın
 * dilinde olmalı. /og/{type}/{slug}.png route'unda locale prefix'i YOK; dil şöyle çözülür:
 *   - post: kaydın kendi `locale` alanı (slug dil-başına benzersiz) → URL değişmez.
 *   - diğer tipler (aynı slug her dilde ortak): `?lang=xx` query parametresi;
 *     yoksa marka varsayılan dili (applytogerman → en). <x-seo> bunu otomatik ekler.
 *
 * Cache yolu sürümlü + dil-başına: og/{brand}/v2/{locale}/{type}-{slug}.png. Eski
 * (Türkçe metinli) og/{brand}/{type}-{slug}.png dosyaları artık okunmaz.
 */
final class OgCard
{
    /** Metin/tasarım değişince artır → eski cache dosyaları otomatik devreden çıkar. */
    public const VERSION = 'v2';

    public const TYPES = ['university', 'program', 'city', 'field', 'profession', 'post', 'scholarship'];

    /** Kaydı kendi dilini taşıyan tipler: dil kayıttan gelir, URL'ye lang eklenmez. */
    public const RECORD_LOCALE_TYPES = ['post'];

    public const QUERY_PARAM = 'lang';

    /** @return list<string> */
    public static function locales(): array
    {
        return Hreflang::activeLocales() ?: ['tr', 'en', 'de'];
    }

    public static function normalizeLocale(mixed $locale): ?string
    {
        if (! is_string($locale) || $locale === '') {
            return null;
        }
        $locale = strtolower(substr($locale, 0, 5));

        return in_array($locale, self::locales(), true) ? $locale : null;
    }

    /** Dil bilgisi olmayan (eski) URL'ler için: marka varsayılanı → config → en. */
    public static function defaultLocale(): string
    {
        return self::normalizeLocale(brand('default_locale'))
            ?? self::normalizeLocale(config('locale.default'))
            ?? 'en';
    }

    /**
     * Kartın dili. post: kaydın locale'i (istenen dilde aynı slug varsa o kayıt);
     * diğerleri: istenen dil ?: varsayılan. Kayıt yoksa null (→ 404).
     */
    public static function localeFor(string $type, string $slug, ?string $requested): ?string
    {
        if ($type === 'post') {
            $post = self::findPost($slug, $requested);
            if (! $post) {
                return null;
            }

            return self::normalizeLocale($post->locale) ?? $requested ?? self::defaultLocale();
        }

        return $requested ?? self::defaultLocale();
    }

    public static function cachePath(string $brandKey, string $locale, string $type, string $slug): string
    {
        return "og/{$brandKey}/" . self::VERSION . "/{$locale}/{$type}-{$slug}.png";
    }

    /** Eski (sürümsüz, dil-bağımsız) cache yolu — yalnızca temizlik için. */
    public static function legacyCachePath(string $brandKey, string $type, string $slug): string
    {
        return "og/{$brandKey}/{$type}-{$slug}.png";
    }

    /** Kart URL'si; kayıt-dilli tiplerde lang eklenmez (mevcut URL korunur). */
    public static function url(string $type, string $slug, ?string $locale = null): string
    {
        $url = route('og.image', ['type' => $type, 'slug' => $slug . '.png']);

        return self::localizeUrl($url, $locale);
    }

    /**
     * Bu sitenin /og/{type}/{slug}.png adresine (lang yoksa) sayfa dilini ekler.
     * Dış görsel, post kartı veya zaten lang taşıyan URL'ye dokunmaz.
     */
    public static function localizeUrl(?string $url, ?string $locale = null): ?string
    {
        if (! is_string($url) || $url === '') {
            return $url;
        }
        $locale = self::normalizeLocale($locale ?? app()->getLocale());
        if ($locale === null) {
            return $url;
        }

        $parts = parse_url($url);
        $path = $parts['path'] ?? '';
        if (! preg_match('#^/og/(' . implode('|', self::TYPES) . ')/[a-z0-9\-]+\.png$#', $path, $m)) {
            return $url;
        }
        if (in_array($m[1], self::RECORD_LOCALE_TYPES, true)) {
            return $url;
        }
        if (isset($parts['host']) && strcasecmp($parts['host'], (string) request()->getHost()) !== 0) {
            return $url;
        }
        parse_str($parts['query'] ?? '', $query);
        if (isset($query[self::QUERY_PARAM])) {
            return $url;
        }

        return $url . (str_contains($url, '?') ? '&' : '?') . self::QUERY_PARAM . '=' . $locale;
    }

    /**
     * Kart metinleri (render girdisi) — verilen dilde. Global app locale yalnızca model
     * accessor'ları (Category/City/State… name) için GEÇİCİ değiştirilir ve geri alınır.
     *
     * @return array{badge:string,title:string,subtitle:string,meta:string,color:string,brand:string,tagline:string,domain:string,locale:string}|null
     */
    public static function data(string $type, string $slug, string $locale): ?array
    {
        return self::withLocale($locale, function () use ($type, $slug, $locale) {
            $d = match ($type) {
                'university'  => self::forUniversity($slug, $locale),
                'program'     => self::forProgram($slug, $locale),
                'city'        => self::forCity($slug, $locale),
                'field'       => self::forField($slug, $locale),
                'profession'  => self::forProfession($slug, $locale),
                'post'        => self::forPost($slug, $locale),
                'scholarship' => self::forScholarship($slug, $locale),
                default       => null,
            };
            if ($d === null) {
                return null;
            }

            return $d + [
                'brand'   => (string) brand('name'),
                'tagline' => (string) (brand('tagline') ?: ''),
                'domain'  => (string) brand('domain'),
                'locale'  => $locale,
            ];
        });
    }

    /** Dile duyarlı büyük harf (TR: i→İ, ı→I). */
    public static function upper(string $text, string $locale): string
    {
        if ($locale === 'tr') {
            $text = strtr($text, ['i' => 'İ', 'ı' => 'I']);
        }

        return mb_strtoupper($text, 'UTF-8');
    }

    /* ------------------------------------------------------------------ tipler */

    private static function forUniversity(string $slug, string $l): ?array
    {
        $u = University::with('city.state')->where('slug', $slug)->first();
        if (! $u) return null;

        $sub = collect([$u->city?->name, $u->city?->state?->name])->filter()->join(' · ');

        return [
            'badge'    => self::upper(__('University', [], $l), $l),
            'title'    => (string) ($u->short_name ?: $u->name_de),
            'subtitle' => $sub ?: __('Germany', [], $l),
            'meta'     => match ($u->type) {
                'public'           => __('Public university', [], $l),
                'private'          => __('Private university', [], $l),
                'applied_sciences' => __('University of Applied Sciences', [], $l),
                'art'              => __('Art & music college', [], $l),
                'religion'         => __('Church-affiliated university', [], $l),
                default            => __('Higher education institution', [], $l),
            },
            'color'    => '#1E40AF',
        ];
    }

    private static function forProgram(string $slug, string $l): ?array
    {
        $p = Program::with('university')->where('slug', $slug)->first();
        if (! $p) return null;

        $degree = in_array(strtolower((string) $p->degree), ['', 'other', 'unknown'], true)
            ? __('Programme', [], $l)
            : degree_label($p->degree);

        return [
            'badge'    => self::upper($degree, $l),
            'title'    => (string) ($p->name_de ?: $p->name),
            'subtitle' => (string) ($p->university?->name_de ?: ''),
            'meta'     => $p->teaching_language === 'en' ? __('Taught in English', [], $l) : __('Taught in German', [], $l),
            'color'    => '#0F766E',
        ];
    }

    private static function forCity(string $slug, string $l): ?array
    {
        $c = City::with('state')->where('slug', $slug)->first();
        if (! $c) return null;

        $uniCount = $c->universities()->where('is_active', true)->count();

        return [
            'badge'    => self::upper(__('City', [], $l), $l),
            'title'    => (string) $c->name,
            'subtitle' => $c->state?->name ?: __('Germany', [], $l),
            'meta'     => $uniCount === 1 ? __('1 university', [], $l) : __(':count universities', ['count' => $uniCount], $l),
            'color'    => '#B45309',
        ];
    }

    private static function forField(string $slug, string $l): ?array
    {
        $f = FieldOfStudy::where('slug', $slug)->first();
        if (! $f) return null;

        $title = (string) $f->name;

        return [
            'badge'    => self::upper(__('Study Field', [], $l), $l),
            'title'    => $title,
            'subtitle' => ($f->name_de && $f->name_de !== $title) ? $f->name_de : '',
            'meta'     => __('Germany', [], $l),
            'color'    => $f->color ?: '#7C3AED',
        ];
    }

    private static function forProfession(string $slug, string $l): ?array
    {
        $p = Profession::with('field')->where('slug', $slug)->first();
        if (! $p) return null;

        $title = (string) ($p->name ?: $p->name_de);

        return [
            'badge'    => self::upper(__('Profession', [], $l), $l),
            'title'    => $title,
            'subtitle' => ($p->name_de && $p->name_de !== $title) ? $p->name_de : ($p->field?->name ?: __('Germany', [], $l)),
            'meta'     => $p->kldb_code ? "KldB {$p->kldb_code}" : 'BERUFENET',
            'color'    => $p->field?->color ?: '#0369A1',
        ];
    }

    private static function forPost(string $slug, string $l): ?array
    {
        $p = self::findPost($slug, $l);
        if (! $p) return null;

        $minutes = (int) $p->reading_minutes;

        return [
            'badge'    => $p->type === 'news' ? self::upper(__('News', [], $l), $l) : 'BLOG',
            'title'    => (string) $p->title,
            'subtitle' => $p->category?->name ?: (string) brand('name'),
            'meta'     => $minutes > 0 ? __(':count min read', ['count' => $minutes], $l) : '',
            'color'    => '#BE185D',
        ];
    }

    private static function forScholarship(string $slug, string $l): ?array
    {
        $s = Scholarship::where('slug', $slug)->first();
        if (! $s) return null;

        // Resmi ad (DAAD): DE sayfada Almanca, diğerlerinde İngilizce — çeviri parantezi yok.
        $title = $l === 'de'
            ? (string) ($s->name_de ?: $s->name_en)
            : (string) ($s->name_en ?: $s->name_de);
        $sub = (string) ($s->programmname ?? '');
        if ($sub === '' || mb_strtolower(trim($sub)) === mb_strtolower(trim($title))) {
            $sub = __('Germany', [], $l);
        }

        return [
            'badge'    => self::upper($s->is_daad ? __('DAAD Scholarship', [], $l) : __('Scholarship', [], $l), $l),
            'title'    => $title,
            'subtitle' => $sub,
            'meta'     => '',
            'color'    => '#15803D',
        ];
    }

    /* ------------------------------------------------------------------ yardımcı */

    private static function findPost(string $slug, ?string $locale): ?Post
    {
        if ($locale !== null) {
            $hit = Post::where('slug', $slug)->where('locale', $locale)->first();
            if ($hit) {
                return $hit;
            }
        }

        return Post::where('slug', $slug)->first();
    }

    /**
     * @template T
     * @param  callable(): T  $fn
     * @return T
     */
    private static function withLocale(string $locale, callable $fn): mixed
    {
        $prev = app()->getLocale();
        app()->setLocale($locale);
        try {
            return $fn();
        } finally {
            app()->setLocale($prev);
        }
    }
}
