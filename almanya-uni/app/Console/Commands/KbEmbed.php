<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Faq;
use App\Models\KbChunk;
use App\Models\Post;
use App\Models\Program;
use App\Models\University;
use App\Services\Rag\GeminiEmbedder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * RAG bilgi tabanını üret/güncelle — içeriği chunk'la, embed et, kb_chunks'a yaz.
 *
 * Artımlı: bir satırın chunk hash kümesi değişmediyse yeniden embed ETMEZ (API tasarrufu).
 * Lokalde DB + GEMINI_API_KEY ile çalışır; prod'da /admin/ops/kb-embed ile tetiklenir.
 *
 *   php artisan kb:embed --source=faq,post,university,city,program [--locale=tr] [--limit=50] [--fresh] [--dry-run]
 *
 * (doc/CHATBOT-RAG-PLAYBOOK.md — Faz 1: faq+post. Faz 3: university/city + program.)
 *
 * Şeritler:
 *  - TAVSİYE (saf semantik): faq, post, university, city → her locale ayrı chunk.
 *  - PROGRAM (yapısal+semantik): program → 1 çok-dilli chunk/program (locale='mul'),
 *    URL'i locale-bağımsız (`/programs/{slug}`); retrieval'da aktif locale eklenir.
 */
class KbEmbed extends Command
{
    private const SOURCES = ['faq', 'post', 'university', 'city', 'program'];

    protected $signature = 'kb:embed
        {--source=faq,post : Kaynak türleri (faq,post,university,city,program)}
        {--locale= : Sadece bu locale (boş=hepsi; program çok-dilli, etkilenmez)}
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
            if (! in_array($src, self::SOURCES, true)) {
                $this->warn("Atlanıyor (bilinmeyen kaynak): $src");
                continue;
            }
            // Program çok-dilli (locale='mul') → locale filtresi onu kapsamaz.
            $localeForSrc = $src === 'program' ? null : $localeFilter;
            if ($this->option('fresh') && ! $this->dry) {
                $n = KbChunk::where('source_type', $src)
                    ->when($localeForSrc, fn ($q) => $q->where('locale', $localeForSrc))
                    ->delete();
                $this->line("  [$src] --fresh: $n eski chunk silindi");
            }
            match ($src) {
                'faq'        => $this->processFaqs($localeFilter, $limit),
                'post'       => $this->processPosts($localeFilter, $limit),
                'university' => $this->processUniversities($localeFilter, $limit),
                'city'       => $this->processCities($localeFilter, $limit),
                'program'    => $this->processPrograms($limit),
            };
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

    // ───────────────────────── University ─────────────────────────

    private function processUniversities(?string $locale, int $limit): void
    {
        $locales = $locale ? [$locale] : ['tr', 'en', 'de'];
        $q = University::query()->where('is_active', true)->with('city')->orderBy('id');
        if ($limit > 0) $q->limit($limit);

        $this->withProgress('university', $q->count());
        $q->chunkById(100, function ($unis) use ($locales) {
            foreach ($unis as $u) {
                $this->rows++;
                foreach ($locales as $loc) {
                    $name = (string) ($u->{'name_' . $loc} ?: $u->name_de);
                    $desc = $this->stripMd((string) ($u->{'description_' . $loc} ?? ''));
                    $blocks = $this->blocksToText($u->{$loc === 'tr' ? 'content_blocks' : 'content_blocks_' . $loc});
                    $cityName = (string) ($u->city?->{'name_' . $loc} ?: $u->city?->name_de);
                    $body = trim($desc . "\n\n" . $blocks);
                    if ($body === '') continue; // bu locale için içerik yok → atla
                    $head = trim($name . ($cityName ? " — {$cityName}" : ''));
                    $url = '/' . $loc . '/universities/' . $u->slug;
                    $chunks = $this->chunkProse($head, $body);
                    $metas = array_map(fn ($c) => ['title' => $name, 'url' => $url, 'content' => $c], $chunks);
                    if ($metas) $this->stage('university', $u->id, $loc, $metas);
                }
            }
        });
    }

    // ───────────────────────── City ─────────────────────────

    private function processCities(?string $locale, int $limit): void
    {
        $locales = $locale ? [$locale] : ['tr', 'en', 'de'];
        $q = City::query()->where('is_active', true)->with('state')->orderBy('id');
        if ($limit > 0) $q->limit($limit);

        $this->withProgress('city', $q->count());
        $q->chunkById(100, function ($cities) use ($locales) {
            foreach ($cities as $c) {
                $this->rows++;
                foreach ($locales as $loc) {
                    $name = (string) ($c->{'name_' . $loc} ?: $c->name_de);
                    $blocks = $this->blocksToText($c->{$loc === 'tr' ? 'content_blocks' : 'content_blocks_' . $loc});
                    if (trim($blocks) === '') continue;
                    $url = '/' . $loc . '/cities/' . $c->slug;
                    $chunks = $this->chunkProse($name, $blocks);
                    $metas = array_map(fn ($ch) => ['title' => $name, 'url' => $url, 'content' => $ch], $chunks);
                    if ($metas) $this->stage('city', $c->id, $loc, $metas);
                }
            }
        });
    }

    // ───────────────────────── Program (çok-dilli tek chunk) ─────────────────────────

    /** Yapısal alan etiketleri (çok-dilli embedding'e bağlam katmak için). */
    private const DEGREE_LABEL = [
        'bachelor' => 'Bachelor / Lisans', 'master' => 'Master / Yüksek Lisans',
        'phd' => 'PhD / Doktora / Promotion', 'studienkolleg' => 'Studienkolleg',
        'sprachkurs' => 'Sprachkurs / Dil Kursu',
    ];
    private const LANG_LABEL = [
        'en' => 'İngilizce / English / Englisch', 'de' => 'Almanca / German / Deutsch',
        'both' => 'İngilizce+Almanca / English & German', 'other' => 'Diğer dil',
    ];
    private const ADMISSION_LABEL = [
        'zulassungsfrei' => 'NC yok — zulassungsfrei (kontenjansız, serbest başvuru)',
        'oertlich' => 'Yerel NC — örtlich zulassungsbeschränkt', 'bundesweit' => 'Ülke geneli NC — bundesweit',
        'auswahl' => 'Seçme sınavı / Auswahlverfahren',
    ];

    private function processPrograms(int $limit): void
    {
        $q = Program::query()->where('is_active', true)
            ->with(['university:id,name_de,name_en,name_tr,city_id', 'university.city:id,name_de,name_en,name_tr', 'field:id,name_tr,name_en,name_de'])
            ->orderBy('id');
        if ($limit > 0) $q->limit($limit);

        $this->withProgress('program', $q->count());
        $q->chunkById(300, function ($programs) {
            foreach ($programs as $p) {
                $this->rows++;
                $content = $this->programText($p);
                if (trim($content) === '') continue;
                $title = (string) ($p->name_de ?: $p->name_en ?: $p->name_tr);
                // URL locale-bağımsız saklanır; retrieval aktif locale'i ekler.
                $url = '/programs/' . $p->slug;
                $this->stage('program', $p->id, 'mul', [
                    ['title' => $title, 'url' => $url, 'content' => $content],
                ]);
            }
        });
    }

    /** Bir programın çok-dilli embed metnini kur (ad + yapısal alanlar + açıklamalar + üni/şehir/alan). */
    private function programText(Program $p): string
    {
        $names = array_values(array_unique(array_filter([$p->name_de, $p->name_en, $p->name_tr])));
        $lines = [];
        if ($names) $lines[] = implode(' · ', $names);

        $field = $p->field;
        if ($field) {
            $fn = array_values(array_unique(array_filter([$field->name_tr, $field->name_en, $field->name_de])));
            if ($fn) $lines[] = 'Alan / Field: ' . implode(' · ', $fn);
        }
        if ($p->degree && isset(self::DEGREE_LABEL[$p->degree])) $lines[] = 'Derece: ' . self::DEGREE_LABEL[$p->degree];
        if ($p->language && isset(self::LANG_LABEL[$p->language])) $lines[] = 'Dil: ' . self::LANG_LABEL[$p->language];
        if ($p->admission_mode && isset(self::ADMISSION_LABEL[$p->admission_mode])) $lines[] = 'Kabul: ' . self::ADMISSION_LABEL[$p->admission_mode];

        $uni = $p->university;
        if ($uni) {
            $un = (string) ($uni->name_de ?: $uni->name_en);
            $city = (string) ($uni->city?->name_de ?: $uni->city?->name_en);
            $lines[] = 'Üniversite: ' . trim($un . ($city ? " ({$city})" : ''));
        }

        $desc = $this->stripMd((string) ($p->description_tr ?? '')) . "\n\n" . $this->stripMd((string) ($p->description_en ?? ''));
        $desc = trim($desc);
        if ($desc !== '') $lines[] = $desc;

        return trim(implode("\n", $lines));
    }

    /** content_blocks dizisini düz metne indir (intro/body_md/h alanları). */
    private function blocksToText($blocks): string
    {
        if (! is_array($blocks)) return '';
        $parts = [];
        foreach ($blocks as $b) {
            if (! is_array($b)) continue;
            foreach (['h', 'title', 'body_md', 'body', 'text'] as $f) {
                if (! empty($b[$f]) && is_string($b[$f])) $parts[] = $b[$f];
            }
        }
        return $this->stripMd(implode("\n\n", $parts));
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
