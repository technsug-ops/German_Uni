<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Program;
use App\Models\University;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * İnce-içerik denetimi — indexleme kalitesi için. "Discovered-not-indexed" sorununun
 * kaynağı çoğu zaman çok sayıda zayıf sayfadır; Google kaliteli sayfaları indexlemez.
 * Bu komut ince sayfaları (zenginleştirilmemiş şehir, çok kısa açıklama vb.) raporlar.
 * İnce şehirler noindex'li + sitemap dışı (City::isThinForLocale).
 */
class ContentThinAudit extends Command
{
    protected $signature = 'content:thin-audit';

    protected $description = 'İnce-içerik sayfalarını raporlar (indexleme kalitesi).';

    public function handle(): int
    {
        $this->line(str_repeat('═', 64));
        $this->info('  İNCE-İÇERİK DENETİMİ (indexleme kalitesi)');
        $this->line(str_repeat('═', 64));

        // Şehirler (TR content_blocks < 3 blok)
        $citiesTotal = City::whereHas('universities', fn ($q) => $q->where('is_active', 1))->count();
        $citiesThin = City::whereHas('universities', fn ($q) => $q->where('is_active', 1))
            ->where(fn ($q) => $q->whereNull('content_blocks')->orWhereRaw('JSON_LENGTH(content_blocks) < 3'))
            ->count();
        $this->row('Şehir sayfası', $citiesTotal, $citiesThin, 'noindex + sitemap dışı');

        // Üniversiteler
        $uniTotal = University::where('is_active', 1)->count();
        $uniThin = University::where('is_active', 1)
            ->where(fn ($q) => $q->whereNull('content_blocks')->orWhereRaw('JSON_LENGTH(content_blocks) < 3'))
            ->count();
        $this->row('Üniversite sayfası', $uniTotal, $uniThin, $uniThin ? 'enrichment gerek' : '✓ hepsi zengin');

        // Programlar — açıklama çok kısa (ham thin sinyali)
        $progTotal = Program::where('is_active', 1)->count();
        $progThin = Program::where('is_active', 1)
            ->whereRaw('CHAR_LENGTH(COALESCE(description_tr, "")) < 150')
            ->whereNull('qualification_requirements_en')->whereNull('qualification_requirements_tr')
            ->count();
        $this->row('Program (kısa açıklama+gereklilik yok)', $progTotal, $progThin, 'açıklama/gereklilik zenginleştir');

        $this->newLine();
        $this->line('İnce şehirler otomatik noindex + sitemap dışı. Zenginleştirmek için:');
        $this->line('  php artisan cities:enrich   (AI content_blocks üretir)');
        return self::SUCCESS;
    }

    private function row(string $label, int $total, int $thin, string $action): void
    {
        $pct = $total ? round($thin / $total * 100) : 0;
        $icon = $thin === 0 ? '✓' : ($pct > 20 ? '✖' : '▲');
        $this->line(sprintf('  %s  %-42s %5d / %-5d ince (%%%d)  → %s', $icon, $label, $thin, $total, $pct, $action));
    }
}
