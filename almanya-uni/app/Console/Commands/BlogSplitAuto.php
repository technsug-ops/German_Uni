<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Generic uzun-blog bölücü. Herhangi bir blogu Gemini ile tematik N parçaya böler:
 *   - AI H2'leri mantıklı gruplara ayırır + her gruba başlık/slug/excerpt/meta/kapak-prompt önerir
 *   - Pollinations kapak indirilir (lokal storage)
 *   - Yazar + tarih rotasyonu
 *   - Post kaydedilir (autolink observer'da otomatik)
 *   - Orijinal unpublish
 *
 * Kullanım: php artisan blog:split-auto {id} --parts=2 [--dry-run]
 */
class BlogSplitAuto extends Command
{
    protected $signature = 'blog:split-auto {id} {--parts=2} {--dry-run}';
    protected $description = 'Uzun blogu AI ile tematik parçalara böl + kapak + yazar/tarih';

    private const MODEL = 'gemini-2.5-flash';
    private const API = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function handle(): int
    {
        $source = Post::find((int) $this->argument('id'));
        if (! $source) { $this->error('Post bulunamadı.'); return self::FAILURE; }

        $parts = max(2, min(4, (int) $this->option('parts')));
        $sections = $this->splitSections($source->content_md);
        if (count($sections) < $parts + 1) {
            $this->warn('Yeterli H2 yok (' . count($sections) . '), bölme atlanıyor.');
            return self::FAILURE;
        }

        // 1) AI'dan tematik bölme planı
        $plan = $this->planSplit($source, array_keys($sections), $parts);
        if (! $plan) { $this->error('AI bölme planı alınamadı.'); return self::FAILURE; }

        $this->info("📋 {$parts} parçaya bölünecek: {$source->title}");
        foreach ($plan as $i => $p) {
            $secCount = count($p['sections'] ?? []);
            $this->line(sprintf('  %d. %s (%d bölüm) → %s', $i + 1, mb_substr($p['title'], 0, 50), $secCount, $p['slug']));
        }
        if ($this->option('dry-run')) return self::SUCCESS;

        $authors = $this->authors();
        $authorKeys = array_keys($authors);
        $baseDate = now()->subDays(30);

        foreach ($plan as $i => $p) {
            // İlgili H2 section'larını topla
            $md = [];
            foreach ($p['sections'] as $h2) {
                foreach ($sections as $title => $body) {
                    if (Str::startsWith(mb_strtolower($title), mb_strtolower(mb_substr($h2, 0, 20)))) {
                        $md[] = "## {$title}\n\n{$body}";
                        break;
                    }
                }
            }
            if (empty($md)) continue;

            $author = $authors[$authorKeys[$i % count($authorKeys)]];
            $publishedAt = $baseDate->copy()->addDays($i * 9 + random_int(0, 3))->setTime(random_int(9, 17), random_int(0, 59));
            $cover = $this->downloadCover($p['cover_prompt'] ?? $source->title, $p['slug']);

            $post = Post::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'user_id'          => $author->id,
                    'category_id'      => $source->category_id,
                    'title'            => $p['title'],
                    'excerpt'          => $p['excerpt'] ?? null,
                    'content_md'       => implode("\n\n", $md),
                    'featured_image'   => $cover,
                    'meta_title'       => $p['meta_title'] ?? $p['title'],
                    'meta_description' => $p['meta_description'] ?? $p['excerpt'] ?? null,
                    'is_published'     => true,
                    'published_at'     => $publishedAt,
                ]
            );
            $this->info("  ✅ {$post->title} · {$author->name} · {$publishedAt->format('d.m.Y')}");
        }

        $source->update(['is_published' => false]);
        $this->warn("Orijinal #{$source->id} yayından kaldırıldı.");
        return self::SUCCESS;
    }

    private function planSplit(Post $source, array $h2List, int $parts): ?array
    {
        $key = config('services.gemini.key');
        if (! $key) return null;

        $list = "- " . implode("\n- ", $h2List);
        $cat = $source->category?->name ?? '';

        $prompt = <<<TXT
Bir uzun blog yazısını $parts mantıklı tematik parçaya bölüyorsun (her parça bağımsız bir blog olacak).

ORİJİNAL BAŞLIK: {$source->title}
KATEGORİ: {$cat}

H2 BÖLÜMLER (sırayla):
$list

GÖREV: Bu H2 bölümlerini $parts tematik gruba ayır. Her grup ardışık bölümlerden oluşmalı (sırayı koru). Her grup için JSON üret:

{
  "parts": [
    {
      "title": "SEO uyumlu, Türkçe, çekici başlık (max 70 karakter, yıl 2026 ekleyebilirsin)",
      "slug": "url-uyumlu-slug-turkce-karaktersiz",
      "excerpt": "2 cümle özet, merak uyandıran",
      "meta_title": "SEO title 50-60 karakter",
      "meta_description": "SEO description 150-160 karakter",
      "cover_prompt": "İngilizce, fotoğrafik kapak görseli prompt'u (Almanya/öğrenci temalı)",
      "sections": ["bu gruba ait H2 başlıkları, ORİJİNAL metinle aynen"]
    }
  ]
}

KURALLAR:
- $parts parça, dengeli dağıt
- "Giriş", "Sonuç", "Sıkça Sorulanlar" gibi bölümleri uygun parçalara dağıt
- sections içindeki başlıklar orijinal H2'lerle birebir aynı olmalı
- ÇIKTI: SADECE JSON
TXT;

        try {
            $resp = Http::asJson()->timeout(120)->withHeaders(['x-goog-api-key' => $key])->retry(2, 2000)
                ->post(self::API . self::MODEL . ':generateContent', [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.4, 'maxOutputTokens' => 3000, 'responseMimeType' => 'application/json'],
                ]);
            if (! $resp->ok()) { $this->error('AI HTTP ' . $resp->status()); return null; }
            $text = $resp->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $parsed = json_decode($text, true);
            return $parsed['parts'] ?? null;
        } catch (\Throwable $e) {
            $this->error('AI hata: ' . mb_substr($e->getMessage(), 0, 80));
            return null;
        }
    }

    private function splitSections(string $md): array
    {
        $parts = preg_split('/^##\s+(.+)$/m', $md, -1, PREG_SPLIT_DELIM_CAPTURE);
        $sections = [];
        for ($i = 1; $i < count($parts); $i += 2) {
            $sections[trim($parts[$i])] = trim($parts[$i + 1] ?? '');
        }
        return $sections;
    }

    private function downloadCover(string $prompt, string $slug): string
    {
        $url = 'https://image.pollinations.ai/prompt/' . rawurlencode($prompt . ', photorealistic, Germany, students')
            . '?width=1200&height=630&nologo=true&seed=' . random_int(1, 9999);
        $path = "blog/{$slug}.jpg";
        try {
            $resp = Http::timeout(90)->retry(2, 3000)->get($url);
            if ($resp->ok() && str_contains((string) $resp->header('Content-Type'), 'image') && strlen($resp->body()) > 5000) {
                Storage::disk('public')->put($path, $resp->body());
                return $path;
            }
        } catch (\Throwable $e) {}
        return $url;
    }

    private function authors(): array
    {
        // Sadece içerik editörleri (Yapra hariç) — blog yazarlığı dağıtılsın
        $emails = ['elif@almanyauni.com', 'gamze@almanyauni.com', 'hakan@almanyauni.com', 'caner@almanyauni.com'];
        $authors = User::whereIn('email', $emails)->get()->keyBy('id')->all();
        return $authors ?: User::where('is_author', true)->where('name', '!=', 'Yapra')->get()->keyBy('id')->all();
    }
}
