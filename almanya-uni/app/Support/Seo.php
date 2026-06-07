<?php

namespace App\Support;

use App\Models\Post;
use App\Models\University;

class Seo
{
    /**
     * content_blocks JSON'ından SEO description çıkar (intro > section > generic).
     * Markdown'ı temizler, 155 char ile keser.
     */
    public static function descriptionFromBlocks(?array $blocks, ?string $fallback = null): string
    {
        // Content blocks single-language (TR). EN/DE locale'de TR sızıntı önle.
        if (app()->getLocale() !== 'tr') {
            return $fallback ?? '';
        }
        if (empty($blocks)) {
            return $fallback ?? '';
        }

        $text = null;
        foreach ($blocks as $b) {
            if (($b['type'] ?? null) === 'intro' && !empty($b['body_md'])) {
                $text = $b['body_md'];
                break;
            }
        }
        if (!$text) {
            foreach ($blocks as $b) {
                if (($b['type'] ?? null) === 'section' && !empty($b['body_md'])) {
                    $text = $b['body_md'];
                    break;
                }
            }
        }

        if (!$text) {
            return $fallback ?? '';
        }

        // Markdown ve HTML temizle
        $text = preg_replace('/[#*_`>~\[\]\(\)]+/u', '', $text);
        $text = preg_replace('/!\[.*?\]\(.*?\)/u', '', $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim(strip_tags($text));

        return mb_strlen($text) > 155 ? mb_substr($text, 0, 152) . '…' : $text;
    }

    /**
     * content_blocks içindeki faq blok'larından schema.org FAQPage üret.
     * Google rich snippets (featured Q&A) için.
     */
    public static function faqPageFromBlocks(?array $blocks): ?array
    {
        // Content blocks TR-only — EN/DE'de FAQ schema yok
        if (app()->getLocale() !== 'tr') return null;
        if (empty($blocks)) return null;

        $faqs = [];
        foreach ($blocks as $b) {
            if (($b['type'] ?? null) === 'faq' && !empty($b['items'])) {
                foreach ($b['items'] as $item) {
                    if (empty($item['q']) || empty($item['a'])) continue;
                    $faqs[] = [
                        '@type' => 'Question',
                        'name' => $item['q'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => self::stripMarkdown($item['a']),
                        ],
                    ];
                }
            }
        }

        if (empty($faqs)) return null;

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqs,
        ];
    }

    /**
     * Generic FAQPage JSON-LD from a simple array of ['q','a'] entries.
     * Multi-locale safe — caller passes already-translated strings.
     */
    public static function genericFaqPage(array $faqs): ?array
    {
        $mainEntity = [];
        foreach ($faqs as $faq) {
            $q = trim((string) ($faq['q'] ?? ''));
            $a = trim((string) ($faq['a'] ?? ''));
            if ($q === '' || $a === '') continue;
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $q,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => self::stripMarkdown($a),
                ],
            ];
        }
        if (empty($mainEntity)) return null;
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity,
        ];
    }

    /**
     * Program → schema.org Course
     */
    public static function courseSchema(\App\Models\Program $program): array
    {
        $uniName = $program->university?->name_de;
        $degreeLabel = match ($program->degree) {
            'bachelor' => 'Bachelor', 'master' => 'Master', 'phd' => 'PhD',
            'staatsexamen' => 'Staatsexamen', 'diplom' => 'Diplom',
            default => ucfirst((string) $program->degree),
        };
        $langCode = match ($program->language) {
            'en' => 'en', 'de' => 'de', 'both' => 'de', default => 'de',
        };

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => $program->name_de,
            'description' => $program->description_tr ?: $program->description_en,
            'provider' => $uniName ? [
                '@type' => 'CollegeOrUniversity',
                'name' => $uniName,
                'sameAs' => $program->university?->website_url,
            ] : null,
            'educationalCredentialAwarded' => $degreeLabel,
            'inLanguage' => $langCode,
            'courseCode' => $program->slug,
            'url' => route('programs.show', $program->slug),
        ]);
    }

    private static function stripMarkdown(string $text): string
    {
        $text = preg_replace('/[#*_`>~\[\]\(\)]+/u', '', $text);
        $text = preg_replace('/!\[.*?\]\(.*?\)/u', '', $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim(strip_tags($text));
    }

    public static function organization(): array
    {
        // Brand domain-aware: hangi host'tan ise o brand'in adı/logosu/url'i
        $brandName = brand('name');
        $brandDomain = brand('domain');
        $url = 'https://' . $brandDomain;
        $logo = $url . brand('logo');

        // Aktif sosyal medya linkleri sameAs'a dahil (admin'den yönetilir)
        $social = [];
        try {
            $social = \App\Models\SocialLink::visible()->pluck('url')->all();
        } catch (\Throwable $e) {}

        $defaultSameAs = config('seo.organization.sameAs', []);

        // Çift-marka: aynı kurumun diğer adı alternateName olarak verilir →
        // AI/arama motorları tek temiz varlık (entity) kurar (ApplyToGerman ↔ AlmanyaUni).
        $alternateName = brand_key() === 'applytogerman' ? 'AlmanyaUni' : 'ApplyToGerman';

        return self::clean([
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            'name' => $brandName,
            'alternateName' => $alternateName,
            'url' => $url,
            'logo' => $logo,
            'sameAs' => array_values(array_unique(array_merge($defaultSameAs, $social))),
        ]);
    }

    /**
     * schema.org Dataset — program/üniversite veritabanını makine-okunur tanımlar.
     * AI motorlarının alıntıladığı "quotable" istatistik sinyali (X program · Y üni · Z şehir).
     * $stats: ['programs' => int, 'universities' => int, 'cities' => int]
     */
    public static function dataset(array $stats = []): array
    {
        $brandName = brand('name');
        $url = 'https://' . brand('domain');

        $measured = [];
        $map = [
            'programs'     => __('study programs'),
            'universities' => __('universities'),
            'cities'       => __('cities'),
        ];
        $descParts = [];
        foreach ($map as $key => $label) {
            $val = (int) ($stats[$key] ?? 0);
            if ($val <= 0) {
                continue;
            }
            $measured[] = [
                '@type' => 'PropertyValue',
                'name'  => $label,
                'value' => $val,
            ];
            $descParts[] = number_format($val) . ' ' . $label;
        }

        $description = $descParts
            ? trim($brandName . ' — ' . implode(' · ', $descParts))
            : $brandName;

        return self::clean([
            '@context' => 'https://schema.org',
            '@type' => 'Dataset',
            'name' => $brandName . ' — ' . __('German universities & study programs'),
            'description' => $description,
            'url' => $url,
            'keywords' => ['Germany', 'universities', 'study programs', 'Hochschule', 'Studium', 'international students'],
            'inLanguage' => app()->getLocale(),
            'isAccessibleForFree' => true,
            'creator' => [
                '@type' => 'Organization',
                'name' => $brandName,
                'url' => $url,
            ],
            'variableMeasured' => $measured ?: null,
        ]);
    }

    public static function website(): array
    {
        $brandName = brand('name');
        $url = 'https://' . brand('domain');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $brandName,
            'url' => $url,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => $url . '/search?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * BreadcrumbList from an ordered list of [name, url] pairs.
     */
    public static function breadcrumbs(array $items): array
    {
        $list = [];
        foreach ($items as $i => $item) {
            $list[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list,
        ];
    }

    public static function article(Post $post, ?string $url = null): array
    {
        $author = null;
        if ($post->author) {
            $authorSameAs = [];
            foreach ((array) ($post->author->social_links ?? []) as $type => $value) {
                $authorSameAs[] = match ($type) {
                    'twitter'  => 'https://twitter.com/' . ltrim($value, '@'),
                    'linkedin' => 'https://linkedin.com/in/' . $value,
                    'github'   => 'https://github.com/' . $value,
                    'email'    => null,
                    default    => $value,
                };
            }
            $author = array_filter([
                '@type' => 'Person',
                'name' => $post->author->name,
                'description' => $post->author->bio,
                'jobTitle' => $post->author->role_label,
                'image' => $post->author->avatar_url,
                'sameAs' => array_values(array_filter($authorSameAs)),
            ]);
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => $post->metaDescriptionResolved(),
            'image' => $post->featured_image,
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'inLanguage' => app()->getLocale(),
            'author' => $author,
            'publisher' => [
                '@type' => 'Organization',
                'name' => brand('name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => 'https://' . brand('domain') . brand('logo'),
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $url ?? url()->current(),
            ],
            'articleSection' => $post->category?->name,
            'wordCount' => max(1, (int) ($post->reading_minutes * 220)),
        ];
    }

    /**
     * Schema.org City — Almanya şehirleri için yerel SEO sinyali.
     * Google "Berlin universities", "Munich student life" gibi yerel aramalarda
     * bu schema'yı kullanarak doğru rich snippet üretir.
     */
    public static function cityPlace(\App\Models\City $city, ?string $url = null): array
    {
        $address = [
            '@type' => 'PostalAddress',
            'addressLocality' => $city->name_de ?: $city->name,
            'addressCountry' => 'DE',
        ];
        if ($city->state) {
            $address['addressRegion'] = $city->state->name_de ?: $city->state->name;
        }

        $geo = ($city->latitude && $city->longitude) ? [
            '@type' => 'GeoCoordinates',
            'latitude' => (float) $city->latitude,
            'longitude' => (float) $city->longitude,
        ] : null;

        // Şehirdeki aktif üniversiteler — Place içinde containsPlace
        $containsPlace = $city->relationLoaded('universities')
            ? $city->universities
                ->where('is_active', true)
                ->take(20)
                ->map(fn ($u) => [
                    '@type' => 'CollegeOrUniversity',
                    'name' => $u->name_de,
                    'url' => route('universities.show', $u->slug),
                ])
                ->values()
                ->all()
            : [];

        return [
            '@context' => 'https://schema.org',
            '@type' => 'City',
            'name' => $city->name,
            'alternateName' => $city->name_de !== $city->name ? $city->name_de : null,
            'url' => $url ?? url()->current(),
            'address' => $address,
            'geo' => $geo,
            'population' => $city->population ?: null,
            'containsPlace' => $containsPlace ?: null,
            'image' => $city->image_url ?: null,
        ];
    }

    public static function universityOrg(University $university, ?string $url = null): array
    {
        $address = [];
        if ($university->city) {
            $address = [
                '@type' => 'PostalAddress',
                'addressLocality' => $university->city->name_de,
                'addressRegion' => $university->city->state?->name_de,
                'addressCountry' => 'DE',
            ];
        }

        return [
            '@context' => 'https://schema.org',
            // schema.org'da FH (Hochschule für angewandte Wissenschaften) ile klasik
            // Üniversite için ayrı tip yok — ikisi de CollegeOrUniversity.
            '@type' => 'CollegeOrUniversity',
            'name' => $university->name_de,
            'alternateName' => $university->name_en !== $university->name_de ? $university->name_en : null,
            'url' => $url ?? url()->current(),
            'logo' => $university->logo_url,
            'sameAs' => array_filter([
                $university->website_url,
                $university->wikipedia_url_de,
                $university->wikipedia_url_en,
            ]),
            'foundingDate' => $university->founded_year ? (string) $university->founded_year : null,
            'address' => $address ?: null,
            'geo' => $university->latitude && $university->longitude ? [
                '@type' => 'GeoCoordinates',
                'latitude' => (float) $university->latitude,
                'longitude' => (float) $university->longitude,
            ] : null,
            'numberOfStudents' => $university->student_count ? [
                '@type' => 'QuantitativeValue',
                'value' => $university->student_count,
            ] : null,
        ];
    }

    /**
     * ItemList for a ranking page.
     * $items: array of ['url' => ..., 'name' => ...]
     */
    public static function itemList(string $name, array $items, ?string $description = null): array
    {
        $list = [];
        foreach ($items as $i => $item) {
            $list[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'url' => $item['url'],
                'name' => $item['name'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $name,
            'description' => $description,
            'itemListElement' => $list,
            'numberOfItems' => count($list),
        ];
    }

    /**
     * Remove null values recursively for clean JSON-LD output.
     */
    public static function clean(array $data): array
    {
        $cleaned = [];
        foreach ($data as $key => $value) {
            if ($value === null || $value === '' || (is_array($value) && empty(self::clean($value)))) {
                continue;
            }
            $cleaned[$key] = is_array($value) ? self::clean($value) : $value;
        }
        return $cleaned;
    }
}
