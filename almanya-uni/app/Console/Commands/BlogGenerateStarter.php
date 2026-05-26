<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Services\Content\CommunityInsightsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Türk öğrenci pain-point'lerinden 10 başlangıç blog yazısı üret.
 * Her biri community-aware (Forum + Telegram) + 1500-2000 kelime Türkçe.
 * is_published=false (draft) — admin onayından sonra yayınlanır.
 */
class BlogGenerateStarter extends Command
{
    protected $signature = 'blog:generate-starter
        {--limit=10 : Üretilecek yazı sayısı}
        {--sleep=3 : Gemini rate-limit için bekleme}
        {--dry-run : Önizleme, kaydetme}';

    protected $description = 'Topluluk pain-point\'lerinden 10 SEO uyumlu Türkçe blog yazısı üret';

    private const TOPICS = [
        ['title' => 'Sperrkonto 2025 Tam Rehber: Vize İçin Bloke Hesap', 'topic' => 'para',
         'kw' => 'sperrkonto', 'category' => 'finans', 'pain' => 'Vize için 992€/ay kanıtı zorunlu, banka seçimi karmaşık'],
        ['title' => 'Almanya Vize Randevusu Nasıl Alınır? idata + iVisa Gerçeği', 'topic' => 'vize',
         'kw' => 'almanya vize randevusu', 'category' => 'vize', 'pain' => 'idata randevuları aylarca beklemek'],
        ['title' => 'TestDaF vs DSH vs telc: 2025 Hangisi Daha Avantajlı?', 'topic' => 'dil',
         'kw' => 'testdaf dsh telc karşılaştırma', 'category' => 'dil', 'pain' => 'Hangi sınav uni tarafından kabul edilir karışıklığı'],
        ['title' => 'Uni-Assist Başvuru A-Z: Belgeler, VPD, Maliyet, Süre', 'topic' => 'uni_assist',
         'kw' => 'uni-assist başvuru rehberi', 'category' => 'basvuru', 'pain' => 'Hangi belgeler, ne kadar sürer, hata yapmamak'],
        ['title' => 'Studienkolleg: Kimler İçin? Nasıl Başvurulur?', 'topic' => 'studienkolleg',
         'kw' => 'studienkolleg başvuru', 'category' => 'basvuru', 'pain' => 'Lise türü ve YKS sonucu kapsamı'],
        ['title' => 'Almanya\'da WG Bulma: Öğrenci Yurdu vs WG vs Stüdyo', 'topic' => 'yurt',
         'kw' => 'almanya wg bulmak', 'category' => 'yasam', 'pain' => 'WG-Gesucht kullanımı, scam kaçınma, fiyat tuzakları'],
        ['title' => 'Anmeldung: Almanya\'da İlk 14 Günde Ne Yapmalı?', 'topic' => 'anmeldung',
         'kw' => 'anmeldung nasıl yapılır', 'category' => 'yasam', 'pain' => 'Bürgeramt randevusu + belgeler + cezadan kaçınma'],
        ['title' => 'İngilizce Master: Almanca Olmadan Almanya\'da Okumak Mümkün mü?', 'topic' => 'dil',
         'kw' => 'ingilizce master almanya', 'category' => 'basvuru', 'pain' => 'Almanca öğrenmek istemeyen, hangi programlar İngilizce'],
        ['title' => 'Almanya\'da Öğrenci İş: 20 Saat Kuralı + Vergi + Krankenversicherung', 'topic' => 'is',
         'kw' => 'almanya öğrenci işi 20 saat', 'category' => 'yasam', 'pain' => 'Çalışma izni sınırı, vergi dilimleri, sigorta etkisi'],
        ['title' => 'Aile Birleşimi: Eş ve Çocuk Almanya\'ya Nasıl Gelir?', 'topic' => 'vize',
         'kw' => 'aile birleşimi vizesi almanya', 'category' => 'vize', 'pain' => 'Evli öğrenciler için süreç + A1 dil + finansal güvence'],
    ];

    public function handle(CommunityInsightsService $community): int
    {
        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            $this->error('GEMINI_API_KEY eksik');
            return self::FAILURE;
        }

        $topics = array_slice(self::TOPICS, 0, (int) $this->option('limit'));
        $author = User::where('is_admin', true)->orderBy('id')->first();

        $this->info("📝 " . count($topics) . " blog yazısı üretilecek (community-aware AI)");
        $this->newLine();

        $success = 0; $failed = 0; $start = now();

        foreach ($topics as $i => $t) {
            $this->line(sprintf('[%d/%d] %s', $i + 1, count($topics), $t['title']));

            // Skip if a post with similar title already exists
            $kw = mb_substr($t['kw'], 0, 30);
            if (Post::where('title', 'like', '%' . $kw . '%')->exists()) {
                $this->line('  ⏭️ Zaten draft/post var, atlandı');
                continue;
            }

            // Community insights
            $insights = $community->getInsightsFor($t['title'], tgLimit: 12, forumLimit: 5);
            $commContext = $community->formatForPrompt($insights);

            // AI call
            $body = $this->callGemini($t, $commContext, $apiKey);
            if (!$body) { $failed++; continue; }

            if ($this->option('dry-run')) {
                $this->info('  ✅ Üretildi: ' . mb_substr($body['title'], 0, 60) . ' (' . mb_strlen($body['content_md']) . ' karakter)');
                $success++;
                continue;
            }

            // Save as draft
            $category = Category::firstOrCreate(
                ['slug' => $t['category']],
                ['name' => ucfirst($t['category']), 'color' => '#1E40AF', 'is_active' => 1]
            );

            $post = Post::create([
                'user_id' => $author?->id,
                'category_id' => $category->id,
                'title' => $body['title'] ?: $t['title'],
                'slug' => Str::slug($body['title'] ?: $t['title']),
                'excerpt' => $body['excerpt'],
                'content_md' => $body['content_md'],
                'meta_title' => $body['meta_title'],
                'meta_description' => $body['meta_description'],
                'reading_minutes' => max(3, (int) round(str_word_count(strip_tags($body['content_md'])) / 200)),
                'is_published' => false,
            ]);
            $this->info("  ✅ #{$post->id} kaydedildi (draft) — /admin/posts/{$post->id}/edit");
            $success++;

            if ($i < count($topics) - 1) sleep((int) $this->option('sleep'));
        }

        $duration = $start->diffInSeconds(now());
        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("✅ {$success} başarılı, ❌ {$failed} başarısız, ⏱️ {$duration}s");
        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function countTopics(array $topics): int { return count($topics); }

    /**
     * Marker'lı plain text'i parse et.
     */
    private function parseMarkers(string $text): ?array
    {
        $text = trim($text);
        if (preg_match('/```(?:markdown|md|text)?\s*\n?(.+)\n?```/s', $text, $m)) {
            $text = trim($m[1]);
        }

        $get = function (string $marker) use ($text): ?string {
            if (preg_match('/\[' . $marker . '\]\s*\n(.+?)(?=\n\[[A-Z_]+\]|\z)/s', $text, $m)) {
                return trim($m[1]);
            }
            return null;
        };

        $title = $get('TITLE');
        $excerpt = $get('EXCERPT');
        $metaTitle = $get('META_TITLE') ?: $title;
        $metaDesc = $get('META_DESCRIPTION') ?: $excerpt;
        $content = $get('CONTENT');

        if (!$title || !$content || mb_strlen($content) < 500) {
            return null;
        }

        return [
            'title' => mb_substr($title, 0, 200),
            'excerpt' => mb_substr($excerpt ?? strip_tags($content), 0, 275),
            'meta_title' => mb_substr($metaTitle, 0, 250),
            'meta_description' => mb_substr($metaDesc, 0, 295),
            'content_md' => $content,
        ];
    }

    private function callGemini(array $topic, string $communityContext, string $apiKey): ?array
    {
        $prompt = <<<TXT
Sen AlmanyaUni'nin SEO editörüsün. Türk öğrencilere yönelik 1500-2000 kelimelik Türkçe blog yazısı üret.

KONU: {$topic['title']}
ANAHTAR KELİME: {$topic['kw']}
PAIN POINT: {$topic['pain']}

{$communityContext}

GÖREV: Marker'lı plain text formatında comprehensive blog yazısı üret. Topluluk insightlarındaki gerçek soruları FAQ bölümünde cevapla.

ÇIKTI FORMATI — TAM ŞU YAPIDA OLSUN:

[TITLE]
Click-bait olmayan SEO başlık 60-70 char

[EXCERPT]
1-2 cümle özet 140-160 char

[META_TITLE]
SEO title 60 char altı

[META_DESCRIPTION]
SEO description 155 char

[CONTENT]
## Giriş
Markdown içerik başlar buradan. H2 (##), H3 (###), kalın (**bold**), liste (- veya 1.), tablo, blockquote (>) kullan. Türkçe doğal dil, Sperrkonto/Anmeldung gibi Almanca terimler parantez içinde açıklansın. 1500-2000 kelime.

## Bölüm 2
...

## Sıkça Sorulanlar
### Soru 1?
Cevap.

### Soru 2?
Cevap.

(4-6 gerçek topluluk sorusu)

## Sonuç
Kısa kapanış + CTA.

KURALLAR:
- HALÜSİNASYON YOK — bilmediğin spesifik rakam/tarih/kural yok
- Anahtar kelimeyi başlık + ilk paragraf + 2-3 alt başlıkta doğal kullan
- Marker'ları [TITLE], [EXCERPT] vb. tam aynen yaz (köşeli parantez içinde)
- Her marker'dan sonra yeni satır + içerik
- JSON YAZMA — sadece marker formatı
TXT;

        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $resp = Http::asJson()
                    ->timeout(180)
                    ->withHeaders(['x-goog-api-key' => $apiKey])
                    ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent', [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => [
                            'temperature' => 0.6,
                            'maxOutputTokens' => 16384,
                        ],
                    ]);
                if (!$resp->ok()) {
                    if ($attempt < 2) { sleep(5); continue; }
                    $this->error('  HTTP ' . $resp->status());
                    return null;
                }
                $data = $resp->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $parsed = $this->parseMarkers($text);
                if ($parsed) return $parsed;
                if ($attempt < 2) { sleep(3); continue; }
                $this->error('  Parse fail: ' . substr($text, 0, 150));
                return null;
            } catch (\Throwable $e) {
                if ($attempt < 2) { sleep(5); continue; }
                $this->error('  ' . substr($e->getMessage(), 0, 100));
                return null;
            }
        }
        return null;
    }
}
