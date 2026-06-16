<?php

namespace App\Console\Commands;

use App\Models\University;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * quick_facts bloklarındaki DETERMİNİSTİK doğrulanabilir alanları DB gerçeğiyle düzeltir:
 *  - Program sayıları (toplam/lisans/yüksek lisans/doktora) → gerçek aktif program sayısı
 *  - Üniversite tipi → SADECE traegerschaft doluyken ve quick_facts değeri onunla
 *    çelişiyorsa (ör. "Devlet" ama traeger=privat). traeger boşsa DOKUNMAZ (DB'den çözülemez).
 *
 * 3 locale variant'ında çalışır (content_blocks=tr, _en, _de). AI etiketleri locale + üni
 * bazında değişken olduğundan anahtar-kelime ile eşleşir; emin olmadığı alana dokunmaz.
 *
 *   php artisan universities:fix-quickfacts           → DRY-RUN (her değişikliği gösterir)
 *   php artisan universities:fix-quickfacts --apply
 */
class UniversitiesFixQuickFacts extends Command
{
    protected $signature = 'universities:fix-quickfacts {--apply : Yaz} {--samples=20 : Gösterilecek örnek değişiklik}';

    protected $description = 'quick_facts program-sayısı + tip alanlarını DB gerçeğiyle düzeltir.';

    private array $localeOf = ['content_blocks' => 'tr', 'content_blocks_en' => 'en', 'content_blocks_de' => 'de'];

    public function handle(): int
    {
        $apply = $this->option('apply');
        $samples = (int) $this->option('samples');
        $this->info($apply ? '🔥 APPLY' : '🔍 DRY-RUN');

        // Tüm aktif üni için gerçek program sayıları (tek sorgu)
        $counts = DB::table('programs')->where('is_active', 1)
            ->select('university_id', 'degree', DB::raw('count(*) c'))
            ->groupBy('university_id', 'degree')->get()
            ->groupBy('university_id');

        $fixedCount = 0; $fixedType = 0; $shown = 0; $uniTouched = 0;

        University::where('is_active', 1)
            ->select('id', 'name_de', 'traegerschaft', 'content_blocks', 'content_blocks_en', 'content_blocks_de')
            ->chunkById(200, function ($unis) use ($apply, $samples, $counts, &$fixedCount, &$fixedType, &$shown, &$uniTouched) {
                foreach ($unis as $u) {
                    $real = $this->realCounts($counts->get($u->id));
                    $typePolarity = $this->traegerPolarity($u->traegerschaft);

                    $dirty = false;
                    foreach ($this->localeOf as $col => $locale) {
                        $blocks = $u->{$col};
                        if (! is_array($blocks)) continue;
                        foreach ($blocks as &$b) {
                            if (($b['type'] ?? '') !== 'quick_facts' || empty($b['items']) || ! is_array($b['items'])) continue;
                            foreach ($b['items'] as &$it) {
                                $label = (string) ($it['label'] ?? '');
                                $val = $it['value'] ?? '';

                                // 1) PROGRAM SAYISI — SADECE düz-sayı değer (breakdown'lı zengin değere dokunma)
                                $deg = $this->countDegree($label);
                                $isPlainNum = is_string($val) && preg_match('/^[\s~]*\d[\d.,]*\s*$/u', $val);
                                if ($deg !== null && $real[$deg] !== null && ($isPlainNum || is_array($val))) {
                                    $newVal = (string) $real[$deg];
                                    $curDigits = is_array($val) ? 'ARR' : preg_replace('/\D/', '', $val);
                                    if ($curDigits !== $newVal) {
                                        if ($shown < $samples) { $this->line("  #{$u->id} [{$locale}] «{$label}»: " . (is_array($val) ? 'ARR' : $val) . " → {$newVal}"); $shown++; }
                                        $it['value'] = $newVal; $dirty = true; $fixedCount++;
                                    }
                                    continue;
                                }
                                // NOT: Tip-düzeltme KASITLI yapılmıyor — traegerschaft "staatlich anerkannt"
                                // (devlet-ONAYLI = özel) gibi nüanslarda polarite güvenilmez; asıl yanlış
                                // vakalarda (HMS/nta Isny) traeger zaten boş. Yanlış düzeltme riskinden kaçınıldı.
                            }
                            unset($it);
                        }
                        unset($b);
                        if ($dirty && $apply) {
                            University::whereKey($u->id)->update([$col => json_encode($blocks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
                        }
                    }
                    if ($dirty) $uniTouched++;
                }
            });

        $this->newLine();
        $this->line("Düzeltilen program-sayısı alanı: {$fixedCount}  ·  Etkilenen üni: {$uniTouched}");
        if (! $apply && ($fixedCount || $fixedType)) $this->warn('Uygulamak için --apply.');
        return self::SUCCESS;
    }

    /** @return array{total:?int,bachelor:?int,master:?int,phd:?int} */
    private function realCounts($rows): array
    {
        $by = ['bachelor' => 0, 'master' => 0, 'phd' => 0];
        $total = 0;
        foreach ($rows ?? [] as $r) {
            $total += $r->c;
            if (isset($by[$r->degree])) $by[$r->degree] += $r->c;
        }
        return ['total' => $total, 'bachelor' => $by['bachelor'], 'master' => $by['master'], 'phd' => $by['phd']];
    }

    /** Label'dan derece tespiti (master ÖNCE — "Yüksek Lisans" "Lisans" içerir). */
    private function countDegree(string $label): ?string
    {
        $l = mb_strtolower($label);
        // SADECE program-sayısı alanı (öğrenci sayısı/Studierenden HARİÇ): "program" veya
        // "studieng"(änge) geçmeli. "Öğrenci Sayısı"/"Anzahl der Studierenden" eşleşmez.
        if (! preg_match('/program|studieng/u', $l)) return null;
        if (preg_match('/yüksek lisans|master/u', $l)) return 'master';
        if (preg_match('/doktora|phd|promotion|doctora/u', $l)) return 'phd';
        if (preg_match('/lisans|bachelor/u', $l)) return 'bachelor';
        if (preg_match('/toplam|total|gesamt/u', $l)) return 'total';
        // Düz "Program Sayısı / Number of Programs / Anzahl Studiengänge" → toplam
        return 'total';
    }

    private function isTypeLabel(string $label): bool
    {
        return (bool) preg_match('/tipi|hochschultyp|university type|träger|trager/u', mb_strtolower($label));
    }

    private function valuePolarity(string $v): ?string
    {
        $v = mb_strtolower($v);
        if (preg_match('/privat|özel|ozel|private|kirchlich/u', $v)) return 'private';
        if (preg_match('/public|devlet|staatlich|öffentlich|offentlich/u', $v)) return 'public';
        return null;
    }

    private function traegerPolarity(?string $t): ?string
    {
        if (! $t) return null;
        $t = mb_strtolower($t);
        if (str_contains($t, 'privat') || str_contains($t, 'kirchlich')) return 'private';
        if (str_contains($t, 'öffentlich') || str_contains($t, 'offentlich')) return 'public';
        return null;
    }

    private function typeLabel(string $pol, string $locale): string
    {
        $map = [
            'public'  => ['tr' => 'Devlet (Public)', 'en' => 'Public', 'de' => 'Staatlich'],
            'private' => ['tr' => 'Özel (Private)',  'en' => 'Private', 'de' => 'Privat'],
        ];
        return $map[$pol][$locale] ?? $map[$pol]['en'];
    }
}
