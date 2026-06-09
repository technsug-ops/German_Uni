<?php

namespace App\Filament\Resources\HealthInsuranceProviders\Pages;

use App\Filament\Resources\HealthInsuranceProviders\HealthInsuranceProviderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHealthInsuranceProviders extends ListRecords
{
    protected static string $resource = HealthInsuranceProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
