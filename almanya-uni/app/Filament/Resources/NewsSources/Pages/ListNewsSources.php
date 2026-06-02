<?php

namespace App\Filament\Resources\NewsSources\Pages;

use App\Filament\Resources\NewsSources\NewsSourceResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListNewsSources extends ListRecords
{
    protected static string $resource = NewsSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tüm aktif kaynaklardan otomatik aday çek (gelen kutusuna düşer).
            Action::make('fetchAll')
                ->label('Tüm Aktif Kaynaklardan Çek')
                ->icon('heroicon-o-arrow-down-on-square-stack')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Otomatik haber çek')
                ->modalDescription('Tüm AKTİF kaynaklardan yeni aday çekilir. Onaylayıp yayınlamadan yayına çıkmaz.')
                ->action(function () {
                    Artisan::call('news:fetch');
                    $out = trim(Artisan::output());
                    Notification::make()
                        ->title('Otomatik çekme tamamlandı')
                        ->body($out ?: 'Çıktı yok.')
                        ->success()
                        ->send();
                }),

            CreateAction::make()->label('Kaynak Ekle'),
        ];
    }
}
