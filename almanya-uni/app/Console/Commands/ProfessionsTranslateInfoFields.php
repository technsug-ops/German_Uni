<?php

namespace App\Console\Commands;

use App\Models\Profession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * BERUFENET info_fields (Almanca) → TR + EN, TÜM alanlar, key-bazlı idempotent.
 *
 * Çıktı Almanca key ile yazılır: info_fields_tr = { 'Trends' => '…', 'Kompetenzen' => '…' } (+ _en).
 * View (show.blade) bu key'leri BerufenetLabels sözlüğüyle eşleyip locale'e göre gösterir.
 *
 * İdempotency KEY bazında: kaynak info_fields key'lerinden info_fields_tr/_en'de EKSİK olanlar
 * çevrilir; doluysa atlanır. Böylece BerufenetImport diff'te bir key'in çevirisini silince
 * sadece o key yeniden çevrilir ("sadece değişeni işle").
 *
 * Token güvenliği: değer başına ~1000 char kırpılır; eksik key'ler CHUNK'lara bölünüp
 * (varsayılan 10) ayrı Gemini çağrılarıyla çevrilir → uzun/çok alanlı mesleklerde güvenli JSON.
 */
class ProfessionsTranslateInfoFields extends Command
{
    private const CHUNK = 10;        // tek Gemini çağrısında çevrilecek alan sayısı
    private const MAX_VALUE = 1000;  // alan başına kaynak char sınırı

    protected $signature = 'professions:translate-info-fields
        {--limit=50 : Bu çalıştırmada işlenecek meslek sayısı (0 = sınırsız)}
        {--max-seconds=0 : Bu süreden sonra yeni mesleğe başlama, dur (0 = sınırsız). Gateway timeout için.}
        {--sleep=2 : Meslekler arası bekleme (saniye)}
        {--force : Tüm alanları yeniden çevir (info_fields_tr dolu olsa da)}
        {--missing : info_fields_tr dolu olsa da eksik alanı olanları da tara (çeyreklik resync)}
        {--slug= : Tek bir meslek (slug)}
        {--dry-run : Önizleme, kaydetme}';

    protected $description = 'BERUFENET info_fields\'ın TÜM alanlarını TR + EN\'ye çevirir (key-bazlı idempotent).';

    public function handle(): int
    {
        $apiKey = config('services.gemini.key');
        if (! $apiKey) {
            $this->error('GEMINI_API_KEY eksik');
            return self::FAILURE;
        }

        $force = (bool) $this->option('force');

        $q = Profession::query()
            ->whereNotNull('info_fields')
            ->where('info_fields', '!=', '[]');

        $missingMode = (bool) $this->option('missing');

        if ($slug = $this->option('slug')) {
            $q->where('slug', $slug);
        } elseif ($force) {
            // tüm meslekler (genelde --slug ile birlikte; tüm tabloda --limit ile ilerlemez)
        } elseif ($missingMode) {
            // info_fields_tr dolu olsa da eksik alanı olabilir → hepsini tara, döngüde key-bazlı atla.
            // Çeyreklik resync: --limit=0 (sınırsız) çalıştır; az API çağrısı (sadece eksikler).
        } else {
            // İlk backfill: sadece hiç çevrilmemişler → whereNull batch'leri ilerler (stuck olmaz).
            $q->where(function ($w) {
                $w->whereNull('info_fields_tr')->orWhere('info_fields_tr', '[]');
            });
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

        $this->info("📚 {$total} meslek info_fields çevrilecek (chunk: " . self::CHUNK . ', sleep: ' . $this->option('sleep') . 's)');
        $this->newLine();

        $success = 0; $failed = 0; $skipped = 0;
        $maxSeconds = (int) $this->option('max-seconds');
        $started = microtime(true);
        $stoppedByTime = false;

        foreach ($items as $i => $p) {
            // Gateway timeout koruması: süre dolduysa yeni mesleğe başlama, temiz çık.
            if ($maxSeconds > 0 && (microtime(true) - $started) >= $maxSeconds) {
                $stoppedByTime = true;
                $this->warn("⏱️ Zaman bütçesi ({$maxSeconds}s) doldu — {$i}/{$total} işlendi, kalanı için tekrar çağır.");
                break;
            }

            $source = is_array($p->info_fields) ? $p->info_fields : [];
            $tr = is_array($p->info_fields_tr) ? $p->info_fields_tr : [];
            $en = is_array($p->info_fields_en) ? $p->info_fields_en : [];

            // Çevrilecek key'ler: kaynakta var, (force değilse) tr VEYA en'de eksik.
            $missing = [];
            foreach ($source as $key => $val) {
                if (! is_string($val) || mb_strlen(trim($val)) < 5) continue;
                if ($force || ! isset($tr[$key]) || ! isset($en[$key])) {
                    $missing[$key] = mb_substr(trim($val), 0, self::MAX_VALUE);
                }
            }

            if (empty($missing)) {
                $skipped++;
                continue;
            }

            $this->line(sprintf('[%d/%d] %s — %d alan', $i + 1, $total, mb_substr($p->name_de, 0, 50), count($missing)));

            $allOk = true;
            foreach (array_chunk($missing, self::CHUNK, true) as $chunk) {
                $result = $this->callGemini($chunk, $p->name_de, $apiKey);
                if (! $result) { $allOk = false; break; }
                foreach ($result['tr'] as $k => $v) { $tr[$k] = $v; }
                foreach ($result['en'] as $k => $v) { $en[$k] = $v; }
            }

            if (! $allOk) { $failed++; continue; }

            if ($this->option('dry-run')) {
                $this->info('  ✅ ' . count($missing) . ' alan (dry-run)');
                $success++;
            } else {
                $p->update(['info_fields_tr' => $tr, 'info_fields_en' => $en]);
                $this->info('  ✅ TR: ' . count($tr) . ' · EN: ' . count($en) . ' alan');
                $success++;
            }

            if ($i < $total - 1) {
                sleep((int) $this->option('sleep'));
            }
        }

        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("✅ {$success} başarılı · ⏭️ {$skipped} atlandı (zaten tam) · ❌ {$failed} başarısız");

        // İlk backfill modunda kalan (hiç çevrilmemiş) meslek sayısını bildir → "bitti mi?" göstergesi.
        if (! $force && ! $missingMode && ! $this->option('slug')) {
            $remaining = Profession::query()
                ->whereNotNull('info_fields')->where('info_fields', '!=', '[]')
                ->where(function ($w) {
                    $w->whereNull('info_fields_tr')->orWhere('info_fields_tr', '[]');
                })->count();
            $this->info($remaining > 0
                ? "⏳ KALAN: {$remaining} meslek — bu URL'yi tekrar çağır."
                : '🎉 TAMAMLANDI — kalan meslek yok.');
        }

        return $failed > 0 && $success === 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Bir Almanca {key: value} chunk'ını TR + EN'ye çevirir, AYNI key'lerle döndürür.
     * @return array{tr: array<string,string>, en: array<string,string>}|null
     */
    private function callGemini(array $source, string $nameDe, string $apiKey): ?array
    {
        $sourceJson = json_encode($source, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $keysJson = json_encode(array_keys($source), JSON_UNESCAPED_UNICODE);

        $prompt = <<<TXT
Sen AlmanyaUni'nin meslek editörüsün. BERUFENET'ten alınan ALMANCA meslek bilgi alanlarını
Türkçe ve İngilizce'ye çeviriyorsun. Türk lise/üniversite öğrencisi okuyacak.

ALMAN MESLEK ADI: {$nameDe}

KAYNAK (Almanca, JSON — key = BERUFENET alan adı, value = Almanca metin):
{$sourceJson}

GÖREV: Her value'yu hem Türkçe hem İngilizce'ye çevir. Doğal, akıcı, kısa cümleler.

KURALLAR:
- Halüsinasyon yok — kaynakta olmayan rakam/süre/maaş/yer ekleme.
- Almanya'ya özgü kavramları KORU + ilk geçişte parantezle aç: Ausbildung (mesleki eğitim),
  Studium (lisans), Berufsschule (meslek okulu), IHK, Bundesland (eyalet), BERUFENET.
- Maaş/rakam söylenemiyorsa ("können nicht ... getroffen werden") → sade ver:
  TR "Maaş bilgisi değişkendir, BERUFENET'i kontrol edin" / EN "Earnings vary; check BERUFENET".
- Liste/link içeren alanları (kaynaklar, borsalar, dernekler) kısa ve düz çevir, uydurma ekleme.
- Madde imi yok; kısa paragraf. Lise mezunu anlasın.

ÇIKTI: SADECE şu JSON, başka açıklama yok. Key'leri kaynaktakiyle BİREBİR AYNI bırak:
{
  "tr": { <her kaynak key için Türkçe çeviri> },
  "en": { <her kaynak key için İngilizce çeviri> }
}
Çevrilecek key listesi (ikisinde de aynen kullan): {$keysJson}
TXT;

        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $resp = Http::asJson()
                    ->timeout(120)
                    ->withHeaders(['x-goog-api-key' => $apiKey])
                    ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent', [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => [
                            'temperature'      => 0.4,
                            'maxOutputTokens'  => 8000,
                            'responseMimeType' => 'application/json',
                        ],
                    ]);

                if (! $resp->ok()) {
                    if ($attempt < 2) { sleep(5); continue; }
                    $this->error('  HTTP ' . $resp->status() . ' — ' . mb_substr($resp->body(), 0, 160));
                    return null;
                }

                $text = $resp->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $parsed = $this->parseJson($text);
                if ($parsed) {
                    return $parsed;
                }
                if ($attempt < 2) { sleep(3); continue; }
                $this->error('  Parse fail: ' . mb_substr($text, 0, 160));
                return null;
            } catch (\Throwable $e) {
                if ($attempt < 2) { sleep(5); continue; }
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
            'tr' => array_filter((array) $data['tr'], fn ($v) => is_string($v) && trim($v) !== ''),
            'en' => array_filter((array) $data['en'], fn ($v) => is_string($v) && trim($v) !== ''),
        ];
    }
}
