<?php

namespace App\Filament\Resources\PremiumInterests\Pages;

use App\Filament\Resources\PremiumInterests\PremiumInterestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPremiumInterest extends EditRecord
{
    protected static string $resource = PremiumInterestResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
