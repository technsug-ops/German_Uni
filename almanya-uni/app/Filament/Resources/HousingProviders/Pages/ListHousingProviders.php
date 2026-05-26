<?php

namespace App\Filament\Resources\HousingProviders\Pages;

use App\Filament\Resources\HousingProviders\HousingProviderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHousingProviders extends ListRecords
{
    protected static string $resource = HousingProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
