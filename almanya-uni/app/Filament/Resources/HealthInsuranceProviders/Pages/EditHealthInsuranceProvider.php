<?php

namespace App\Filament\Resources\HealthInsuranceProviders\Pages;

use App\Filament\Resources\HealthInsuranceProviders\HealthInsuranceProviderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHealthInsuranceProvider extends EditRecord
{
    protected static string $resource = HealthInsuranceProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
