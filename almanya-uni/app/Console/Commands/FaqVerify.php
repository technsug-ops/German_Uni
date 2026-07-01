<?php

namespace App\Console\Commands;

use App\Models\Faq;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Taslak FAQ cevaplarındaki SPESİFİK FAKTLERİ (rakam/tarih/eşik/ücret/kurum) Gemini +
 * Google arama (grounding) ile GÜNCEL resmi bilgiye karşı doğrular. Yalnız NET yanlış/eski
 * olanları raporlar. DB'ye YAZMAZ (rapor) → admin /admin/faqs'ta düzeltir. Öğrenciye
 * yanlış vize/para bilgisi gitmesini önler (üretim halüsinasyon riski).
 *
 *   php artisan faq:verify [--limit=20] [--all] [--sleep=1]
 *   (varsayılan: sadece taslaklar; --all yayındakileri de kontrol eder)
 */
class FaqVerify extends Command
{
    protected $signature = 'faq:verify
        {--limit=20 : Kontrol edilecek FAQ sayısı}
        {--all : Yayındakiler dahil (varsayılan: sadece taslaklar)}
        {--sleep=1 : Çağrılar arası bekleme (sn)}';

    protected $description = 'Taslak FAQ faktlerini Gemini + Google arama ile doğrular (rapor)';

    private const MODEL = 'gemini-2.5-flash';
    private const API = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function handle(): int
    {
        $key = config('services.gemini.key');
        if (! $key) { $this->error('GEMINI_API_KEY yok'); return self::FAILURE; }

        $faqs = Faq::query()->with('topic')
            ->whereNotNull('answer_md')->where('answer_md', '!=', '')
            ->when(! $this->option('all'), fn ($x) => $x->where('is_published', false))
            ->orderBy('id')->limit((int) $this->option('limit'))->get();

        if ($faqs->isEmpty()) {
            $this->info('Kontrol edilecek FAQ yok' . ($this->option('all') ? '.' : ' (taslak). --all ile yayındakileri de tara.'));
            return self::SUCCESS;
        }

        $this->info($faqs->count() . ' FAQ fakt-kontrolü yapılıyor (Gemini + Google arama)...');
        $this->newLine();

        $ok = 0; $flagged = 0; $err = 0;
        foreach ($faqs as $f) {
            $v = $this->verify($f->question, (string) $f->answer_md, $key);
            if ($v === null) {
                $err++;
                $this->line("  ❓ #{$f->id} kontrol edilemedi (API)");
            } elseif ($v['status'] === 'INCELE') {
                $flagged++;
                $this->warn("  ⚠️  #{$f->id} [" . (optional($f->topic)->slug ?? '?') . "] " . mb_substr($f->question, 0, 65));
                $this->line("       SORUN: " . $v['note']);
            } else {
                $ok++;
                $this->line("  ✅ #{$f->id} temiz");
            }
            if ((int) $this->option('sleep') > 0) sleep((int) $this->option('sleep'));
        }

        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("✅ $ok temiz · ⚠️ $flagged incelenecek · ❓ $err hata");
        if ($flagged > 0) {
            $this->warn("→ İşaretlenen FAQ'leri /admin/faqs'ta düzelt, sonra yayınla.");
        }
        return self::SUCCESS;
    }

    /** @return array{status:string,note:string}|null */
    private function verify(string $question, string $answer, string $key): ?array
    {
        $prompt = <<<TXT
Almanya öğrenci rehberi SSS editörüsün. Aşağıdaki SSS cevabındaki SPESİFİK FAKTLERİ
(rakam, tarih, ücret, eşik, kural, kurum adı) GÜNCEL resmi bilgiyle karşılaştır — WEB ARAMASI YAP.
Yalnız NET yanlış veya eskimiş faktleri işaretle. Genel/hedge'li ifadeler
("resmi kaynaktan doğrulayın", "yaklaşık", "değişebilir") SORUN DEĞİL → OK say.
Spesifik fakt YOKSA → OK.

SORU: {$question}
CEVAP: {$answer}

ÇIKTI (tam bu format, başka hiçbir şey yazma):
DURUM: OK
NOT:
— veya —
DURUM: INCELE
NOT: [hangi fakt yanlış/eski + doğrusu ne, tek cümle Türkçe]
TXT;

        try {
            $resp = Http::asJson()->timeout(90)->withHeaders(['x-goog-api-key' => $key])
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
                $note = trim(preg_replace('/\s+/u', ' ', $m[1]));
            }
            return ['status' => $status, 'note' => mb_substr($note, 0, 300)];
        } catch (\Throwable $e) {
            return null;
        }
    }
}
