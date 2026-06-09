<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Telegram analiz JSON'unu (78MB) okuyup topic başına sample soruları
 * storage/app/community/telegram_by_topic.json olarak küçük + hızlı cache'ler.
 *
 * Brief Suggestion Service bu cache'i kullanır — her çağrıda 78MB okumak zorunda kalmaz.
 */
class CommunityPrepareTelegram extends Command
{
    protected $signature = 'community:prepare-telegram
        {--path= : Telegram JSON dosyası path}
        {--sample=80 : Topic başına maksimum soru cache\'lensin}';

    protected $description = 'Telegram analiz JSON\'unu topic başına soru cache\'ine ayrıştırır.';

    public function handle(): int
    {
        $path = $this->option('path')
            ?: 'C:\\Users\\Yapra\\OneDrive\\Masaüstü\\uni icerik\\telegram_ogrenci_2026-05-16.json';

        if (!is_file($path)) {
            $this->error("Dosya bulunamadı: $path");
            return self::FAILURE;
        }

        $this->info('JSON okunuyor (78MB, ~30sn)...');
        $raw = file_get_contents($path);
        $data = json_decode($raw, true);

        if (!$data || empty($data['messages'])) {
            $this->error('JSON parse hatası veya messages boş');
            return self::FAILURE;
        }

        $this->line('  ' . count($data['messages']) . ' mesaj yüklendi');

        $sampleSize = (int) $this->option('sample');
        $byTopic = [];

        $bar = $this->output->createProgressBar(count($data['messages']));
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%%');
        $bar->start();

        foreach ($data['messages'] as $m) {
            $bar->advance();

            if (!($m['is_question'] ?? false)) continue;
            if (($m['text_len'] ?? 0) < 30 || ($m['text_len'] ?? 0) > 250) continue;

            $text = trim(preg_replace('/\s+/u', ' ', $m['text'] ?? ''));
            if (!$text) continue;

            // Doktor topic'leri filtrele (kullanıcı kararı: kapsam dışı)
            $topics = collect($m['topics'] ?? [])
                ->reject(fn ($t) => str_starts_with($t, 'doktor_'))
                ->all();

            // 'kariyer' topic'i: kaynak JSON'da hazır etiket YOK → metin keyword'üyle
            // sınıflandır (kullanıcı isteği "Almanya'da kariyer": mezuniyet-sonrası kariyer,
            // sektörler, maaş, iş imkanı). Bir mesaj birden çok topic'e girebilir (mevcut
            // davranış) → kariyer EK etiket olur, diğer topic'lerle aynı şekilde cache'lenir.
            $lc = mb_strtolower($text);
            foreach (['kariyer', 'karriere', 'sektör', 'maaş', 'gehalt', 'iş imkan', 'iş bulma', 'çalışma alanı', 'mezuniyet sonra', 'mezun olduktan', 'kariyer fırsat', 'hangi sektör', 'iş hayat', 'kariyer yap'] as $ck) {
                if (mb_strpos($lc, $ck) !== false) {
                    $topics[] = 'kariyer';
                    break;
                }
            }

            if (empty($topics)) continue;

            foreach ($topics as $topic) {
                $byTopic[$topic] ??= [];
                if (count($byTopic[$topic]) < $sampleSize) {
                    $byTopic[$topic][] = $text;
                }
            }
        }
        $bar->finish();
        $this->newLine(2);

        // Order topic'leri sample sayısına göre
        uasort($byTopic, fn ($a, $b) => count($b) - count($a));

        $output = [
            'source' => basename($path),
            'generated_at' => now()->toIso8601String(),
            'sample_per_topic' => $sampleSize,
            'topics' => $byTopic,
        ];

        $cachePath = storage_path('app/community/telegram_by_topic.json');
        if (!is_dir(dirname($cachePath))) {
            mkdir(dirname($cachePath), 0755, true);
        }
        file_put_contents($cachePath, json_encode($output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $this->info('✅ Cache yazıldı: ' . $cachePath);
        $this->line('Boyut: ' . round(filesize($cachePath) / 1024, 1) . ' KB');
        $this->newLine();
        $this->info('Topic başına soru sayısı:');
        foreach ($byTopic as $t => $msgs) {
            $this->line('  ' . str_pad($t, 18) . ': ' . count($msgs));
        }

        return self::SUCCESS;
    }
}
