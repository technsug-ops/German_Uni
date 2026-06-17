<?php

namespace App\Services\Enrichment;

use App\Models\University;
use App\Services\Content\AlmanyaUniForumSearch;
use App\Services\Content\CommunityInsightsService;

class UniversityEnrichmentService
{
    use \App\Services\Enrichment\Concerns\FetchesSourceSnippet;

    public function __construct(
        private WikipediaExtract $wiki,
        private AiContentBlockGenerator $ai,
        private CommunityInsightsService $community,
        private AlmanyaUniForumSearch $auForum,
    ) {}

    /**
     * Bir üniversite için tam içerik üret + content_blocks JSON'a kaydet.
     */
    /**
     * @param string[] $sourceUrls Küratörlü ek kaynak linkleri (resmi üni sitesi, iyi makale…).
     */
    public function enrich(University $uni, bool $force = false, array $sourceUrls = []): array
    {
        if (!$force && $uni->last_enriched_at && $uni->last_enriched_at->diffInDays(now()) < 30) {
            return ['success' => false, 'error' => 'Yakın zamanda enrich edildi'];
        }

        // 1. Wikipedia (üninin wiki URL'leri varsa onlardan, yoksa name_de ile)
        $wikiData = [];
        $heroImage = null;
        $sourceText = '';

        foreach (['de', 'en', 'tr'] as $lang) {
            $url = $uni->{'wikipedia_url_' . $lang} ?? null;
            $result = null;
            if ($url) {
                $result = $this->wiki->fetchByUrl($url, $lang);
            } else if ($lang === 'de') {
                $result = $this->wiki->fetchByTitle($uni->name_de, $lang);
            }
            if ($result) {
                $wikiData[$lang] = $result;
                if ($result['extract']) {
                    $sourceText .= "\n\n[Wikipedia $lang]\n" . $result['extract'];
                }
                if (!$heroImage && !empty($result['thumbnail_url'])) {
                    $heroImage = $result['thumbnail_url'];
                }
            }
        }

        // 1b. Küratörlü kaynaklar (resmi üni sitesi vb.) — otoriter ek grounding.
        $sourceText = $this->appendSourceSnippets($sourceText, $sourceUrls);

        // 2. DB context
        $city = $uni->city;
        $state = $city?->state;
        $programs = $uni->programs()->where('is_active', 1)->count();
        $bachelorCount = $uni->programs()->where('is_active', 1)->where('degree', 'bachelor')->count();
        $masterCount = $uni->programs()->where('is_active', 1)->where('degree', 'master')->count();
        $phdCount = $uni->programs()->where('is_active', 1)->where('degree', 'phd')->count();

        $context = "Üni adı: {$uni->name_de}\n";
        $context .= 'Şehir: ' . ($city?->name_de ?? '?') . "\n";
        $context .= 'Eyalet: ' . ($state?->name_de ?? '?') . "\n";
        $context .= 'Tip: ' . ($uni->hochschultyp ?: $uni->type) . "\n";
        $context .= 'Taşıyıcı: ' . ($uni->traegerschaft ?: '?') . "\n";
        $context .= 'Kuruluş yılı: ' . ($uni->founded_year ?: '?') . "\n";
        $context .= 'Öğrenci sayısı: ' . ($uni->student_count ? number_format($uni->student_count) : '?') . "\n";
        $context .= 'AlmanyaUni DB program: ' . $programs . " (Bachelor=$bachelorCount, Master=$masterCount, PhD=$phdCount)\n";
        $context .= 'Resmi site: ' . ($uni->website_url ?: '?') . "\n";
        $context .= 'HRK üyesi: ' . ($uni->hrk_member ? 'Evet' : 'Hayır') . "\n";
        $context .= 'Uni-Assist üyesi: ' . ($uni->is_uni_assist_member ? 'Evet' : 'Hayır');

        // 3. Topluluk içgörüleri — Forum + Telegram (üni adı + şehir adı için)
        $entityForCommunity = $uni->name_de . ($city ? ' ' . $city->name_de : '');
        $insights = $this->community->getInsightsFor($entityForCommunity, tgLimit: 12, forumLimit: 5);
        $communityContext = $this->community->formatForPrompt($insights);

        // 4. AI üret
        $seoGaps = (array) ($uni->getAttribute('_seo_gaps') ?? []);
        $result = $this->ai->generate(
            'üniversite',
            $uni->name_de,
            $context . $communityContext,
            $sourceText ?: "Üniversite: {$uni->name_de}, {$city?->name_de}, Almanya.",
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
                'alt' => $uni->name_de,
                'source' => 'wikipedia',
            ]);
        } elseif ($uni->logo_url) {
            array_unshift($blocks, [
                'type' => 'hero',
                'image_url' => $uni->logo_url,
                'alt' => $uni->name_de,
                'source' => 'logo',
            ]);
        }

        // İlgili Forum Konuları (DeutschStudent topluluk verisi)
        if (!empty($insights['forum_topics_with_url'])) {
            $blocks[] = [
                'type' => 'related_forum_topics',
                'h' => "{$uni->name_de} hakkında topluluk tartışmaları",
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

        // AlmanyaUni kendi forumumuzdaki ilgili konular
        $auTopics = $this->auForum->search($uni->name_de, 5);
        if (!empty($auTopics)) {
            $blocks[] = [
                'type' => 'almanyauni_forum_topics',
                'h' => "{$uni->name_de} — AlmanyaUni Forumunda",
                'items' => $auTopics,
                'cta_url' => '/forum/',
            ];
        }

        // Gallery — Wikipedia sayfasındaki görselleri çek
        $galleryImages = $this->wiki->fetchImages($uni->name_de, 'de', 50);
        if (count($galleryImages) < 5) {
            $galleryImages = array_merge($galleryImages, $this->wiki->fetchImages($uni->name_de, 'en', 50));
        }
        $curated = $this->wiki->curateGallery($galleryImages, 8);
        if (!empty($curated)) {
            $blocks[] = [
                'type' => 'gallery',
                'h' => "{$uni->name_de} — Görseller",
                'items' => $curated,
                'source' => 'wikipedia',
            ];
        }

        // Programs summary block
        if ($programs > 0) {
            $blocks[] = [
                'type' => 'programs_summary',
                'h' => 'Programlar',
                'total' => $programs,
                'bachelor' => $bachelorCount,
                'master' => $masterCount,
                'phd' => $phdCount,
            ];
        }

        // External links block
        $links = [];
        if ($uni->website_url) {
            $links[] = ['label' => 'Resmi web sitesi', 'url' => $uni->website_url, 'type' => 'official'];
        }
        foreach (['de', 'en', 'tr'] as $lang) {
            if (!empty($wikiData[$lang]['source_url'])) {
                $links[] = [
                    'label' => 'Wikipedia (' . strtoupper($lang) . ')',
                    'url' => $wikiData[$lang]['source_url'],
                    'type' => 'wikipedia',
                ];
            }
        }
        if ($uni->hs_nummer) {
            $links[] = [
                'label' => 'Hochschulkompass',
                'url' => 'https://www.hochschulkompass.de/hochschulen/hochschulsuche/detail/all/show/Hochschule/page/1/hs_nr/' . $uni->hs_nummer . '.html',
                'type' => 'official',
            ];
        }
        if ($links) {
            $blocks[] = ['type' => 'external_links', 'h' => 'Faydalı Linkler', 'items' => $links];
        }

        // Schema.org EducationalOrganization
        $blocks[] = [
            'type' => 'schema_jsonld',
            'data' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollegeOrUniversity',
                'name' => $uni->name_de,
                'description' => collect($blocks)->firstWhere('type', 'intro')['body_md'] ?? '',
                'url' => $uni->website_url,
                'foundingDate' => $uni->founded_year ? (string) $uni->founded_year : null,
                'numberOfStudents' => $uni->student_count,
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $city?->name_de,
                    'addressRegion' => $state?->name_de,
                    'streetAddress' => $uni->street,
                    'postalCode' => $uni->postal_code,
                    'addressCountry' => 'DE',
                ],
                'logo' => $uni->logo_url,
                'image' => $heroImage,
                'sameAs' => collect($wikiData)->pluck('source_url')->filter()->values()->toArray(),
            ],
        ];

        // Meilisearch yoksa Scout sync'i kapat
        if (method_exists(University::class, 'disableSearchSyncing')) {
            University::disableSearchSyncing();
        }
        // image_url için kart-uygun görsel: curated > heroImage > logo
        $cardImage = !empty($curated) ? $curated[0]['url'] : ($heroImage ?: null);

        $updateData = [
            'content_blocks' => $blocks,
            'last_enriched_at' => now(),
        ];
        if ($cardImage) {
            $updateData['image_url'] = $cardImage;
        }
        $uni->update($updateData);

        return [
            'success' => true,
            'blocks_count' => count($blocks),
            'sources' => [
                'wikipedia_languages' => array_keys($wikiData),
                'hero_image' => $heroImage,
                'programs' => $programs,
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
