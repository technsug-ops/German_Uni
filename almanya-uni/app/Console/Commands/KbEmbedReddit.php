<?php

namespace App\Console\Commands;

use App\Models\KbChunk;
use App\Services\Rag\GeminiEmbedder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Reddit (r/germany) topluluk Q&A bilgisini RAG kb_chunks'a embed eder.
 *
 * source_type = 'community'. resources/data/community/reddit_kb.json okur
 * (build_kb_reddit.py üretir: soru + en yüksek puanlı topluluk cevabı).
 * Artımlı: content_hash zaten varsa atlar (API tasarrufu). KbEmbed ile AYNI
 * embedder/model (gemini-embedding-001, 768d, L2-norm) → aynı vektör uzayı.
 *
 *   php artisan kb:embed-reddit [--limit=0] [--fresh] [--dry-run]
 *
 * ⚠️ Retriever::ADVICE_TYPES içine 'community' eklenmeli, yoksa asistan
 *    bu chunk'ları GETİRMEZ (embed edilir ama aramada görünmez).
 */
class KbEmbedReddit extends Command
{
    protected $signature = 'kb:embed-reddit
        {--limit=0 : İşlenecek chunk sınırı (0=hepsi)}
        {--fresh : Mevcut community chunk\'larını önce sil}
        {--dry-run : Embed/yazma YOK; sadece plan}';

    protected $description = 'Reddit topluluk Q&A bilgisini RAG kb_chunks\'a embed eder (source_type=community)';

    private const SOURCE = 'community';
    private const LOCALE = 'tr';   // TR kitle (içerik EN; çok-dilli embedding TR sorguyla eşleşir)
    private const BATCH = 100;

    public function handle(): int
    {
        $path = base_path('resources/data/community/reddit_kb.json');
        if (! is_file($path)) {
            $this->error("Dosya yok: $path (build_kb_reddit.py ile üret + kopyala)");
            return self::FAILURE;
        }
        $chunks = json_decode(file_get_contents($path), true)['chunks'] ?? [];
        if (! $chunks) { $this->error('reddit_kb.json boş.'); return self::FAILURE; }

        $dry = (bool) $this->option('dry-run');
        $limit = (int) $this->option('limit');

        if ($this->option('fresh') && ! $dry) {
            $n = KbChunk::where('source_type', self::SOURCE)->delete();
            $this->line("--fresh: $n eski community chunk silindi");
        }

        // Artımlı: mevcut content_hash kümesi
        $existing = $dry ? [] : KbChunk::where('source_type', self::SOURCE)->pluck('content_hash')->flip()->all();

        // Hazırla + dedup (mevcut ve run-içi)
        $pending = [];
        $skipped = 0;
        foreach ($chunks as $c) {
            $content = trim($c['content'] ?? '');
            if ($content === '') continue;
            $hash = hash('sha256', $content);
            if (isset($existing[$hash])) { $skipped++; continue; }
            $existing[$hash] = true;
            $pending[] = [
                'title'   => mb_substr($c['title'] ?? '', 0, 255),
                'url'     => mb_substr($c['url'] ?? '', 0, 512),
                'content' => $content,
                'hash'    => $hash,
            ];
        }

        // Limit'i DEDUP'tan SONRA uygula → ?limit=N her çağrıda SIRADAKİ N embed-edilmemişi alır
        // (timeout'suz parça parça: 8.6k'yı ?limit=1000 ile ~9 kez çağır).
        $totalPending = count($pending);
        if ($limit > 0) $pending = array_slice($pending, 0, $limit);

        $this->info(count($pending) . ' chunk embed edilecek (kalan toplam: ' . $totalPending . '), ' . $skipped . ' atlandı (zaten var).');
        if ($dry || empty($pending)) {
            $this->info('Toplam community chunk: ' . ($dry ? '(dry)' : KbChunk::where('source_type', self::SOURCE)->count()));
            return self::SUCCESS;
        }

        $embedder = new GeminiEmbedder();
        $sid = (int) (KbChunk::where('source_type', self::SOURCE)->max('source_id') ?? 0);
        $embedded = 0;

        foreach (array_chunk($pending, self::BATCH) as $bi => $batch) {
            try {
                $vectors = $embedder->embedMany(array_column($batch, 'content'), GeminiEmbedder::TASK_DOCUMENT);
            } catch (\Throwable $e) {
                $this->error('  batch ' . ($bi + 1) . ' embed hata: ' . mb_substr($e->getMessage(), 0, 120));
                continue;
            }
            $now = now();
            $rows = [];
            foreach ($batch as $i => $c) {
                $rows[] = [
                    'source_type'    => self::SOURCE,
                    'source_id'      => ++$sid,
                    'locale'         => self::LOCALE,
                    'chunk_index'    => 0,
                    'title'          => $c['title'],
                    'url'            => $c['url'],
                    'content'        => $c['content'],
                    'token_estimate' => (int) ceil(mb_strlen($c['content']) / 4),
                    'embedding'      => GeminiEmbedder::pack($vectors[$i]),
                    'dims'           => $embedder->dims(),
                    'model'          => $embedder->modelName(),
                    'content_hash'   => $c['hash'],
                    'embedded_at'    => $now,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }
            DB::transaction(fn () => KbChunk::insert($rows));
            $embedded += count($rows);
            $this->line('  batch ' . ($bi + 1) . ': +' . count($rows) . " (toplam $embedded)");
        }

        $this->newLine();
        $this->info("✅ $embedded chunk embed edildi. Toplam community: " . KbChunk::where('source_type', self::SOURCE)->count());
        $this->warn('→ Retriever ADVICE_TYPES\'a "community" ekli olmalı (yoksa asistan getirmez).');
        return self::SUCCESS;
    }
}
