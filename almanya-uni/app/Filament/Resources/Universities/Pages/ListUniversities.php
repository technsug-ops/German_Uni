<?php

namespace App\Filament\Resources\Universities\Pages;

use App\Filament\Resources\Universities\UniversityResource;
use App\Services\PartnerApiClient;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListUniversities extends ListRecords
{
    protected static string $resource = UniversityResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [CreateAction::make()];

        if (app(PartnerApiClient::class)->isConfigured()) {
            $actions[] = Action::make('partnerSync')
                ->label('🔄 Partner Sync (Şimdi)')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Partner API\'sinden senkronizasyon')
                ->modalDescription('Son sync\'ten beri değişen üni + programları çeker. 1-3 dakika sürebilir.')
                ->modalSubmitActionLabel('Sync\'i başlat')
                ->action(function () {
                    try {
                        $exitCode = Artisan::call('partner:sync', ['--only' => 'both']);
                        $output = Artisan::output();

                        if ($exitCode === 0) {
                            Notification::make()
                                ->title('Partner sync tamamlandı')
                                ->body(mb_substr($output, -500))
                                ->success()
                                ->persistent()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Partner sync başarısız')
                                ->body(mb_substr($output, -500))
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Partner sync hatası')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                });

            $actions[] = Action::make('partnerFullSync')
                ->label('🔁 Partner Full Sync')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('TAM senkronizasyon (delta değil)')
                ->modalDescription('Tüm 15.782 programı baştan çeker. 5-15 dakika sürer. Manuel girişler korunur (data_source flag).')
                ->modalSubmitActionLabel('Full sync\'i başlat')
                ->action(function () {
                    Artisan::call('partner:sync', ['--only' => 'both', '--full' => true]);
                    Notification::make()
                        ->title('Full sync arka planda başladı')
                        ->body('Sonuçlar için storage/logs/partner-sync.log dosyasına bak.')
                        ->info()
                        ->send();
                });
        }

        return $actions;
    }
}
