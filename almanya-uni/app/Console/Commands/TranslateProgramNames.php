<?php

namespace App\Console\Commands;

use App\Models\Program;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Program RESMİ ismi (name_de) korunur; sayfa dilindeki KARŞILIĞINI üretir
 * (name_tr / name_en) — küçük puntoyla helper olarak gösterilir (x-program-name).
 * Benzersiz isimleri dedupe eder (14.5k program ↔ ~birkaç bin benzersiz isim) →
 * çeviri sayısı çok düşer. Aynı name_de'li tüm programlara toplu yazar.
 *
 *   php artisan programs:translate-names --locales=tr,en --only-missing
 */
class TranslateProgramNames extends Command
{
    protected $signature = 'programs:translate-names
        {--locales=tr,en : Hedef diller}
        {--only-missing : Sadece karşılığı boş benzersiz isimler}
        {--limit=0 : Maks benzersiz isim (0=hepsi)}
        {--batch=25 : API çağrısı başına isim}
        {--sleep=2 : Çağrılar arası sn}
        {--dry-run}';

    protected $description = 'Program isimlerinin sayfa-dili karşılığını üretir (name_tr/name_en); resmi name_de korunur';

    private const MODEL = 'gemini-2.5-flash';
    private const API = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function handle(): int
    {
        $key = config('services.gemini.key');
        if (! $key) { $this->error('GEMINI_API_KEY yok'); return self::FAILURE; }

        $locales = array_filter(array_map('trim', explode(',', $this->option('locales'))));
        $batch = max(1, (int) $this->option('batch'));
        $sleep = (int) $this->option('sleep');
        $limit = (int) $this->option('limit');
        $dry = (bool) $this->option('dry-run');

        foreach ($locales as $loc) {
            if (! in_array($loc, ['tr', 'en', 'de'], true)) continue;
            $col = 'name_' . $loc;

            // Çevrilecek BENZERSİZ resmi isimler
            $q = Program::query()->where('is_active', 1)
                ->whereNotNull('name_de')->where('name_de', '<>', '');
            if ($this->option('only-missing')) {
                $q->where(function ($w) use ($col) {
                    $w->whereNull($col)->orWhere($col, '');
                });
            }
            $names = $q->distinct()->orderBy('name_de')->pluck('name_de')->all();
            if ($limit > 0) $names = array_slice($names, 0, $limit);

            $this->info("🌍 {$loc}: " . count($names) . ' benzersiz isim çevrilecek');
            $done = 0;

            foreach (array_chunk($names, $batch) as $chunk) {
                if ($dry) { $this->line('  [dry] ' . count($chunk) . ' isim'); continue; }

                $map = $this->translateBatch($chunk, $loc, $key);
                if (! $map) { $this->warn('  batch başarısız, atlandı'); continue; }

                foreach ($chunk as $orig) {
                    $tr = $map[$orig] ?? null;
                    if (! $tr || mb_strtolower(trim($tr)) === mb_strtolower(trim($orig))) continue;
                    // aynı resmi isimli TÜM aktif programlara yaz
                    DB::table('programs')->where('name_de', $orig)->update([$col => $tr]);
                    $done++;
                }
                if ($sleep > 0) sleep($sleep);
                $this->line("  ... {$done}/" . count($names));
            }

            $this->info("✅ {$loc}: {$done} benzersiz isim güncellendi" . ($dry ? ' (DRY)' : ''));
        }

        return self::SUCCESS;
    }

    /** @param string[] $names @return array<string,string>|null orijinal→çeviri */
    private function translateBatch(array $names, string $loc, string $key): ?array
    {
        $langName = ['tr' => 'Turkish', 'en' => 'English', 'de' => 'German'][$loc] ?? $loc;

        $list = '';
        foreach ($names as $i => $n) {
            $list .= "[$i] " . mb_substr($n, 0, 200) . "\n";
        }

        $prompt = <<<TXT
These are official German university STUDY PROGRAM names (some already in English, some in German). Translate each into {$langName} — the natural, concise term a {$langName}-speaking student would recognize (NOT a literal word-for-word translation, NOT an explanation). Keep it short (a program title, not a sentence).

Rules:
- If a name is ALREADY in {$langName}, return it unchanged.
- Keep well-known proper nouns / specializations intact where natural.
- No degree prefixes (no "Bachelor/Master"), no quotes, just the program title.

NAMES:
{$list}

Return ONLY JSON: { "0": "...", "1": "...", ... } (same indices).
TXT;

        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $resp = Http::asJson()->timeout(120)->withHeaders(['x-goog-api-key' => $key])
                    ->post(self::API . self::MODEL . ':generateContent', [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 8000, 'responseMimeType' => 'application/json'],
                    ]);
                if (! $resp->ok()) { if ($attempt < 2) { sleep(4); continue; } return null; }
                $text = trim($resp->json()['candidates'][0]['content']['parts'][0]['text'] ?? '');
                if (preg_match('/```(?:json)?\s*\n?(.+)\n?```/s', $text, $m)) $text = trim($m[1]);
                $parsed = json_decode($text, true);
                if (! is_array($parsed)) { if ($attempt < 2) { sleep(3); continue; } return null; }

                // index→çeviri'yi orijinal→çeviri'ye çevir
                $out = [];
                foreach ($names as $i => $orig) {
                    if (isset($parsed[(string) $i]) && is_string($parsed[(string) $i])) {
                        $out[$orig] = trim($parsed[(string) $i]);
                    }
                }
                return $out;
            } catch (\Throwable $e) {
                if ($attempt < 2) { sleep(3); continue; }
            }
        }
        return null;
    }
}
