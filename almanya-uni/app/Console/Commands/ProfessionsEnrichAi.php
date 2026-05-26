<?php

namespace App\Console\Commands;

use App\Models\Profession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * BERUFENET'ten alınan meslekler için Türkçe açıklama (description_tr) ve
 * adlandırma (name_tr) üretir. Boş olanlar zenginleştirilir (sadece name_de'den AI ile).
 *
 * Strateji:
 *  - description_tr boş ise hedeftir
 *  - Almanca prose varsa onu Türkçe'ye çevirir + Türk öğrenciye uyarlar
 *  - Yoksa name_de + kldb_code + cluster üzerinden AI ile yazı üretir
 */
class ProfessionsEnrichAi extends Command
{
    protected $signature = 'professions:enrich-ai
        {--limit=50 : Bu çalıştırmada işlenecek meslek sayısı}
        {--sleep=2 : Gemini rate-limit için bekleme (saniye)}
        {--only-empty : Sadece description_de + steckbrief boş olanları işle (en zayıf kayıtlar)}
        {--force : description_tr dolu olsa da yeniden üret}
        {--slug= : Tek bir meslek (slug)}
        {--dry-run : Önizleme, kaydetme}';

    protected $description = 'Meslekler için AI ile Türkçe açıklama ve isim üret';

    public function handle(): int
    {
        $apiKey = config('services.gemini.key');
        if (! $apiKey) {
            $this->error('GEMINI_API_KEY eksik');
            return self::FAILURE;
        }

        $q = Profession::query()->with('field');

        if ($slug = $this->option('slug')) {
            $q->where('slug', $slug);
        } else {
            if (! $this->option('force')) {
                $q->whereNull('description_tr');
            }
            if ($this->option('only-empty')) {
                $q->whereNull('description_de')->whereNull('steckbrief');
            }
            $q->orderByRaw('CASE WHEN description_de IS NOT NULL THEN 0 ELSE 1 END')
              ->orderBy('id')
              ->limit((int) $this->option('limit'));
        }

        $items = $q->get();
        $total = $items->count();

        if ($total === 0) {
            $this->info('Hedef meslek yok — hepsi enrich edilmiş olabilir.');
            return self::SUCCESS;
        }

        $this->info("📚 {$total} meslek AI ile zenginleştirilecek (sleep: " . $this->option('sleep') . 's)');
        $this->newLine();

        $success = 0; $failed = 0;
        $start = now();

        foreach ($items as $i => $p) {
            $label = $p->name_de . ($p->kldb_code ? " (KldB {$p->kldb_code})" : '');
            $this->line(sprintf('[%d/%d] %s', $i + 1, $total, mb_substr($label, 0, 70)));

            $payload = $this->buildPayload($p);
            $result = $this->callGemini($payload, $apiKey);

            if (! $result) {
                $failed++;
                continue;
            }

            if ($this->option('dry-run')) {
                $this->info('  ✅ ' . ($result['name_tr'] ?? '-'));
                $this->line('     ' . mb_substr($result['description_tr'], 0, 120) . '...');
                $success++;
            } else {
                $p->update([
                    'name_tr'        => $result['name_tr'] ?? $p->name_tr,
                    'description_tr' => $result['description_tr'],
                ]);
                $this->info('  ✅ ' . ($result['name_tr'] ?? '-') . ' · ' . mb_strlen($result['description_tr']) . ' karakter');
                $success++;
            }

            if ($i < $total - 1) {
                sleep((int) $this->option('sleep'));
            }
        }

        $duration = $start->diffInSeconds(now());
        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("✅ {$success} başarılı, ❌ {$failed} başarısız, ⏱️ {$duration}s");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function buildPayload(Profession $p): array
    {
        $typeLabels = [
            'ausbildung'    => 'Ausbildung (mesleki eğitim)',
            'studienberuf'  => 'Studienberuf (üniversite eğitimi gerektiren meslek)',
            'weiterbildung' => 'Weiterbildung (ileri eğitim/uzmanlık)',
            'grundberuf'    => 'Grundberuf (temel meslek)',
        ];

        return [
            'name_de'        => $p->name_de,
            'kldb'           => $p->kldb_code,
            'cluster'        => $p->cluster_label ?: $p->cluster,
            'type'           => $typeLabels[$p->type] ?? null,
            'field_tr'       => $p->field?->name_tr,
            'steckbrief'     => $p->clean_steckbrief,
            'description_de' => $p->description_de,
            'info_summary'   => $p->info_summary,
        ];
    }

    private function callGemini(array $payload, string $apiKey): ?array
    {
        $context = [];
        if ($payload['type'])           $context[] = "Meslek tipi: {$payload['type']}";
        if ($payload['field_tr'])       $context[] = "Alan: {$payload['field_tr']}";
        if ($payload['kldb'])           $context[] = "KldB Kodu: {$payload['kldb']}";
        if ($payload['cluster'])        $context[] = "Berufskurzgruppe: {$payload['cluster']}";
        if ($payload['steckbrief'])     $context[] = "Steckbrief (kısa Almanca veri):\n{$payload['steckbrief']}";
        if ($payload['description_de']) $context[] = "Resmi BERUFENET açıklaması (Almanca):\n" . mb_substr($payload['description_de'], 0, 2500);
        if (! empty($payload['info_summary'])) $context[] = "BERUFENET detay alanları (Almanca):\n" . $payload['info_summary'];
        $ctx = implode("\n\n", $context);

        $prompt = <<<TXT
Sen AlmanyaUni'nin meslek editörüsün. Türk öğrenciler/profesyoneller için Türkçe meslek tanıtımı yazıyorsun.

ALMAN MESLEK ADI: {$payload['name_de']}

KAYNAK BİLGİ:
{$ctx}

GÖREV: Bu Alman mesleği için TÜRKÇE tanıtım metni üret. Marker formatında ver:

[NAME_TR]
Türkçe karşılık. Birebir çeviri varsa onu kullan (örn. "Kimya Teknisyeni"). Yoksa anlamlı Türkçe karşılık ver. Eğer Türkçe'de tam karşılığı yoksa "Almanca adı + (Türkçe açıklaması)" formatında ver. Maks 80 karakter.

[DESCRIPTION_TR]
1. Cümle: Bu mesleğin Almanya'daki rolünü ve ne yaptığını özetle.
2-3. Cümle: Görev alanları, çalışma ortamı, kullandığı araçlar.
4-5. Cümle: Almanya'da hangi eğitim yolu (Ausbildung süresi, Studium derecesi, gereklilikler) ile bu mesleğe ulaşılır.
6. Cümle (varsa): Türk öğrenciler için ipucu — denklik, dil, iş bulma, maaş beklentisi.

KURALLAR:
- 200-400 kelime arası, akıcı Türkçe paragraf(lar)
- Halüsinasyon yok — kaynak bilgide olmayan spesifik rakam/süre/maaş yazma
- Almanca terim varsa parantez içinde aç (örn. "Ausbildung (3 yıllık mesleki eğitim programı)")
- Lise mezunu Türk öğrenci anlayacak sade dil — abartılı SEO yok
- Madde imi kullanma, paragraf olarak yaz
- "Türkiye'de", "ülkemizde" gibi taraf seçen ifadeler kullanma — nötr ol

ÇIKTI: Sadece [NAME_TR] ve [DESCRIPTION_TR] markerları, başka açıklama yok.
TXT;

        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $resp = Http::asJson()
                    ->timeout(120)
                    ->withHeaders(['x-goog-api-key' => $apiKey])
                    ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent', [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => [
                            'temperature'     => 0.5,
                            'maxOutputTokens' => 2048,
                        ],
                    ]);

                if (! $resp->ok()) {
                    if ($attempt < 2) {
                        sleep(5);
                        continue;
                    }
                    $this->error('  HTTP ' . $resp->status());
                    return null;
                }

                $data = $resp->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $parsed = $this->parseMarkers($text);
                if ($parsed) {
                    return $parsed;
                }
                if ($attempt < 2) {
                    sleep(3);
                    continue;
                }
                $this->error('  Parse fail: ' . mb_substr($text, 0, 150));
                return null;
            } catch (\Throwable $e) {
                if ($attempt < 2) {
                    sleep(5);
                    continue;
                }
                $this->error('  ' . mb_substr($e->getMessage(), 0, 100));
                return null;
            }
        }

        return null;
    }

    private function parseMarkers(string $text): ?array
    {
        $text = trim($text);
        if (preg_match('/```(?:markdown|md|text)?\s*\n?(.+)\n?```/s', $text, $m)) {
            $text = trim($m[1]);
        }

        $get = function (string $marker) use ($text): ?string {
            if (preg_match('/\[' . $marker . '\]\s*\n(.+?)(?=\n\[[A-Z_]+\]|\z)/s', $text, $m)) {
                return trim($m[1]);
            }
            return null;
        };

        $name = $get('NAME_TR');
        $desc = $get('DESCRIPTION_TR');

        if (! $desc || mb_strlen($desc) < 150) {
            return null;
        }

        return [
            'name_tr'        => $name ? mb_substr($name, 0, 200) : null,
            'description_tr' => mb_substr($desc, 0, 5000),
        ];
    }
}
