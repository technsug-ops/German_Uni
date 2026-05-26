<?php

namespace App\Filament\Resources\BlockedAccountProviders\Pages;

use App\Filament\Resources\BlockedAccountProviders\BlockedAccountProviderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBlockedAccountProviders extends ListRecords
{
    protected static string $resource = BlockedAccountProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
