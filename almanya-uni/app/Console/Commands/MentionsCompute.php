<?php

namespace App\Console\Commands;

use App\Models\University;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('mentions:compute {--telegram= : Telegram JSON path (default: detect)} {--min-students=1000 : Only score universities above this size to avoid noise}')]
#[Description('Topluluk mention skoru: Telegram + Forum havuzunda her üniversiteden bahseden mesaj sayısını üniversiteye yazar.')]
class MentionsCompute extends Command
{
    public function handle(): int
    {
        $telegramPath = $this->option('telegram') ?: 'C:\\Users\\Yapra\\OneDrive\\Masaüstü\\uni icerik\\telegram_ogrenci_2026-05-16.json';
        $minStudents = (int) $this->option('min-students');

        if (! file_exists($telegramPath)) {
            $this->error("Telegram JSON bulunamadı: {$telegramPath}");
            return self::FAILURE;
        }

        $this->info("Mention compute başladı...");
        $this->line("Telegram: {$telegramPath} (" . round(filesize($telegramPath) / 1048576, 1) . " MB)");

        // 1) Üniversite eşleştirme sözlüğü kur
        $unis = University::where('is_active', 1)
            ->when($minStudents > 0, fn ($q) => $q->where('student_count', '>=', $minStudents))
            ->get(['id', 'name_de', 'short_name', 'name_tr', 'name_en']);

        $this->line("Eşleştirme havuzu: {$unis->count()} üniversite (>= {$minStudents} öğrenci)");

        // Her üni için arama terimleri (long form + short_name)
        $terms = [];
        foreach ($unis as $u) {
            $unique = collect([$u->short_name, $u->name_de, $u->name_tr, $u->name_en])
                ->filter()
                ->map(fn ($s) => trim($s))
                ->filter(fn ($s) => mb_strlen($s) >= 6)
                ->unique()
                ->values()
                ->all();
            if ($unique) {
                $terms[$u->id] = $unique;
            }
        }

        $this->line("Toplam arama terimi: " . array_sum(array_map('count', $terms)));

        // 2) Telegram JSON yükle (tüm dosya, ~78MB — bellek yeterli)
        $this->line("Telegram JSON parse ediliyor...");
        ini_set('memory_limit', '1G');
        $data = json_decode(file_get_contents($telegramPath), true);
        if (! is_array($data) || empty($data['messages'])) {
            $this->error("Geçerli JSON yapısı değil (messages array bekleniyor)");
            return self::FAILURE;
        }
        $messages = $data['messages'];
        $totalMessages = count($messages);
        $this->line("Mesajlar: " . number_format($totalMessages));

        // 3) Her mesajda hangi üniversite geçiyor say
        $scores = array_fill_keys(array_keys($terms), 0);

        $bar = $this->output->createProgressBar($totalMessages);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% — %message%');
        $bar->setMessage('mesajlar taranıyor');
        $bar->start();

        foreach ($messages as $i => $m) {
            $text = $m['text'] ?? '';
            if (mb_strlen($text) < 8) {
                $bar->advance();
                continue;
            }

            foreach ($terms as $uniId => $uniTerms) {
                foreach ($uniTerms as $t) {
                    if (stripos($text, $t) !== false) {
                        $scores[$uniId]++;
                        break;
                    }
                }
            }
            if ($i % 500 === 0) $bar->setMessage("matched: " . array_sum($scores));
            $bar->advance();
        }
        $bar->finish();
        $this->newLine(2);

        // 4) DB'ye yaz
        $nonZero = array_filter($scores, fn ($s) => $s > 0);
        arsort($nonZero);
        $this->info("Mention'lı üniversite: " . count($nonZero) . " / " . count($scores));
        $this->line("Top 10:");
        $top = array_slice($nonZero, 0, 10, true);
        foreach ($top as $uid => $score) {
            $u = $unis->firstWhere('id', $uid);
            $this->line("  {$score} × " . ($u->short_name ?: $u->name_de));
        }

        if (! $this->confirm("Skorları DB'ye yazayım mı?", true)) {
            $this->warn('İptal edildi.');
            return self::SUCCESS;
        }

        $updated = 0;
        foreach ($scores as $uid => $score) {
            University::where('id', $uid)->update([
                'community_mention_score' => $score,
                'community_mention_updated_at' => now(),
            ]);
            $updated++;
        }

        $this->info("✓ {$updated} üniversite güncellendi.");
        $this->line("Sıralamayı gör: /rankings/toplulukta-en-cok-konusulan-universiteler");

        return self::SUCCESS;
    }
}
