<?php

namespace App\Filament\Resources\EventRsvps\Pages;

use App\Filament\Resources\EventRsvps\EventRsvpResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEventRsvps extends ListRecords
{
    protected static string $resource = EventRsvpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
