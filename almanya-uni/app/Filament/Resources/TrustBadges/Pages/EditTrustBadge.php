<?php

namespace App\Filament\Resources\TrustBadges\Pages;

use App\Filament\Resources\TrustBadges\TrustBadgeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTrustBadge extends EditRecord
{
    protected static string $resource = TrustBadgeResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
