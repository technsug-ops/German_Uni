<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Models\University;
use App\Services\GeminiTranslator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Partner importunun yarattığı İngilizce-isimli duplike üni kayıtlarını
 * Gemini ile kanonik (hs_nummer'li, resmi) muadiline eşleştirip merge eder.
 *
 * Aşama 1 (default): AI eşleştirme → storage/app/dedupe-proposals.json + tablo.
 * Aşama 2 (--execute): JSON'daki onaylı çiftleri merge eder.
 *
 * String matching çevrilmiş isimleri (University of Bonn = Rheinische FW Uni Bonn)
 * yakalayamadığı için AI kullanılır. Sadece AYNI kurum eşleşir; farklıysa NONE.
 */
class UnisDedupeAi extends Command
{
    protected $signature = 'unis:dedupe-ai
        {--execute : Proposals JSON\'daki çiftleri merge et}
        {--limit=0 : İlk N shell (test için)}
        {--id= : Tek shell id (test)}';

    protected $description = 'Partner duplike İngilizce üni kayıtlarını AI ile kanonik muadiline merge eder';

    private const PROPOSALS_FILE = 'dedupe-proposals.json';

    private const SYSTEM = <<<'TXT'
Sen bir Alman yükseköğretim veri uzmanısın. Sana bir "shell" üniversite adı (genelde İngilizce) ve aynı şehirdeki resmi üniversite adaylarının listesi verilecek.
Görevin: shell'in HANGİ adayla AYNI gerçek kurum olduğunu bulmak (resmi Almanca adı farklı/çeviri olsa bile). Örnek: "University of Bonn" = "Rheinische Friedrich-Wilhelms-Universität Bonn".
KURALLAR:
- Sadece AYNI tüzel kurumsa eşleştir. Aynı şehirdeki FARKLI kurumları (ör. bir devlet üniversitesi ile özel bir işletme okulu) ASLA eşleştirme.
- Üniversite (Universität) ile Uygulamalı Bilimler Üniversitesi (Hochschule/FH) FARKLI kurumlardır; eşleştirme.
- Emin değilsen NONE.
SADECE şu formatta yanıt ver: eşleşen adayın id numarası (ör. "461") veya "NONE". Başka hiçbir şey yazma.
TXT;

    public function handle(GeminiTranslator $ai): int
    {
        if ($this->option('execute')) {
            return $this->applyMerge();
        }
        return $this->match($ai);
    }

    private function match(GeminiTranslator $ai): int
    {
        if (! $ai->isConfigured()) {
            $this->error('Gemini yapılandırılmamış (GEMINI_API_KEY).');
            return self::FAILURE;
        }

        $shells = University::query()
            ->where('is_active', 1)
            ->where('data_source', 'like', 'partner%')
            ->whereNull('hs_nummer')
            ->whereNotNull('city_id')
            ->orderBy('id');

        if ($id = $this->option('id')) {
            $shells->where('id', (int) $id);
        }
        if ($this->option('limit') > 0) {
            $shells->limit((int) $this->option('limit'));
        }
        $shells = $shells->with('city:id,name_de')->get(['id', 'name_de', 'name_en', 'city_id', 'partner_id']);

        // Kanonik havuz: resmi (hs_nummer'li) aktif üniler. Şehir İSMİNE göre indexli
        // (partner importu duplike şehir kaydı yarattığı için city_id güvenilmez).
        // Şehir adı normalizasyonu: "Freiburg im Breisgau"→"freiburg", "Frankfurt am Main"→"frankfurt"
        $norm = function ($s) {
            $s = mb_strtolower(trim((string) $s));
            $s = preg_split('/\s+(am|im|an der|a\.\s?d\.|\/)\s|\s*\(/u', $s)[0];
            return trim($s);
        };
        // Kanonik = hs_nummer'lı (resmi) VEYA partner-shell olmayan aktif kayıtlar
        $pool = University::query()->where('is_active', 1)
            ->where(fn ($q) => $q->where('data_source', 'not like', 'partner%')
                ->orWhereNull('data_source')
                ->orWhereNotNull('hs_nummer'))
            ->with('city:id,name_de')
            ->get(['id', 'name_de', 'name_en', 'short_name', 'city_id'])
            ->groupBy(fn ($u) => $norm($u->city?->name_de));

        $this->info("{$shells->count()} shell, AI ile eşleştiriliyor...");
        $bar = $this->output->createProgressBar($shells->count());
        $bar->start();

        $proposals = [];
        $matched = 0;
        $none = 0;
        $nocand = 0;

        foreach ($shells as $shell) {
            $cands = $pool->get($norm($shell->city?->name_de), collect());
            if ($cands->isEmpty()) {
                $nocand++;
                $bar->advance();
                continue;
            }

            $list = $cands->map(fn ($c) => "{$c->id}: {$c->name_de}")->implode("\n");
            $prompt = "Shell: \"{$shell->name_de}\"\nAdaylar:\n{$list}";

            $res = $ai->translate($prompt, self::SYSTEM);
            $ans = $res['translation'] ?? 'NONE';
            $canonId = preg_match('/\b(\d+)\b/', $ans, $m) ? (int) $m[1] : null;

            if ($canonId && $cands->firstWhere('id', $canonId)) {
                $canon = $cands->firstWhere('id', $canonId);
                $proposals[] = [
                    'shell_id'    => $shell->id,
                    'shell_name'  => $shell->name_de,
                    'canon_id'    => $canonId,
                    'canon_name'  => $canon->name_de,
                ];
                $matched++;
            } else {
                $none++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        Storage::put(self::PROPOSALS_FILE, json_encode($proposals, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $rows = array_map(fn ($p) => [
            $p['shell_id'], mb_substr($p['shell_name'], 0, 36),
            $p['canon_id'], mb_substr($p['canon_name'], 0, 40),
        ], $proposals);
        $this->table(['Shell id', 'Shell (İng)', 'Kanonik id', 'Kanonik (DE)'], $rows);

        $this->info("Eşleşen: {$matched} | NONE: {$none} | aday yok: {$nocand}");
        $this->warn('Öneriler kaydedildi: storage/app/' . self::PROPOSALS_FILE);
        $this->warn('İncele, gerekiyorsa düzenle, sonra: php artisan unis:dedupe-ai --execute');

        return self::SUCCESS;
    }

    private function applyMerge(): int
    {
        if (! Storage::exists(self::PROPOSALS_FILE)) {
            $this->error('Proposals dosyası yok. Önce eşleştirmeyi çalıştır (--execute olmadan).');
            return self::FAILURE;
        }
        $proposals = json_decode(Storage::get(self::PROPOSALS_FILE), true) ?: [];
        if (! $proposals) {
            $this->warn('Proposals boş.');
            return self::SUCCESS;
        }

        $this->info(count($proposals) . ' çift merge ediliyor...');
        $merged = 0;
        $progMoved = 0;

        foreach ($proposals as $p) {
            $shell = University::find($p['shell_id']);
            $canon = University::find($p['canon_id']);
            if (! $shell || ! $canon) {
                $this->warn("Atlandı (kayıt yok): {$p['shell_id']} -> {$p['canon_id']}");
                continue;
            }

            University::withoutSyncingToSearch(function () use ($shell, $canon, &$progMoved) {
                DB::transaction(function () use ($shell, $canon, &$progMoved) {
                    // 1) Programları kanoniğe taşı (partner_id korunur → gelecek sync yerinde günceller)
                    $moved = Program::where('university_id', $shell->id)
                        ->update(['university_id' => $canon->id]);
                    $progMoved += $moved;

                    // 2) Favorileri taşı
                    DB::table('favorites')
                        ->where(['favoriteable_id' => $shell->id, 'favoriteable_type' => University::class])
                        ->update(['favoriteable_id' => $canon->id]);

                    // 3) Shell değerlerini yakala, sonra shell'i SİL (partner_id UNIQUE — önce sil)
                    $shellName    = $shell->name_de;
                    $shellPartner = $shell->partner_id;
                    $shellFields  = [];
                    foreach (['uni_assist_id', 'is_uni_assist_member', 'website_url', 'logo_url', 'image_url', 'description_en'] as $f) {
                        $shellFields[$f] = $shell->{$f};
                    }
                    $shell->delete();

                    // 4) RE-CREATION ÖNLEME: shell'in İngilizce adını + partner_id'yi kanoniğe yaz →
                    //    gelecek partner sync findUniversityByName ile kanoniği bulur, yeni kayıt açmaz.
                    if (blank($canon->name_en) || $canon->name_en !== $shellName) {
                        $canon->name_en = $shellName;
                    }
                    if (blank($canon->partner_id)) {
                        $canon->partner_id = $shellPartner;
                    }
                    foreach ($shellFields as $f => $val) {
                        if (blank($canon->{$f}) && filled($val)) {
                            $canon->{$f} = $val;
                        }
                    }
                    $canon->save();
                });
            });
            $merged++;
        }

        $this->newLine();
        $this->info("✅ Merge: {$merged} üni | taşınan program: {$progMoved} | kalan üni: " . University::count());
        return self::SUCCESS;
    }
}
