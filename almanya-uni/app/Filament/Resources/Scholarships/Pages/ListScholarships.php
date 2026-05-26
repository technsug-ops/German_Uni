<?php

namespace App\Filament\Resources\Scholarships\Pages;

use App\Filament\Resources\Scholarships\ScholarshipResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListScholarships extends ListRecords
{
    protected static string $resource = ScholarshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncNow')
                ->label('🔄 DAAD Sync')
                ->color('primary')
                ->requiresConfirmation()
                ->modalDescription('DAAD scholarship database\'den 166 burs çekilecek. Birkaç dakika sürebilir.')
                ->action(function () {
                    Artisan::queue('daad:scholarships:sync');
                    Notification::make()
                        ->title('Sync sıraya alındı')
                        ->body('Queue worker\'da çalışacak. Log: storage/logs/daad-scholarships.log')
                        ->success()
                        ->send();
                }),
        ];
    }
}
