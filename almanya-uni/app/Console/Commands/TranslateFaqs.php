<?php

namespace App\Console\Commands;

use App\Models\Faq;
use App\Services\Content\ContentVoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * FAQ EN/DE satırlarını TR kardeşinden (translation_group_id) DOĞRU çevirir:
 * soru → soru (kısa), cevap → cevap (ayrı) — birleştirme YOK. Eski çeviri
 * süreci ~10 EN / 9 DE satırda cevabı soru alanına kaynatmış + bir kısım
 * cevap TR kalmış; --only-broken sadece bunları hedefler (ucuz). slug,
 * is_featured, is_published, view_count KORUNUR.
 *
 *   php artisan faq:translate --locale=en,de --only-broken
 *   php artisan faq:translate --locale=en,de --force   (tümü)
 */
class TranslateFaqs extends Command
{
    protected $signature = 'faq:translate
        {--locale=en,de : Hedef diller (virgülle)}
        {--only-broken : Sadece bozuk satırlar (soru>200 veya TR sızıntısı)}
        {--force : Tüm satırları yeniden çevir}
        {--limit=0 : Dil başına maksimum satır (0=hepsi)}
        {--batch=8 : API çağrısı başına soru/cevap çifti}
        {--sleep=2 : Çağrılar arası bekleme (sn)}
        {--dry-run : Önizleme, kaydetme}';

    protected $description = 'FAQ EN/DE satırlarını TR kardeşinden native olarak yeniden çevirir (birleşmiş/eksik düzeltir)';

    private const MODEL = 'gemini-2.5-flash';
    private const API = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function handle(): int
    {
        $key = config('services.gemini.key');
        if (! $key) { $this->error('GEMINI_API_KEY yok'); return self::FAILURE; }

        $locales = array_filter(array_map('trim', explode(',', $this->option('locale'))));
        $batch = max(1, (int) $this->option('batch'));
        $sleep = (int) $this->option('sleep');
        $limit = (int) $this->option('limit');
        $dry = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $onlyBroken = (bool) $this->option('only-broken');

        $totalFixed = 0;

        foreach ($locales as $loc) {
            if ($loc === 'tr') continue;

            $rows = Faq::where('locale', $loc)
                ->whereNotNull('translation_group_id')
                ->get();

            // TR kardeşleri tek seferde topla
            $groups = $rows->pluck('translation_group_id')->unique()->all();
            $tr = Faq::where('locale', 'tr')
                ->whereIn('translation_group_id', $groups)
                ->get()
                ->keyBy('translation_group_id');

            // Hedef satırları filtrele
            $targets = $rows->filter(function (Faq $r) use ($tr, $force, $onlyBroken) {
                if (! isset($tr[$r->translation_group_id])) return false;
                if ($force) return true;
                if ($onlyBroken) return $this->isBroken($r);
                return $this->isBroken($r); // varsayılan = sadece bozuk
            })->values();

            if ($limit > 0) $targets = $targets->take($limit);

            $this->info("🌍 {$loc}: " . $targets->count() . ' satır çevrilecek' . ($onlyBroken || ! $force ? ' (bozuk)' : ' (force)'));

            foreach ($targets->chunk($batch) as $chunk) {
                $pairs = [];
                foreach ($chunk as $i => $r) {
                    $src = $tr[$r->translation_group_id];
                    $pairs[$i] = ['q' => $src->question, 'a' => (string) $src->answer_md];
                }

                if ($dry) {
                    foreach ($chunk as $i => $r) {
                        $this->line("   [dry] #{$r->id} ← TR: " . mb_substr($pairs[$i]['q'], 0, 60));
                    }
                    continue;
                }

                $out = $this->translateBatch($pairs, $loc, $key);
                if (! $out) { $this->warn('   batch başarısız, atlanıyor'); continue; }

                foreach ($chunk as $i => $r) {
                    if (! isset($out[$i]['question'])) continue;
                    $r->question = trim($out[$i]['question']);
                    $r->answer_md = trim($out[$i]['answer'] ?? $r->answer_md);
                    $r->save(); // answer_html + answer_minutes saving hook ile
                    $totalFixed++;
                    $this->info("   ✅ #{$r->id} " . mb_substr($r->question, 0, 55));
                }

                if ($sleep > 0) sleep($sleep);
            }
        }

        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("✅ {$totalFixed} FAQ satırı düzeltildi" . ($dry ? ' (DRY)' : ''));
        return self::SUCCESS;
    }

    /** Soru cevapla kaynamış mı, ya da hâlâ Türkçe mi? */
    private function isBroken(Faq $r): bool
    {
        if (mb_strlen((string) $r->question) > 200) return true; // cevap soruya kaynamış
        $trPattern = '/[şğıİ]|\b(için|nedir|nasıl|gerekli|kaç|mı|mi)\b/u';
        if (preg_match($trPattern, (string) $r->question)) return true;
        if (preg_match('/[şğı]/u', (string) $r->answer_md)) return true;
        return false;
    }

    /** @param array<int,array{q:string,a:string}> $pairs @return array<int,array>|null */
    private function translateBatch(array $pairs, string $loc, string $key): ?array
    {
        $voice = ContentVoice::for($loc);
        $langName = ContentVoice::languageName($loc);

        $items = '';
        foreach ($pairs as $i => $p) {
            $items .= "\n[$i] SORU: {$p['q']}\nCEVAP: {$p['a']}\n";
        }

        $prompt = <<<TXT
You are translating FAQ entries for AlmanyaUni (a guide for students applying to Germany) from Turkish into {$langName}.

VOICE & STYLE (follow strictly):
{$voice}

CRITICAL RULES:
- Translate the QUESTION as a SHORT, natural question (one sentence). NEVER merge the answer into the question.
- Translate the ANSWER separately, preserving markdown structure, lists, and bold (**...**).
- Keep German proper nouns/terms (Sperrkonto, Hochschule, TestDaF, Anabin, BAföG, uni-assist, Studienkolleg) untranslated.
- Native, SEO-friendly phrasing — not literal word-for-word. No Turkish words may remain.

SOURCE ITEMS (Turkish):
{$items}

Return ONLY this JSON, one object per index:
{
$this->jsonShape($pairs)
}
TXT;

        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $resp = Http::asJson()->timeout(180)->withHeaders(['x-goog-api-key' => $key])
                    ->post(self::API . self::MODEL . ':generateContent', [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.4, 'maxOutputTokens' => 16000, 'responseMimeType' => 'application/json'],
                    ]);
                if (! $resp->ok()) { if ($attempt < 2) { sleep(5); continue; } $this->error('   HTTP ' . $resp->status()); return null; }
                $text = trim($resp->json()['candidates'][0]['content']['parts'][0]['text'] ?? '');
                if (preg_match('/```(?:json)?\s*\n?(.+)\n?```/s', $text, $m)) $text = trim($m[1]);
                $parsed = json_decode($text, true);
                if (is_array($parsed)) return $parsed;
                if ($attempt < 2) { sleep(3); continue; }
            } catch (\Throwable $e) {
                if ($attempt < 2) { sleep(3); continue; }
                $this->error('   ' . mb_substr($e->getMessage(), 0, 120));
            }
        }
        return null;
    }

    private function jsonShape(array $pairs): string
    {
        $lines = [];
        foreach (array_keys($pairs) as $i) {
            $lines[] = "  \"$i\": { \"question\": \"...\", \"answer\": \"...\" }";
        }
        return implode(",\n", $lines);
    }
}
