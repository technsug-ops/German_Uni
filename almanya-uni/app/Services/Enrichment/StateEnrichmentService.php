<?php

namespace App\Services\Enrichment;

use App\Models\City;
use App\Models\State;
use App\Models\University;
use App\Services\Content\AlmanyaUniForumSearch;
use App\Services\Content\CommunityInsightsService;

/**
 * Eyalet (State) için Wikipedia + AI + community enrichment.
 * City/Field pattern'inin aynısı + eyalete özgü (başkent, şehirler, üniler).
 */
class StateEnrichmentService
{
    public function __construct(
        private WikipediaExtract $wiki,
        private AiContentBlockGenerator $ai,
        private CommunityInsightsService $community,
        private AlmanyaUniForumSearch $auForum,
    ) {}

    public function enrich(State $state, bool $force = false): array
    {
        if (!$force && $state->last_enriched_at && $state->last_enriched_at->diffInDays(now()) < 30) {
            return ['success' => false, 'error' => 'Yakın zamanda enrich edildi'];
        }

        // Wikipedia DE + EN + TR
        $wikiData = $this->wiki->fetchMultiLang($state->name_de, ['de', 'en', 'tr']);
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

        // DB context
        $cityCount = City::where('state_id', $state->id)->count();
        $uniCount = University::whereHas('city', fn ($q) => $q->where('state_id', $state->id))
            ->where('is_active', 1)->count();
        $publicUni = University::whereHas('city', fn ($q) => $q->where('state_id', $state->id))
            ->where('is_active', 1)->where('type', 'public')->count();
        $privateUni = University::whereHas('city', fn ($q) => $q->where('state_id', $state->id))
            ->where('is_active', 1)->where('type', 'private')->count();

        $topCities = City::where('state_id', $state->id)
            ->withCount(['universities' => fn ($q) => $q->where('is_active', 1)])
            ->orderByDesc('universities_count')
            ->take(8)
            ->pluck('name_de')
            ->toArray();

        $topUnis = University::whereHas('city', fn ($q) => $q->where('state_id', $state->id))
            ->where('is_active', 1)
            ->orderByDesc('student_count')
            ->take(8)
            ->pluck('name_de')
            ->toArray();

        $context = "Eyalet: {$state->name_de} (TR: {$state->name_tr})\n";
        $context .= "Şehir sayısı (DB): {$cityCount}\n";
        $context .= "Üniversite sayısı: {$uniCount} (devlet={$publicUni}, özel={$privateUni})\n";
        $context .= "Koordinat: " . ($state->latitude ? "{$state->latitude}, {$state->longitude}" : '?') . "\n";
        if ($topCities) $context .= 'Top şehirler: ' . implode(', ', $topCities) . "\n";
        if ($topUnis) $context .= 'Top üniler: ' . implode(', ', array_slice($topUnis, 0, 6));

        // Community insights
        $insights = $this->community->getInsightsFor($state->name_de . ' ' . $state->name_tr, tgLimit: 12, forumLimit: 5);
        $communityContext = $this->community->formatForPrompt($insights);

        // AI
        $result = $this->ai->generate(
            'eyalet',
            $state->name_tr,
            $context . $communityContext,
            $sourceText ?: "Eyalet: {$state->name_de}, Almanya."
        );

        if (!$result['success']) {
            return ['success' => false, 'error' => $result['error']];
        }

        $blocks = $result['blocks'];

        if ($heroImage) {
            array_unshift($blocks, [
                'type' => 'hero',
                'image_url' => $heroImage,
                'alt' => $state->name_de,
                'source' => 'wikipedia',
            ]);
        }

        // Gallery
        $galleryImages = $this->wiki->fetchImages($state->name_de, 'de', 40);
        if (count($galleryImages) < 5) {
            $galleryImages = array_merge($galleryImages, $this->wiki->fetchImages($state->name_de, 'en', 40));
        }
        $curated = $this->wiki->curateGallery($galleryImages, 8);
        if (!empty($curated)) {
            $blocks[] = [
                'type' => 'gallery',
                'h' => "{$state->name_de} — Görseller",
                'items' => $curated,
                'source' => 'wikipedia',
            ];
        }

        // Üniversiteler özet (universities_in_city pattern'ini kullan)
        if ($uniCount > 0) {
            $blocks[] = [
                'type' => 'universities_in_city',
                'h' => "{$state->name_de} eyaletinde üniversiteler",
                'total' => $uniCount,
                'public' => $publicUni,
                'private' => $privateUni,
                'top_unis' => $topUnis,
            ];
        }

        // DeutschStudent forum
        if (!empty($insights['forum_topics_with_url'])) {
            $blocks[] = [
                'type' => 'related_forum_topics',
                'h' => "{$state->name_de} hakkında topluluk tartışmaları",
                'source' => 'DeutschStudent',
                'items' => array_map(fn ($t) => [
                    'title' => $t['title'] ?? '',
                    'url' => $t['url'] ?? '',
                    'views' => (int) ($t['views'] ?? 0),
                    'replies' => (int) ($t['replies'] ?? 0),
                    'category' => $t['category'] ?? null,
                ], array_slice($insights['forum_topics_with_url'], 0, 5)),
            ];
        }

        // AlmanyaUni forum
        $auTopics = $this->auForum->search($state->name_de, 5);
        if (!empty($auTopics)) {
            $blocks[] = [
                'type' => 'almanyauni_forum_topics',
                'h' => "{$state->name_de} — AlmanyaUni Forumunda",
                'items' => $auTopics,
                'cta_url' => '/forum/',
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
        if ($state->wikidata_id) {
            $links[] = ['label' => 'Wikidata', 'url' => 'https://www.wikidata.org/wiki/' . $state->wikidata_id, 'type' => 'wikidata'];
        }
        if ($links) {
            $blocks[] = ['type' => 'external_links', 'h' => 'Faydalı Linkler', 'items' => $links];
        }

        // Schema.org AdministrativeArea
        $blocks[] = [
            'type' => 'schema_jsonld',
            'data' => [
                '@context' => 'https://schema.org',
                '@type' => 'AdministrativeArea',
                'name' => $state->name_de,
                'alternateName' => $state->name_tr,
                'description' => collect($blocks)->firstWhere('type', 'intro')['body_md'] ?? '',
                'image' => $heroImage,
                'containedInPlace' => ['@type' => 'Country', 'name' => 'Germany'],
                'geo' => $state->latitude ? [
                    '@type' => 'GeoCoordinates',
                    'latitude' => (float) $state->latitude,
                    'longitude' => (float) $state->longitude,
                ] : null,
                'sameAs' => collect($wikiData)->pluck('source_url')->filter()->values()->toArray(),
            ],
        ];

        $cardImage = !empty($curated) ? $curated[0]['url'] : $heroImage;

        $state->update([
            'content_blocks' => $blocks,
            'last_enriched_at' => now(),
            'image_url' => $cardImage,
        ]);

        return [
            'success' => true,
            'blocks_count' => count($blocks),
            'sources' => [
                'wikipedia_languages' => array_keys($wikiData),
                'hero_image' => $heroImage,
                'cities' => $cityCount,
                'unis' => $uniCount,
                'community' => [
                    'tg_questions' => count($insights['telegram_questions'] ?? []),
                    'forum_titles' => count($insights['forum_titles'] ?? []),
                ],
            ],
            'tokens' => $result['tokens'],
        ];
    }
}
