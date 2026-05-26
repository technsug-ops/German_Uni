<?php

namespace App\Filament\Resources\HousingProviders\Pages;

use App\Filament\Resources\HousingProviders\HousingProviderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHousingProvider extends EditRecord
{
    protected static string $resource = HousingProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
