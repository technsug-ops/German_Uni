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
                    // Zaman bütçesi: KAS gateway timeout'undan önce temiz çık.
                    // Kalan kaynak olursa tekrar bas — kısmi ilerleme zaten kayıtlı.
                    @set_time_limit(120);
                    try {
                        Artisan::call('news:fetch', ['--max-seconds' => 35]);
                        $out = trim(Artisan::output());
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Çekme hatası')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                        return;
                    }
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
