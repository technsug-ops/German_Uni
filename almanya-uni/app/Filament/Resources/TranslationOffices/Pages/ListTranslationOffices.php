<?php

namespace App\Filament\Resources\TranslationOffices\Pages;

use App\Filament\Resources\TranslationOffices\TranslationOfficeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTranslationOffices extends ListRecords
{
    protected static string $resource = TranslationOfficeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
