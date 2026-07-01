<?php

namespace App\Console\Commands;

use App\Models\Faq;
use App\Services\Content\ContentVoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
        {--create-missing : TR FAQ\'ların EKSİK EN/DE kardeşlerini YARAT (onarma değil)}
        {--only-broken : Sadece bozuk satırlar (soru>200 veya TR sızıntısı)}
        {--force : Tüm satırları yeniden çevir}
        {--limit=0 : Dil başına maksimum satır (0=hepsi)}
        {--batch=4 : API çağrısı başına soru/cevap çifti}
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

        if ($this->option('create-missing')) {
            return $this->createMissing($locales, $key, $batch, $sleep, $limit, $dry);
        }

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
            $targets = $rows->filter(function (Faq $r) use ($tr, $force) {
                if (! isset($tr[$r->translation_group_id])) return false;
                if ($force) return true;
                return $r->contentIsBroken(); // varsayılan/only-broken = sadece bozuk
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

    /**
     * TR FAQ'ların EKSİK EN/DE kardeşlerini yaratır (onarma değil).
     * Idempotent: grup zaten $loc satırına sahipse atlar. is_published TR'den miras alınır
     * (moderasyon: taslak TR → taslak kardeş).
     */
    private function createMissing(array $locales, string $key, int $batch, int $sleep, int $limit, bool $dry): int
    {
        $totalCreated = 0;

        // Grupsuz TR FAQ'lara (eski generate-ai çıktıları) grup ata ki çevrilebilsinler.
        $orphans = Faq::where('locale', 'tr')->whereNull('translation_group_id')->get();
        if ($orphans->isNotEmpty()) {
            if ($dry) {
                $this->line('   [dry] ' . $orphans->count() . ' grupsuz TR FAQ\'a grup atanacak');
            } else {
                foreach ($orphans as $o) {
                    $o->translation_group_id = (string) Str::uuid();
                    $o->save();
                }
                $this->info('🔗 ' . $orphans->count() . ' grupsuz TR FAQ\'a grup atandı');
            }
        }

        foreach ($locales as $loc) {
            if ($loc === 'tr') continue;

            // Bu locale'e ZATEN kardeşi olan gruplar
            $haveSibling = Faq::where('locale', $loc)
                ->whereNotNull('translation_group_id')
                ->pluck('translation_group_id')->unique()->flip();

            // Grup'u olan + bu locale kardeşi OLMAYAN TR FAQ'lar
            $targets = Faq::where('locale', 'tr')
                ->whereNotNull('translation_group_id')
                ->get()
                ->filter(fn (Faq $r) => ! isset($haveSibling[$r->translation_group_id]))
                ->values();

            if ($limit > 0) $targets = $targets->take($limit);

            $this->info("🌍 {$loc}: " . $targets->count() . ' eksik kardeş yaratılacak');

            foreach ($targets->chunk($batch) as $chunk) {
                $pairs = [];
                foreach ($chunk as $i => $r) {
                    $pairs[$i] = ['q' => $r->question, 'a' => (string) $r->answer_md];
                }

                if ($dry) {
                    foreach ($chunk as $i => $r) {
                        $this->line("   [dry] TR#{$r->id} → {$loc}: " . mb_substr($pairs[$i]['q'], 0, 55));
                    }
                    continue;
                }

                $out = $this->translateBatch($pairs, $loc, $key);
                if (! $out) { $this->warn('   batch başarısız, atlanıyor'); continue; }

                foreach ($chunk as $i => $r) {
                    if (empty($out[$i]['question'])) continue;

                    $slug = Str::limit(Str::slug($out[$i]['question']), 175, '');
                    if ($slug === '' || Faq::where('slug', $slug)->exists()) {
                        $slug = ($slug ?: $r->slug) . '-' . $loc;
                    }
                    if (Faq::where('slug', $slug)->exists()) $slug .= '-' . Str::random(4);

                    $faq = new Faq();
                    $faq->faq_topic_id = $r->faq_topic_id;
                    $faq->translation_group_id = $r->translation_group_id;
                    $faq->locale = $loc;
                    $faq->slug = $slug;
                    $faq->question = trim($out[$i]['question']);
                    $faq->answer_md = trim($out[$i]['answer'] ?? '');
                    $faq->intent = $r->intent;
                    $faq->has_answer = true;
                    $faq->is_published = (bool) $r->is_published;
                    $faq->sort_order = $r->sort_order;
                    $faq->save(); // answer_html + answer_minutes saving hook ile
                    $totalCreated++;
                    $this->info("   ✅ {$loc} #{$faq->id} " . mb_substr($faq->question, 0, 50));
                }

                if ($sleep > 0) sleep($sleep);
            }
        }

        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("✅ {$totalCreated} eksik FAQ kardeşi yaratıldı" . ($dry ? ' (DRY)' : ''));
        return self::SUCCESS;
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

        $shape = $this->jsonShape($pairs);

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
$shape
}
TXT;

        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $resp = Http::asJson()->timeout(180)->withHeaders(['x-goog-api-key' => $key])
                    ->post(self::API . self::MODEL . ':generateContent', [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.4, 'maxOutputTokens' => 24000, 'responseMimeType' => 'application/json'],
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
