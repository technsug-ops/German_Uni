<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Models\University;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Alt-birim (enstitü/fakülte) kayıtlarını tespit eder ve istenirse ana kuruma merge eder.
 *
 * ÇÖZDÜĞÜ SORUN — [[duplicate-university-records]]:
 * Bazı üniversitelerin programları, o üninin bir alt-birimine ait AYRI bir universities
 * kaydına bağlanmış. Örnek: "Technische Universität Berlin Institut für Technische Akustik"
 * 36 mühendislik programı taşıyor ama qs/the/arwu NULL; asıl "Technische Universität Berlin"
 * kaydında ise QS #154 var, program yok. Sonuç: kalite bazlı alan sıralamasında asıl TU Berlin
 * hiç görünmüyor, yerine alt-birim 9. sırada çıkıyor.
 *
 * UniversitiesMergeDupes'tan FARKI: o komut DAAD shell ↔ HRK eşleşmesini ad BENZERLİĞİYLE
 * arar (similar_text). Alt-birim kayıtları ana kurumun adını TAM İÇERİR + ek token taşır,
 * bu yüzden benzerlik eşiğine takılmazlar. Buradaki tespit "prefix + ek token" mantığıyla.
 *
 * Kullanım:
 *   php artisan universities:subunits                    → yalnızca rapor (hiçbir şey değişmez)
 *   php artisan universities:subunits --merge=123,456    → SADECE bu ID'leri ana kuruma merge et
 *
 * Bilinçli olarak toplu --execute YOK: her merge geri alınamaz (kayıt silinir), bu yüzden
 * hangi ID'lerin birleşeceği rapora bakılarak elle onaylanır.
 */
class UniversitiesSubunits extends Command
{
    protected $signature = 'universities:subunits
        {--merge= : Virgülle ayrılmış alt-birim ID listesi — SADECE bunlar merge edilir}';

    protected $description = 'Alt-birim/enstitü üniversite kayıtlarını raporlar; --merge=ID,ID ile ana kuruma birleştirir.';

    /** Üniversite olmayan araştırma kurumları — ana kurumları yok, merge edilemez. */
    private const NON_UNIVERSITY_PREFIXES = [
        'max-planck', 'max planck', 'helmholtz', 'fraunhofer', 'leibniz-institut',
        'leibniz institute', 'deutsches zentrum', 'informationszentrum',
    ];

    public function handle(): int
    {
        $all = University::query()
            ->select([
                'id', 'slug', 'name_de', 'name_en', 'city_id', 'student_count',
                'qs_world_rank', 'the_world_rank', 'arwu_world_rank', 'is_active',
            ])
            ->withCount(['programs as programs_count' => fn ($q) => $q->where('is_active', 1)])
            ->get();

        $this->line('Toplam üniversite kaydı: ' . $all->count());

        [$subunits, $orphans] = $this->detect($all);

        $this->newLine();
        $this->info('═══ ALT-BİRİM ADAYLARI (ana kurumu bulunan) ═══');
        if ($subunits->isEmpty()) {
            $this->line('— yok —');
        } else {
            $this->table(
                ['Alt-birim ID', 'Alt-birim', 'prog', 'öğr', 'rank', '→ Ana ID', 'Ana kurum', 'prog', 'öğr', 'rank'],
                $subunits->map(fn ($m) => [
                    $m['sub']->id,
                    mb_substr($m['sub']->name_de ?? $m['sub']->name_en ?? '?', 0, 38),
                    $m['sub']->programs_count,
                    $m['sub']->student_count ?? '—',
                    $this->rankOf($m['sub']) ?? '—',
                    $m['parent']->id,
                    mb_substr($m['parent']->name_de ?? $m['parent']->name_en ?? '?', 0, 30),
                    $m['parent']->programs_count,
                    $m['parent']->student_count ?? '—',
                    $this->rankOf($m['parent']) ?? '—',
                ])->all()
            );
            $this->line('Merge komutu: php artisan universities:subunits --merge='
                . $subunits->pluck('sub.id')->implode(','));
        }

        $this->newLine();
        $this->info('═══ ÜNİVERSİTE OLMAYAN KAYITLAR (araştırma kurumu — merge edilemez) ═══');
        if ($orphans->isEmpty()) {
            $this->line('— yok —');
        } else {
            $this->line('Bunların ana kurumu yok; merge yerine is_active=0 düşünülmeli.');
            $this->table(
                ['ID', 'Ad', 'aktif', 'prog', 'öğr'],
                $orphans->map(fn ($u) => [
                    $u->id,
                    mb_substr($u->name_de ?? $u->name_en ?? '?', 0, 55),
                    $u->is_active ? 'evet' : 'hayır',
                    $u->programs_count,
                    $u->student_count ?? '—',
                ])->all()
            );
        }

        $mergeIds = array_filter(array_map('intval', explode(',', (string) $this->option('merge'))));
        if (!$mergeIds) {
            $this->newLine();
            $this->warn('RAPOR MODU — hiçbir kayıt değiştirilmedi.');
            return self::SUCCESS;
        }

        return $this->merge($subunits, $mergeIds);
    }

    /**
     * @return array{0: \Illuminate\Support\Collection, 1: \Illuminate\Support\Collection}
     */
    private function detect($all): array
    {
        // Ana kurum havuzu: adı kısa olanlar önce → en spesifik (en uzun) eşleşmeyi seçebilelim.
        $byNorm = [];
        foreach ($all as $u) {
            foreach ([$u->name_de, $u->name_en] as $n) {
                $norm = $this->normalize($n);
                if ($norm !== '' && str_word_count($norm) >= 2) {
                    $byNorm[$norm][] = $u;
                }
            }
        }

        $subunits = collect();
        $orphans = collect();

        foreach ($all as $u) {
            $name = mb_strtolower($u->name_de ?? $u->name_en ?? '');
            $norm = $this->normalize($u->name_de ?? $u->name_en);
            if ($norm === '') {
                continue;
            }

            $parent = null;
            $parentLen = 0;

            // KRİTİK: yalnızca adı içermek YETMEZ. Düz str_contains ile "technische universität
            // münchen", "universität münchen" kaydını içerdiği için TUM, LMU'nun alt-birimi
            // sanılırdı. Bu yüzden ayrıca alt-birim anahtar kelimesi aranır — gerçek alt-birim
            // kayıtları daima "Institut/Fakultät/Zentrum/..." taşır, kurumun kendisi taşımaz.
            if ($this->isSubunitName($norm)) {
                foreach ($byNorm as $candNorm => $cands) {
                    if ($candNorm === $norm || mb_strlen($candNorm) <= $parentLen) {
                        continue;
                    }
                    // Token sınırında içerme — "uni köln" ile "uni kölnisch" eşleşmesin.
                    if (!str_contains(" {$norm} ", " {$candNorm} ")) {
                        continue;
                    }
                    // Ana kurumun kendisi alt-birim adı taşımamalı (zincirleme merge önlenir).
                    if ($this->isSubunitName($candNorm)) {
                        continue;
                    }
                    foreach ($cands as $c) {
                        if ($c->id === $u->id) {
                            continue;
                        }
                        $parent = $c;
                        $parentLen = mb_strlen($candNorm);
                    }
                }
            }

            if ($parent) {
                $subunits->push(['sub' => $u, 'parent' => $parent]);
                continue;
            }

            foreach (self::NON_UNIVERSITY_PREFIXES as $p) {
                if (str_contains($name, $p)) {
                    $orphans->push($u);
                    break;
                }
            }
        }

        // En çok programı taşıyan alt-birim en üstte — etkisi en büyük olan.
        return [$subunits->sortByDesc(fn ($m) => $m['sub']->programs_count)->values(), $orphans->values()];
    }

    private function merge($subunits, array $mergeIds): int
    {
        $selected = $subunits->whereIn('sub.id', $mergeIds);

        if ($selected->count() !== count($mergeIds)) {
            $found = $selected->pluck('sub.id')->all();
            $missing = array_diff($mergeIds, $found);
            $this->error('Şu ID\'ler alt-birim adayı değil, merge edilmedi: ' . implode(',', $missing));
            if ($selected->isEmpty()) {
                return self::FAILURE;
            }
        }

        if (method_exists(University::class, 'disableSearchSyncing')) {
            University::disableSearchSyncing();
        }

        // KRİTİK: University'de WebhookEventObserver kayıtlı (updated/deleted → DeliverWebhook).
        // Abone uç noktası hata dönerse job RuntimeException fırlatıyor ve bu, merge'ün
        // DB::transaction'ı içinde patlayıp TÜM merge'i geri alıyor (prod'da birebir yaşandı:
        // abone HTTP 405 döndü, hiçbir kayıt birleşmedi). Kayıt konsolidasyonu zaten dışarıya
        // "üniversite silindi" diye duyurulmamalı — bu bir veri temizliği, iş olayı değil.
        return Model::withoutEvents(fn () => $this->runMerges($selected));
    }

    private function runMerges($selected): int
    {

        // Ana kuruma taşınacak alanlar: SADECE ana kurumda boşsa doldurulur.
        // Ad ve slug ASLA taşınmaz — doğru olanlar ana kurumda.
        $backfill = [
            'student_count', 'founded_year', 'website_url', 'logo_url', 'city_id', 'type',
            'qs_world_rank', 'the_world_rank', 'arwu_world_rank',
            'description_tr', 'description_en', 'description_de',
        ];

        $merged = $progMoved = $favMoved = 0;

        foreach ($selected as $m) {
            DB::transaction(function () use ($m, $backfill, &$progMoved, &$favMoved) {
                $sub = $m['sub'];
                $parent = University::findOrFail($m['parent']->id);
                $subFull = University::findOrFail($sub->id);

                $progMoved += Program::where('university_id', $sub->id)
                    ->update(['university_id' => $parent->id]);

                $favMoved += DB::table('favorites')
                    ->where('favoriteable_id', $sub->id)
                    ->where('favoriteable_type', University::class)
                    ->update(['favoriteable_id' => $parent->id]);

                foreach ($backfill as $f) {
                    if (blank($parent->{$f}) && filled($subFull->{$f})) {
                        $parent->{$f} = $subFull->{$f};
                    }
                }
                $parent->save();
                $subFull->delete();
            });

            $merged++;
            $this->line(sprintf(
                '✓ #%d %s → #%d %s',
                $m['sub']->id,
                mb_substr($m['sub']->name_de ?? '', 0, 40),
                $m['parent']->id,
                mb_substr($m['parent']->name_de ?? '', 0, 40)
            ));
        }

        $this->newLine();
        $this->info('═══ SONUÇ ═══');
        $this->line("Merge edilen alt-birim: $merged");
        $this->line("Taşınan program:        $progMoved");
        $this->line("Taşınan favori:         $favMoved");
        $this->line('Kalan toplam üni:       ' . University::count());
        $this->warn('Sıralama cache\'i: rankings.all.* anahtarları 6 saat TTL — gerekirse temizle.');

        return self::SUCCESS;
    }

    /**
     * Ad, bir alt-birime mi işaret ediyor? "Karlsruher Institut für Technologie" gibi
     * kurum adlarında da "institut" geçtiği için bu tek başına merge sebebi DEĞİLDİR —
     * yalnızca ana kurum adını token sınırında içeren kayıtlarda tetikleyici olarak kullanılır.
     */
    private function isSubunitName(string $norm): bool
    {
        foreach (['institut', 'fakultat', 'fakultät', 'fachbereich', 'zentrum', 'klinik',
                  'abteilung', 'lehrstuhl', 'seminar', 'bibliothek', 'museum', 'archiv'] as $kw) {
            if (str_contains($norm, $kw)) {
                return true;
            }
        }

        return false;
    }

    private function rankOf(University $u): ?int
    {
        $ranks = array_filter([$u->qs_world_rank, $u->the_world_rank, $u->arwu_world_rank]);

        return $ranks ? min($ranks) : null;
    }

    /** Ad normalizasyonu — noktalama/çoklu boşluk atılır, kurum tipi KORUNUR. */
    private function normalize(?string $s): string
    {
        if (!$s) {
            return '';
        }
        $s = mb_strtolower($s);
        $s = preg_replace('/[^a-z0-9äöüß ]/u', ' ', $s);

        return trim(preg_replace('/\s+/', ' ', $s));
    }
}
