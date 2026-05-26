<?php

namespace App\Filament\Resources\BlockedAccountProviders\Pages;

use App\Filament\Resources\BlockedAccountProviders\BlockedAccountProviderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBlockedAccountProvider extends EditRecord
{
    protected static string $resource = BlockedAccountProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
