<?php

namespace App\Services\Rag;

use App\Models\KbChunk;

/**
 * RAG retrieval — TAVSİYE şeridi (FAQ + blog + üni/şehir açıklama).
 *
 * Sorguyu embed eder, kb_chunks vektörleriyle cosine sıralar, eşik üstü top-K döner.
 * Çok dilli: tüm dillerde içerik aranır; aktif locale'e küçük artı (kaynak tutarlılığı).
 *
 * BELLEK: vektörler AKIŞ halinde (chunkById) tek tek açılıp skorlanır, hiçbir zaman
 * tüm vektörler aynı anda bellekte tutulmaz (KAS PHP memory_limit güvenliği). İçerik
 * metni yalnızca kazanan top-K için çekilir. (doc/CHATBOT-RAG-PLAYBOOK.md)
 */
class Retriever
{
    /** Tavsiye şeridi kaynak türleri. ('community' = r/germany topluluk Q&A, kb:embed-reddit) */
    private const ADVICE_TYPES = ['faq', 'post', 'university', 'city', 'community'];

    /** Aktif locale eşleşmesine eklenen küçük benzerlik artısı. */
    private const LOCALE_BOOST = 0.03;

    /**
     * Community (r/germany) YUMUŞAK kaynak — anekdot/görüş, otoriter değil. Skoruna ceza
     * verilir ki FAQ/blog/program/üni gibi otoriter içerik faktüel sorularda community'yi
     * HEP geçsin; community yalnız hiçbir otoriter kaynağın güçlü eşleşmediği deneyimsel
     * sorularda öne çıksın. (chat:eval: cezasız groundedness 0.83→0.78 düşüyordu.)
     */
    private const COMMUNITY_PENALTY = 0.08;

    /** Tarama sırasında bellekte tutulan aday havuzu (top-K'dan geniş). */
    private const POOL = 40;

    /** Tek seferde DB'den çekilen satır (bellek tavanı). */
    private const SCAN_BATCH = 400;

    public function __construct(private ?GeminiEmbedder $embedder = null)
    {
        $this->embedder ??= new GeminiEmbedder();
    }

    /**
     * @param array|null $queryVector  Önceden hesaplanmış sorgu vektörü (iki şerit
     *                                  aynı embed'i paylaşsın diye); null ise burada üretilir.
     * @return array{results: array<int,array>, top: float}
     */
    public function retrieve(string $query, string $locale, int $k = 6, ?array $queryVector = null): array
    {
        $query = trim($query);
        if ($query === '') return ['results' => [], 'top' => 0.0];

        $qv = $queryVector ?? $this->embedder->embedOne($query, GeminiEmbedder::TASK_QUERY);

        // Akışlı tarama: her vektörü aç, skorla, SADECE top havuzunu tut (vektörü at).
        $pool = [];
        KbChunk::query()
            ->whereIn('source_type', self::ADVICE_TYPES)
            ->whereNotNull('embedding')
            ->select(['id', 'source_type', 'locale', 'title', 'url', 'embedding'])
            ->chunkById(self::SCAN_BATCH, function ($rows) use (&$pool, $qv, $locale) {
                foreach ($rows as $r) {
                    $vec = GeminiEmbedder::unpack($r->embedding);
                    $score = GeminiEmbedder::dot($qv, $vec);
                    if ($r->locale === $locale) $score += self::LOCALE_BOOST;
                    if ($r->source_type === 'community') $score -= self::COMMUNITY_PENALTY;
                    $pool[] = [
                        'score'       => $score,
                        'id'          => $r->id,
                        'title'       => $r->title,
                        'url'         => $r->url,
                        'source_type' => $r->source_type,
                        'locale'      => $r->locale,
                    ];
                    // $vec burada kapsam dışı → GC; tüm vektörler asla birlikte tutulmaz
                }
                // Havuzu periyodik buda (bellek sabit kalsın)
                usort($pool, fn ($a, $b) => $b['score'] <=> $a['score']);
                $pool = array_slice($pool, 0, self::POOL);
            });

        if (empty($pool)) return ['results' => [], 'top' => 0.0];

        usort($pool, fn ($a, $b) => $b['score'] <=> $a['score']);
        $top = (float) $pool[0]['score'];
        $selected = array_slice($pool, 0, $k);

        // İçerik metnini yalnız kazananlar için çek (bellek tasarrufu)
        $contents = KbChunk::whereIn('id', array_column($selected, 'id'))->pluck('content', 'id');
        $results = array_map(fn ($s) => [
            'score'       => $s['score'],
            'title'       => $s['title'],
            'url'         => $s['url'],
            'content'     => (string) ($contents[$s['id']] ?? ''),
            'source_type' => $s['source_type'],
            'locale'      => $s['locale'],
        ], $selected);

        return ['results' => $results, 'top' => $top];
    }
}
