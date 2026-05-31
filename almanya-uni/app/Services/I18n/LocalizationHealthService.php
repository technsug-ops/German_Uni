<?php

namespace App\Services\I18n;

use App\Models\Category;
use App\Models\City;
use App\Models\FaqTopic;
use App\Models\FieldOfStudy;
use App\Models\Post;
use App\Models\State;
use App\Models\University;

/**
 * Çapraz-katman lokalizasyon senkron raporu: hangi içerik tipi, hangi dilde
 * ne kadar çevrili / ne eksik. CLI (i18n:health) + admin paneli (Dil Durumu)
 * ortak kullanır. "İçeriklerin senkron olup olmadığı" tek yerden denetlenir.
 */
class LocalizationHealthService
{
    /** Turkish-only karakterler — bir değerin "hâlâ Türkçe" olduğunun sinyali. */
    private const TR_CHARS = '/[ışğİ]/u';

    /** @return array<int,array> her satır: type, total, locales=>[en=>[done,missing,pct], de=>...], note */
    public function report(): array
    {
        return [
            $this->uiStrings(),
            $this->taxonomy('Blog Kategorileri', Category::class),
            $this->taxonomy('FAQ Konuları', FaqTopic::class),
            $this->blocks('Şehir içeriği', City::class),
            $this->blocks('Üniversite içeriği', University::class),
            $this->blocks('Alan içeriği', FieldOfStudy::class),
            $this->blocks('Eyalet içeriği', State::class),
            $this->posts(),
        ];
    }

    private function uiStrings(): array
    {
        $en = $this->lang('en');
        $de = $this->lang('de');
        $enLeak = $this->leakCount($en);
        $deLeak = $this->leakCount($de);
        $tot = max(count($en), count($de));
        return [
            'type' => 'UI metinleri (lang)',
            'total' => $tot,
            'locales' => [
                'en' => ['done' => $tot - $enLeak, 'missing' => $enLeak, 'pct' => $tot ? round(100 * ($tot - $enLeak) / $tot) : 100],
                'de' => ['done' => $tot - $deLeak, 'missing' => $deLeak, 'pct' => $tot ? round(100 * ($tot - $deLeak) / $tot) : 100],
            ],
            'note' => $enLeak || $deLeak ? "TR sızıntısı: EN {$enLeak}, DE {$deLeak}" : 'temiz',
            'missing_label' => 'TR sızıntısı',
        ];
    }

    private function taxonomy(string $label, string $model): array
    {
        $total = $model::count();
        $row = ['type' => $label, 'total' => $total, 'locales' => [], 'missing_label' => 'çeviri eksik'];
        foreach (['en', 'de'] as $loc) {
            $done = $model::whereNotNull("name_$loc")->where("name_$loc", '<>', '')->count();
            $row['locales'][$loc] = ['done' => $done, 'missing' => $total - $done, 'pct' => $total ? round(100 * $done / $total) : 100];
        }
        return $row;
    }

    private function blocks(string $label, string $model): array
    {
        // baz = TR content_blocks dolu olanlar (çevrilecek olanlar)
        $total = $model::whereNotNull('content_blocks')->where('content_blocks', '<>', '[]')->count();
        $row = ['type' => $label, 'total' => $total, 'locales' => [], 'missing_label' => 'çeviri eksik'];
        foreach (['en', 'de'] as $loc) {
            $done = $model::whereNotNull("content_blocks_$loc")->count();
            $row['locales'][$loc] = ['done' => $done, 'missing' => max(0, $total - $done), 'pct' => $total ? round(100 * min($done, $total) / $total) : 100];
        }
        return $row;
    }

    private function posts(): array
    {
        $tr = Post::where('locale', 'tr')->count();
        $row = ['type' => 'Blog yazıları', 'total' => $tr, 'locales' => [], 'missing_label' => 'çeviri eksik'];
        foreach (['en', 'de'] as $loc) {
            $done = Post::where('locale', $loc)->count();
            $row['locales'][$loc] = ['done' => $done, 'missing' => max(0, $tr - $done), 'pct' => $tr ? round(100 * min($done, $tr) / $tr) : 100];
        }
        return $row;
    }

    private function lang(string $loc): array
    {
        $path = lang_path("$loc.json");
        if (! is_file($path)) return [];
        return json_decode(file_get_contents($path), true) ?: [];
    }

    private function leakCount(array $arr): int
    {
        return count(array_filter($arr, fn ($v) => is_string($v) && preg_match(self::TR_CHARS, $v)));
    }
}
