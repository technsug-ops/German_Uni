<?php

namespace App\Services\Content;

use App\Models\ContentAsset;
use App\Models\ContentBrief;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentGenerationService
{
    private const API_BASE = 'https://generativelanguage.googleapis.com/v1beta/models/';
    private const MODEL = 'gemini-2.5-flash';

    public function __construct(
        private ContentPrompts $prompts,
    ) {}

    public function isConfigured(): bool
    {
        return !empty(config('services.gemini.key'));
    }

    /**
     * Brief'ten istenen asset türünü üret. Mevcut asset varsa UPSERT.
     * @return array{success: bool, asset?: ContentAsset, error?: string, tokens?: array}
     */
    public function generateAsset(ContentBrief $brief, string $assetType): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'GEMINI_API_KEY .env\'de yok'];
        }

        $prompt = $this->prompts->build($brief, $assetType);

        try {
            $result = $this->callGemini($prompt);
        } catch (\Throwable $e) {
            Log::error("Content gen fail asset=$assetType brief={$brief->id}: " . $e->getMessage());
            return ['success' => false, 'error' => substr($e->getMessage(), 0, 500)];
        }

        if (empty($result['text'])) {
            return ['success' => false, 'error' => 'Gemini boş cevap'];
        }

        $asset = ContentAsset::updateOrCreate(
            ['content_brief_id' => $brief->id, 'asset_type' => $assetType],
            [
                'body_md' => $result['text'],
                'generated_by' => 'ai_gemini',
                'prompt_used' => $prompt,
                'status' => 'draft',
            ]
        );

        return [
            'success' => true,
            'asset' => $asset,
            'tokens' => [
                'input' => $result['input_tokens'],
                'output' => $result['output_tokens'],
            ],
        ];
    }

    /**
     * Brief için tüm seçili asset türlerini sırayla üret.
     * @param  array<int, string>|null  $types  null = TYPES'taki tümünü dene
     * @return array<string, array>
     */
    public function generateAll(ContentBrief $brief, ?array $types = null): array
    {
        $types ??= array_keys(ContentAsset::TYPES);
        $results = [];

        foreach ($types as $type) {
            $results[$type] = $this->generateAsset($brief, $type);
            usleep(800 * 1000); // saygılı rate limit (free tier)
        }

        return $results;
    }

    private function callGemini(string $prompt): array
    {
        $url = self::API_BASE . self::MODEL . ':generateContent';

        $resp = Http::asJson()
            ->timeout(180)
            ->withHeaders(['x-goog-api-key' => config('services.gemini.key')])
            ->retry(2, 3000, function ($exception) {
                if (!$exception instanceof \Illuminate\Http\Client\RequestException) return false;
                $status = $exception->response?->status();
                return $status === 429 || ($status >= 500 && $status < 600);
            }, throw: false)
            ->post($url, [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'temperature' => 0.7,        // yaratıcılık
                    'maxOutputTokens' => 8192,
                    'topP' => 0.95,
                ],
                'safetySettings' => [
                    ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                    ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_ONLY_HIGH'],
                    ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                    ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                ],
            ]);

        if (!$resp->ok()) {
            $resp->throw();
        }

        $data = $resp->json();
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        $usage = $data['usageMetadata'] ?? [];

        return [
            'text' => $text ? trim($text) : null,
            'input_tokens' => (int) ($usage['promptTokenCount'] ?? 0),
            'output_tokens' => (int) ($usage['candidatesTokenCount'] ?? 0),
        ];
    }
}
