<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * #12 storytelling Faz-2: blog yazılarının "sesli makale" (podcast) sürümü.
 * Makale özeti/girişini ElevenLabs TTS ile sese çevirir, mp3'ü PUBLIC'e yazar
 * (public/audio/podcasts/{group}-{locale}.mp3) → deploy bundle'ına dahil,
 * prod'da key gerekmez. İdempotent (mp3 varsa atlar). Free tier sınırı için
 * metin kırpılır (~:chars karakter). ELEVENLABS_API_KEY yoksa sessiz çıkar.
 */
class StorytellingPodcasts extends Command
{
    protected $signature = 'storytelling:podcasts
        {--limit=5 : İşlenecek yazı sayısı}
        {--locale=tr : Hangi dil}
        {--chars=1400 : Seslendirilecek maksimum karakter (free tier)}
        {--force : mp3 varsa yeniden üret}';

    protected $description = 'Blog yazılarının sesli (podcast) sürümünü ElevenLabs ile üret';

    public function handle(): int
    {
        $key = config('services.elevenlabs.key');
        if (! $key) {
            $this->warn('ELEVENLABS_API_KEY yok — atlandı.');
            return self::SUCCESS;
        }
        $voice = config('services.elevenlabs.voice_id', 'CwhRBWXzGAHq8TQ4Fs17');
        $model = config('services.elevenlabs.model', 'eleven_multilingual_v2');

        $dir = public_path('audio/podcasts');
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $posts = Post::whereNotNull('content_brief_id')
            ->where('locale', $this->option('locale'))
            ->whereNotNull('translation_group_id')
            ->orderBy('id')
            ->limit((int) $this->option('limit'))
            ->get(['id', 'slug', 'locale', 'translation_group_id', 'content_md']);

        foreach ($posts as $p) {
            $file = $dir . '/' . $p->translation_group_id . '-' . $p->locale . '.mp3';
            if (is_file($file) && ! $this->option('force')) {
                $this->line("atla (var): {$p->slug}");
                continue;
            }

            // Markdown → düz konuşma metni → free tier için kırp
            $text = trim(preg_replace('/\s+/u', ' ', strip_tags(Str::markdown((string) $p->content_md))));
            $text = Str::limit($text, (int) $this->option('chars'), '');
            if (mb_strlen($text) < 50) {
                $this->warn("FAIL {$p->slug}: metin çok kısa");
                continue;
            }

            try {
                $resp = Http::withHeaders(['xi-api-key' => $key])
                    ->timeout(120)
                    ->post("https://api.elevenlabs.io/v1/text-to-speech/{$voice}", [
                        'text'     => $text,
                        'model_id' => $model,
                    ]);
            } catch (\Throwable $e) {
                $this->warn("FAIL {$p->slug}: " . mb_substr($e->getMessage(), 0, 150));
                continue;
            }

            if ($resp->successful() && strlen($resp->body()) > 1000) {
                file_put_contents($file, $resp->body());
                $this->info('✅ OK ' . $p->slug . ' (' . round(strlen($resp->body()) / 1024) . ' KB)');
            } else {
                $this->warn("FAIL {$p->slug}: HTTP {$resp->status()} " . mb_substr($resp->body(), 0, 150));
            }
        }

        return self::SUCCESS;
    }
}
