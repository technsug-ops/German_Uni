<?php

namespace App\Filament\Resources\EventRsvps\Pages;

use App\Filament\Resources\EventRsvps\EventRsvpResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEventRsvp extends EditRecord
{
    protected static string $resource = EventRsvpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
