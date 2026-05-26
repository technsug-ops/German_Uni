<?php

namespace App\Services\Enrichment;

use App\Models\FieldOfStudy;
use App\Models\Program;
use App\Models\University;
use App\Services\Content\AlmanyaUniForumSearch;
use App\Services\Content\CommunityInsightsService;

/**
 * Alan (FieldOfStudy) için Wikipedia + AI + community-aware enrichment.
 * Şehir/üni servisinin aynı pattern'i — programlar, üniler, topluluk soruları, forum konuları.
 */
class FieldEnrichmentService
{
    public function __construct(
        private WikipediaExtract $wiki,
        private AiContentBlockGenerator $ai,
        private CommunityInsightsService $community,
        private AlmanyaUniForumSearch $auForum,
    ) {}

    public function enrich(FieldOfStudy $field, bool $force = false): array
    {
        if (!$force && $field->last_enriched_at && $field->last_enriched_at->diffInDays(now()) < 30) {
            return ['success' => false, 'error' => 'Yakın zamanda enrich edildi'];
        }

        // 1. Wikipedia DE + EN — alan adı (Türkçe yerine Almanca daha kaliteli sonuç)
        $wikiData = [];
        $heroImage = null;
        $sourceText = '';
        foreach (['de', 'en'] as $lang) {
            $r = $this->wiki->fetchByTitle($field->name_de, $lang);
            if ($r) {
                $wikiData[$lang] = $r;
                if (!empty($r['extract'])) {
                    $sourceText .= "\n\n[Wikipedia $lang]\n" . $r['extract'];
                }
                if (!$heroImage && !empty($r['thumbnail_url'])) {
                    $heroImage = $r['thumbnail_url'];
                }
            }
        }

        // 2. DB context — bu alanda kaç program, kaç üni, top örnekler
        $programCount = Program::where('field_of_study_id', $field->id)->where('is_active', 1)->count();
        $programEn = Program::where('field_of_study_id', $field->id)->where('is_active', 1)
            ->whereIn('language', ['en', 'both'])->count();
        $bachelorCount = Program::where('field_of_study_id', $field->id)->where('is_active', 1)
            ->where('degree', 'bachelor')->count();
        $masterCount = Program::where('field_of_study_id', $field->id)->where('is_active', 1)
            ->where('degree', 'master')->count();

        $topUnis = University::whereHas('programs', fn ($q) => $q->where('field_of_study_id', $field->id)->where('is_active', 1))
            ->where('is_active', 1)
            ->orderByDesc('student_count')
            ->take(8)
            ->pluck('name_de')
            ->toArray();

        $topPrograms = Program::where('field_of_study_id', $field->id)
            ->where('is_active', 1)
            ->orderBy('name_de')
            ->take(10)
            ->pluck('name_de')
            ->toArray();

        $context = "Alan: {$field->name_tr} (DE: {$field->name_de}, EN: {$field->name_en})\n";
        $context .= "Bu alanda program sayısı: {$programCount} ({$programEn} İngilizce)\n";
        $context .= "Bachelor: {$bachelorCount}, Master: {$masterCount}\n";
        if ($topUnis) {
            $context .= 'Bu alanı sunan top üniler: ' . implode(', ', $topUnis) . "\n";
        }
        if ($topPrograms) {
            $context .= 'Örnek programlar: ' . implode(', ', array_slice($topPrograms, 0, 5));
        }

        // 3. Community insights — alan TR + DE adı için
        $entityForCommunity = $field->name_tr . ' ' . $field->name_de;
        $insights = $this->community->getInsightsFor($entityForCommunity, tgLimit: 15, forumLimit: 6);
        $communityContext = $this->community->formatForPrompt($insights);

        // 4. AI üret
        $seoGaps = (array) ($field->getAttribute('_seo_gaps') ?? []);
        $result = $this->ai->generate(
            'alan',
            $field->name_tr,
            $context . $communityContext,
            $sourceText ?: "Akademik alan: {$field->name_tr} ({$field->name_de})",
            $seoGaps
        );

        if (!$result['success']) {
            return ['success' => false, 'error' => $result['error']];
        }

        $blocks = $result['blocks'];

        // Hero
        if ($heroImage) {
            array_unshift($blocks, [
                'type' => 'hero',
                'image_url' => $heroImage,
                'alt' => $field->name_de,
                'source' => 'wikipedia',
            ]);
        }

        // Gallery
        $galleryImages = $this->wiki->fetchImages($field->name_de, 'de', 40);
        if (count($galleryImages) < 5) {
            $galleryImages = array_merge($galleryImages, $this->wiki->fetchImages($field->name_de, 'en', 40));
        }
        $curated = $this->wiki->curateGallery($galleryImages, 8);
        if (!empty($curated)) {
            $blocks[] = [
                'type' => 'gallery',
                'h' => "{$field->name_tr} — Görseller",
                'items' => $curated,
                'source' => 'wikipedia',
            ];
        }

        // Programs summary
        if ($programCount > 0) {
            $blocks[] = [
                'type' => 'programs_summary',
                'h' => "{$field->name_tr} alanında programlar",
                'total' => $programCount,
                'bachelor' => $bachelorCount,
                'master' => $masterCount,
                'phd' => Program::where('field_of_study_id', $field->id)->where('is_active', 1)->where('degree', 'phd')->count(),
            ];
        }

        // DeutschStudent forum konuları
        if (!empty($insights['forum_topics_with_url'])) {
            $blocks[] = [
                'type' => 'related_forum_topics',
                'h' => "{$field->name_tr} hakkında topluluk tartışmaları",
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
        $auTopics = $this->auForum->search($field->name_tr, 5);
        if (!empty($auTopics)) {
            $blocks[] = [
                'type' => 'almanyauni_forum_topics',
                'h' => "{$field->name_tr} — AlmanyaUni Forumunda",
                'items' => $auTopics,
                'cta_url' => '/forum/',
            ];
        }

        // External links
        $links = [];
        foreach (['de', 'en'] as $lang) {
            if (!empty($wikiData[$lang]['source_url'])) {
                $links[] = [
                    'label' => 'Wikipedia (' . strtoupper($lang) . ')',
                    'url' => $wikiData[$lang]['source_url'],
                    'type' => 'wikipedia',
                ];
            }
        }
        if ($links) {
            $blocks[] = ['type' => 'external_links', 'h' => 'Faydalı Linkler', 'items' => $links];
        }

        // Schema.org
        $blocks[] = [
            'type' => 'schema_jsonld',
            'data' => [
                '@context' => 'https://schema.org',
                '@type' => 'EducationalOccupationalCredential',
                'name' => $field->name_tr,
                'description' => collect($blocks)->firstWhere('type', 'intro')['body_md'] ?? '',
                'image' => $heroImage,
                'sameAs' => collect($wikiData)->pluck('source_url')->filter()->values()->toArray(),
            ],
        ];

        $cardImage = !empty($curated) ? $curated[0]['url'] : $heroImage;

        $field->update([
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
                'programs' => $programCount,
                'community' => [
                    'tg_questions' => count($insights['telegram_questions'] ?? []),
                    'forum_titles' => count($insights['forum_titles'] ?? []),
                ],
            ],
            'tokens' => $result['tokens'],
        ];
    }
}
