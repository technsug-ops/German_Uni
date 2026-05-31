<?php

namespace App\Filament\Pages;

use App\Services\I18n\LocalizationHealthService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

/**
 * "Dil Durumu / İçerik Senkronizasyonu" — içerik tipi × dil matrisi.
 * Hangi içerik hangi dilde eksik, tek bakışta. LocalizationHealthService ile
 * i18n:health komutu aynı veriyi paylaşır.
 */
class LocalizationHealth extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;
    protected static ?string $navigationLabel = '🌐 Dil Durumu';
    protected static ?string $title = 'Dil Durumu / İçerik Senkronizasyonu';
    protected static ?int $navigationSort = 90;
    protected static string|\UnitEnum|null $navigationGroup = 'İçerik';

    protected string $view = 'filament.pages.localization-health';

    public array $rows = [];

    public function mount(LocalizationHealthService $svc): void
    {
        $this->rows = $svc->report();
    }
}
