<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Faq;
use App\Models\FieldOfStudy;
use App\Models\State;
use App\Models\University;
use Illuminate\Console\Command;

/**
 * Local'de üretilen çevirileri (content_blocks_en/de + FAQ EN/DE soru/cevap)
 * gzip'li veri dosyalarına dökerek prod'a migration ile taşınmasını sağlar.
 * KAS'ta SSH/CLI yok → çeviri local'de yapılır, buradan export edilir,
 * `i18n:import-content` (migration veya /admin/ops üzerinden) prod'a uygular.
 *
 *   php artisan i18n:export-content                  (hepsi)
 *   php artisan i18n:export-content --entity=city,faq
 */
class ExportTranslations extends Command
{
    protected $signature = 'i18n:export-content
        {--entity=city,university,field,state,faq : Hangi içerikler (virgülle)}';

    protected $description = 'content_blocks_en/de + FAQ EN/DE çevirilerini gzip veri dosyalarına export et (prod migration için)';

    /** entity => [model, dosya-adı] */
    private const BLOCK_ENTITIES = [
        'city'       => [City::class, 'city_blocks'],
        'university' => [University::class, 'university_blocks'],
        'field'      => [FieldOfStudy::class, 'field_blocks'],
        'state'      => [State::class, 'state_blocks'],
    ];

    public function handle(): int
    {
        $dir = database_path('migrations/data');
        if (! is_dir($dir)) mkdir($dir, 0775, true);

        $entities = array_filter(array_map('trim', explode(',', $this->option('entity'))));

        foreach ($entities as $entity) {
            if ($entity === 'faq') { $this->exportFaqs($dir); continue; }
            if (! isset(self::BLOCK_ENTITIES[$entity])) { $this->warn("bilinmeyen entity: $entity"); continue; }
            [$model, $file] = self::BLOCK_ENTITIES[$entity];
            $this->exportBlocks($entity, $model, "$dir/$file.json.gz");
        }

        return self::SUCCESS;
    }

    private function exportBlocks(string $entity, string $model, string $path): void
    {
        $out = [];
        $count = 0;
        $model::query()
            ->where(function ($w) {
                $w->whereNotNull('content_blocks_en')->orWhereNotNull('content_blocks_de');
            })
            ->select('slug', 'content_blocks_en', 'content_blocks_de')
            ->chunk(200, function ($rows) use (&$out, &$count) {
                foreach ($rows as $r) {
                    if (! $r->slug) continue;
                    $rec = [];
                    if ($r->content_blocks_en) $rec['en'] = $r->content_blocks_en; // already array (cast)
                    if ($r->content_blocks_de) $rec['de'] = $r->content_blocks_de;
                    if ($rec) { $out[$r->slug] = $rec; $count++; }
                }
            });

        $this->writeGz($path, $out);
        $this->info("✅ {$entity}: {$count} kayıt → " . basename($path) . ' (' . $this->kb($path) . ')');
    }

    /**
     * FAQ EN/DE satırları — translation_group_id + locale ile anahtarlanır
     * (id ortamlar arası farklı olabilir, group stabil). Sadece "sağlam"
     * (TR sızıntısız, birleşmemiş) satırlar export edilir.
     */
    private function exportFaqs(string $dir): void
    {
        $out = [];
        $count = 0;
        Faq::whereIn('locale', ['en', 'de'])
            ->whereNotNull('translation_group_id')
            ->chunk(200, function ($rows) use (&$out, &$count) {
                foreach ($rows as $r) {
                    if ($this->faqLooksBroken($r)) continue; // bozuğu taşımayalım
                    $key = $r->translation_group_id . ':' . $r->locale;
                    $out[$key] = [
                        'question'       => $r->question,
                        'answer_md'      => $r->answer_md,
                        'answer_html'    => $r->answer_html,
                        'answer_minutes' => $r->answer_minutes,
                        'slug'           => $r->slug,
                    ];
                    $count++;
                }
            });

        $this->writeGz("$dir/faq_translations.json.gz", $out);
        $this->info("✅ faq: {$count} sağlam EN/DE satır → faq_translations.json.gz (" . $this->kb("$dir/faq_translations.json.gz") . ')');
    }

    private function faqLooksBroken(Faq $r): bool
    {
        if (mb_strlen((string) $r->question) > 200) return true;
        if (preg_match('/[şğıİ]/u', (string) $r->question)) return true;
        if (preg_match('/[şğı]/u', (string) $r->answer_md)) return true;
        return false;
    }

    private function writeGz(string $path, array $data): void
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        file_put_contents($path, gzencode($json, 9));
    }

    private function kb(string $path): string
    {
        return round(filesize($path) / 1024) . ' KB';
    }
}
