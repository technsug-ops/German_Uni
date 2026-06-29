<?php

namespace App\Services\Rag;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Gemini çok dilli embedding sarmalayıcısı (gemini-embedding-001).
 *
 * - Asimetrik task-type: indeksleme RETRIEVAL_DOCUMENT, sorgu RETRIEVAL_QUERY.
 * - Vektörler L2-normalize edilir → benzerlik = nokta çarpımı (cosine).
 * - Depolama: float32 little-endian binary (pack 'g'). kb_chunks.embedding.
 *
 * (doc/CHATBOT-RAG-PLAYBOOK.md)
 */
class GeminiEmbedder
{
    private const API = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public const TASK_DOCUMENT = 'RETRIEVAL_DOCUMENT';
    public const TASK_QUERY    = 'RETRIEVAL_QUERY';

    /** Tek istekteki güvenli karakter tavanı (token≈char/4; ~2k token sınırının altında). */
    private const MAX_CHARS = 8000;

    /** batchEmbedContents başına istek sayısı. */
    private const BATCH = 100;

    private string $key;
    private string $model;
    private int $dims;

    public function __construct(?string $key = null, ?string $model = null, ?int $dims = null)
    {
        $this->key   = $key   ?? (string) config('services.gemini.key');
        $this->model = $model ?? (string) config('services.gemini.embed_model', 'gemini-embedding-001');
        $this->dims  = $dims  ?? (int) config('services.gemini.embed_dims', 768);
        if ($this->key === '') {
            throw new RuntimeException('GEMINI_API_KEY yok — embedding yapılamaz.');
        }
    }

    public function dims(): int
    {
        return $this->dims;
    }

    public function modelName(): string
    {
        return $this->model;
    }

    /** Tek metni embed et → L2-normalize float dizisi. */
    public function embedOne(string $text, string $taskType = self::TASK_DOCUMENT): array
    {
        return $this->embedMany([$text], $taskType)[0];
    }

    /**
     * Çok sayıda metni embed et (otomatik 100'lük parçalara böler).
     * Dönüş: girişle aynı sırada, her biri L2-normalize float dizisi.
     */
    public function embedMany(array $texts, string $taskType = self::TASK_DOCUMENT): array
    {
        $out = [];
        foreach (array_chunk($texts, self::BATCH) as $batch) {
            foreach ($this->callBatch(array_values($batch), $taskType) as $vec) {
                $out[] = $vec;
            }
        }
        return $out;
    }

    /** Bir parça (<=100) için batchEmbedContents çağrısı. */
    private function callBatch(array $texts, string $taskType): array
    {
        $requests = [];
        foreach ($texts as $t) {
            $clean = mb_substr(trim($t), 0, self::MAX_CHARS);
            if ($clean === '') $clean = '-'; // boş içerik API'yi kızdırmasın
            $requests[] = [
                'model'   => 'models/' . $this->model,
                'content' => ['parts' => [['text' => $clean]]],
                'taskType' => $taskType,
                'outputDimensionality' => $this->dims,
            ];
        }

        $resp = Http::asJson()
            ->timeout(120)
            ->withHeaders(['x-goog-api-key' => $this->key])
            ->retry(3, 3000, throw: false)
            ->post(self::API . $this->model . ':batchEmbedContents', ['requests' => $requests]);

        if (! $resp->ok()) {
            throw new RuntimeException('Embed HTTP ' . $resp->status() . ': ' . mb_substr($resp->body(), 0, 300));
        }

        $embeddings = $resp->json('embeddings') ?? [];
        if (count($embeddings) !== count($texts)) {
            throw new RuntimeException('Embed sayısı uyuşmuyor: ' . count($embeddings) . ' / ' . count($texts));
        }

        return array_map(fn ($e) => $this->normalize($e['values'] ?? []), $embeddings);
    }

    /** L2-normalize. */
    public function normalize(array $vec): array
    {
        $sum = 0.0;
        foreach ($vec as $v) $sum += $v * $v;
        $norm = sqrt($sum);
        if ($norm <= 1e-12) return $vec;
        return array_map(fn ($v) => $v / $norm, $vec);
    }

    /** float dizisi → float32 LE binary (depolama). */
    public static function pack(array $vec): string
    {
        return pack('g*', ...$vec);
    }

    /** float32 LE binary → float dizisi. */
    public static function unpack(string $bin): array
    {
        if ($bin === '') return [];
        return array_values(unpack('g*', $bin));
    }

    /** İki (normalize) vektörün nokta çarpımı = cosine benzerliği. */
    public static function dot(array $a, array $b): float
    {
        $s = 0.0;
        $n = min(count($a), count($b));
        for ($i = 0; $i < $n; $i++) $s += $a[$i] * $b[$i];
        return $s;
    }
}
