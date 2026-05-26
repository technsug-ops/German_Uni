<?php

namespace App\Services\Content;

use App\Models\ContentAsset;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Asset için görsel üretir. Default Pollinations.ai (ücretsiz, FLUX modeli).
 * İleride: Replicate, DALL-E 3, Imagen 4 (Tier 2 paid) fallback'leri eklenebilir.
 *
 * Storage: storage/app/public/content/{asset_id}/{slug}.jpg
 * Public URL: /storage/content/{asset_id}/{slug}.jpg
 */
class ImageGenerationService
{
    private const POLLINATIONS_BASE = 'https://image.pollinations.ai/prompt/';

    /**
     * Asset türüne göre default image size'lar.
     * Asset spec'inde override edilebilir.
     */
    private const SIZE_PRESETS = [
        'blog'          => ['width' => 1200, 'height' => 630, 'count' => 1, 'label' => 'OG header'],
        'youtube_long'  => ['width' => 1280, 'height' => 720, 'count' => 1, 'label' => 'Thumbnail'],
        'youtube_short' => ['width' => 720, 'height' => 1280, 'count' => 1, 'label' => 'Vertical cover'],
        'tiktok'        => ['width' => 720, 'height' => 1280, 'count' => 1, 'label' => 'Cover'],
        'instagram'     => ['width' => 1080, 'height' => 1080, 'count' => 8, 'label' => 'Carousel slides'],
        'twitter'       => ['width' => 1200, 'height' => 675, 'count' => 1, 'label' => 'Card image'],
        'linkedin'      => ['width' => 1200, 'height' => 627, 'count' => 1, 'label' => 'Post image'],
        'pinterest'     => ['width' => 1000, 'height' => 1500, 'count' => 1, 'label' => 'Pin'],
        'podcast'       => ['width' => 1400, 'height' => 1400, 'count' => 1, 'label' => 'Episode cover'],
        'newsletter'    => ['width' => 1200, 'height' => 600, 'count' => 1, 'label' => 'Header banner'],
        'visual_brief'  => ['width' => 1024, 'height' => 1024, 'count' => 4, 'label' => 'Concept images'],
    ];

    public function isConfigured(): bool
    {
        return true; // Pollinations ücretsiz, key gerek yok
    }

    /**
     * Asset için görseller üret. Asset türüne göre count + size belirlenir.
     * @return array{success: bool, images?: array, error?: string}
     */
    public function generateForAsset(ContentAsset $asset, ?array $customPrompts = null): array
    {
        $preset = self::SIZE_PRESETS[$asset->asset_type] ?? self::SIZE_PRESETS['blog'];
        $prompts = $customPrompts ?? $this->extractPrompts($asset, $preset['count']);

        if (empty($prompts)) {
            return ['success' => false, 'error' => 'Asset için image prompt çıkarılamadı (body_md veya visual_brief boş?).'];
        }

        $images = [];
        $errors = [];

        foreach ($prompts as $index => $prompt) {
            try {
                $result = $this->generateOne($asset, $prompt, $preset['width'], $preset['height'], $index);
                if ($result) {
                    $images[] = $result;
                }
            } catch (\Throwable $e) {
                $errors[] = "Image $index: " . $e->getMessage();
                Log::warning("Image gen fail asset={$asset->id} idx=$index: " . $e->getMessage());
            }
            // Nano Banana: 10 RPM rate limit (~6sn aralık). Pollinations: yavaş yavaş.
            // Son image değilse bekle:
            if ($index < count($prompts) - 1) {
                sleep(7);
            }
        }

        if (empty($images)) {
            return ['success' => false, 'error' => 'Hiç görsel üretilemedi. ' . implode('; ', $errors)];
        }

        // Asset media kolonuna ekle (mevcut media varsa birleştir)
        $existing = $asset->media ?? [];
        $newMedia = array_merge($existing, $images);
        $asset->update(['media' => $newMedia]);

        return [
            'success' => true,
            'images' => $images,
            'count' => count($images),
            'errors' => $errors,
        ];
    }

    /**
     * Asset'in body_md veya visual_brief'inden image prompt'ları çıkar.
     */
    private function extractPrompts(ContentAsset $asset, int $count): array
    {
        // 1. Eğer visual_brief asset'i varsa onun body_md'sinden çıkar
        $brief = $asset->brief;
        $visualBriefAsset = $brief?->assets()->where('asset_type', 'visual_brief')->first();

        if ($visualBriefAsset && $visualBriefAsset->body_md) {
            $prompts = $this->parsePromptsFromMarkdown($visualBriefAsset->body_md);
            if (!empty($prompts)) {
                return array_slice($prompts, 0, $count);
            }
        }

        // 2. Fallback: brief'in title + topic + pain_point'inden generic prompt üret
        if ($brief) {
            $base = $this->buildGenericPrompt($brief);
            return array_fill(0, $count, $base);
        }

        return [];
    }

    /**
     * Visual brief markdown'ından "Prompt:" satırlarını çıkar.
     */
    private function parsePromptsFromMarkdown(string $md): array
    {
        $prompts = [];

        // Pattern 1: **Prompt:** "..." veya **Prompt:** ...\n
        if (preg_match_all('/\*\*Prompt:?\*\*\s*[\["]?([^\n\[\]"]+)/iu', $md, $m)) {
            foreach ($m[1] as $p) {
                $clean = trim($p, " \t\n\r\0\x0B\"[]");
                if (mb_strlen($clean) > 20) $prompts[] = $clean;
            }
        }

        // Pattern 2: kod bloğu içindeki promptlar
        if (preg_match_all('/```\s*\n([^`]+)```/u', $md, $m)) {
            foreach ($m[1] as $p) {
                $clean = trim($p);
                if (mb_strlen($clean) > 20 && mb_strlen($clean) < 1000) {
                    $prompts[] = $clean;
                }
            }
        }

        return array_values(array_unique($prompts));
    }

    private function buildGenericPrompt(\App\Models\ContentBrief $brief): string
    {
        return "modern flat illustration for blog post about \"{$brief->title}\", "
            . "Turkish students studying in Germany context, "
            . "AlmanyaUni brand colors deep blue (#1E40AF) and warm orange (#F97316), "
            . "professional but approachable, friendly mood, "
            . "subject: {$brief->topic}";
    }

    /**
     * Tek görsel — provider zinciri ile dene: Nano Banana → Pollinations.
     * Config: services.image.providers (öncelik sırası)
     * @return array{url, local_path, prompt, width, height, size_bytes, generated_by}|null
     */
    private function generateOne(ContentAsset $asset, string $prompt, int $width, int $height, int $index): ?array
    {
        $cleanPrompt = trim(preg_replace('/\s+/u', ' ', $prompt));
        $providers = (array) config('services.image.providers', ['nano_banana', 'pollinations']);

        $lastError = null;
        foreach ($providers as $provider) {
            try {
                $result = match ($provider) {
                    'nano_banana' => $this->generateWithNanoBanana($cleanPrompt, $width, $height),
                    'pollinations' => $this->generateWithPollinations($cleanPrompt, $width, $height),
                    default => null,
                };
                if ($result) {
                    return $this->saveImageMeta($asset, $cleanPrompt, $width, $height, $index, $result['body'], $result['ext'], $provider);
                }
            } catch (\Throwable $e) {
                $lastError = "$provider: " . $e->getMessage();
                continue; // fallback'e geç
            }
        }
        throw new \RuntimeException("Tüm provider'lar fail: " . ($lastError ?? '?'));
    }

    /**
     * Gemini 2.5 Flash Image (Nano Banana) — text-to-image.
     * Tier 1 paid: ~10 RPM (request per minute) limit.
     * @return array{body: string, ext: string}|null
     */
    private function generateWithNanoBanana(string $prompt, int $width, int $height): ?array
    {
        $key = config('services.gemini.key');
        if (!$key) return null;

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-image:generateContent';
        $payload = [
            'contents' => [[
                'parts' => [['text' => $prompt . "\n\nGenerate a single high-quality {$width}x{$height} image, modern flat illustration style, AlmanyaUni brand: deep blue #1E40AF + warm orange #F97316."]]
            ]],
            'generationConfig' => ['responseModalities' => ['IMAGE']],
        ];

        $attempt = 0;
        $maxAttempts = 3;
        while ($attempt < $maxAttempts) {
            $attempt++;
            $resp = Http::timeout(180)
                ->withHeaders(['x-goog-api-key' => $key])
                ->post($url, $payload);

            if ($resp->ok()) break;

            $status = $resp->status();
            if ($status === 429) {
                // Per-minute rate limit aşıldı — 65 saniye bekle
                if ($attempt < $maxAttempts) {
                    sleep(65);
                    continue;
                }
            }
            throw new \RuntimeException("Nano Banana HTTP $status (attempt $attempt): " . substr($resp->body(), 0, 200));
        }

        if (!$resp->ok()) {
            throw new \RuntimeException("Nano Banana HTTP {$resp->status()}: " . substr($resp->body(), 0, 200));
        }

        $data = $resp->json();
        $parts = $data['candidates'][0]['content']['parts'] ?? [];
        foreach ($parts as $p) {
            if (isset($p['inlineData']['data'])) {
                $bytes = base64_decode($p['inlineData']['data']);
                $mime = $p['inlineData']['mimeType'] ?? 'image/png';
                $ext = str_contains($mime, 'jpeg') ? 'jpg' : 'png';
                return ['body' => $bytes, 'ext' => $ext];
            }
        }
        throw new \RuntimeException('Nano Banana yanıtında inlineData yok');
    }

    /**
     * Pollinations.ai FLUX — ücretsiz.
     * @return array{body: string, ext: string}|null
     */
    private function generateWithPollinations(string $prompt, int $width, int $height): ?array
    {
        $encodedPrompt = rawurlencode(mb_substr($prompt, 0, 800));
        $url = self::POLLINATIONS_BASE . $encodedPrompt
            . '?width=' . $width
            . '&height=' . $height
            . '&model=flux'
            . '&nologo=true'
            . '&seed=' . random_int(1, 999999);

        $resp = Http::timeout(120)->get($url);
        if (!$resp->ok()) {
            throw new \RuntimeException("Pollinations HTTP {$resp->status()}");
        }

        $contentType = $resp->header('Content-Type') ?? '';
        if (!str_contains($contentType, 'image/')) {
            throw new \RuntimeException("Yanıt görsel değil: $contentType");
        }

        $ext = str_contains($contentType, 'jpeg') ? 'jpg' : 'png';
        return ['body' => $resp->body(), 'ext' => $ext];
    }

    private function saveImageMeta(ContentAsset $asset, string $prompt, int $width, int $height, int $index, string $body, string $ext, string $provider): array
    {
        $filename = Str::slug($asset->asset_type . '-' . $index) . '-' . substr(md5($prompt . $provider), 0, 8) . '.' . $ext;
        $relPath = "content/{$asset->id}/{$filename}";
        Storage::disk('public')->put($relPath, $body);

        return [
            'type' => 'image',
            'url' => Storage::disk('public')->url($relPath),
            'local_path' => $relPath,
            'prompt' => mb_substr($prompt, 0, 500),
            'width' => $width,
            'height' => $height,
            'size_bytes' => strlen($body),
            'generated_by' => $provider, // nano_banana | pollinations
            'generated_at' => now()->toIso8601String(),
        ];
    }

}
