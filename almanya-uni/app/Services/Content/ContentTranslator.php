<?php

namespace App\Services\Content;

use App\Models\ContentAsset;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Translates a ContentAsset to one of 10 target languages via Gemini.
 * Preserves Markdown structure, code blocks, JSON-LD, links, and emojis.
 *
 * Idempotent: if a translation already exists for (source_asset_id, language),
 * returns it instead of re-translating (override with force=true).
 */
class ContentTranslator
{
    /** Valid translation targets = every locale in the registry (config/locale.php). */
    public static function supportedLanguages(): array
    {
        return ContentVoice::allLocales();
    }

    public function __construct(private ?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? config('services.gemini.key');
    }

    /**
     * Translate an asset into a single target language.
     *
     * @return ContentAsset The new (or existing) translated asset.
     */
    public function translate(ContentAsset $asset, string $targetLang, bool $force = false): ContentAsset
    {
        if (! in_array($targetLang, self::supportedLanguages(), true)) {
            throw new \InvalidArgumentException("Unsupported language: {$targetLang}");
        }

        if ($targetLang === $asset->language) {
            return $asset;
        }

        // Idempotency: existing translation under same source
        $existing = ContentAsset::where('source_asset_id', $asset->id)
            ->where('language', $targetLang)
            ->where('asset_type', $asset->asset_type)
            ->first();

        if ($existing && ! $force) {
            return $existing;
        }

        if (! $this->apiKey) {
            throw new \RuntimeException('GEMINI_API_KEY is not configured.');
        }

        $sourceLangName = ContentVoice::languageName($asset->language);
        $targetLangName = ContentVoice::languageName($targetLang);

        // Localize to the target language's native register (e.g. DE -> "du", not "Sie").
        $voice = ContentVoice::for($targetLang);

        $prompt = <<<TXT
You are a professional content translator and localizer for the AlmanyaUni / ApplyToGerman platform — a guide for international students applying to German universities.

TASK: Translate AND LOCALIZE the following content from {$sourceLangName} to {$targetLangName}. This is not a literal translation — render it in the native register and idiom of the target language per the VOICE rules below.

TARGET-LANGUAGE VOICE & REGISTER (follow strictly):
{$voice}

PRESERVATION RULES (CRITICAL):
1. Preserve Markdown structure EXACTLY (headings, lists, tables, code blocks, blockquotes).
2. Do NOT translate code blocks (```), inline code (`...`), or JSON-LD blocks — keep them verbatim.
3. Do NOT translate URLs, file paths, schema.org keys, or HTML attributes.
4. Translate ALT text, captions, headings, and natural-language content fully.
5. Keep emojis exactly where they are. Do not add new emojis.
6. Preserve frontmatter keys (title, slug, meta_description, etc.) — translate only the values.
7. Slug: convert to a valid kebab-case slug in the target language (no diacritics).
8. Adapt cultural references (currency, holidays, examples) for the target audience.
9. For German technical terms (Sperrkonto, Studienkolleg, Anabin, TV-L, etc.), keep the German word and add a brief in-language gloss in parentheses on first mention.

OUTPUT: ONLY the translated content. No commentary, no "Here is the translation:" preface.

--- SOURCE BEGIN ({$sourceLangName}) ---
{$asset->body_md}
--- SOURCE END ---
TXT;

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key=' . $this->apiKey;

        $response = Http::timeout(180)->post($url, [
            'contents' => [
                ['parts' => [['text' => $prompt]]],
            ],
            'generationConfig' => [
                'temperature' => 0.3,
                'maxOutputTokens' => 8192,
            ],
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Gemini API error: ' . $response->status() . ' — ' . substr($response->body(), 0, 300));
        }

        $translated = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (empty($translated)) {
            throw new \RuntimeException('Empty translation returned from Gemini.');
        }

        // Create new asset (or update if force re-translate)
        $newAsset = $existing ?: new ContentAsset();
        $newAsset->fill([
            'content_brief_id' => $asset->content_brief_id,
            'asset_type'       => $asset->asset_type,
            'language'         => $targetLang,
            'source_asset_id'  => $asset->id,
            'spec'             => $asset->spec,
            'body_md'          => trim($translated),
            'generated_by'     => 'gemini-translator',
            'prompt_used'      => substr($prompt, 0, 500) . '...',
            'status'           => 'draft',
        ]);
        $newAsset->save();

        return $newAsset;
    }

    /**
     * Translate to all 10 languages at once (excluding source).
     *
     * @return array<string, ContentAsset|\Throwable>
     */
    public function translateToAll(ContentAsset $asset, bool $force = false, int $sleepBetween = 2): array
    {
        $results = [];
        foreach (ContentVoice::contentLocales() as $lang) {
            if ($lang === $asset->language) continue;
            try {
                $results[$lang] = $this->translate($asset, $lang, $force);
                if ($sleepBetween > 0) sleep($sleepBetween);
            } catch (\Throwable $e) {
                Log::warning("ContentTranslator failed for asset {$asset->id} → {$lang}: " . $e->getMessage());
                $results[$lang] = $e;
            }
        }
        return $results;
    }
}
