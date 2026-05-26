<?php

namespace App\Services\Seo;

use App\Models\City;
use App\Models\FieldOfStudy;
use App\Models\Post;
use App\Models\Program;
use App\Models\SeoAudit;
use App\Models\University;
use App\Services\Content\CommunityInsightsService;
use App\Services\Enrichment\AiContentBlockGenerator;
use App\Services\Enrichment\CityEnrichmentService;
use App\Services\Enrichment\FieldEnrichmentService;
use App\Services\Enrichment\UniversityEnrichmentService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SEO Audit "Aktivasyon" — audit + community insights + AI ile içerik üretir,
 * entity'nin content_blocks'una append eder. Sayfada anında görünür.
 *
 * Strateji:
 *   1. Mevcut enrichment service'i varsa onu zorla çalıştır (entity bütünsel yenilenir)
 *   2. Yoksa (program/post) targeted AI çağrısı yap + gap'leri kapsayan block'lar üret
 */
class SeoActivationService
{
    private const API_BASE = 'https://generativelanguage.googleapis.com/v1beta/models/';
    private const MODEL = 'gemini-2.5-flash';

    public function __construct(
        private CommunityInsightsService $community,
        private CityEnrichmentService $cityEnrich,
        private UniversityEnrichmentService $uniEnrich,
        private FieldEnrichmentService $fieldEnrich,
    ) {}

    public function activate(SeoAudit $audit, string $entityTable, string $slug): array
    {
        try {
            $entity = $this->findEntity($entityTable, $slug);
            if (! $entity) {
                return ['success' => false, 'error' => "Entity bulunamadı: $entityTable/$slug"];
            }

            $gaps = (array) $audit->high_value_gaps;
            $aiSugg = $audit->ai_suggestions;

            // Entity tipine göre işle
            return match ($entityTable) {
                'cities'        => $this->activateCity($entity, $audit, $gaps),
                'universities'  => $this->activateUniversity($entity, $audit, $gaps),
                'fields'        => $this->activateField($entity, $audit, $gaps),
                'programs'      => $this->activateProgram($entity, $audit, $gaps),
                'posts'         => $this->activatePost($entity, $audit, $gaps),
                default         => ['success' => false, 'error' => "Tip desteklenmiyor: $entityTable"],
            };
        } catch (\Throwable $e) {
            Log::error('SeoActivation failed', ['audit_id' => $audit->id, 'error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function findEntity(string $table, string $slug)
    {
        return match ($table) {
            'cities'       => City::where('slug', $slug)->first(),
            'universities' => University::where('slug', $slug)->first(),
            'fields'       => FieldOfStudy::where('slug', $slug)->first(),
            'programs'     => Program::where('slug', $slug)->first(),
            'posts'        => Post::where('slug', $slug)->first(),
            default        => null,
        };
    }

    private function activateCity(City $city, SeoAudit $audit, array $gaps): array
    {
        // Mevcut CityEnrichmentService zaten community insights kullanıyor.
        // Audit gap'lerini "extra hedef konular" olarak inject etmek için
        // City modelinin geçici bir context attribute'unu kullanırız.
        $city->setAttribute('_seo_gaps', array_keys($gaps));
        $r = $this->cityEnrich->enrich($city, force: true);

        if ($r['success'] ?? false) {
            return [
                'success' => true,
                'summary' => "Şehir '{$city->name_de}' yeniden enrich edildi. " . ($r['blocks_count'] ?? 0) . " block · " . ($r['tokens']['total'] ?? '?') . " token. Audit'in eksik " . count($gaps) . " keyword'ü context'e dahil edildi.",
            ];
        }
        return ['success' => false, 'error' => $r['error'] ?? 'Enrichment hata'];
    }

    private function activateUniversity(University $uni, SeoAudit $audit, array $gaps): array
    {
        $uni->setAttribute('_seo_gaps', array_keys($gaps));
        $r = $this->uniEnrich->enrich($uni, force: true);

        if ($r['success'] ?? false) {
            return [
                'success' => true,
                'summary' => "Üniversite '{$uni->name_de}' enrich edildi. " . ($r['blocks_count'] ?? 0) . " block. Eksik " . count($gaps) . " konu hedeflendi.",
            ];
        }
        return ['success' => false, 'error' => $r['error'] ?? 'Enrichment hata'];
    }

    private function activateField(FieldOfStudy $field, SeoAudit $audit, array $gaps): array
    {
        $field->setAttribute('_seo_gaps', array_keys($gaps));
        $r = $this->fieldEnrich->enrich($field, force: true);

        if ($r['success'] ?? false) {
            return [
                'success' => true,
                'summary' => "Alan '{$field->name_tr}' enrich edildi. " . ($r['blocks_count'] ?? 0) . " block.",
            ];
        }
        return ['success' => false, 'error' => $r['error'] ?? 'Enrichment hata'];
    }

    private function activateProgram(Program $program, SeoAudit $audit, array $gaps): array
    {
        // Program için yeni AI call (dedicated enrichment yok)
        $blocks = $this->generateSeoBlocks(
            entityType: 'program',
            name: $program->name_tr ?: $program->name_de,
            extraContext: "Üniversite: " . $program->university?->name_de . " · Şehir: " . $program->university?->city?->name_de . " · Derece: " . $program->degree . " · Dil: " . $program->language,
            gaps: $gaps,
        );

        if (! $blocks) {
            return ['success' => false, 'error' => 'AI block üretemedi'];
        }

        // description_tr'e ekle (programs.content_blocks yoksa)
        $existingDesc = $program->description_tr ?? '';
        $newContent = $existingDesc . "\n\n" . $blocks['markdown'];
        $program->update(['description_tr' => $newContent]);

        return [
            'success' => true,
            'summary' => "Program '{$program->name_tr}' için " . ($blocks['section_count'] ?? 0) . " yeni section description_tr'ye eklendi.",
        ];
    }

    private function activatePost(Post $post, SeoAudit $audit, array $gaps): array
    {
        $blocks = $this->generateSeoBlocks(
            entityType: 'blog',
            name: $post->title,
            extraContext: "Mevcut özet: " . ($post->excerpt ?? '—'),
            gaps: $gaps,
        );

        if (! $blocks) {
            return ['success' => false, 'error' => 'AI block üretemedi'];
        }

        // Post tablosunda 'body' yok → content_md. Observer content_html'i yeniden üretir.
        $newContent = ($post->content_md ?? '') . "\n\n" . $blocks['markdown'];
        $post->update(['content_md' => $newContent]);

        return [
            'success' => true,
            'summary' => "Blog yazısı '{$post->title}'nin sonuna " . ($blocks['section_count'] ?? 0) . " yeni section eklendi (content_md güncellendi).",
        ];
    }

    /**
     * VERİ BANKASI context: gaps + entity adında geçen kelimeleri DB'de ara
     * (şehir / üniversite / burs / program / meslek) → AI'ya iç-link verilebilir kayıt listesi.
     */
    private function buildDbContext(array $gapTerms, string $name): string
    {
        $haystack = mb_strtolower($name . ' ' . implode(' ', $gapTerms));
        $lines = [];

        // Şehirler
        $cities = \App\Models\City::where('is_active', 1)
            ->get(['name_de', 'slug'])
            ->filter(fn ($c) => str_contains($haystack, mb_strtolower($c->name_de)))
            ->take(5);
        foreach ($cities as $c) {
            $lines[] = "- Şehir: {$c->name_de} → /cities/{$c->slug}";
        }

        // Üniversiteler (isim uzun olduğu için kısmi eşleşme: short_name veya tam ad geçişi)
        $unis = \App\Models\University::where('is_active', 1)
            ->get(['name_de', 'short_name', 'slug'])
            ->filter(function ($u) use ($haystack) {
                if ($u->short_name && mb_strlen($u->short_name) >= 3 && str_contains($haystack, mb_strtolower($u->short_name))) return true;
                return str_contains($haystack, mb_strtolower($u->name_de));
            })
            ->take(6);
        foreach ($unis as $u) {
            $lines[] = "- Üniversite: {$u->name_de} → /universities/{$u->slug}";
        }

        // Burslar — gaps'te burs/daad geçiyorsa burs sayfalarına genel link
        if (str_contains($haystack, 'daad')) {
            $lines[] = "- DAAD Bursları → /scholarships/daad";
        } elseif (str_contains($haystack, 'burs') || str_contains($haystack, 'scholarship')) {
            $lines[] = "- Tüm Burslar → /scholarships";
        }

        // İç araç linkleri (gaps'e göre)
        $toolMap = [
            'sperrkonto'   => ['Bütçe Planlayıcı', '/tools/budget-planner'],
            'bloke hesap'  => ['Bütçe Planlayıcı', '/tools/budget-planner'],
            'yaşam mali'   => ['Yaşam Maliyeti Hesaplayıcı', '/tools/cost-of-living'],
            'maliyet'      => ['Yaşam Maliyeti Hesaplayıcı', '/tools/cost-of-living'],
            'vize'         => ['Vize Maliyeti Hesaplayıcı', '/tools/visa-cost'],
            'deadline'     => ['Başvuru Takvimi', '/tools/deadlines'],
            'meslek'       => ['Kariyer Pusulası', '/tools/career-compass'],
            'kariyer'      => ['Kariyer Pusulası', '/tools/career-compass'],
            'yurt'         => ['Yurt Sağlayıcılar', '/housing/providers'],
            'konaklama'    => ['Yurt Sağlayıcılar', '/housing/providers'],
        ];
        $addedTools = [];
        foreach ($toolMap as $kw => [$label, $url]) {
            if (str_contains($haystack, $kw) && ! in_array($url, $addedTools)) {
                $lines[] = "- Araç: {$label} → {$url}";
                $addedTools[] = $url;
            }
        }

        if (empty($lines)) return '';

        return "\n🔗 İLGİLİ VERİTABANI KAYITLARI (içeriğe doğal iç link ver — sadece bunları kullan):\n" . implode("\n", array_slice($lines, 0, 12)) . "\n";
    }

    /**
     * Generic AI block generator — audit gap'lerini hedef olarak kullanır.
     */
    private function generateSeoBlocks(string $entityType, string $name, string $extraContext, array $gaps): ?array
    {
        $key = config('services.gemini.key');
        if (! $key || empty($gaps)) return null;

        // Community insights (Forum + Telegram)
        $insights = $this->community->getInsightsFor($name, tgLimit: 10, forumLimit: 6);
        $insightsText = $this->community->formatForPrompt($insights);

        $gapList = "- " . implode("\n- ", array_slice(array_keys($gaps), 0, 20));

        // VERİ BANKASI: gaps + entity adında geçen şehir/üni/burs/program'ları DB'de bul.
        // AI bunlara doğal iç link verebilsin + faktüel kalsın.
        $dbContext = $this->buildDbContext(array_keys($gaps), $name);

        $prompt = <<<TXT
AlmanyaUni (Türk öğrencilere Almanya rehberi) — bir "$entityType" sayfasına SEO güçlendirme için yeni bölümler üretiyorsun.

ENTITY: $name
EK BAĞLAM: $extraContext

🚨 MUTLAKA KAPSANMASI GEREKEN KONULAR (topluluk verisinde sıkça konuşulan, ama sayfada YOK):
$gapList

📊 TOPLULUK İÇGÖRÜLERİ (Forum + Telegram'dan):
$insightsText
$dbContext
GÖREV: 4-6 yeni Markdown section yaz. Her section:
- ## H2 başlık (Türkçe, keyword içeren)
- 200-400 kelime gövde (kullanıcı sorularını cevaplayan)
- En az 1 listeli içerik
- Topluluk içgörülerine doğal entegre referans

KURALLAR:
- Tahmin/halüsinasyon YOK. Bilmiyorsan "ilgili kurumdan doğrula" de.
- Resmi linkler verirken sadece üst-domain (örn: stw.berlin) — derin URL uydurma.
- Türk öğrenci perspektifi (vize, denklik, dil seviyesi, Sperrkonto vb.)
- Hiçbir promosyon dili kullanma, faktüel + net + yardımcı.
- VERİTABANI KAYITLARI bölümünde verilen iç sayfalara markdown link ver: [İsim](/url). Sadece listede verilenleri kullan, URL uydurma.

ÇIKTI: Sadece Markdown. Başlangıçta yorum/açıklama yok.
TXT;

        try {
            $resp = Http::asJson()
                ->timeout(180)
                ->withHeaders(['x-goog-api-key' => $key])
                ->retry(2, 3000)
                ->post(self::API_BASE . self::MODEL . ':generateContent', [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.5, 'maxOutputTokens' => 6000, 'topP' => 0.95],
                ]);

            if (! $resp->ok()) return null;
            $data = $resp->json();
            $markdown = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if (! $markdown) return null;

            $sectionCount = preg_match_all('/^##\s/m', $markdown);

            return [
                'markdown' => trim($markdown),
                'section_count' => $sectionCount ?: 0,
            ];
        } catch (\Throwable $e) {
            Log::warning('SeoActivation AI fail', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
