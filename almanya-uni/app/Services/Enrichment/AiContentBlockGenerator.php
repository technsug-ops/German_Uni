<?php

namespace App\Services\Enrichment;

use Illuminate\Support\Facades\Http;

/**
 * Wikipedia extract (DE/EN) + ek context → Türkçe SEO-uyumlu content block'lar üretir.
 *
 * Çıktı: blocks array [{type, h, body_md, ...}, ...]
 */
class AiContentBlockGenerator
{
    private const API_BASE = 'https://generativelanguage.googleapis.com/v1beta/models/';
    private const MODEL = 'gemini-2.5-flash';

    public function isConfigured(): bool
    {
        return !empty(config('services.gemini.key'));
    }

    /**
     * @return array{success: bool, blocks?: array, tokens?: array, error?: string}
     */
    public function generate(string $entityType, string $name, string $contextPrompt, string $sourceText, array $seoGaps = []): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Gemini API key yok'];
        }

        $prompt = $this->buildPrompt($entityType, $name, $contextPrompt, $sourceText, $seoGaps);

        try {
            $resp = Http::asJson()
                ->timeout(180)
                ->withHeaders(['x-goog-api-key' => config('services.gemini.key')])
                ->retry(2, 3000)
                ->post(self::API_BASE . self::MODEL . ':generateContent', [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'temperature' => 0.5,
                        'maxOutputTokens' => 12288,
                        'topP' => 0.95,
                        'responseMimeType' => 'application/json',
                    ],
                ]);

            if (!$resp->ok()) {
                return ['success' => false, 'error' => 'HTTP ' . $resp->status() . ': ' . substr($resp->body(), 0, 200)];
            }

            $data = $resp->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $usage = $data['usageMetadata'] ?? [];

            $parsed = json_decode($text, true);
            if (!is_array($parsed) || empty($parsed['blocks'])) {
                $clean = preg_replace('/^```(?:json)?\s*|\s*```$/u', '', trim($text));
                $parsed = json_decode($clean, true);
            }

            if (!is_array($parsed) || empty($parsed['blocks'])) {
                return ['success' => false, 'error' => 'JSON parse fail. Raw: ' . substr($text, 0, 300)];
            }

            return [
                'success' => true,
                'blocks' => $parsed['blocks'],
                'tokens' => [
                    'input' => $usage['promptTokenCount'] ?? 0,
                    'output' => $usage['candidatesTokenCount'] ?? 0,
                ],
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => substr($e->getMessage(), 0, 300)];
        }
    }

    private function buildPrompt(string $entityType, string $name, string $contextPrompt, string $sourceText, array $seoGaps = []): string
    {
        $gapSection = '';
        if (! empty($seoGaps)) {
            $gapList = '- ' . implode("\n- ", array_slice($seoGaps, 0, 25));
            $gapSection = <<<GAP

🚨 SEO AUDIT — MUTLAKA KAPSANMASI GEREKEN KONULAR:
Aşağıdaki konular topluluk verisinde (Forum + Telegram) sıkça konuşulan ama mevcut sayfada eksik. Üretilen bloklarda (özellikle section + faq + intro) bu konulara doğal olarak değin:
$gapList

GAP;
        }


        $entitySpecific = $entityType === 'şehir'
            ? <<<CITY
ŞEHİR İÇİN ÖZEL BLOKLAR (uygunsa mutlaka ekle):
- "cost_of_living": Aylık tahmini yaşam maliyeti kartları. Wikipedia/genel bilgiden tahmini EUR aralık. Belirsizse "ortalama" yaz, uydurma.
- "places": Gezilecek/öğrenciye uygun yerler. Kütüphaneler, müzeler, meşhur meydanlar, parklar, kafeler. Her birinin tipini belirt (library/museum/square/park/landmark).
- "student_culture": Öğrenci yaşamı, gece hayatı, etkinlikler, ulaşım, bisiklet kültürü vs.
- "table": Karşılaştırmalı veriler için (örn. eyaletlere göre ücret, mahallelere göre kira).
CITY
            : <<<UNI
ÜNİVERSİTE İÇİN ÖZEL BLOKLAR (uygunsa mutlaka ekle):
- "places": Kampüsler, fakülteler, kütüphane, ünlü binalar.
- "student_culture": Öğrenci kulüpleri, etkinlikler, mezunlar, kampüs yaşamı.
- "table": Karşılaştırmalı veriler (örn. bölümlere göre öğrenci sayısı, fakülteler).
- "cost_of_living": Bu üniversite şehrindeki tahmini öğrenci yaşam maliyeti.
UNI;

        return <<<TXT
Sen AlmanyaUni içerik editörüsün. Türk öğrencilere hitap eden, SEO-uyumlu ve doğal Türkçe içerik bloğu üretiyorsun.

VARLIK TÜRÜ: $entityType
İSİM: $name

EK CONTEXT (siteden DB verisi):
$contextPrompt
$gapSection
KAYNAK METİN (Wikipedia + ek):
$sourceText

GÖREV: Bu varlık için zengin, çok-blok bir sayfa üret. JSON yapısı:

{
  "blocks": [
    {"type": "intro", "h": null, "body_md": "150-200 kelimelik Türkçe açılış. SEO keyword'leri doğal şekilde."},
    {"type": "quick_facts", "h": "Hızlı Bakış", "items": [{"label": "Nüfus", "value": "..."}, ...]},
    {"type": "section", "h": "H2 başlık", "body_md": "200-400 kelime markdown."},
    {"type": "cost_of_living", "h": "Aylık Yaşam Maliyeti (Tahmini)", "currency": "EUR", "items": [{"label": "Kira (paylaşımlı)", "amount": "300-450", "note": "WG odası"}, {"label": "Yemek", "amount": "200-300"}, {"label": "Ulaşım (Semesterticket)", "amount": "0-50"}], "total": "750-1100"},
    {"type": "places", "h": "Gezilecek Yerler ve Öğrenci Mekânları", "items": [{"name": "...", "type": "library|museum|square|park|landmark", "description": "1-2 cümle", "url": null}, ...]},
    {"type": "student_culture", "h": "Öğrenci Yaşamı ve Kültür", "body_md": "Öğrenci hayatı anlatımı (markdown).", "highlights": ["Bisiklet kültürü güçlü", "Semesterticket ile tüm BW ücretsiz", ...]},
    {"type": "table", "h": "Karşılaştırma", "headers": ["Sütun 1", "Sütun 2"], "rows": [["a", "b"], ["c", "d"]], "caption": "opsiyonel"},
    {"type": "video", "h": "Video", "platform": "youtube", "url": null, "title": "Önerilen video başlığı (kullanıcı kendi ekleyebilir)", "description": "Bu sayfaya hangi video uygun olur açıklaması"},
    {"type": "faq", "h": "Sıkça Sorulanlar", "items": [{"q": "...", "a": "..."}, ...]},
    {"type": "cta", "h": null, "body_md": "Kısa CTA."}
  ]
}

$entitySpecific

KURALLAR:
- Türkçe doğal dil (Almanca terimler parantez içinde açıklansın: Sperrkonto, Anmeldung, Semesterticket)
- HALÜSİNASYON YOK — emin değilsen "resmi sayfadan doğrula" de
- intro + quick_facts (5-8 öğe) + en az 3 section mutlaka
- cost_of_living: belirsiz aralık ver, uydurma. Bilgi yoksa atla.
- places: 4-8 yer öner. Sadece bilinen, gerçek yerler.
- student_culture: 100-200 kelime + 3-5 highlight bullet
- table: ancak gerçek karşılaştırılabilir veri varsa
- video: url null olabilir (placeholder), kullanıcı sonra ekler. AMA description ve başlık öneri ver.
- faq 4-7 SORU. Yukarıda "TOPLULUK İÇGÖRÜLERİ" varsa: oradaki Telegram/Forum sorularını ÖNCELİKLE FAQ olarak kullan. Bu Türk öğrencilerin gerçekten merak ettiği şeyler — uydurma değil.
- intro/section'larda forum ısı haritası ve trending kalıpları doğal şekilde işle (örn. "Türk öğrenciler en çok şunu sorar: ...")
- cta sona
- ÇIKTI: SADECE JSON, markdown wrap YOK
TXT;
    }
}
