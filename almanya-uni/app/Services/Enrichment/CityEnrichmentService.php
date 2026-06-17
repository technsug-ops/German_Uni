<?php

namespace App\Services\Enrichment;

use App\Models\City;
use App\Services\Content\AlmanyaUniForumSearch;
use App\Services\Content\CommunityInsightsService;

class CityEnrichmentService
{
    use \App\Services\Enrichment\Concerns\FetchesSourceSnippet;

    public function __construct(
        private WikipediaExtract $wiki,
        private AiContentBlockGenerator $ai,
        private CommunityInsightsService $community,
        private AlmanyaUniForumSearch $auForum,
    ) {}

    /**
     * @param string[] $sourceUrls Küratörlü ek kaynak linkleri (resmi site, iyi makaleler…).
     *                             İçerik bu otoriter kaynaklara grounding'lenir (kullanıcı onaylı).
     */
    public function enrich(City $city, bool $force = false, array $sourceUrls = []): array
    {
        if (!$force && $city->last_enriched_at && $city->last_enriched_at->diffInDays(now()) < 30) {
            return ['success' => false, 'error' => 'Yakın zamanda enrich edildi'];
        }

        // 1. Wikipedia (DE + EN + TR)
        $wikiData = $this->wiki->fetchMultiLang($city->name_de, ['de', 'en', 'tr']);
        $heroImage = null;
        $sourceText = '';
        foreach ($wikiData as $lang => $r) {
            if (!empty($r['extract'])) {
                $sourceText .= "\n\n[Wikipedia $lang]\n" . $r['extract'];
            }
            if (!$heroImage && !empty($r['thumbnail_url'])) {
                $heroImage = $r['thumbnail_url'];
            }
        }

        // 1b. Küratörlü kaynaklar (verilirse) — otoriter ek grounding (meta açıklama + ana metin).
        $sourceText = $this->appendSourceSnippets($sourceText, $sourceUrls);

        // 2. DB context
        $state = $city->state;
        $uniCount = $city->universities()->where('is_active', 1)->count();
        $publicUni = $city->universities()->where('is_active', 1)->where('type', 'public')->count();
        $privateUni = $city->universities()->where('is_active', 1)->where('type', 'private')->count();
        $topUnis = $city->universities()->where('is_active', 1)
            ->orderByDesc('student_count')
            ->take(5)
            ->pluck('name_de')
            ->toArray();
        $programs = \App\Models\Program::whereHas('university', fn ($q) => $q->where('city_id', $city->id))->where('is_active', 1)->count();

        $context = "Şehir adı: {$city->name_de} (TR: {$city->name_tr})\n";
        $context .= 'Eyalet: ' . ($state?->name_de ?? '?') . "\n";
        $context .= 'Nüfus: ' . ($city->population ? number_format($city->population) : '?') . "\n";
        $context .= 'Koordinat: ' . ($city->latitude ? "{$city->latitude}, {$city->longitude}" : '?') . "\n";
        $context .= "Bu şehirdeki üni sayısı: $uniCount (devlet=$publicUni, özel=$privateUni)\n";
        $context .= 'Bu şehirdeki toplam program: ' . $programs . "\n";
        if ($topUnis) {
            $context .= 'Top üniler: ' . implode(', ', $topUnis);
        }

        // Topluluk içgörüleri — Forum + Telegram + visa/denklik (~1M mesaj havuzundan)
        $insights = $this->community->getInsightsFor($city->name_de, tgLimit: 15, forumLimit: 6);
        $communityContext = $this->community->formatForPrompt($insights);

        $seoGaps = (array) ($city->getAttribute('_seo_gaps') ?? []);
        $result = $this->ai->generate(
            'şehir',
            $city->name_de,
            $context . $communityContext,
            $sourceText ?: "Şehir: {$city->name_de}, {$state?->name_de}, Almanya.",
            $seoGaps
        );

        if (!$result['success']) {
            return ['success' => false, 'error' => $result['error']];
        }

        $blocks = $result['blocks'];

        // Hero image
        if ($heroImage) {
            array_unshift($blocks, [
                'type' => 'hero',
                'image_url' => $heroImage,
                'alt' => $city->name_de,
                'source' => 'wikipedia',
            ]);
        }

        // Gallery — Wikipedia DE/EN sayfasındaki görselleri çek
        $galleryImages = $this->wiki->fetchImages($city->name_de, 'de', 50);
        if (count($galleryImages) < 5) {
            $galleryImages = array_merge($galleryImages, $this->wiki->fetchImages($city->name_de, 'en', 50));
        }
        $curated = $this->wiki->curateGallery($galleryImages, 16);
        if (!empty($curated)) {
            $blocks[] = [
                'type' => 'gallery',
                'h' => "{$city->name_de} — Görseller",
                'items' => $curated,
                'source' => 'wikipedia',
            ];
        }

        // İlgili Forum Konuları (DeutschStudent topluluk verisi)
        if (!empty($insights['forum_topics_with_url'])) {
            $blocks[] = [
                'type' => 'related_forum_topics',
                'h' => "{$city->name_de} hakkında topluluk tartışmaları",
                'source' => 'DeutschStudent',
                'items' => array_map(fn ($t) => [
                    'title' => $t['title'] ?? '',
                    'url' => $t['url'] ?? '',
                    'views' => (int) ($t['views'] ?? 0),
                    'replies' => (int) ($t['replies'] ?? 0),
                    'category' => $t['category'] ?? null,
                ], array_slice($insights['forum_topics_with_url'], 0, 6)),
            ];
        }

        // AlmanyaUni kendi forumumuzdaki ilgili konular
        $auTopics = $this->auForum->search($city->name_de, 5);
        if (!empty($auTopics)) {
            $blocks[] = [
                'type' => 'almanyauni_forum_topics',
                'h' => "{$city->name_de} — AlmanyaUni Forumunda",
                'items' => $auTopics,
                'cta_url' => '/forum/',
            ];
        }

        // Üniler listesi (programatik)
        if ($uniCount > 0) {
            $blocks[] = [
                'type' => 'universities_in_city',
                'h' => "{$city->name_de} Üniversiteleri ($uniCount)",
                'total' => $uniCount,
                'public' => $publicUni,
                'private' => $privateUni,
                'top_unis' => $topUnis,
            ];
        }

        // External links
        $links = [];
        foreach (['de', 'en', 'tr'] as $lang) {
            if (!empty($wikiData[$lang]['source_url'])) {
                $links[] = [
                    'label' => 'Wikipedia (' . strtoupper($lang) . ')',
                    'url' => $wikiData[$lang]['source_url'],
                    'type' => 'wikipedia',
                ];
            }
        }
        if ($city->wikidata_id) {
            $links[] = [
                'label' => 'Wikidata',
                'url' => 'https://www.wikidata.org/wiki/' . $city->wikidata_id,
                'type' => 'wikidata',
            ];
        }
        if ($links) {
            $blocks[] = ['type' => 'external_links', 'h' => 'Faydalı Linkler', 'items' => $links];
        }

        // Schema.org Place / City
        $blocks[] = [
            'type' => 'schema_jsonld',
            'data' => [
                '@context' => 'https://schema.org',
                '@type' => 'City',
                'name' => $city->name_de,
                'alternateName' => $city->name_tr,
                'description' => collect($blocks)->firstWhere('type', 'intro')['body_md'] ?? '',
                'image' => $heroImage,
                'containedInPlace' => $state ? [
                    '@type' => 'AdministrativeArea',
                    'name' => $state->name_de,
                ] : null,
                'geo' => $city->latitude ? [
                    '@type' => 'GeoCoordinates',
                    'latitude' => (float) $city->latitude,
                    'longitude' => (float) $city->longitude,
                ] : null,
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $city->name_de,
                    'addressRegion' => $state?->name_de,
                    'addressCountry' => 'DE',
                ],
                'sameAs' => collect($wikiData)->pluck('source_url')->filter()->values()->toArray(),
            ],
        ];

        // image_url için kart-uygun görsel: önce curated gallery'den (gerçek manzara),
        // yoksa Wikipedia summary thumbnail (genelde bayrak/arma — fallback)
        $cardImage = !empty($curated) ? $curated[0]['url'] : $heroImage;

        $updateData = [
            'content_blocks' => $blocks,
            'last_enriched_at' => now(),
        ];
        if ($cardImage) {
            $updateData['image_url'] = $cardImage;
        }
        $city->update($updateData);

        return [
            'success' => true,
            'blocks_count' => count($blocks),
            'sources' => [
                'wikipedia_languages' => array_keys($wikiData),
                'hero_image' => $heroImage,
                'uni_count' => $uniCount,
                'external_links' => count($links),
                'community' => [
                    'tg_questions' => count($insights['telegram_questions'] ?? []),
                    'forum_titles' => count($insights['forum_titles'] ?? []),
                    'heatmap_pairs' => count($insights['co_occurrence'] ?? []),
                ],
            ],
            'tokens' => $result['tokens'],
        ];
    }
}
