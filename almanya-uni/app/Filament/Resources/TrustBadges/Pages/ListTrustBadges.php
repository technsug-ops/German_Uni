<?php

namespace App\Filament\Resources\TrustBadges\Pages;

use App\Filament\Resources\TrustBadges\TrustBadgeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrustBadges extends ListRecords
{
    protected static string $resource = TrustBadgeResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
