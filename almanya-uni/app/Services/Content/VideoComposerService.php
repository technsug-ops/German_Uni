<?php

namespace App\Services\Content;

use App\Models\ContentAsset;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

/**
 * Asset'in görsel + ses kombinasyonundan slideshow video üretir.
 *
 * FFmpeg yüklüyse: Direkt MP4 üretir (vertical 1080×1920 reel veya horizontal 1920×1080)
 * FFmpeg yoksa: scene_script.json üretir (CapCut/Pictory/Synthesia import için)
 */
class VideoComposerService
{
    /**
     * @return array{success: bool, video_path?: string, scene_script_path?: string, error?: string}
     */
    public function composeForAsset(ContentAsset $asset): array
    {
        $media = $asset->media ?? [];
        $images = array_values(array_filter($media, fn ($m) => ($m['type'] ?? '') === 'image'));
        $audios = array_values(array_filter($media, fn ($m) => ($m['type'] ?? '') === 'audio'));

        if (empty($images)) {
            return ['success' => false, 'error' => 'Görsel yok. Önce 🎨 Görsel Üret butonuna bas.'];
        }
        if (empty($audios)) {
            return ['success' => false, 'error' => 'Ses yok. Önce 🎙️ Ses Üret butonuna bas (TikTok/Shorts/Podcast için).'];
        }

        // Asset türüne göre boyut + format
        $spec = $this->presetForType($asset->asset_type);

        // Scene script her zaman üretilir (geleceğe yatırım + CapCut import)
        $sceneScript = $this->buildSceneScript($asset, $images, $audios[0], $spec);
        $scriptPath = "content/{$asset->id}/scene_script.json";
        Storage::disk('public')->put($scriptPath, json_encode($sceneScript, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        // FFmpeg varsa direkt video üret
        $ffmpegPath = $this->findFfmpeg();
        if ($ffmpegPath) {
            try {
                $videoPath = $this->renderWithFfmpeg($ffmpegPath, $asset, $images, $audios[0], $spec);
                $asset->update(['video_path' => $videoPath]);
                return [
                    'success' => true,
                    'video_path' => $videoPath,
                    'video_url' => Storage::disk('public')->url($videoPath),
                    'scene_script_path' => $scriptPath,
                ];
            } catch (\Throwable $e) {
                Log::error("Video render fail asset={$asset->id}: " . $e->getMessage());
                return [
                    'success' => false,
                    'error' => 'FFmpeg render hatası: ' . $e->getMessage(),
                    'scene_script_path' => $scriptPath, // yine de script üretildi
                    'scene_script_url' => Storage::disk('public')->url($scriptPath),
                ];
            }
        }

        // FFmpeg yok → sadece scene script
        return [
            'success' => true,
            'video_path' => null,
            'scene_script_path' => $scriptPath,
            'scene_script_url' => Storage::disk('public')->url($scriptPath),
            'note' => 'FFmpeg sistemde yok. Scene script üretildi — CapCut/Pictory/Synthesia\'ya import edilebilir.',
        ];
    }

    private function presetForType(string $type): array
    {
        return match ($type) {
            'tiktok', 'youtube_short' => [
                'width' => 1080, 'height' => 1920, 'fps' => 30,
                'orientation' => 'vertical', 'duration_per_scene' => 4,
            ],
            'instagram' => [
                'width' => 1080, 'height' => 1920, 'fps' => 30,
                'orientation' => 'vertical', 'duration_per_scene' => 5,
            ],
            'youtube_long' => [
                'width' => 1920, 'height' => 1080, 'fps' => 30,
                'orientation' => 'horizontal', 'duration_per_scene' => 8,
            ],
            'podcast' => [
                'width' => 1920, 'height' => 1080, 'fps' => 24,
                'orientation' => 'horizontal', 'duration_per_scene' => 15,
            ],
            default => [
                'width' => 1920, 'height' => 1080, 'fps' => 30,
                'orientation' => 'horizontal', 'duration_per_scene' => 5,
            ],
        };
    }

    /**
     * Scene script (CapCut/Pictory/manual editing için).
     */
    private function buildSceneScript(ContentAsset $asset, array $images, array $audio, array $spec): array
    {
        $scenes = [];
        $totalDuration = 0;
        $perScene = $spec['duration_per_scene'];

        foreach ($images as $i => $img) {
            $scenes[] = [
                'index' => $i,
                'start_at' => $totalDuration,
                'duration' => $perScene,
                'image_url' => $img['url'],
                'image_local' => $img['local_path'],
                'transition' => $i === 0 ? 'none' : 'fade',
                'on_screen_text' => $this->extractCaptionForScene($asset, $i),
            ];
            $totalDuration += $perScene;
        }

        return [
            'meta' => [
                'asset_id' => $asset->id,
                'asset_type' => $asset->asset_type,
                'brief_title' => $asset->brief?->title,
                'orientation' => $spec['orientation'],
                'resolution' => $spec['width'] . 'x' . $spec['height'],
                'fps' => $spec['fps'],
                'estimated_duration_sec' => $totalDuration,
                'generated_at' => now()->toIso8601String(),
            ],
            'audio' => [
                'url' => $audio['url'],
                'local' => $audio['local_path'],
                'voice_id' => $audio['voice_id'] ?? null,
                'duration_estimate' => round(($audio['character_count'] ?? 0) / 15), // ~15 char/sec
            ],
            'scenes' => $scenes,
            'export_hint' => 'Bu JSON\'u CapCut "Komut Dosyası" veya Pictory "Storyboard" akışına aktarabilirsin. Her scene için image_url + on_screen_text kullan, audio.url ile birleştir.',
        ];
    }

    private function extractCaptionForScene(ContentAsset $asset, int $sceneIndex): ?string
    {
        $md = $asset->body_md ?? '';
        // Tek satır kısa caption — body'den ilgili paragrafı çıkar (basit)
        $paragraphs = array_filter(array_map('trim', preg_split('/\n\s*\n/', $md)));
        $paragraphs = array_values($paragraphs);
        $p = $paragraphs[$sceneIndex] ?? null;
        if (!$p) return null;
        $clean = strip_tags(preg_replace('/[*#`]+/u', '', $p));
        return mb_substr(trim($clean), 0, 80);
    }

    /**
     * FFmpeg ile gerçek MP4 render.
     */
    private function renderWithFfmpeg(string $ffmpeg, ContentAsset $asset, array $images, array $audio, array $spec): string
    {
        $outputDir = storage_path("app/public/content/{$asset->id}");
        if (!is_dir($outputDir)) mkdir($outputDir, 0755, true);

        $outputFile = "$outputDir/video.mp4";
        $listFile = "$outputDir/_concat_list.txt";

        // Concat list: her image için duration
        $duration = $spec['duration_per_scene'];
        $lines = [];
        foreach ($images as $img) {
            $absPath = storage_path('app/public/' . $img['local_path']);
            $lines[] = "file '$absPath'";
            $lines[] = "duration $duration";
        }
        // Son frame için dummy entry (FFmpeg requirement)
        if (!empty($images)) {
            $lines[] = "file '" . storage_path('app/public/' . end($images)['local_path']) . "'";
        }
        file_put_contents($listFile, implode("\n", $lines));

        $audioAbs = storage_path('app/public/' . $audio['local_path']);
        [$w, $h] = [$spec['width'], $spec['height']];

        // FFmpeg komutu: image sequence + ses + scale/pad
        $cmd = [
            $ffmpeg, '-y',
            '-f', 'concat', '-safe', '0', '-i', $listFile,
            '-i', $audioAbs,
            '-vf', "scale={$w}:{$h}:force_original_aspect_ratio=decrease,pad={$w}:{$h}:(ow-iw)/2:(oh-ih)/2:black,setsar=1",
            '-c:v', 'libx264', '-pix_fmt', 'yuv420p',
            '-c:a', 'aac', '-b:a', '128k',
            '-shortest', '-r', (string) $spec['fps'],
            '-movflags', '+faststart',
            $outputFile,
        ];

        $process = new Process($cmd);
        $process->setTimeout(600);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException('FFmpeg fail: ' . substr($process->getErrorOutput(), 0, 500));
        }

        @unlink($listFile);
        return "content/{$asset->id}/video.mp4";
    }

    private function findFfmpeg(): ?string
    {
        // Common Windows paths
        $candidates = [
            'ffmpeg', // PATH
            'ffmpeg.exe',
            'C:\\ffmpeg\\bin\\ffmpeg.exe',
            'C:\\laragon\\bin\\ffmpeg\\bin\\ffmpeg.exe',
            'C:\\Program Files\\ffmpeg\\bin\\ffmpeg.exe',
            'C:\\Program Files\\Prezi\\Prezi.Next-2.30.3.0\\ffmpeg.exe', // tespit edilen yer
        ];

        foreach ($candidates as $path) {
            if ($path === 'ffmpeg' || $path === 'ffmpeg.exe') {
                // PATH kontrolü
                $check = new Process([$path, '-version']);
                $check->run();
                if ($check->isSuccessful()) return $path;
            } else {
                if (is_file($path)) return $path;
            }
        }
        return null;
    }
}
