<?php

namespace App\Filament\Resources\LanguageCourses\Pages;

use App\Filament\Resources\LanguageCourses\LanguageCourseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLanguageCourses extends ListRecords
{
    protected static string $resource = LanguageCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
