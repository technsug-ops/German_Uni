<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\GeminiTranslator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * #12 storytelling Faz-2: blog yazılarının "sesli makale" (podcast) sürümü.
 *
 * İKİ AŞAMA (mekanik olmaması için):
 *  1) Gemini ile makaleyi AKICI KONUŞMA DİLİ script'e çevir (başlık/etiket/madde
 *     yok — sohbet havasında, bir sunucu anlatıyormuş gibi).
 *  2) ElevenLabs TTS — doğal ses ayarlarıyla (stability düşük = ifadeli) mp3.
 *
 * mp3 PUBLIC'e yazılır (public/audio/podcasts/{group}-{locale}.mp3) → deploy
 * bundle'ına dahil, prod'da key gerekmez. İdempotent (mp3 varsa atlar, --force ile
 * yeniden üretir). ELEVENLABS_API_KEY yoksa sessiz çıkar.
 */
class StorytellingPodcasts extends Command
{
    protected $signature = 'storytelling:podcasts
        {--limit=5 : İşlenecek yazı sayısı}
        {--locale=tr : Hangi dil}
        {--force : mp3 varsa yeniden üret}';

    protected $description = 'Blog yazılarının sesli (podcast) sürümünü ElevenLabs ile üret';

    /** Konuşma script'i yazan sunucu persona — locale-aware. */
    private function scriptPrompt(string $locale): string
    {
        return match ($locale) {
            'en' => <<<'TXT'
You are the warm, friendly host of a short podcast for international students planning to study in Germany. You are given the text of a blog article. Rewrite it as a SPOKEN script that you would read aloud — natural, conversational English, like talking to one student over coffee.

Rules:
- Output ONLY the spoken words. No headings, no labels, no markdown, no bullet points, no stage directions, no "[music]".
- Open with a warm one-line hook (not "Welcome to the podcast"). Then explain the key points in flowing sentences. Close with one encouraging line.
- Use contractions and easy rhythm. Short sentences. Sound like a person, not a document.
- Keep German terms in German (BAföG, Anmeldung, Sperrkonto, Bürgeramt, Termin, etc.).
- Target 160-200 spoken words total. Do not exceed.
- Do not invent facts not in the article.
TXT,
            'de' => <<<'TXT'
Du bist der warmherzige Host eines kurzen Podcasts für internationale Studierende, die in Deutschland studieren wollen. Du bekommst den Text eines Blogartikels. Schreibe ihn als GESPROCHENES Skript um, das du laut vorliest — natürliches, lockeres Deutsch, als würdest du mit einer Person sprechen.

Regeln:
- Gib NUR die gesprochenen Worte aus. Keine Überschriften, keine Labels, kein Markdown, keine Aufzählungen, keine Regieanweisungen.
- Beginne mit einem warmen Einzeiler (nicht "Willkommen zum Podcast"). Dann die Kernpunkte in fließenden Sätzen. Schließe mit einem ermutigenden Satz.
- Kurze Sätze, natürlicher Rhythmus. Klinge wie ein Mensch, nicht wie ein Dokument.
- Deutsche Fachbegriffe bleiben deutsch (BAföG, Anmeldung, Sperrkonto, Bürgeramt, Termin).
- Ziel: 160-200 gesprochene Wörter. Nicht überschreiten.
- Erfinde keine Fakten, die nicht im Artikel stehen.
TXT,
            default => <<<'TXT'
Almanya'da okumak isteyen Türk öğrenciler için kısa bir podcast'in sıcak, samimi sunucususun. Sana bir blog yazısının metni veriliyor. Bunu, sesli okuyacağın bir KONUŞMA script'ine dönüştür — doğal, sohbet havasında Türkçe, sanki bir öğrenciyle karşılıklı konuşuyormuşsun gibi.

Kurallar:
- SADECE konuşulan sözleri yaz. Başlık yok, etiket yok ("30 saniyelik özet" gibi), markdown yok, madde işareti yok, parantez içi yönerge yok.
- Sıcak tek cümlelik bir girişle başla ("Podcast'e hoş geldiniz" deme). Sonra ana noktaları akıcı cümlelerle anlat. Cesaret veren tek bir cümleyle bitir.
- Kısa cümleler, doğal ritim. Bir insan gibi konuş, belge gibi değil. "Bak", "şöyle ki", "merak etme" gibi doğal bağlaçlar serbest.
- Almanca terimleri Almanca bırak (BAföG, Anmeldung, Sperrkonto, Bürgeramt, Termin, Studienkolleg vb.).
- Toplam 160-200 sözcük hedefle. Aşma.
- Yazıda olmayan bilgi uydurma.
TXT,
        };
    }

    public function handle(GeminiTranslator $gemini): int
    {
        $key = config('services.elevenlabs.key');
        if (! $key) {
            $this->warn('ELEVENLABS_API_KEY yok — atlandı.');
            return self::SUCCESS;
        }
        if (! $gemini->isConfigured()) {
            $this->warn('GEMINI_API_KEY yok — script üretilemez, atlandı.');
            return self::SUCCESS;
        }
        $voice = config('services.elevenlabs.voice_id', 'CwhRBWXzGAHq8TQ4Fs17');
        $model = config('services.elevenlabs.model', 'eleven_multilingual_v2');
        $locale = (string) $this->option('locale');

        $dir = public_path('audio/podcasts');
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $posts = Post::whereNotNull('content_brief_id')
            ->where('locale', $locale)
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

            // 1) Makaleyi düz metne indir (script kaynağı — Gemini'ye fazla token verme)
            $article = trim(preg_replace('/\s+/u', ' ', strip_tags(Str::markdown((string) $p->content_md))));
            $article = Str::limit($article, 4500, '');
            if (mb_strlen($article) < 80) {
                $this->warn("FAIL {$p->slug}: makale çok kısa");
                continue;
            }

            // 2) Gemini → akıcı konuşma script'i
            try {
                $res = $gemini->translate($article, $this->scriptPrompt($p->locale));
            } catch (\Throwable $e) {
                $this->warn("FAIL {$p->slug}: script üretilemedi — " . mb_substr($e->getMessage(), 0, 120));
                continue;
            }
            $script = trim((string) ($res['translation'] ?? ''));
            if (mb_strlen($script) < 60) {
                $this->warn("FAIL {$p->slug}: script boş");
                continue;
            }

            // 3) ElevenLabs TTS — doğal ses ayarları (stability düşük = daha ifadeli)
            try {
                $resp = Http::withHeaders(['xi-api-key' => $key])
                    ->timeout(120)
                    ->post("https://api.elevenlabs.io/v1/text-to-speech/{$voice}", [
                        'text'           => $script,
                        'model_id'       => $model,
                        'voice_settings' => [
                            'stability'         => 0.42,
                            'similarity_boost'  => 0.80,
                            'style'             => 0.35,
                            'use_speaker_boost' => true,
                        ],
                    ]);
            } catch (\Throwable $e) {
                $this->warn("FAIL {$p->slug}: TTS — " . mb_substr($e->getMessage(), 0, 120));
                continue;
            }

            if ($resp->successful() && strlen($resp->body()) > 1000) {
                file_put_contents($file, $resp->body());
                $this->info('✅ OK ' . $p->slug . ' (' . round(strlen($resp->body()) / 1024) . ' KB, ' . str_word_count($script) . ' söz)');
            } else {
                $this->warn("FAIL {$p->slug}: HTTP {$resp->status()} " . mb_substr($resp->body(), 0, 150));
            }
        }

        return self::SUCCESS;
    }
}
