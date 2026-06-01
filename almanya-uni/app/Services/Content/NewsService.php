<?php

namespace App\Services\Content;

use App\Models\Category;
use App\Models\NewsCandidate;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Haber boru hattı: URL içerik çekme → ÖZGÜN editöryel AI taslak (telif-güvenli)
 * → çoklu dilde type='news' Post yayını. Gemini deseni TranslatePosts ile aynı.
 */
class NewsService
{
    private const MODEL = 'gemini-2.5-flash';
    private const ENDPOINT = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function __construct(private ?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? config('services.gemini.key');
    }

    // ───────────────────────── 1) URL içerik çek (link modu) ─────────────────────────

    /**
     * Bir URL'den okunabilir içerik + meta çıkarır (server-side).
     * @return array{title:?string,excerpt:?string,image:?string,text:?string}
     */
    public function fetchUrl(string $url): array
    {
        $out = ['title' => null, 'excerpt' => null, 'image' => null, 'text' => null];
        try {
            $resp = Http::timeout(25)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; AlmanyaUniBot/1.0; +https://almanyauni.com)'])
                ->get($url);
            if (! $resp->ok()) return $out;
            $html = $resp->body();

            $out['title']   = $this->metaContent($html, 'og:title') ?: $this->tagText($html, 'title');
            $out['excerpt'] = $this->metaContent($html, 'og:description') ?: $this->metaName($html, 'description');
            $out['image']   = $this->metaContent($html, 'og:image');
            $out['text']    = $this->readableText($html);
        } catch (\Throwable $e) {
            Log::warning('NewsService::fetchUrl fail ' . $url . ' — ' . $e->getMessage());
        }
        return $out;
    }

    private function metaContent(string $html, string $property): ?string
    {
        if (preg_match('/<meta[^>]+property=["\']' . preg_quote($property, '/') . '["\'][^>]+content=["\'](.*?)["\']/is', $html, $m)) {
            return html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8') ?: null;
        }
        return null;
    }

    private function metaName(string $html, string $name): ?string
    {
        if (preg_match('/<meta[^>]+name=["\']' . preg_quote($name, '/') . '["\'][^>]+content=["\'](.*?)["\']/is', $html, $m)) {
            return html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8') ?: null;
        }
        return null;
    }

    private function tagText(string $html, string $tag): ?string
    {
        if (preg_match('/<' . $tag . '[^>]*>(.*?)<\/' . $tag . '>/is', $html, $m)) {
            return html_entity_decode(trim(strip_tags($m[1])), ENT_QUOTES | ENT_HTML5, 'UTF-8') ?: null;
        }
        return null;
    }

    /** Kaba okunabilir metin çıkarımı — script/style at, p/h içeriklerini topla. */
    private function readableText(string $html): ?string
    {
        $html = preg_replace('/<(script|style|nav|footer|header|aside)\b[^>]*>.*?<\/\1>/is', ' ', $html) ?? $html;
        if (preg_match('/<(article|main)\b[^>]*>(.*?)<\/\1>/is', $html, $m)) {
            $html = $m[2];
        }
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)) ?? '');
        return $text !== '' ? mb_substr($text, 0, 6000) : null;
    }

    // ───────────────────────── 2) Editöryel AI taslak (özgün) ─────────────────────────

    /**
     * Adaydan birincil dilde ÖZGÜN editöryel taslak üretir (kopya değil).
     * Başlık + 2 cümle özet + markdown gövde (ne oldu / neden önemli / ne yapmalısın / kaynak).
     */
    public function generateDraft(NewsCandidate $c): bool
    {
        if (! $this->apiKey) {
            throw new \RuntimeException('GEMINI_API_KEY eksik.');
        }

        $locale = $c->primary_locale ?: 'tr';
        $voice  = ContentVoice::for($locale);
        $langName = ContentVoice::languageName($locale);

        $sourceBlock = trim(
            "SOURCE NAME: " . ($c->source_name ?: '—') . "\n" .
            "SOURCE URL: " . ($c->source_url ?: '—') . "\n" .
            "ORIGINAL TITLE: " . ($c->orig_title ?: '—') . "\n" .
            "SUMMARY: " . ($c->raw_excerpt ?: '—') . "\n" .
            "CONTENT EXCERPT:\n" . mb_substr((string) $c->fetched_content, 0, 5000)
        );

        $prompt = <<<TXT
You are an editor for AlmanyaUni / ApplyToGerman — a guide for INTERNATIONAL students & prospective migrants applying to German universities (audience is global, not only one nationality).

Write an ORIGINAL short news brief in {$langName} based on the source below. This is NOT a translation or a copy — it is your own editorial summary. NEVER reproduce the source text verbatim (copyright). Attribute and link the source instead.

TARGET-LANGUAGE VOICE & REGISTER (follow strictly):
{$voice}

STRUCTURE of the markdown body (use ## subheadings, keep it tight — 150-300 words):
1. What happened (the news, concrete facts/numbers/dates only if present in the source — NEVER invent).
2. "Neden önemli" / Why it matters — for an international student/applicant specifically.
3. "Ne yapmalısın" / What to do — a short actionable note; if relevant, hint at the matching tool (visa cost, blocked account, deadlines, eligibility) WITHOUT inventing URLs.
4. End with a one-line source attribution naming the source.

RULES:
- If a fact (number, deadline, threshold) is not in the source, do NOT state it — tell the reader to verify on the official source.
- No AI-slop clichés, no emoji-stuffing.

SOURCE:
{$sourceBlock}

OUTPUT: ONLY valid JSON, no other text:
{
  "title": "punchy headline in {$langName}, max 120 chars",
  "excerpt": "2-sentence summary, max 220 chars",
  "content_md": "the markdown body"
}
TXT;

        $parsed = $this->callGeminiJson($prompt, 4000);
        if (! $parsed || empty($parsed['title']) || empty($parsed['content_md'])) {
            return false;
        }

        $c->draft_title   = Str::limit($parsed['title'], 280, '');
        $c->draft_excerpt = Str::limit($parsed['excerpt'] ?? '', 240, '...');
        $c->draft_md      = $parsed['content_md'];
        $c->save();

        return true;
    }

    // ───────────────────── 2.5) AI görsel (resim yoksa) ─────────────────────

    /**
     * Habere uygun ÖZGÜN bir editöryel İLLÜSTRASYON üretir (Imagen 4 fast).
     * Fotoğraf değil, flat/vektör illüstrasyon → "sahte haber fotoğrafı" riski yok.
     * public/images/news/ altına kaydeder, web yolunu döndürür (yoksa null).
     */
    public function generateImage(NewsCandidate $c): ?string
    {
        return $this->illustration(
            $c->draft_title ?: $c->orig_title ?: 'studying in Germany',
            $c->suggested_category_id ? Category::find($c->suggested_category_id)?->name_en : null,
            (string) ($c->id ?: substr(sha1($c->draft_title ?? 'news'), 0, 8))
        );
    }

    /** Yayınlanmış bir Post için illüstrasyon (backfill komutu kullanır). */
    public function generatePostImage(\App\Models\Post $post): ?string
    {
        return $this->illustration(
            $post->title ?: 'studying in Germany',
            $post->category?->name_en,
            'post' . $post->id
        );
    }

    /** Ortak Imagen illüstrasyon üretimi + public/images/news kaydı. */
    private function illustration(string $title, ?string $cat, string $idHint): ?string
    {
        if (! $this->apiKey) return null;

        $prompt = 'Clean modern editorial FLAT VECTOR ILLUSTRATION (NOT a photograph) for a news article titled: "'
            . $title . '". Subject: international students studying or immigrating to Germany'
            . ($cat ? ', topic: ' . $cat : '')
            . '. Calm professional palette with subtle German flag accents (black, red, gold). '
            . 'Friendly, minimal, conceptual. ABSOLUTELY NO text, NO words, NO letters, NO logos. 16:9.';

        try {
            $resp = Http::timeout(120)->withHeaders(['x-goog-api-key' => $this->apiKey])
                ->post(self::ENDPOINT . 'imagen-4.0-fast-generate-001:predict', [
                    'instances'  => [['prompt' => $prompt]],
                    'parameters' => ['sampleCount' => 1, 'aspectRatio' => '16:9'],
                ]);

            $b64 = data_get($resp->json(), 'predictions.0.bytesBase64Encoded');
            if (! $b64) {
                Log::warning('NewsService image empty: ' . mb_substr($resp->body(), 0, 200));
                return null;
            }

            $dir = public_path('images/news');
            if (! is_dir($dir)) @mkdir($dir, 0775, true);
            $file = preg_replace('/[^a-z0-9]+/i', '', $idHint) . '-' . substr((string) Str::uuid(), 0, 6) . '.png';

            if (@file_put_contents($dir . '/' . $file, base64_decode($b64)) === false) {
                Log::warning('NewsService image write failed: ' . $dir . ' (yazma izni?)');
                return null;
            }
            return '/images/news/' . $file;
        } catch (\Throwable $e) {
            Log::warning('NewsService image exc: ' . mb_substr($e->getMessage(), 0, 150));
            return null;
        }
    }

    // ───────────────────────── 3) Yayınla (çoklu dil) ─────────────────────────

    /**
     * Adayı yayınlar: birincil dilde Post + diğer aktif dillere çeviri.
     * @return array{group:string,locales:array<int,string>}
     */
    public function publish(NewsCandidate $c, ?int $userId = null): array
    {
        if (! $c->hasDraft()) {
            throw new \RuntimeException('Taslak yok — önce taslağı üret/gir.');
        }

        // Uygun görsel yoksa AI ile konuya yakın illüstrasyon üret.
        if (empty($c->image_url)) {
            $img = $this->generateImage($c);
            if ($img) {
                $c->image_url = $img;
                $c->save();
            }
        }

        $primary = $c->primary_locale ?: 'tr';
        $group   = (string) Str::uuid();
        $userId  = $userId ?? User::where('is_admin', true)->value('id');

        $category = $c->suggested_category_id
            ? Category::find($c->suggested_category_id)
            : Category::where('kind', 'news')->where('slug', 'practical')->first();

        // Çevirileri ÖNCE al — İngilizce başlık, İngilizce slug tabanı için gerekli.
        // Sadece AKTİF diller (tr/en/de). coming_soon dilleri otomatik çevrilmez (YMYL+token).
        $targets = array_values(array_diff(self::activeLocales(), [$primary]));
        $tr = ($targets && $this->apiKey)
            ? $this->translate($c->draft_title, $c->draft_excerpt ?? '', $c->draft_md, $targets)
            : [];

        // SLUG TABANI = İNGİLİZCE (linkler İngilizce olmalı). Birincil = taban,
        // diğer diller = taban-{locale} (posts.slug global unique).
        $enTitle  = ($primary === 'en') ? $c->draft_title : ($tr['en']['title'] ?? $c->draft_title);
        $baseSlug = Str::limit(Str::slug((string) $enTitle), 80, '') ?: 'news';
        $baseSlug .= '-' . now()->format('ymdHis') . substr((string) Str::uuid(), 0, 4);

        $common = [
            'translation_group_id' => $group,
            'type'                 => 'news',
            'user_id'              => $userId,
            'category_id'          => $category?->id,
            'source_url'           => $c->source_url,
            'source_name'          => $c->source_name,
            'event_date'           => $c->event_date,
            'news_priority'        => $c->priority ?? 0,
            'featured_image'       => $c->image_url,
        ];

        // Birincil dil (İngilizce slug tabanı)
        Post::create(array_merge(
            $this->postPayload($primary, $c->draft_title, $c->draft_excerpt, $c->draft_md, $baseSlug),
            $common
        ));
        $done = [$primary];

        // Diğer aktif diller (aynı İngilizce taban + locale eki)
        foreach ($targets as $loc) {
            if (empty($tr[$loc]['title']) || empty($tr[$loc]['content_md'])) continue;
            Post::create(array_merge(
                $this->postPayload($loc, $tr[$loc]['title'], $tr[$loc]['excerpt'] ?? '', $tr[$loc]['content_md'], $baseSlug . '-' . $loc),
                $common
            ));
            $done[] = $loc;
        }

        $c->update([
            'status'             => NewsCandidate::STATUS_PUBLISHED,
            'published_group_id' => $group,
            'decided_at'         => now(),
        ]);

        return ['group' => $group, 'locales' => $done];
    }

    /** UI'da yayında (active=true) diller — config/locale.php'den. */
    private static function activeLocales(): array
    {
        return array_keys(array_filter(
            config('locale.locales', []),
            fn ($c) => $c['active'] ?? false
        ));
    }

    /** Tek dil Post alan kümesi (content_html, reading_minutes Post::saving hook'unda). */
    private function postPayload(string $locale, string $title, ?string $excerpt, string $md, string $slug): array
    {
        return [
            'locale'           => $locale,
            'title'            => Str::limit($title, 250, ''),
            'slug'             => $slug,
            'excerpt'          => Str::limit($excerpt ?: '', 250, '...'),
            'content_md'       => $md,
            'meta_title'       => Str::limit($title, 250, ''),
            'meta_description' => Str::limit($excerpt ?: '', 250, '...'),
            'is_published'     => true,
            'published_at'     => now(),
        ];
    }

    /**
     * Birincil dildeki taslağı hedef dillere çevirir (Gemini tek call JSON).
     * @param  array<int,string>  $locales
     * @return array<string,array{title,slug,excerpt,content_md}>
     */
    private function translate(string $title, string $excerpt, string $md, array $locales): array
    {
        $voiceBlock = '';
        foreach ($locales as $loc) {
            $voiceBlock .= "\n--- {$loc} (" . ContentVoice::languageName($loc) . ") ---\n" . ContentVoice::for($loc) . "\n";
        }
        $localeList = implode(', ', array_map(fn ($l) => $l . ' (' . ContentVoice::languageName($l) . ')', $locales));

        $prompt = <<<TXT
You are localizing a short news brief about studying/living in Germany for AlmanyaUni / ApplyToGerman (international audience).
Translate AND localize the SOURCE into {$localeList} — native and idiomatic, NOT literal. Preserve markdown structure. Keep German technical terms verbatim (Sperrkonto, Studienkolleg, Chancenkarte, BAföG, uni-assist) with a gloss in parentheses on first mention. Never invent facts.

VOICE & REGISTER per target locale (follow strictly):
{$voiceBlock}

SOURCE TITLE: {$title}
SOURCE EXCERPT: {$excerpt}
SOURCE BODY:
{$md}

OUTPUT: ONLY valid JSON, no other text. For each target locale provide title (max 200), slug (kebab-case, max 80), excerpt (max 230), content_md (full body):
{
TXT;
        foreach ($locales as $loc) {
            $prompt .= "\n  \"{$loc}\": { \"title\": \"...\", \"slug\": \"...\", \"excerpt\": \"...\", \"content_md\": \"...\" },";
        }
        $prompt = rtrim($prompt, ',') . "\n}";

        return $this->callGeminiJson($prompt, 12000) ?: [];
    }

    /** Ortak Gemini JSON çağrısı + 3 deneme + ```json``` soyma. */
    private function callGeminiJson(string $prompt, int $maxTokens): ?array
    {
        if (! $this->apiKey) return null;

        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $resp = Http::asJson()
                    ->timeout(180)
                    ->withHeaders(['x-goog-api-key' => $this->apiKey])
                    ->post(self::ENDPOINT . self::MODEL . ':generateContent', [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => [
                            'temperature'      => 0.4,
                            'maxOutputTokens'  => $maxTokens,
                            'responseMimeType' => 'application/json',
                        ],
                    ]);

                if (! $resp->ok()) {
                    if ($attempt < 2) { sleep(4); continue; }
                    Log::warning('NewsService Gemini HTTP ' . $resp->status() . ' — ' . mb_substr($resp->body(), 0, 200));
                    return null;
                }

                $text = trim($resp->json('candidates.0.content.parts.0.text') ?? '');
                if (preg_match('/```(?:json)?\s*\n?(.+)\n?```/s', $text, $m)) {
                    $text = trim($m[1]);
                }
                $parsed = json_decode($text, true);
                if (is_array($parsed)) return $parsed;

                if ($attempt < 2) { sleep(3); continue; }
            } catch (\Throwable $e) {
                if ($attempt < 2) { sleep(4); continue; }
                Log::warning('NewsService Gemini exc: ' . mb_substr($e->getMessage(), 0, 150));
            }
        }
        return null;
    }
}
