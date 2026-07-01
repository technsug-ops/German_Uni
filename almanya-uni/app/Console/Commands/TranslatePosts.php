<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\Content\ContentVoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * TR Post → EN + DE çevirisi (Gemini) + aynı translation_group_id altında kayıt.
 *
 * Idempotent: translation_group_id altında locale zaten varsa atla (--force ile yeniden).
 */
class TranslatePosts extends Command
{
    protected $signature = 'content:translate-posts
        {--post= : Tek bir post id}
        {--posts= : Virgülle ayrılmış id listesi (örn. 75,76,77)}
        {--all-untranslated : Translation_group\'unda EN+DE eksik olan TR post\'ların hepsi}
        {--published-only : Sadece yayındaki TR post\'lar (moderasyon: taslakları çevirme)}
        {--force : Mevcut çeviri varsa üzerine yaz}
        {--sleep=3 : Gemini istekleri arası bekleme}
        {--dry-run : Önizleme}';

    protected $description = 'TR post\'ları Gemini ile EN + DE\'ye çevirir, aynı translation_group altında kaydeder';

    public function handle(): int
    {
        $apiKey = config('services.gemini.key');
        if (! $apiKey) {
            $this->error('GEMINI_API_KEY eksik');
            return self::FAILURE;
        }

        $q = Post::query()->where('locale', 'tr');

        if ($id = $this->option('post')) {
            $q->where('id', (int) $id);
        } elseif ($ids = $this->option('posts')) {
            $q->whereIn('id', explode(',', $ids));
        } elseif ($this->option('all-untranslated')) {
            // EN VEYA DE eksik olan tüm TR post'lar (yalnız ikisi-birden-eksik değil).
            // Loop içindeki $needed = array_diff(['en','de'], $existing) tamamlananı atlar.
            $q->whereNotNull('translation_group_id')
              ->where(function ($w) {
                  $w->whereDoesntHave('siblings', fn ($s) => $s->where('locale', 'en'))
                    ->orWhereDoesntHave('siblings', fn ($s) => $s->where('locale', 'de'));
              });
        } else {
            $this->error('Bir hedef seç: --post=N, --posts=A,B,C veya --all-untranslated');
            return self::INVALID;
        }

        // Moderasyon: sadece yayındaki TR'leri çevir (taslak DE/EN sızmasın).
        if ($this->option('published-only')) {
            $q->where('is_published', true);
        }

        $posts = $q->get();
        $total = $posts->count();

        if ($total === 0) {
            $this->info('Hedef post yok.');
            return self::SUCCESS;
        }

        $this->info("🌍 {$total} TR post için EN + DE çeviri üretilecek (sleep: " . $this->option('sleep') . 's)');
        $this->newLine();

        $success = 0; $failed = 0;

        foreach ($posts as $i => $p) {
            $this->line(sprintf('[%d/%d] #%d %s', $i + 1, $total, $p->id, mb_substr($p->title, 0, 60)));

            // Mevcut translation_group'unda hangi locale'ler var?
            $existing = Post::where('translation_group_id', $p->translation_group_id)
                ->pluck('locale')
                ->all();

            $needed = array_diff(['en', 'de'], $existing);
            if (empty($needed) && ! $this->option('force')) {
                $this->line('   ⏭️ EN + DE zaten var, atla');
                continue;
            }

            if ($this->option('force')) {
                $needed = ['en', 'de'];
            }

            $result = $this->translate($p, $needed, $apiKey);

            // Fallback: tek bir call'da fail olursa, locale başına ayrı call dene
            if (! $result && count($needed) > 1) {
                $this->line('   ⚠️ Toplu call fail — locale başına ayrı dene');
                $result = [];
                foreach ($needed as $loc) {
                    $single = $this->translate($p, [$loc], $apiKey);
                    if ($single && isset($single[$loc])) {
                        $result[$loc] = $single[$loc];
                    }
                    sleep(2);
                }
                if (empty($result)) {
                    $failed++;
                    continue;
                }
                // Sadece başarılı olanları işle
                $needed = array_keys($result);
            } elseif (! $result) {
                $failed++;
                continue;
            }

            if ($this->option('dry-run')) {
                foreach ($needed as $loc) {
                    $this->info('   🔍 ' . strtoupper($loc) . ' title: ' . mb_substr($result[$loc]['title'], 0, 70));
                }
                $success++;
            } else {
                foreach ($needed as $loc) {
                    $data = $result[$loc];
                    $contentHtml = Str::markdown($data['content_md'], ['html_input' => 'allow']);

                    Post::updateOrCreate(
                        [
                            'translation_group_id' => $p->translation_group_id,
                            'locale' => $loc,
                        ],
                        [
                            'user_id' => $p->user_id,
                            'category_id' => $p->category_id,
                            'title' => Str::limit($data['title'], 250, ''),
                            'slug' => Str::limit($data['slug'], 250, ''),
                            'excerpt' => Str::limit($data['excerpt'], 250, '...'),
                            'content_md' => $data['content_md'],
                            'content_html' => $contentHtml,
                            'meta_title' => Str::limit($data['title'], 250, ''),
                            'meta_description' => Str::limit($data['excerpt'], 250, '...'),
                            'reading_minutes' => $p->reading_minutes,
                            // Kaynağın (TR) durumunu miras al: TR taslaksa DE/EN de taslak kalır.
                            'is_published' => (bool) $p->is_published,
                            'published_at' => $p->published_at ?? ($p->is_published ? now() : null),
                        ]
                    );
                    $this->info('   ✅ ' . strtoupper($loc) . ' saved');
                }
                $success++;
            }

            if ($i < $total - 1) {
                sleep((int) $this->option('sleep'));
            }
        }

        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("Success: {$success}, Failed: {$failed}");

        return self::SUCCESS;
    }

    /**
     * Gemini'den tek call'da TR → istenen locale'lere çeviri al.
     * @param  array<int,string>  $locales  (örn. ['en','de'])
     * @return array<string,array{title,slug,excerpt,content_md}>|null
     */
    private function translate(Post $p, array $locales, string $apiKey): ?array
    {
        $localeLabels = ['en' => 'English', 'de' => 'German'];
        $sectionDefs = [];
        foreach ($locales as $loc) {
            $sectionDefs[] = $loc . ' (' . $localeLabels[$loc] . ')';
        }
        $localeList = implode(', ', $sectionDefs);

        // Translate the FULL source (long guides run 15-23k chars); 24k output tokens cover it.
        $sourceContent = mb_substr($p->content_md, 0, 30000);

        // Per-locale register + native + anti-AI-slop voice (du for DE, "you" for EN, …).
        $voiceBlock = '';
        foreach ($locales as $loc) {
            $voiceBlock .= "\n--- {$loc} ---\n" . ContentVoice::for($loc) . "\n";
        }

        $prompt = <<<TXT
You are localizing a Turkish blog post about studying in Germany for AlmanyaUni / ApplyToGerman.
Translate AND localize the SOURCE into {$localeList} — native and idiomatic, NOT literal. Render the ENTIRE source (every section to the end). Preserve markdown formatting (headings, bold, lists, tables, blockquotes, links). Keep German technical terms verbatim (Sperrkonto, Studienkolleg, Ausbildung, Rundfunkbeitrag, etc.) with a gloss in parentheses on first mention.

VOICE & REGISTER per target locale (follow strictly):
{$voiceBlock}

SOURCE (Turkish):

# {$p->title}

> {$p->excerpt}

{$sourceContent}

OUTPUT FORMAT — return ONLY valid JSON, no other text. For each target locale, provide:
  - title: Localized title (max 200 chars)
  - slug: URL-safe lowercase slug, locale-appropriate words, hyphens only (max 80 chars)
  - excerpt: 2-sentence summary (max 230 chars)
  - content_md: Full translated body markdown (preserve all structure)

JSON shape:
{
TXT;
        foreach ($locales as $loc) {
            $prompt .= "\n  \"{$loc}\": {\n    \"title\": \"...\",\n    \"slug\": \"...\",\n    \"excerpt\": \"...\",\n    \"content_md\": \"...\"\n  },";
        }
        $prompt = rtrim($prompt, ',') . "\n}\n\nReturn ONLY the JSON object.";

        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $resp = Http::asJson()
                    ->timeout(180)
                    ->withHeaders(['x-goog-api-key' => $apiKey])
                    ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent', [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => [
                            'temperature' => 0.4,
                            'maxOutputTokens' => 24000,
                            'responseMimeType' => 'application/json',
                        ],
                    ]);

                if (! $resp->ok()) {
                    if ($attempt < 2) {
                        sleep(5);
                        continue;
                    }
                    $this->error('   HTTP ' . $resp->status() . ' — ' . mb_substr($resp->body(), 0, 200));
                    return null;
                }

                $data = $resp->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $text = trim($text);
                if (preg_match('/```(?:json)?\s*\n?(.+)\n?```/s', $text, $m)) {
                    $text = trim($m[1]);
                }

                $parsed = json_decode($text, true);
                if (! is_array($parsed)) {
                    if ($attempt < 2) {
                        sleep(3);
                        continue;
                    }
                    $this->error('   JSON parse fail: ' . mb_substr($text, 0, 200));
                    return null;
                }

                // Locale'ler eksik mi?
                $missing = [];
                foreach ($locales as $loc) {
                    if (empty($parsed[$loc]['title']) || empty($parsed[$loc]['content_md'])) {
                        $missing[] = $loc;
                    }
                }
                if (! empty($missing)) {
                    if ($attempt < 2) {
                        sleep(3);
                        continue;
                    }
                    $this->error('   Missing locales: ' . implode(',', $missing));
                    return null;
                }

                return $parsed;
            } catch (\Throwable $e) {
                if ($attempt < 2) {
                    sleep(5);
                    continue;
                }
                $this->error('   ' . mb_substr($e->getMessage(), 0, 150));
                return null;
            }
        }

        return null;
    }
}
