<?php

namespace App\Filament\Resources\FieldOfStudies\Pages;

use App\Filament\Resources\FieldOfStudies\FieldOfStudyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFieldOfStudy extends EditRecord
{
    protected static string $resource = FieldOfStudyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
