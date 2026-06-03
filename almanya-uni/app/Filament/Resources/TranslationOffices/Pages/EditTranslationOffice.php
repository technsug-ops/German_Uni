<?php

namespace App\Filament\Resources\TranslationOffices\Pages;

use App\Filament\Resources\TranslationOffices\TranslationOfficeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTranslationOffice extends EditRecord
{
    protected static string $resource = TranslationOfficeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
