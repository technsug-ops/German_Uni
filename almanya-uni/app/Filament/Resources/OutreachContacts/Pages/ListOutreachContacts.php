<?php

namespace App\Filament\Resources\OutreachContacts\Pages;

use App\Filament\Resources\OutreachContacts\OutreachContactResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOutreachContacts extends ListRecords
{
    protected static string $resource = OutreachContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Yeni kontak'),
        ];
    }
}
