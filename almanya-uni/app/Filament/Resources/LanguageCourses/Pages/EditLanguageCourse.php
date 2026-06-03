<?php

namespace App\Filament\Resources\LanguageCourses\Pages;

use App\Filament\Resources\LanguageCourses\LanguageCourseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLanguageCourse extends EditRecord
{
    protected static string $resource = LanguageCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
