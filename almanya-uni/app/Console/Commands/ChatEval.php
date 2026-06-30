<?php

namespace App\Console\Commands;

use App\Services\Rag\ChatService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Chatbot ÖLÇÜM KAPISI (RAG Faz 5) — "ölçülmeden yayın yok".
 *
 * Altın seti (resources/data/chat_eval.json) uçtan uca çalıştırır ve ölçer:
 *  - groundedness (LLM-judge): cevaptaki her iddia getirilen kaynaklarca destekleniyor mu? (halüsinasyon karşıtı)
 *  - relevance (LLM-judge): cevap soruyu gerçekten yanıtlıyor mu?
 *  - abstain doğruluğu: konu-dışı soruda "emin değilim" diyebiliyor mu? (yanlış-cevaplama karşıtı)
 *  - program isabeti: program-arayan soruda somut program kaynağı geliyor mu?
 *  - kapsama: cevaplaması beklenen sorulardan kaçını cevapladı (içerik boşluğu sinyali)
 *
 * KAPI: ort. groundedness ≥ eşik VE abstain doğruluğu ≥ eşik olmadan canlıya çıkma.
 *
 *   php artisan chat:eval [--lang=tr] [--limit=0] [--json]
 *
 * (doc/CHATBOT-RAG-PLAYBOOK.md §10)
 */
class ChatEval extends Command
{
    protected $signature = 'chat:eval
        {--lang= : Sadece bu dildeki sorular}
        {--limit=0 : İlk N soru (0=hepsi)}
        {--json : Sonucu JSON olarak yaz}';

    protected $description = 'Chatbot altın setini çalıştırıp groundedness + abstain doğruluğunu ölçer (yayın kapısı)';

    private const API = 'https://generativelanguage.googleapis.com/v1beta/models/';

    /** Yayın kapısı eşikleri. */
    private const GATE_GROUNDEDNESS = 0.80;
    private const GATE_ABSTAIN      = 0.80;

    public function handle(ChatService $chat): int
    {
        $path = resource_path('data/chat_eval.json');
        if (! is_file($path)) {
            $this->error("Altın set yok: $path");
            return self::FAILURE;
        }
        $items = json_decode((string) file_get_contents($path), true)['items'] ?? [];
        if ($lang = $this->option('lang')) {
            $items = array_values(array_filter($items, fn ($i) => ($i['lang'] ?? 'tr') === $lang));
        }
        if (($limit = (int) $this->option('limit')) > 0) {
            $items = array_slice($items, 0, $limit);
        }
        if (empty($items)) {
            $this->error('Çalıştırılacak soru yok.');
            return self::FAILURE;
        }

        $key = (string) config('services.gemini.key');
        if ($key === '') {
            $this->error('GEMINI_API_KEY yok — judge çalışmaz.');
            return self::FAILURE;
        }

        $rows = [];
        $gSum = 0.0; $gCount = 0;               // groundedness (cevaplananlarda)
        $rSum = 0.0;                             // relevance
        $answeredExpected = 0; $expectedAnswer = 0;
        $abstainOk = 0; $expectedAbstain = 0;
        $programOk = 0; $programNeeded = 0;

        foreach ($items as $n => $it) {
            $q = (string) $it['q'];
            $lang = $it['lang'] ?? 'tr';
            $expect = $it['expect'] ?? 'answer';
            $needsProgram = (bool) ($it['needs_program'] ?? false);

            $res = $chat->ask($q, $lang, [], debug: true);
            $sources = $res['sources'] ?? [];
            $hasProgram = false;
            foreach ($sources as $s) {
                if (str_contains($s['url'] ?? '', '/programs/')) { $hasProgram = true; break; }
            }

            // Çekinme METİNDE olur (kaynak dolu olsa da). LLM-judge ile sınıflandır.
            $j = $this->judge($key, $q, $res['answer'] ?? '', $res['context'] ?? []);
            $answered = $j['answered'] === 1;
            $verdict = '';

            if ($expect === 'abstain') {
                $expectedAbstain++;
                $ok = ! $answered;          // esaslı yanıt vermediyse = çekindi (doğru)
                if ($ok) $abstainOk++;
                $verdict = $ok ? 'ABSTAIN ✓' : 'YANLIŞ-CEVAP ✗';
            } else {
                $expectedAnswer++;
                if ($needsProgram) $programNeeded++;
                if ($answered) {
                    $answeredExpected++;
                    if ($needsProgram && $hasProgram) $programOk++;
                    $gSum += $j['grounded']; $gCount++; $rSum += $j['relevant'];
                    $progNote = $needsProgram ? ($hasProgram ? ' +prog' : ' -PROG') : '';
                    $verdict = sprintf('g=%.2f r=%.2f%s', $j['grounded'], $j['relevant'], $progNote);
                } else {
                    $verdict = 'KAPSAM-DIŞI (çekindi)';
                }
            }

            $rows[] = [
                'q'        => mb_substr($q, 0, 42),
                'lang'     => $lang,
                'expect'   => $expect,
                'conf'     => $res['confidence'] ?? '-',
                'top'      => $res['top'] ?? 0,
                'src'      => count($sources),
                'verdict'  => $verdict,
            ];
            $this->line(sprintf('  [%2d/%2d] %-44s → %s', $n + 1, count($items), mb_substr($q, 0, 44), $verdict));
        }

        // ── Özet ──
        $avgG = $gCount ? $gSum / $gCount : 0.0;
        $avgR = $gCount ? $rSum / $gCount : 0.0;
        $abstainAcc = $expectedAbstain ? $abstainOk / $expectedAbstain : 1.0;
        $coverage = $expectedAnswer ? $answeredExpected / $expectedAnswer : 1.0;
        $progAcc = $programNeeded ? $programOk / $programNeeded : 1.0;

        $this->newLine();
        $this->table(['Metrik', 'Değer', 'Kapı'], [
            ['Groundedness (ort.)', sprintf('%.2f', $avgG), '≥ ' . self::GATE_GROUNDEDNESS],
            ['Relevance (ort.)',    sprintf('%.2f', $avgR), '—'],
            ['Abstain doğruluğu',   sprintf('%.0f%% (%d/%d)', $abstainAcc * 100, $abstainOk, $expectedAbstain), '≥ ' . (self::GATE_ABSTAIN * 100) . '%'],
            ['Program isabeti',     sprintf('%.0f%% (%d/%d)', $progAcc * 100, $programOk, $programNeeded), '—'],
            ['Kapsama',             sprintf('%.0f%% (%d/%d)', $coverage * 100, $answeredExpected, $expectedAnswer), '(içerik boşluğu)'],
        ]);

        $pass = $avgG >= self::GATE_GROUNDEDNESS && $abstainAcc >= self::GATE_ABSTAIN;

        if ($this->option('json')) {
            $this->line(json_encode([
                'groundedness' => round($avgG, 3), 'relevance' => round($avgR, 3),
                'abstain_accuracy' => round($abstainAcc, 3), 'program_accuracy' => round($progAcc, 3),
                'coverage' => round($coverage, 3), 'pass' => $pass,
            ], JSON_PRETTY_PRINT));
        }

        if ($pass) {
            $this->info('✅ KAPI GEÇTİ — groundedness ve abstain eşikleri sağlandı.');
            return self::SUCCESS;
        }
        $this->error('❌ KAPI KALDI — eşik altı. Düşük groundedness = halüsinasyon riski; düşük abstain = yanlış-cevaplama.');
        return self::FAILURE;
    }

    /**
     * LLM-judge: groundedness + relevance + answered (esaslı yanıt mı yoksa çekinme/yönlendirme mi).
     * NOT: gemini-2.5-flash bir "düşünen" modeldir → thinkingBudget=0 + yeterli token yoksa
     * metin boş döner (düşünme bütçesi token'ları yer). Bu yüzden ikisi de ayarlı.
     * @return array{grounded:float,relevant:float,answered:int}
     */
    private function judge(string $key, string $question, string $answer, array $context): array
    {
        $ctx = '';
        foreach ($context as $i => $c) {
            $ctx .= '[' . ($i + 1) . "] {$c['title']}\n" . trim((string) $c['content']) . "\n\n";
        }

        $prompt = <<<TXT
Sen titiz bir kalite denetçisisin. Aşağıda bir SORU, asistanın CEVABI ve cevabın
dayandırılması gereken KAYNAKLAR var.

Değerlendir:
- answered: Asistan kullanıcının sorusunu ESASLI biçimde yanıtladı mı? 1 = esaslı bilgi
  verdi; 0 = "kaynaklarımda bilgi yok / yardımcı olamam / şu sayfaya bak" diyerek çekindi
  ya da yönlendirdi (esaslı yanıt YOK).
- grounded: Cevaptaki bilgilerin tamamı KAYNAKLAR'da destekleniyor mu? 1.0 = her iddia
  kaynaklarda var; 0.5 = bir kısmı desteksiz; 0.0 = büyük ölçüde uydurma. Asistan çekindiyse
  (answered=0) grounded=1.0 ver (uydurma yok demektir).
- relevant: Cevap soruyu gerçekten yanıtlıyor mu? 1.0 = tam; 0.0 = alakasız. Çekinme = 0.

SADECE şu JSON'u döndür, başka hiçbir şey yazma:
{"answered": <0 veya 1>, "grounded": <0-1>, "relevant": <0-1>}

SORU: {$question}

CEVAP:
{$answer}

KAYNAKLAR:
{$ctx}
TXT;

        try {
            $resp = Http::asJson()->timeout(60)
                ->withHeaders(['x-goog-api-key' => $key])
                ->retry(2, 1500, throw: false)
                ->post(self::API . config('services.gemini.chat_model', 'gemini-2.5-flash') . ':generateContent', [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'temperature' => 0.0,
                        'maxOutputTokens' => 600,
                        'responseMimeType' => 'application/json',
                        'thinkingConfig' => ['thinkingBudget' => 0],
                    ],
                ]);
            if (! $resp->ok()) return ['grounded' => 0.0, 'relevant' => 0.0, 'answered' => 1];
            $json = json_decode((string) $resp->json('candidates.0.content.parts.0.text'), true);
            return [
                'grounded' => max(0.0, min(1.0, (float) ($json['grounded'] ?? 0))),
                'relevant' => max(0.0, min(1.0, (float) ($json['relevant'] ?? 0))),
                'answered' => ((int) ($json['answered'] ?? 1)) === 0 ? 0 : 1,
            ];
        } catch (\Throwable $e) {
            return ['grounded' => 0.0, 'relevant' => 0.0, 'answered' => 1];
        }
    }
}
