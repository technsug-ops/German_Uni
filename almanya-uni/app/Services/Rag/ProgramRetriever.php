<?php

namespace App\Services\Rag;

use App\Models\City;
use App\Models\KbChunk;
use App\Models\Program;
use Illuminate\Support\Facades\Cache;

/**
 * RAG retrieval — PROGRAM şeridi (yapısal SQL ön-filtre + semantik sıralama).
 *
 * 14.5k aktif programın vektörünü her istekte taramak KAS'ta olmaz. Bunun yerine:
 *  1) Sorgudan yapısal kriter çıkar (derece / dil / NC / şehir) — kesin kısıtlar.
 *  2) programs tablosunu bu kriterlerle + FULLTEXT (konu) ile DAR bir aday kümesine indir.
 *  3) Yalnız adayların vektörlerini yükle, sorguyla cosine sırala → top-K.
 *
 * Böylece kesinlik (yapısal kısıt) + alaka (semantik) + KAS gerçekliği bir arada.
 * Program chunk'ı çok-dillidir (locale='mul'); URL retrieval'da aktif locale ile kurulur.
 * (doc/CHATBOT-RAG-PLAYBOOK.md §1)
 */
class ProgramRetriever
{
    /** FULLTEXT/yapısal filtre sonrası vektör yüklenecek azami aday. */
    private const CANDIDATE_CAP = 400;

    /** Aday vektörlerini DB'den çekme batch'i (bellek). */
    private const SCAN_BATCH = 200;

    /** Program adı/açıklaması üzerinde FULLTEXT kolonları (programs index ile eşleşir). */
    private const SEARCH_COLS = ['name_de', 'name_en', 'name_tr', 'description_tr', 'description_en'];

    public function __construct(private ?GeminiEmbedder $embedder = null)
    {
        $this->embedder ??= new GeminiEmbedder();
    }

    /**
     * @param array|null $queryVector  Önceden hesaplanmış sorgu vektörü (advice şeridiyle
     *                                  aynı embed'i paylaşsın diye); null ise burada üretilir.
     * @return array{results: array<int,array>, top: float}
     */
    public function retrieve(string $query, string $locale, int $k = 6, ?array $queryVector = null): array
    {
        $query = trim($query);
        if ($query === '') return ['results' => [], 'top' => 0.0];

        $f = $this->extractFilters($query);

        // 1) Yapısal + FULLTEXT ön-filtre → aday program id'leri.
        $ids = $this->candidateIds($query, $f);
        if (empty($ids)) return ['results' => [], 'top' => 0.0];

        // 2) Sorguyu embed et, aday vektörlerini yükleyip cosine sırala (akışlı).
        $qv = $queryVector ?? $this->embedder->embedOne($query, GeminiEmbedder::TASK_QUERY);
        $pool = [];
        KbChunk::query()
            ->where('source_type', 'program')
            ->whereIn('source_id', $ids)
            ->whereNotNull('embedding')
            ->select(['id', 'source_id', 'title', 'url', 'embedding'])
            ->chunkById(self::SCAN_BATCH, function ($rows) use (&$pool, $qv) {
                foreach ($rows as $r) {
                    $vec = GeminiEmbedder::unpack($r->embedding);
                    $pool[] = [
                        'score'     => GeminiEmbedder::dot($qv, $vec),
                        'id'        => $r->id,
                        'source_id' => $r->source_id,
                        'title'     => $r->title,
                        'url'       => $r->url,
                    ];
                }
            });

        if (empty($pool)) return ['results' => [], 'top' => 0.0];

        usort($pool, fn ($a, $b) => $b['score'] <=> $a['score']);
        $top = (float) $pool[0]['score'];
        $selected = array_slice($pool, 0, $k);

        $contents = KbChunk::whereIn('id', array_column($selected, 'id'))->pluck('content', 'id');
        $results = array_map(fn ($s) => [
            'score'       => $s['score'],
            'title'       => $s['title'],
            'url'         => $this->localeUrl($s['url'], $locale),
            'content'     => (string) ($contents[$s['id']] ?? ''),
            'source_type' => 'program',
            'locale'      => 'mul',
        ], $selected);

        return ['results' => $results, 'top' => $top];
    }

    /** Yapısal kriterlerle + FULLTEXT ile aday program id'lerini topla. */
    private function candidateIds(string $query, array $f): array
    {
        $base = fn () => Program::query()->where('is_active', true)
            ->when($f['degree'], fn ($q) => $q->where('degree', $f['degree']))
            ->when($f['languages'], fn ($q) => $q->whereIn('language', $f['languages']))
            ->when($f['admission'], fn ($q) => $q->where('admission_mode', $f['admission']))
            ->when($f['cityId'], fn ($q) => $q->whereHas('university', fn ($u) => $u->where('city_id', $f['cityId'])));

        $hasFilter = $f['degree'] || $f['languages'] || $f['admission'] || $f['cityId'];

        // Konu odaklı: DOĞAL DİL (OR + alaka) FULLTEXT — sohbet cümlesindeki dolgu
        // kelimeleri sonucu KISITLAMASIN (boolean +AND tüm terimleri zorlardı → 0 sonuç).
        // Aday kümesindeki gürültüyü semantik cosine yeniden sıralaması düzeltir.
        $term = $this->topicTerm($query);
        $ids = [];
        if ($term !== '') {
            $cols = implode(',', array_map(fn ($c) => '`' . $c . '`', self::SEARCH_COLS));
            $ids = $base()
                ->whereFullText(self::SEARCH_COLS, $term) // mode yok = natural language
                ->orderByRaw("MATCH({$cols}) AGAINST(?) DESC", [$term])
                ->limit(self::CANDIDATE_CAP)
                ->pluck('id')->all();
        }

        // FULLTEXT boş döndüyse ama yapısal kısıt varsa: yalnız kısıtla devam et
        // (ör. "Berlin'de ingilizce master" — konu kelimesi az ama kriter net).
        if (empty($ids) && $hasFilter) {
            $ids = $base()->limit(self::CANDIDATE_CAP)->pluck('id')->all();
        }

        return $ids;
    }

    /** Sorgudan konu terimlerini ayıkla (dolgu/durak kelimeleri at → FULLTEXT odaklansın). */
    private function topicTerm(string $query): string
    {
        $tokens = preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($query), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $keep = array_filter($tokens, fn ($t) => mb_strlen($t) >= 3 && ! in_array($t, self::STOPWORDS, true));
        return trim(implode(' ', $keep));
    }

    /** TR/DE/EN dolgu kelimeleri (FULLTEXT alaka gürültüsünü azaltır). */
    private const STOPWORDS = [
        // TR
        'var', 'mı', 'mi', 'mu', 'için', 'nasıl', 'nedir', 'hangi', 'bir', 'ile', 'olan',
        'okumak', 'okunabilecek', 'bölüm', 'bölümü', 'bölümleri', 'program', 'programı', 'programları',
        'öner', 'önerir', 'misin', 'istiyorum', 'arıyorum', 'almanya', 'almanyada', 'üniversite',
        'olmadan', 'olmayan', 'yok', 'lütfen', 'bana', 'kabul', 'eden', 'şehir', 'şehri',
        // DE
        'gibt', 'studiengang', 'studiengänge', 'studium', 'deutschland', 'welche', 'ohne', 'für', 'oder', 'und',
        // EN
        'the', 'and', 'for', 'with', 'without', 'which', 'study', 'program', 'programs', 'programme',
        'degree', 'university', 'germany', 'can', 'are', 'there', 'any', 'recommend', 'want',
    ];

    /** Sorgudan yapısal kriter çıkar (TR/DE/EN sezgisel; emin değilse null). */
    private function extractFilters(string $query): array
    {
        $q = ' ' . mb_strtolower($query) . ' ';

        $degree = null;
        if (preg_match('/\b(master|yüksek lisans|yuksek lisans|m\.?sc|m\.?a\b|masterstudium)\b/u', $q)) $degree = 'master';
        elseif (preg_match('/\b(bachelor|lisans|b\.?sc|b\.?a\b|bachelorstudium|undergraduate)\b/u', $q)) $degree = 'bachelor';
        elseif (preg_match('/\b(phd|ph\.?d|doktora|doctorate|promotion|doktorand)\b/u', $q)) $degree = 'phd';

        // Dil: 'both' programlar hem en hem de aramasına dahil.
        $languages = null;
        if (preg_match('/\b(ingilizce|i̇ngilizce|english|englisch)\b/u', $q)) $languages = ['en', 'both'];
        elseif (preg_match('/\b(almanca|german|deutsch|deutschsprachig)\b/u', $q)) $languages = ['de', 'both'];

        $admission = null;
        if (preg_match('/(nc.?frei|zulassungsfrei|nc.?siz|nc.?suz|kontenjansız|kontenjansiz|nc yok|nc olmadan|nc.?olmayan|ohne nc|nc.?free|no nc|numerus clausus (yok|olmadan|siz))/u', $q)) {
            $admission = 'zulassungsfrei';
        }

        return [
            'degree'    => $degree,
            'languages' => $languages,
            'admission' => $admission,
            'cityId'    => $this->matchCity($q),
        ];
    }

    /** Sorgu metninde geçen aktif şehir adını id'ye eşle (en uzun ad öncelikli). */
    private function matchCity(string $loweredQuery): ?int
    {
        $map = Cache::remember('rag.city_name_map', 3600, function () {
            $out = [];
            City::query()->where('is_active', true)
                ->select(['id', 'name_de', 'name_tr', 'name_en'])
                ->get()
                ->each(function ($c) use (&$out) {
                    foreach ([$c->name_de, $c->name_tr, $c->name_en] as $n) {
                        $n = trim(mb_strtolower((string) $n));
                        if (mb_strlen($n) >= 3) $out[$n] = $c->id;
                    }
                });
            // En uzun ad önce → "frankfurt am main", "frankfurt"tan önce denensin.
            uksort($out, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));
            return $out;
        });

        foreach ($map as $name => $id) {
            // Unicode harf sınırı + opsiyonel Türkçe kesme-ekli biçim ("Berlin'de", "Köln'deki").
            // Sol sınır harf-olmayan olmalı ("essen" → "interessante" içinde EŞLEŞMEZ); ad ya tam
            // kelime biter ya da kesme işareti+ek ile devam eder.
            $n = preg_quote($name, '/');
            if (preg_match('/(?<![\p{L}])' . $n . '(?:[\x{2019}\x{2018}\'`][\p{L}]*)?(?![\p{L}])/u', $loweredQuery)) {
                return $id;
            }
        }
        return null;
    }

    /** Locale-bağımsız chunk URL'ine aktif locale önekini ekle. */
    private function localeUrl(string $url, string $locale): string
    {
        if (! str_starts_with($url, '/programs/')) return $url;
        return '/' . $locale . $url;
    }
}
