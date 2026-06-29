<?php

namespace App\Console\Commands;

use App\Models\Faq;
use App\Models\KbChunk;
use App\Models\Post;
use App\Services\Rag\GeminiEmbedder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * RAG bilgi tabanını üret/güncelle — içeriği chunk'la, embed et, kb_chunks'a yaz.
 *
 * Artımlı: bir satırın chunk hash kümesi değişmediyse yeniden embed ETMEZ (API tasarrufu).
 * Lokalde DB + GEMINI_API_KEY ile çalışır; prod'da /admin/ops/kb-embed ile tetiklenir.
 *
 *   php artisan kb:embed --source=faq,post [--locale=tr] [--limit=50] [--fresh] [--dry-run]
 *
 * (doc/CHATBOT-RAG-PLAYBOOK.md — Faz 1: faq+post. program/university/city: Faz 3.)
 */
class KbEmbed extends Command
{
    protected $signature = 'kb:embed
        {--source=faq,post : Kaynak türleri (faq,post)}
        {--locale= : Sadece bu locale (boş=hepsi)}
        {--limit=0 : Kaynak türü başına işlenecek satır sınırı (0=sınırsız)}
        {--fresh : Bu kaynakların mevcut chunk\'larını önce sil}
        {--dry-run : Embed/yazma YOK; sadece chunk planını raporla}';

    protected $description = 'İçeriği vektörleyip kb_chunks bilgi tabanını üretir (RAG)';

    private GeminiEmbedder $embedder;
    private bool $dry;
    private array $buffer = [];   // bekleyen chunk meta'ları (flush'a kadar)
    private int $bufChunks = 0;
    private int $embedded = 0;
    private int $skipped = 0;
    private int $rows = 0;

    public function handle(): int
    {
        $this->dry = (bool) $this->option('dry-run');
        $sources = array_filter(array_map('trim', explode(',', (string) $this->option('source'))));
        $localeFilter = $this->option('locale') ?: null;
        $limit = (int) $this->option('limit');

        if (! $this->dry) {
            try {
                $this->embedder = new GeminiEmbedder();
            } catch (\Throwable $e) {
                $this->error($e->getMessage());
                return self::FAILURE;
            }
        }

        foreach ($sources as $src) {
            if (! in_array($src, ['faq', 'post'], true)) {
                $this->warn("Atlanıyor (Faz 1 dışı): $src");
                continue;
            }
            if ($this->option('fresh') && ! $this->dry) {
                $n = KbChunk::where('source_type', $src)
                    ->when($localeFilter, fn ($q) => $q->where('locale', $localeFilter))
                    ->delete();
                $this->line("  [$src] --fresh: $n eski chunk silindi");
            }
            $src === 'faq'
                ? $this->processFaqs($localeFilter, $limit)
                : $this->processPosts($localeFilter, $limit);
        }

        $this->flush(); // kalan buffer

        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━");
        $verb = $this->dry ? 'PLAN' : 'TAMAM';
        $this->info("$verb · satır: {$this->rows} · embed edilen chunk: {$this->embedded} · değişmeyen (atlandı): {$this->skipped}");
        if (! $this->dry) {
            $this->line('  kb_chunks toplam: ' . KbChunk::count());
        }
        return self::SUCCESS;
    }

    // ───────────────────────── FAQ ─────────────────────────

    private function processFaqs(?string $locale, int $limit): void
    {
        $q = Faq::query()->where('is_published', true)->with('topic')
            ->when($locale, fn ($q) => $q->where('locale', $locale))
            ->orderBy('id');
        if ($limit > 0) $q->limit($limit);

        $this->withProgress('faq', $q->count());
        $q->chunkById(200, function ($faqs) {
            foreach ($faqs as $f) {
                $this->rows++;
                $topicSlug = optional($f->topic)->slug ?: 'genel';
                $body = $this->stripMd((string) $f->answer_md);
                $text = trim($f->question . "\n\n" . $body);
                if ($text === '') continue;
                $url = '/' . $f->locale . '/faq/' . $topicSlug . '/' . $f->slug;
                $this->stage('faq', $f->id, $f->locale, [
                    ['title' => $f->question, 'url' => $url, 'content' => $text],
                ]);
            }
        });
    }

    // ───────────────────────── Blog/News ─────────────────────────

    private function processPosts(?string $locale, int $limit): void
    {
        $q = Post::query()->where('is_published', true)
            ->when($locale, fn ($q) => $q->where('locale', $locale))
            ->orderBy('id');
        if ($limit > 0) $q->limit($limit);

        $this->withProgress('post', $q->count());
        $q->chunkById(100, function ($posts) {
            foreach ($posts as $p) {
                $this->rows++;
                $seg = ($p->type === 'news') ? 'news' : 'blog';
                $url = '/' . $p->locale . '/' . $seg . '/' . $p->slug;
                $chunks = $this->chunkProse((string) $p->title, $this->stripMd((string) $p->content_md));
                $metas = [];
                foreach ($chunks as $c) {
                    $metas[] = ['title' => $p->title, 'url' => $url, 'content' => $c];
                }
                if ($metas) $this->stage('post', $p->id, $p->locale, $metas);
            }
        });
    }

    // ───────────────────────── Staging & incremental ─────────────────────────

    /**
     * Bir kaynak satırın chunk'larını sahnele. Artımlı: mevcut hash kümesi
     * birebir aynıysa atla; değiştiyse eskiyi sil + yeniyi embed kuyruğuna al.
     */
    private function stage(string $type, int $id, string $locale, array $metas): void
    {
        $hashes = array_map(fn ($m) => hash('sha256', $m['content']), $metas);

        if (! $this->dry) {
            $existing = KbChunk::where('source_type', $type)->where('source_id', $id)
                ->where('locale', $locale)->orderBy('chunk_index')
                ->pluck('content_hash')->all();
            if ($existing === $hashes && count($existing) > 0) {
                $this->skipped += count($metas);
                return;
            }
            // değişti → eskiyi temizle (yeni chunk'lar flush'ta yazılacak)
            KbChunk::where('source_type', $type)->where('source_id', $id)
                ->where('locale', $locale)->delete();
        }

        foreach ($metas as $i => $m) {
            $this->buffer[] = [
                'source_type'    => $type,
                'source_id'      => $id,
                'locale'         => $locale,
                'chunk_index'    => $i,
                'title'          => mb_substr($m['title'], 0, 255),
                'url'            => mb_substr($m['url'], 0, 512),
                'content'        => $m['content'],
                'token_estimate' => (int) ceil(mb_strlen($m['content']) / 4),
                'content_hash'   => $hashes[$i],
            ];
            $this->bufChunks++;
        }

        if ($this->bufChunks >= 100) $this->flush();
    }

    /** Bekleyen chunk'ları embed et + kb_chunks'a yaz. */
    private function flush(): void
    {
        if (empty($this->buffer)) return;

        if ($this->dry) {
            $this->embedded += $this->bufChunks;
            $this->buffer = [];
            $this->bufChunks = 0;
            return;
        }

        $texts = array_column($this->buffer, 'content');
        $vectors = $this->embedder->embedMany($texts, GeminiEmbedder::TASK_DOCUMENT);

        $now = now();
        $rowsToInsert = [];
        foreach ($this->buffer as $i => $row) {
            $row['embedding']   = GeminiEmbedder::pack($vectors[$i]);
            $row['dims']        = $this->embedder->dims();
            $row['model']       = $this->embedder->modelName();
            $row['embedded_at'] = $now;
            $row['created_at']  = $now;
            $row['updated_at']  = $now;
            $rowsToInsert[] = $row;
        }

        DB::transaction(function () use ($rowsToInsert) {
            foreach (array_chunk($rowsToInsert, 200) as $batch) {
                KbChunk::insert($batch);
            }
        });

        $this->embedded += $this->bufChunks;
        $this->buffer = [];
        $this->bufChunks = 0;
    }

    // ───────────────────────── Yardımcılar ─────────────────────────

    /** Markdown'ı kaba düz metne indir (embedding için gürültü azalt). */
    private function stripMd(string $md): string
    {
        $t = $md;
        $t = preg_replace('/!\[[^\]]*\]\([^)]*\)/u', ' ', $t);          // görsel
        $t = preg_replace('/\[([^\]]+)\]\([^)]*\)/u', '$1', $t);        // link → metin
        $t = preg_replace('/`{1,3}[^`]*`{1,3}/u', ' ', $t);            // kod
        $t = preg_replace('/^\s{0,3}#{1,6}\s*/mu', '', $t);            // başlık #
        $t = preg_replace('/^\s{0,3}>\s?/mu', '', $t);                 // alıntı
        $t = preg_replace('/[*_]{1,3}/u', '', $t);                     // bold/italic
        $t = preg_replace('/^\s{0,3}[-*+]\s+/mu', '• ', $t);          // liste
        $t = preg_replace('/\n{3,}/u', "\n\n", $t);
        return trim($t);
    }

    /**
     * Uzun prose'u ~1400 karakterlik (≈350 token) chunk'lara böl; paragraf
     * sınırını koru, her chunk'a başlık önekle (retrieval bağlamı).
     */
    private function chunkProse(string $title, string $body): array
    {
        $body = trim($body);
        if ($body === '') return [];
        $paras = preg_split('/\n{2,}/u', $body) ?: [$body];

        $chunks = [];
        $cur = '';
        foreach ($paras as $p) {
            $p = trim($p);
            if ($p === '') continue;
            if (mb_strlen($cur) + mb_strlen($p) + 2 > 1400 && $cur !== '') {
                $chunks[] = $cur;
                $cur = $p;
            } else {
                $cur = $cur === '' ? $p : $cur . "\n\n" . $p;
            }
        }
        if (trim($cur) !== '') $chunks[] = $cur;

        // Başlık önekle (her chunk kendi başına anlamlı olsun)
        return array_map(fn ($c) => $title . "\n\n" . $c, $chunks);
    }

    private function withProgress(string $label, int $total): void
    {
        $this->line("━━━ [$label] $total satır işleniyor ━━━");
    }
}
