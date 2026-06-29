<?php

namespace App\Services\Rag;

use App\Models\KbChunk;
use Illuminate\Support\Collection;

/**
 * RAG retrieval — TAVSİYE şeridi (FAQ + blog + üni/şehir açıklama).
 *
 * Sorguyu embed eder, kb_chunks vektörleriyle cosine sıralar, eşik üstü top-K döner.
 * Çok dilli: tüm dillerde içerik aranır; aktif locale'e küçük artı (kaynak tutarlılığı).
 *
 * Program şeridi (yapısal+semantik) Faz 3'te eklenecek. (doc/CHATBOT-RAG-PLAYBOOK.md)
 */
class Retriever
{
    /** Tavsiye şeridi kaynak türleri. */
    private const ADVICE_TYPES = ['faq', 'post', 'university', 'city'];

    /** Aktif locale eşleşmesine eklenen küçük benzerlik artısı. */
    private const LOCALE_BOOST = 0.03;

    public function __construct(private ?GeminiEmbedder $embedder = null)
    {
        $this->embedder ??= new GeminiEmbedder();
    }

    /**
     * @return array{results: array<int,array>, top: float}
     *   results: [ ['score','title','url','content','source_type','locale'], ... ]
     *   top: en yüksek skor (eşik kararı için)
     */
    public function retrieve(string $query, string $locale, int $k = 6): array
    {
        $query = trim($query);
        if ($query === '') return ['results' => [], 'top' => 0.0];

        $qv = $this->embedder->embedOne($query, GeminiEmbedder::TASK_QUERY);

        $scored = $this->candidates()
            ->map(function (array $c) use ($qv, $locale) {
                $score = GeminiEmbedder::dot($qv, $c['vec']);
                if ($c['locale'] === $locale) $score += self::LOCALE_BOOST;
                return [
                    'score'       => $score,
                    'title'       => $c['title'],
                    'url'         => $c['url'],
                    'content'     => $c['content'],
                    'source_type' => $c['source_type'],
                    'locale'      => $c['locale'],
                ];
            })
            ->sortByDesc('score')
            ->values();

        $top = $scored->isNotEmpty() ? (float) $scored[0]['score'] : 0.0;

        return [
            'results' => $scored->take($k)->all(),
            'top'     => $top,
        ];
    }

    /**
     * Aday chunk'lar (embedding unpack'li). Süreç-içi cache'lenir.
     * Ölçek: ~birkaç bin chunk × 768 float → PHP belleğinde sorunsuz.
     */
    private function candidates(): Collection
    {
        static $cache = null;
        if ($cache !== null) return $cache;

        $cache = KbChunk::query()
            ->whereIn('source_type', self::ADVICE_TYPES)
            ->whereNotNull('embedding')
            ->get(['source_type', 'locale', 'title', 'url', 'content', 'embedding'])
            ->map(fn (KbChunk $r) => [
                'source_type' => $r->source_type,
                'locale'      => $r->locale,
                'title'       => $r->title,
                'url'         => $r->url,
                'content'     => $r->content,
                'vec'         => GeminiEmbedder::unpack($r->embedding),
            ]);

        return $cache;
    }
}
