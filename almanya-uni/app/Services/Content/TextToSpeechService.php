<?php

namespace App\Services\Content;

use App\Models\ContentAsset;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * ElevenLabs TTS — Türkçe doğal ses üretimi.
 * Model: eleven_multilingual_v2 (TR destekli)
 * Output: storage/app/public/content/{asset_id}/voice.mp3
 *
 * API docs: https://elevenlabs.io/docs/api-reference/text-to-speech
 */
class TextToSpeechService
{
    private const API_BASE = 'https://api.elevenlabs.io/v1';

    public function isConfigured(): bool
    {
        return !empty(config('services.elevenlabs.key'));
    }

    /**
     * Asset için TTS audio üret. Asset türüne göre body'nin uygun bölümü seçilir.
     * @return array{success: bool, audio?: array, error?: string}
     */
    public function generateForAsset(ContentAsset $asset, ?string $customText = null): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'ELEVENLABS_API_KEY .env\'de yok'];
        }

        $text = $customText ?? $this->extractSpeechText($asset);

        if (empty($text)) {
            return ['success' => false, 'error' => 'Asset\'ten okunabilir metin çıkarılamadı (body_md kontrol et).'];
        }

        // ElevenLabs character limit guard
        $maxChars = 4500;
        if (mb_strlen($text) > $maxChars) {
            $text = mb_substr($text, 0, $maxChars) . '...';
        }

        try {
            $result = $this->call($text);
        } catch (\Throwable $e) {
            Log::error("TTS fail asset={$asset->id}: " . $e->getMessage());
            return ['success' => false, 'error' => substr($e->getMessage(), 0, 500)];
        }

        // Storage
        $filename = 'voice-' . substr(md5($text), 0, 8) . '.mp3';
        $relPath = "content/{$asset->id}/{$filename}";
        Storage::disk('public')->put($relPath, $result['audio']);

        $audioMeta = [
            'type' => 'audio',
            'url' => Storage::disk('public')->url($relPath),
            'local_path' => $relPath,
            'size_bytes' => strlen($result['audio']),
            'character_count' => mb_strlen($text),
            'voice_id' => $result['voice_id'],
            'model' => $result['model'],
            'generated_by' => 'elevenlabs',
            'generated_at' => now()->toIso8601String(),
        ];

        // Asset güncelle
        $asset->update([
            'audio_path' => $relPath,
            'media' => array_merge($asset->media ?? [], [$audioMeta]),
        ]);

        return [
            'success' => true,
            'audio' => $audioMeta,
        ];
    }

    /**
     * Asset türüne göre seslendirilecek metni çıkar.
     * Markdown başlıklarını ve formatlamayı temizler.
     */
    public function extractSpeechText(ContentAsset $asset): string
    {
        $md = $asset->body_md ?? '';
        if (!$md) return '';

        return match ($asset->asset_type) {
            'podcast', 'youtube_long' => $this->cleanForSpeech($md, 4000),
            'youtube_short', 'tiktok' => $this->extractShortScript($md),
            'instagram' => $this->extractFirstSection($md),
            default => $this->cleanForSpeech($md, 2000),
        };
    }

    /**
     * Genel temizlik: Markdown → düz konuşulabilir metin.
     */
    private function cleanForSpeech(string $md, int $maxChars = 4000): string
    {
        // Header'ları kaldır ama içeriği koru
        $text = preg_replace('/^#{1,6}\s+/m', '', $md);
        // Bold/italic
        $text = preg_replace('/\*+([^*]+)\*+/u', '$1', $text);
        // Linkler [text](url) → text
        $text = preg_replace('/\[([^\]]+)\]\([^)]+\)/u', '$1', $text);
        // Inline code
        $text = preg_replace('/`([^`]+)`/u', '$1', $text);
        // Code blocks tamamen kaldır
        $text = preg_replace('/```[^`]*```/us', '', $text);
        // HTML tags
        $text = strip_tags($text);
        // Frontmatter (---\n...\n---)
        $text = preg_replace('/^---\s*\n.*?\n---\s*\n/us', '', $text);
        // List markers
        $text = preg_replace('/^[\-*+]\s+/m', '', $text);
        $text = preg_replace('/^\d+\.\s+/m', '', $text);
        // Multiple newlines → single space
        $text = preg_replace('/\n\s*\n+/u', '. ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim(mb_substr($text, 0, $maxChars));
    }

    /**
     * TikTok/Shorts senaryolarında konuşulan kısımları çıkar.
     * "0-3 sn HOOK: [...]" formatından sadece [...] içeriğini al.
     */
    private function extractShortScript(string $md): string
    {
        $lines = preg_split('/\r?\n/', $md);
        $speech = [];
        foreach ($lines as $line) {
            // Hook/Senaryo/CTA satırlarını yakala
            if (preg_match('/^\*+(\d+-\d+\s*sn|HOOK|CTA|SENARYO)\s*[:\-]?\s*\*+\s*(.+)/iu', $line, $m)) {
                $speech[] = trim($m[2], '* [](){}');
            }
            // Veya doğrudan [...] içindeki açıklamalar
            elseif (preg_match('/^\s*\[([^\]]+)\]\s*$/u', $line, $m)) {
                $speech[] = trim($m[1]);
            }
        }
        return implode('. ', $speech) ?: $this->cleanForSpeech($md, 1500);
    }

    private function extractFirstSection(string $md): string
    {
        // İlk ## başlığa kadar olan kısmı al
        $parts = preg_split('/^##\s+/m', $md, 2);
        return $this->cleanForSpeech($parts[0] ?? $md, 1500);
    }

    /**
     * @return array{audio: binary, voice_id: string, model: string}
     */
    private function call(string $text): array
    {
        $voiceId = config('services.elevenlabs.voice_id');
        $model = config('services.elevenlabs.model');
        $url = self::API_BASE . '/text-to-speech/' . $voiceId;

        $resp = Http::withHeaders([
            'xi-api-key' => config('services.elevenlabs.key'),
            'Accept' => 'audio/mpeg',
            'Content-Type' => 'application/json',
        ])
            ->timeout(180)
            ->retry(2, 2000)
            ->post($url, [
                'text' => $text,
                'model_id' => $model,
                'voice_settings' => [
                    'stability' => 0.5,
                    'similarity_boost' => 0.75,
                    'style' => 0.0,
                    'use_speaker_boost' => true,
                ],
            ]);

        if (!$resp->ok()) {
            $body = substr($resp->body(), 0, 300);
            throw new \RuntimeException("ElevenLabs HTTP {$resp->status()}: $body");
        }

        return [
            'audio' => $resp->body(),
            'voice_id' => $voiceId,
            'model' => $model,
        ];
    }
}
