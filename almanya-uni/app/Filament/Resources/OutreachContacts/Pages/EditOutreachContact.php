<?php

namespace App\Filament\Resources\OutreachContacts\Pages;

use App\Filament\Pages\OutreachCompose;
use App\Filament\Resources\OutreachContacts\OutreachContactResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOutreachContact extends EditRecord
{
    protected static string $resource = OutreachContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('compose')
                ->label('Bu kontağa mail gönder')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->url(fn () => OutreachCompose::getUrl() . '?contact=' . $this->record->id)
                ->visible(fn () => filled($this->record->email)),
            DeleteAction::make(),
        ];
    }
}
