<?php

namespace App\Console\Commands;

use App\Models\Profession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * BERUFENET info_fields verilerini TR + EN'ye çevirir.
 *
 * Strateji: Tek bir Gemini çağrısında 6 yararlı başlığı her iki dile çevir.
 * Bu sayede her meslek için 1 API çağrısı yeterli, 3.560 meslek × 1 çağrı.
 * Sleep=2 ile ~2-3 saat sürer, maliyet ~$3-5.
 *
 * Hedef alanlar (BERUFENET):
 *  - Aufgaben und Tätigkeiten kompakt → görev özeti
 *  - Zugang zur Tätigkeit            → mesleğe erişim koşulları
 *  - Verdienst/Einkommen             → maaş
 *  - Arbeitsorte                     → çalışma yerleri
 *  - Arbeitsbereiche/Branchen        → sektörler
 *  - Weiterbildung (beruflicher Aufstieg) → ilerleme yolu
 */
class ProfessionsTranslateInfoFields extends Command
{
    private const TARGET_KEYS = [
        'Aufgaben und Tätigkeiten kompakt',
        'Zugang zur Tätigkeit',
        'Verdienst/Einkommen',
        'Arbeitsorte',
        'Arbeitsbereiche/Branchen',
        'Weiterbildung (beruflicher Aufstieg)',
    ];

    private const SHORT_KEYS = [
        'Aufgaben und Tätigkeiten kompakt'     => 'tasks',
        'Zugang zur Tätigkeit'                 => 'access',
        'Verdienst/Einkommen'                  => 'salary',
        'Arbeitsorte'                          => 'workplace',
        'Arbeitsbereiche/Branchen'             => 'sectors',
        'Weiterbildung (beruflicher Aufstieg)' => 'progression',
    ];

    protected $signature = 'professions:translate-info-fields
        {--limit=50 : Bu çalıştırmada işlenecek meslek sayısı (0 = sınırsız)}
        {--sleep=2 : Gemini rate-limit için bekleme (saniye)}
        {--force : info_fields_tr dolu olsa da yeniden çevir}
        {--slug= : Tek bir meslek (slug)}
        {--dry-run : Önizleme, kaydetme}';

    protected $description = 'BERUFENET info_fields\'\'ı 6 yararlı başlık için TR + EN\'ye çevirir.';

    public function handle(): int
    {
        $apiKey = config('services.gemini.key');
        if (! $apiKey) {
            $this->error('GEMINI_API_KEY eksik');
            return self::FAILURE;
        }

        $q = Profession::query()->whereNotNull('info_fields')->where('info_fields', '!=', '[]');

        if ($slug = $this->option('slug')) {
            $q->where('slug', $slug);
        } elseif (! $this->option('force')) {
            $q->whereNull('info_fields_tr');
        }

        $limit = (int) $this->option('limit');
        if ($limit > 0) {
            $q->limit($limit);
        }
        $q->orderBy('id');

        $items = $q->get();
        $total = $items->count();

        if ($total === 0) {
            $this->info('Hedef meslek yok — hepsi çevrilmiş olabilir.');
            return self::SUCCESS;
        }

        $this->info("📚 {$total} meslek info_fields çevrilecek (sleep: " . $this->option('sleep') . 's)');
        $this->newLine();

        $success = 0; $failed = 0; $skipped = 0;
        $start = now();

        foreach ($items as $i => $p) {
            $this->line(sprintf('[%d/%d] %s', $i + 1, $total, mb_substr($p->name_de, 0, 70)));

            $sourceFields = $this->extractSource($p);
            if (empty($sourceFields)) {
                $this->warn('  ⚠️ Kaynak alanların hiçbiri yok — atlandı');
                $skipped++;
                continue;
            }

            $result = $this->callGemini($sourceFields, $p->name_de, $apiKey);

            if (! $result) {
                $failed++;
                continue;
            }

            if ($this->option('dry-run')) {
                $this->info('  ✅ ' . count($result['tr']) . ' TR + ' . count($result['en']) . ' EN alan');
                $success++;
            } else {
                $p->update([
                    'info_fields_tr' => $result['tr'],
                    'info_fields_en' => $result['en'],
                ]);
                $this->info('  ✅ TR: ' . count($result['tr']) . ' · EN: ' . count($result['en']) . ' alan');
                $success++;
            }

            if ($i < $total - 1) {
                sleep((int) $this->option('sleep'));
            }
        }

        $duration = $start->diffInSeconds(now());
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("✅ {$success} başarılı, ⏭️ {$skipped} atlandı (kaynak yok), ❌ {$failed} başarısız, ⏱️ {$duration}s");

        return $failed > 0 && $success === 0 ? self::FAILURE : self::SUCCESS;
    }

    private function extractSource(Profession $p): array
    {
        $out = [];
        $info = $p->info_fields ?: [];
        foreach (self::TARGET_KEYS as $key) {
            $val = $info[$key] ?? null;
            if ($val && mb_strlen(trim($val)) > 5) {
                $out[self::SHORT_KEYS[$key]] = mb_substr($val, 0, 800);
            }
        }
        return $out;
    }

    private function callGemini(array $source, string $nameDe, string $apiKey): ?array
    {
        $sourceJson = json_encode($source, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $prompt = <<<TXT
Sen AlmanyaUni'nin meslek editörüsün. BERUFENET'ten alınan ALMANCA meslek bilgilerini Türkçe ve İngilizce'ye çeviriyorsun.

ALMAN MESLEK ADI: {$nameDe}

KAYNAK (Almanca, JSON):
{$sourceJson}

GÖREV: Her alanı hem Türkçe hem İngilizce'ye çevir. Doğal, akıcı, kısa cümlelerle.

KURALLAR:
- Halüsinasyon yok — kaynakta olmayan rakam/süre/maaş yazma
- Almanca terim varsa parantez içinde aç (örn. "Ausbildung (mesleki eğitim)" / "Ausbildung (vocational training)")
- "können nicht getroffen werden" gibi standart "rakam söylenemez" cevapları → "Salary data varies, please check BERUFENET" / "Maaş bilgisi değişken, BERUFENET'i kontrol edin" şeklinde sade ver
- Lise mezunu Türk öğrenci anlayacak sade dil
- Madde imi yok, kısa paragraf — her alan 1-3 cümle yeterli
- Almanya'ya özgü kavramları aynen koru (Ausbildung, Berufsschule, Studium, IHK, BERUFENET)

ÇIKTI: TAM olarak bu JSON formatında ver, başka açıklama yok:
{
  "tr": {
    "tasks": "...",
    "access": "...",
    "salary": "...",
    "workplace": "...",
    "sectors": "...",
    "progression": "..."
  },
  "en": {
    "tasks": "...",
    "access": "...",
    "salary": "...",
    "workplace": "...",
    "sectors": "...",
    "progression": "..."
  }
}

Kaynakta olmayan alanı boş string "" olarak ver. Sadece JSON, başka şey yok.
TXT;

        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $resp = Http::asJson()
                    ->timeout(120)
                    ->withHeaders(['x-goog-api-key' => $apiKey])
                    ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent', [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => [
                            'temperature'     => 0.4,
                            'maxOutputTokens' => 8000,
                            'responseMimeType' => 'application/json',
                        ],
                    ]);

                if (! $resp->ok()) {
                    if ($attempt < 2) {
                        sleep(5);
                        continue;
                    }
                    $this->error('  HTTP ' . $resp->status() . ' — ' . mb_substr($resp->body(), 0, 200));
                    return null;
                }

                $data = $resp->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $parsed = $this->parseJson($text);
                if ($parsed) {
                    return $parsed;
                }
                if ($attempt < 2) {
                    sleep(3);
                    continue;
                }
                $this->error('  Parse fail: ' . mb_substr($text, 0, 200));
                return null;
            } catch (\Throwable $e) {
                if ($attempt < 2) {
                    sleep(5);
                    continue;
                }
                $this->error('  ' . mb_substr($e->getMessage(), 0, 150));
                return null;
            }
        }

        return null;
    }

    private function parseJson(string $text): ?array
    {
        $text = trim($text);
        if (preg_match('/```(?:json)?\s*\n?(.+)\n?```/s', $text, $m)) {
            $text = trim($m[1]);
        }

        $data = json_decode($text, true);
        if (! is_array($data) || ! isset($data['tr']) || ! isset($data['en'])) {
            return null;
        }

        return [
            'tr' => array_filter((array) $data['tr'], fn ($v) => is_string($v) && $v !== ''),
            'en' => array_filter((array) $data['en'], fn ($v) => is_string($v) && $v !== ''),
        ];
    }
}
