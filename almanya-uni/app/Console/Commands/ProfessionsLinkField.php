<?php

namespace App\Console\Commands;

use App\Models\FieldOfStudy;
use App\Models\Profession;
use Illuminate\Console\Command;

/**
 * KldB code (Klassifikation der Berufe) ilk hanesi → AlmanyaUni alan eşleştirme.
 *
 * KldB ana grupları (1. hane):
 *   1 = Tarım, ormancılık, bahçecilik
 *   2 = Hammadde, üretim, imalat
 *   3 = İnşaat, mimari
 *   4 = Doğa bilimleri, geografi, bilişim
 *   5 = Lojistik, koruma, güvenlik
 *   6 = Ticaret, satış, turizm
 *   7 = Yönetim, organizasyon, hukuk, finans
 *   8 = Sağlık, sosyal hizmet, eğitim, ders verme
 *   9 = Dil, edebiyat, kültür, sanat, medya
 */
class ProfessionsLinkField extends Command
{
    protected $signature = 'professions:link-field {--dry-run}';
    protected $description = 'KldB code\'a göre meslekleri AlmanyaUni alanlarına bağla';

    public function handle(): int
    {
        $fields = FieldOfStudy::all()->keyBy(function ($f) {
            // Slug bazlı eşleştirme
            return match (true) {
                str_contains($f->slug, 'muhendis') => 'engineering',
                str_contains($f->slug, 'bilisim') || str_contains($f->slug, 'bilgisayar') => 'it',
                str_contains($f->slug, 'matematik') || str_contains($f->slug, 'doga') => 'science',
                str_contains($f->slug, 'tip') || str_contains($f->slug, 'saglik') => 'health',
                str_contains($f->slug, 'hukuk') || str_contains($f->slug, 'ekonomi') => 'law_economics',
                str_contains($f->slug, 'sosyal') => 'social',
                str_contains($f->slug, 'sanat') || str_contains($f->slug, 'tasarim') => 'art',
                str_contains($f->slug, 'dil') || str_contains($f->slug, 'kultur') => 'language_culture',
                str_contains($f->slug, 'tarim') => 'agriculture',
                str_contains($f->slug, 'veteriner') || str_contains($f->slug, 'spor') => 'vet_sports',
                default => $f->slug,
            };
        });

        $missing = collect(['engineering', 'it', 'science', 'health', 'law_economics', 'social', 'art', 'language_culture', 'agriculture', 'vet_sports'])
            ->reject(fn ($k) => $fields->has($k));
        if ($missing->isNotEmpty()) {
            $this->error('Eksik field key: ' . $missing->implode(', '));
            $this->line('Fields: ' . $fields->keys()->implode(', '));
        }

        $map = function (string $kldb) use ($fields): ?int {
            $first = mb_substr($kldb, 0, 1);
            $key = match ($first) {
                '1' => 'agriculture',
                '2', '3' => 'engineering',
                '4' => 'it',           // 4XX bilişim/doğa karışık — IT öncelikli
                '5' => 'law_economics', // lojistik ekonomi'ye en yakın
                '6' => 'law_economics',
                '7' => 'law_economics',
                '8' => 'health',        // tıp+eğitim — sağlık öncelikli
                '9' => 'art',           // sanat+dil+medya — sanat öncelikli
                default => null,
            };
            return $key && $fields->has($key) ? $fields->get($key)->id : null;
        };

        // Daha akıllı 4 (Doğa+IT) ayrımı: 41XX → IT, 42XX-43XX → doğa bilimleri
        $smartMap = function (string $kldb) use ($fields): ?int {
            // Format: "B 23224" — "B " prefix'i kaldır
            $clean = ltrim(preg_replace('/^[A-Z]\s*/', '', $kldb));
            if (!ctype_digit(mb_substr($clean, 0, 1))) return null;
            $two = mb_substr($clean, 0, 2);
            $one = $clean[0] ?? '';

            // İlk iki hane spesifik eşleştirme
            $byTwo = [
                '41' => 'it',        // Mathematik, Naturwissenschaft
                '43' => 'it',        // Informatik
                '81' => 'health',    // Medizin
                '82' => 'health',    // Gesundheitswissenschaft
                '83' => 'social',    // Erziehung, Soziales
                '84' => 'social',    // Lehrer
                '94' => 'language_culture', // Sprache, Literatur
                '92' => 'art',       // Werbung, Marketing
                '93' => 'art',       // Design
                '21' => 'engineering',
                '22' => 'engineering',
                '24' => 'engineering', // Metall
                '25' => 'engineering', // Maschinenbau
                '26' => 'engineering', // Elektro
                '27' => 'engineering', // Mechatronik
                '28' => 'science',     // Chemie
                '29' => 'science',     // Lebensmittel
                '31' => 'engineering', // Hochbau
                '32' => 'engineering', // Tiefbau
                '33' => 'art',         // Innenausbau
                '34' => 'engineering', // Gebäudetechnik
            ];

            if (isset($byTwo[$two]) && $fields->has($byTwo[$two])) {
                return $fields->get($byTwo[$two])->id;
            }

            $byOne = [
                '1' => 'agriculture',
                '2' => 'engineering',
                '3' => 'engineering',
                '4' => 'science',
                '5' => 'law_economics',
                '6' => 'law_economics',
                '7' => 'law_economics',
                '8' => 'social',
                '9' => 'art',
            ];

            $key = $byOne[$one] ?? null;
            return $key && $fields->has($key) ? $fields->get($key)->id : null;
        };

        $total = Profession::count();
        $assigned = 0;
        $skipped = 0;
        $byField = [];

        $this->info("Toplam {$total} meslek inceleniyor…");
        $bar = $this->output->createProgressBar($total);

        Profession::query()->whereNotNull('kldb_code')->chunk(500, function ($chunk) use ($smartMap, &$assigned, &$skipped, &$byField, $bar) {
            foreach ($chunk as $p) {
                $fieldId = $smartMap($p->kldb_code);
                if ($fieldId) {
                    if (!$this->option('dry-run')) {
                        $p->update(['field_of_study_id' => $fieldId]);
                    }
                    $byField[$fieldId] = ($byField[$fieldId] ?? 0) + 1;
                    $assigned++;
                } else {
                    $skipped++;
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ {$assigned} meslek alana bağlandı, {$skipped} atlandı (KldB yok).");

        $this->newLine();
        $this->info('Dağılım:');
        $allFields = FieldOfStudy::all()->keyBy('id');
        arsort($byField);
        foreach ($byField as $id => $count) {
            $name = $allFields[$id]->name_tr ?? $id;
            $this->line(sprintf('  %s %s', str_pad($count, 5, ' ', STR_PAD_LEFT), $name));
        }

        if ($this->option('dry-run')) {
            $this->warn("\n⚠️ DRY-RUN modu — değişiklik yapılmadı. --force kaldırınca canlıya yaz.");
        }

        return self::SUCCESS;
    }
}
