<?php

namespace App\Console\Commands;

use App\Models\Faq;
use App\Models\FaqTopic;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Topluluk soru havuzundan (Telegram + Forum) AI ile GENEL SSS üretir.
 *
 * Ham/kişisel soruları → genel, tekrar sorulabilir SSS'lere soyutlar + faktüel cevap.
 * Üretilenler is_published=FALSE (admin onayına) — kritik konularda kalite kontrolü için.
 *
 * php artisan faq:generate-ai --batch=20 --batches=1 [--publish] [--dry-run]
 */
class FaqGenerateAi extends Command
{
    protected $signature = 'faq:generate-ai
        {--batch=20 : Her AI çağrısında işlenecek ham soru}
        {--batches=1 : Kaç AI çağrısı yapılacak}
        {--publish : Üretilenleri direkt yayınla (varsayılan: taslak/moderasyon)}
        {--dry-run : Önizleme, kaydetme}';

    protected $description = 'Topluluk havuzundan AI ile genel SSS üret';

    private const MODEL = 'gemini-2.5-flash';
    private const API = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function handle(): int
    {
        $key = config('services.gemini.key');
        if (! $key) { $this->error('GEMINI_API_KEY yok'); return self::FAILURE; }

        $topics = FaqTopic::pluck('id', 'slug')->all();
        $topicList = FaqTopic::pluck('name', 'slug')->map(fn ($n, $s) => "$s = $n")->implode(', ');

        // Topluluk havuzu
        $pool = $this->loadPool();
        if (empty($pool)) { $this->error('Topluluk havuzu boş.'); return self::FAILURE; }
        shuffle($pool);

        // Dedupe için mevcut sorular
        $existing = Faq::pluck('question')->map(fn ($q) => mb_strtolower(trim($q)))->all();

        $batchSize = (int) $this->option('batch');
        $batches = (int) $this->option('batches');
        $publish = (bool) $this->option('publish');

        $created = 0; $skipped = 0;
        $maxSort = (int) Faq::max('sort_order');

        for ($b = 0; $b < $batches; $b++) {
            $chunk = array_slice($pool, $b * $batchSize, $batchSize);
            if (empty($chunk)) break;

            $this->line("━━━ Batch " . ($b + 1) . "/$batches (" . count($chunk) . " ham soru) ━━━");
            $faqs = $this->generateBatch($chunk, $topicList, $key);
            if (! $faqs) { $this->warn('  Batch boş/başarısız'); continue; }

            foreach ($faqs as $f) {
                $q = trim($f['question'] ?? '');
                $a = trim($f['answer'] ?? '');
                $topicSlug = $f['topic'] ?? null;
                if (mb_strlen($q) < 10 || mb_strlen($a) < 30 || ! isset($topics[$topicSlug])) {
                    $skipped++;
                    continue;
                }
                // Dedupe (basit benzerlik: tam küçük-harf eşleşme veya çok yakın)
                $qLower = mb_strtolower($q);
                if (in_array($qLower, $existing, true)) { $skipped++; continue; }

                if ($this->option('dry-run')) {
                    $this->info('  ✅ [' . $topicSlug . '] ' . mb_substr($q, 0, 70));
                    $created++;
                    continue;
                }

                $slug = Str::slug($q);
                if (mb_strlen($slug) > 180) $slug = mb_substr($slug, 0, 180);
                if (Faq::where('slug', $slug)->exists()) $slug .= '-' . Str::random(5);

                Faq::create([
                    'faq_topic_id' => $topics[$topicSlug],
                    'question'     => $q,
                    'slug'         => $slug,
                    'answer_md'    => $a,
                    'answer_html'  => Str::markdown($a),
                    'intent'       => 'community',
                    'has_answer'   => true,
                    'is_published' => $publish,
                    'sort_order'   => ++$maxSort,
                ]);
                $existing[] = $qLower;
                $created++;
                $this->info('  ✅ [' . $topicSlug . '] ' . mb_substr($q, 0, 70));
            }

            if ($b < $batches - 1) sleep(2);
        }

        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━");
        $state = $publish ? 'YAYINDA' : 'TASLAK (admin onayı bekliyor)';
        $this->info("✅ $created SSS üretildi ($state), ⏭️ $skipped atlandı");
        if (! $publish && ! $this->option('dry-run') && $created > 0) {
            $this->warn("→ /admin/faqs adresinden gözden geçirip yayınla.");
        }
        return self::SUCCESS;
    }

    private function loadPool(): array
    {
        $out = [];
        foreach (['telegram_report_general.json', 'telegram_report_visa_denklik.json'] as $file) {
            $path = storage_path('app/community/' . $file);
            if (! is_file($path)) continue;
            $data = json_decode(file_get_contents($path), true) ?? [];
            foreach ($data['top500_soru'] ?? [] as $row) {
                $q = trim($row['Soru'] ?? '');
                if (mb_strlen($q) > 25) $out[] = $q;
            }
        }
        return $out;
    }

    private function generateBatch(array $rawQuestions, string $topicList, string $key): ?array
    {
        $list = '';
        foreach ($rawQuestions as $i => $q) {
            $list .= ($i + 1) . '. ' . mb_substr($q, 0, 400) . "\n";
        }

        $prompt = <<<TXT
AlmanyaUni (Türk öğrencilere Almanya rehberi) için SSS editörüsün. Aşağıda Türk öğrenci topluluğundan (Telegram) gelen HAM, kişisel sorular var.

GÖREV: Bu ham sorulardan, GENEL ve tekrar sorulabilir SSS'ler üret. Kişisel/tek seferlik durumları (örn "ben 9 Nisanda ödedim hala atanmadım") GENEL bir soruya soyutla (örn "Vize randevusu atama süreci ne kadar sürer?").

HAM SORULAR:
$list

KONULAR (topic slug = ad): $topicList

Her kaliteli SSS için JSON üret:
{
  "faqs": [
    {
      "question": "Genel, net Türkçe SSS sorusu (kişisel detay YOK, max 120 karakter)",
      "answer": "Faktüel, yardımcı cevap (markdown, 2-4 cümle). Türk öğrenci perspektifi.",
      "topic": "yukarıdaki topic slug'larından biri"
    }
  ]
}

KURALLAR:
- Sadece GENEL, birden fazla kişinin sorabileceği soruları üret. Çok spesifik/kişisel olanları ATLA.
- Benzer ham soruları TEK bir SSS'te birleştir.
- HALÜSİNASYON YOK: spesifik güncel rakam/tarih/ücret verme; "güncel tutarı resmi kaynaktan/konsolosluktan doğrulayın" de.
- Cevaplar nötr, faktüel, kısa. Promosyon dili yok.
- Bu batch'ten en fazla 8 kaliteli SSS çıkar (kalite > kantite).
- ÇIKTI: SADECE JSON.
TXT;

        try {
            $resp = Http::asJson()->timeout(150)->withHeaders(['x-goog-api-key' => $key])->retry(2, 3000)
                ->post(self::API . self::MODEL . ':generateContent', [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.4, 'maxOutputTokens' => 8000, 'responseMimeType' => 'application/json'],
                ]);
            if (! $resp->ok()) { $this->error('  HTTP ' . $resp->status()); return null; }
            $text = $resp->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
            return json_decode($text, true)['faqs'] ?? null;
        } catch (\Throwable $e) {
            $this->error('  ' . mb_substr($e->getMessage(), 0, 80));
            return null;
        }
    }
}
