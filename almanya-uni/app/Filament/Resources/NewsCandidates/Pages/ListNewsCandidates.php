<?php

namespace App\Filament\Resources\NewsCandidates\Pages;

use App\Filament\Resources\NewsCandidates\NewsCandidateResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListNewsCandidates extends ListRecords
{
    protected static string $resource = NewsCandidateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Mod 1 — RSS kaynaklardan otomatik aday çek (gelen kutusuna düşer).
            Action::make('autoFetch')
                ->label('Otomatik Çek')
                ->icon('heroicon-o-arrow-down-on-square-stack')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Otomatik haber çek')
                ->modalDescription('Yapılandırılmış RSS kaynaklardan (Google News, ICEF) yeni aday çekilir. Onaylayıp yayınlamadan yayına çıkmaz.')
                ->action(function () {
                    Artisan::call('news:fetch');
                    $out = trim(Artisan::output());
                    Notification::make()
                        ->title('Otomatik çekme tamamlandı')
                        ->body($out ?: 'Çıktı yok.')
                        ->success()
                        ->send();
                }),

            CreateAction::make()->label('Manuel haber ekle'),
        ];
    }
}
