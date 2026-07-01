<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Blog (TR) yazılarındaki SPESİFİK FAKTLERİ (rakam/tarih/eşik/ücret/kurum) Gemini +
 * Google arama (grounding) ile GÜNCEL resmi bilgiye karşı doğrular. Yalnız NET yanlış/eski
 * olanları raporlar. DB'ye YAZMAZ → admin /admin/posts'ta düzeltir. Özellikle blog butonuyla
 * (Gemini) üretilen taslakların halüsinasyon riskini yakalar. faq:verify'in blog kardeşi.
 *
 *   php artisan blog:verify [--limit=10] [--all] [--sleep=1]
 *   (varsayılan: sadece TR taslaklar; --all yayındakileri de tarar)
 */
class BlogVerify extends Command
{
    protected $signature = 'blog:verify
        {--limit=10 : Kontrol edilecek yazı sayısı}
        {--all : Yayındakiler dahil (varsayılan: sadece taslaklar)}
        {--sleep=1 : Çağrılar arası bekleme (sn)}';

    protected $description = 'Blog (TR) faktlerini Gemini + Google arama ile doğrular (rapor)';

    private const MODEL = 'gemini-2.5-flash';
    private const API = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function handle(): int
    {
        $key = config('services.gemini.key');
        if (! $key) { $this->error('GEMINI_API_KEY yok'); return self::FAILURE; }

        $posts = Post::query()->where('locale', 'tr')
            ->whereNotNull('content_md')->where('content_md', '!=', '')
            ->when(! $this->option('all'), fn ($x) => $x->where('is_published', false))
            ->orderBy('id')->limit((int) $this->option('limit'))->get();

        if ($posts->isEmpty()) {
            $this->info('Kontrol edilecek blog yok' . ($this->option('all') ? '.' : ' (taslak). --all ile yayındakileri de tara.'));
            return self::SUCCESS;
        }

        $this->info($posts->count() . ' blog fakt-kontrolü yapılıyor (Gemini + Google arama)...');
        $this->newLine();

        $ok = 0; $flagged = 0; $err = 0;
        foreach ($posts as $p) {
            $v = $this->verify($p->title, (string) $p->content_md, $key);
            if ($v === null) {
                $err++;
                $this->line("  ❓ #{$p->id} kontrol edilemedi (API)");
            } elseif ($v['status'] === 'INCELE') {
                $flagged++;
                $this->warn("  ⚠️  #{$p->id} " . mb_substr($p->title, 0, 65));
                foreach (preg_split('/\n/', $v['note']) as $line) {
                    $line = trim($line);
                    if ($line !== '') $this->line("       $line");
                }
            } else {
                $ok++;
                $this->line("  ✅ #{$p->id} temiz — " . mb_substr($p->title, 0, 55));
            }
            if ((int) $this->option('sleep') > 0) sleep((int) $this->option('sleep'));
        }

        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("✅ $ok temiz · ⚠️ $flagged incelenecek · ❓ $err hata");
        if ($flagged > 0) {
            $this->warn("→ İşaretlenen yazıları /admin/posts'ta düzelt, sonra DE/EN'i yeniden çevir.");
        }
        return self::SUCCESS;
    }

    /** @return array{status:string,note:string}|null */
    private function verify(string $title, string $content, string $key): ?array
    {
        $content = mb_substr($content, 0, 8000);
        $prompt = <<<TXT
Almanya öğrenci rehberi editörüsün. Aşağıdaki blog yazısındaki SPESİFİK FAKTLERİ
(rakam, tarih, ücret, eşik, kural, kurum adı) GÜNCEL resmi bilgiyle karşılaştır — WEB ARAMASI YAP.
Yalnız NET yanlış veya eskimiş faktleri işaretle (en fazla 5 madde). Genel/hedge'li ifadeler
("resmi kaynaktan doğrulayın", "yaklaşık", "değişebilir") SORUN DEĞİL. Spesifik yanlış fakt yoksa OK.

BAŞLIK: {$title}
İÇERİK:
{$content}

ÇIKTI (tam bu format, başka hiçbir şey yazma):
DURUM: OK
NOT:
— veya —
DURUM: INCELE
NOT:
- [hangi fakt yanlış/eski + doğrusu, tek satır]
- [...]
TXT;

        try {
            $resp = Http::asJson()->timeout(120)->withHeaders(['x-goog-api-key' => $key])
                ->retry(2, 3000, throw: false)
                ->post(self::API . self::MODEL . ':generateContent', [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'tools' => [['google_search' => (object) []]],
                    'generationConfig' => ['temperature' => 0.1],
                ]);
            if (! $resp->ok()) return null;
            $text = trim($resp->json()['candidates'][0]['content']['parts'][0]['text'] ?? '');
            if ($text === '') return null;

            $status = preg_match('/DURUM:\s*INCELE/iu', $text) ? 'INCELE' : 'OK';
            $note = '';
            if (preg_match('/NOT:\s*(.+)/isu', $text, $m)) {
                $note = trim($m[1]);
            }
            return ['status' => $status, 'note' => mb_substr($note, 0, 600)];
        } catch (\Throwable $e) {
            return null;
        }
    }
}
