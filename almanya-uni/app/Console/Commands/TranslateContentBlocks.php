<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\FieldOfStudy;
use App\Models\State;
use App\Models\University;
use App\Services\Content\ContentVoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Translates an entity's TR `content_blocks` (enrichment) into EN/DE and stores
 * them in content_blocks_en / content_blocks_de — preserving JSON structure,
 * numbers, URLs, types, emoji. Native register (du for DE) via ContentVoice.
 * Enrichment-B (see doc/MULTILANG-PLAN).
 */
class TranslateContentBlocks extends Command
{
    protected $signature = 'content:translate-blocks
        {--entity=city : city|university|field|state}
        {--ids= : comma-separated ids (default: all with content_blocks)}
        {--locales=en,de : target locales}
        {--limit=0 : first N (0 = all)}
        {--force : retranslate even if target already filled}
        {--sleep=2 : seconds between calls}
        {--dry-run}';

    protected $description = 'Translate entity content_blocks (TR) → EN/DE (structure-preserving, native register)';

    private const MODELS = [
        'city' => City::class, 'university' => University::class,
        'field' => FieldOfStudy::class, 'state' => State::class,
    ];

    public function handle(): int
    {
        $apiKey = config('services.gemini.key');
        if (! $apiKey) { $this->error('GEMINI_API_KEY eksik'); return self::FAILURE; }

        $entity = $this->option('entity');
        $model = self::MODELS[$entity] ?? null;
        if (! $model) { $this->error("Bilinmeyen entity: $entity"); return self::INVALID; }

        $locales = array_filter(array_map('trim', explode(',', $this->option('locales'))));

        $q = $model::query()->whereNotNull('content_blocks')->where('content_blocks', '<>', '[]');
        if ($ids = $this->option('ids')) $q->whereIn('id', explode(',', $ids));

        // --force YOKKEN: hedef locale'lerden EN AZ BİRİ eksik olan satırları seç. Yoksa
        // --limit ile tekrar tekrar çağrıldığında (token route + Auto Refresh) hep aynı
        // ZATEN ÇEVRİLMİŞ ilk satırları çekip skip eder → liste boyunca ASLA ilerlemez.
        if (! $this->option('force')) {
            $q->where(function ($sub) use ($locales) {
                foreach ($locales as $loc) {
                    $col = 'content_blocks_' . $loc;
                    $sub->orWhereNull($col)->orWhere($col, '[]');
                }
            });
        }

        if (($limit = (int) $this->option('limit')) > 0) $q->limit($limit);

        $rows = $q->get();
        $this->info("🌍 {$entity}: {$rows->count()} kayıt → " . implode('+', $locales));

        $ok = 0; $fail = 0;
        foreach ($rows as $i => $row) {
            $name = $row->name ?? $row->name_tr ?? ('#' . $row->id);
            $blocks = $row->content_blocks;
            if (! is_array($blocks) || ! count($blocks)) continue;
            $this->line(sprintf('[%d/%d] #%d %s (%d blok)', $i + 1, $rows->count(), $row->id, mb_substr($name, 0, 40), count($blocks)));

            foreach ($locales as $loc) {
                $col = 'content_blocks_' . $loc;
                if (! $this->option('force') && ! empty($row->{$col})) { $this->line("   ⏭️ $loc var"); continue; }

                $translated = $this->translateBlocks($blocks, $loc, $apiKey);
                if (! $translated) { $fail++; $this->error("   ✗ $loc fail"); continue; }

                if ($this->option('dry-run')) {
                    $this->info("   🔍 $loc: " . count($translated) . ' blok (kaydedilmedi)');
                } else {
                    $row->{$col} = $translated;
                    $row->save();
                    $this->info("   ✅ $loc kaydedildi (" . count($translated) . ' blok)');
                }
                sleep((int) $this->option('sleep'));
            }
            $ok++;
        }
        $this->newLine();
        $this->info("Bitti. OK: $ok, Fail: $fail");
        return self::SUCCESS;
    }

    /** @return array<int,mixed>|null */
    private function translateBlocks(array $blocks, string $loc, string $apiKey): ?array
    {
        $voice = ContentVoice::for($loc);
        $langName = ContentVoice::languageName($loc);
        $sourceJson = json_encode($blocks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $count = count($blocks);

        $prompt = <<<TXT
Localize this AlmanyaUni enrichment content (a JSON array of blocks) from Turkish into {$langName}.

TARGET VOICE (follow strictly):
{$voice}

RULES:
- Return a JSON ARRAY with EXACTLY {$count} blocks, in the same order, SAME keys and SAME "type" values as the source. This is a structure-preserving localization.
- Translate ONLY human-readable Turkish text: block "h", "body_md", "caption", "title", "description", "note", "label", and the prose inside "items[].name/description/q/a", "highlights[]", "table headers"/"rows" text cells.
- DO NOT translate or change: numeric values, prices, ranges (e.g. "300-450", "11.904 €"), URLs, slugs, "type"/"platform"/"icon"/"currency"/"url" field values, emoji, JSON keys.
- Keep German proper nouns (Sperrkonto, Semesterticket, Anmeldung, Studentenwerk, WG, BAföG, Studienkolleg) untranslated; gloss once in {$langName} if helpful.
- markdown inside body_md must stay valid (headings, **bold**, lists, links).
- Output ONLY the JSON array — no markdown fences, no commentary.

SOURCE (Turkish) JSON array:
{$sourceJson}
TXT;

        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $resp = Http::asJson()->timeout(180)->withHeaders(['x-goog-api-key' => $apiKey])
                    ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent', [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 24000, 'responseMimeType' => 'application/json'],
                    ]);
                if (! $resp->ok()) { if ($attempt < 2) { sleep(5); continue; } return null; }
                $text = trim($resp->json('candidates.0.content.parts.0.text') ?? '');
                $text = preg_replace('/^```(?:json)?\s*|\s*```$/u', '', $text);
                $parsed = json_decode($text, true);
                // Validate: array with same block count
                if (is_array($parsed) && count($parsed) === count($blocks)) return $parsed;
                if ($attempt < 2) { sleep(3); continue; }
            } catch (\Throwable $e) {
                if ($attempt < 2) { sleep(5); continue; }
            }
        }
        return null;
    }
}
