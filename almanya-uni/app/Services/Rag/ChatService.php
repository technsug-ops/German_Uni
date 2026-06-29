<?php

namespace App\Services\Rag;

use App\Support\MarkdownRenderer;
use Illuminate\Support\Facades\Http;

/**
 * RAG sohbet üretimi — retrieval bağlamından GROUNDED cevap.
 *
 * İlkeler (doc/CHATBOT-RAG-PLAYBOOK.md §0):
 *  - Sadece getirilen kaynaklardan cevap; bağlam zayıfsa "emin değilim" + link.
 *  - Her iddia kaynaklı (satır-içi [n]); sayı/tarih hedge'li.
 *  - Kullanıcı diliyle cevap; promosyon dili yok.
 */
class ChatService
{
    private const API = 'https://generativelanguage.googleapis.com/v1beta/models/';

    /** Bu skorun altında "ilgili içerik bulunamadı" (uydurma yerine yönlendir). */
    private const HARD_FLOOR = 0.50;
    /** Bu skorun altında düşük-güven (cevap ver ama temkinli işaretle). */
    private const LOW_CONF = 0.62;

    private string $key;
    private string $model;

    public function __construct(private ?Retriever $retriever = null)
    {
        $this->retriever ??= new Retriever();
        $this->key   = (string) config('services.gemini.key');
        $this->model = (string) config('services.gemini.chat_model', 'gemini-2.5-flash');
    }

    /**
     * @param array $history  [['role'=>'user'|'assistant','content'=>'...'], ...]
     * @return array{answer:string, sources:array, confidence:string, top:float}
     */
    public function ask(string $message, string $locale = 'tr', array $history = []): array
    {
        $message = trim(mb_substr($message, 0, 800));
        if ($message === '') {
            return $this->result($this->noQuestion($locale), [], 'low', 0.0);
        }

        $r = $this->retriever->retrieve($message, $locale, k: 8);
        $top = $r['top'];

        // Hiç ilgili içerik yok → uydurma YOK, yönlendir.
        if ($top < self::HARD_FLOOR || empty($r['results'])) {
            return $this->result($this->noContext($locale), [], 'low', $top);
        }

        $sources = $this->dedupeSources($r['results']);
        $answer = $this->generate($message, $locale, $sources, $history);
        $srcOut = array_map(fn ($s) => ['title' => $s['title'], 'url' => $s['url']], $sources);

        return $this->result($answer, $srcOut, $top >= self::LOW_CONF ? 'high' : 'low', $top);
    }

    /** Sonuç paketi — markdown'ı güvenli HTML'e de render eder (widget için). */
    private function result(string $answer, array $sources, string $confidence, float $top): array
    {
        return [
            'answer'      => $answer,
            'answer_html' => app(MarkdownRenderer::class)->render($answer),
            'sources'     => $sources,
            'confidence'  => $confidence,
            'top'         => round($top, 3),
        ];
    }

    /** Aynı URL'li chunk'ları tek kaynağa indir (atıf temizliği), en fazla 6 kaynak. */
    private function dedupeSources(array $results): array
    {
        $byUrl = [];
        foreach ($results as $row) {
            $u = $row['url'];
            if (! isset($byUrl[$u])) {
                $byUrl[$u] = ['title' => $row['title'], 'url' => $u, 'content' => $row['content'], 'score' => $row['score']];
            } elseif (mb_strlen($byUrl[$u]['content']) < 1600) {
                $byUrl[$u]['content'] .= "\n" . $row['content'];
            }
        }
        $list = array_values($byUrl);
        usort($list, fn ($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($list, 0, 6);
    }

    private function generate(string $message, string $locale, array $sources, array $history): string
    {
        $lang = $this->langName($locale);

        $ctx = '';
        foreach ($sources as $i => $s) {
            $n = $i + 1;
            $body = trim(mb_substr($s['content'], 0, 1400));
            $ctx .= "[{$n}] {$s['title']}\nURL: {$s['url']}\n{$body}\n\n";
        }

        $convo = '';
        foreach (array_slice($history, -4) as $h) {
            $who = ($h['role'] ?? '') === 'assistant' ? 'Asistan' : 'Kullanıcı';
            $convo .= $who . ': ' . trim(mb_substr((string) ($h['content'] ?? ''), 0, 500)) . "\n";
        }
        if ($convo !== '') $convo = "ÖNCEKİ KONUŞMA:\n{$convo}\n";

        $prompt = <<<TXT
Sen AlmanyaUni / ApplyToGerman sitesinin asistanısın — Almanya'da okumak/yaşamak isteyenlere yardım edersin.

KESİN KURALLAR:
- SADECE aşağıdaki KAYNAKLAR'daki bilgiyle cevap ver. Kaynaklarda olmayan hiçbir şey uydurma.
- Kullandığın her bilginin sonuna kaynak numarası ekle: [1], [2] gibi.
- Kaynaklar soruyu tam karşılamıyorsa bunu dürüstçe söyle ve en ilgili sayfaya yönlendir. ASLA tahmin etme.
- Sayı/tarih/ücret/eşik verirken "… itibarıyla; başvurudan önce resmi kaynaktan doğrulayın" şeklinde hedge'le. Asla kalıcı/kesin sunma.
- Cevabı {$lang} dilinde, net ve kısa yaz (gerektiğinde madde işareti). Promosyon/abartı dili yok.
- Markdown kullan. Link verme (kaynak numarası yeterli; linkler ayrı gösterilir).

{$convo}KAYNAKLAR:
{$ctx}
KULLANICI SORUSU: {$message}

Cevap ({$lang}, kaynak numaralı):
TXT;

        try {
            $resp = Http::asJson()->timeout(60)
                ->withHeaders(['x-goog-api-key' => $this->key])
                ->retry(2, 2000, throw: false)
                ->post(self::API . $this->model . ':generateContent', [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.2, 'maxOutputTokens' => 1400],
                ]);
            if (! $resp->ok()) return $this->error($locale);
            $text = $resp->json('candidates.0.content.parts.0.text');
            return trim((string) $text) ?: $this->error($locale);
        } catch (\Throwable $e) {
            return $this->error($locale);
        }
    }

    private function langName(string $l): string
    {
        return match ($l) { 'de' => 'Almanca', 'en' => 'İngilizce', default => 'Türkçe' };
    }

    private function noQuestion(string $l): string
    {
        return match ($l) {
            'de' => 'Bitte stelle eine Frage zum Studium oder Leben in Deutschland.',
            'en' => 'Please ask a question about studying or living in Germany.',
            default => 'Lütfen Almanya\'da okumak veya yaşamak hakkında bir soru sor.',
        };
    }

    private function noContext(string $l): string
    {
        return match ($l) {
            'de' => 'Dazu habe ich in unseren Inhalten keine gesicherte Information gefunden. Schau bitte in unsere FAQ- oder Blog-Seiten oder formuliere die Frage etwas anders.',
            'en' => 'I couldn\'t find reliable information about that in our content. Please check our FAQ or blog pages, or try rephrasing your question.',
            default => 'Bununla ilgili içeriğimizde güvenilir bir bilgi bulamadım. SSS veya blog sayfalarımıza bakabilir ya da soruyu biraz farklı sorabilirsin.',
        };
    }

    private function error(string $l): string
    {
        return match ($l) {
            'de' => 'Es gab ein technisches Problem. Bitte versuche es gleich noch einmal.',
            'en' => 'There was a technical problem. Please try again shortly.',
            default => 'Teknik bir sorun oluştu. Lütfen biraz sonra tekrar dene.',
        };
    }
}
