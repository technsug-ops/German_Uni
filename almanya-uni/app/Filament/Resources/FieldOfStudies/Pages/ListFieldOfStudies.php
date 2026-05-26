<?php

namespace App\Filament\Resources\FieldOfStudies\Pages;

use App\Filament\Resources\FieldOfStudies\FieldOfStudyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFieldOfStudies extends ListRecords
{
    protected static string $resource = FieldOfStudyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
