<?php

namespace App\Filament\Resources\NewsCandidates\Pages;

use App\Filament\Resources\NewsCandidates\NewsCandidateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNewsCandidates extends ListRecords
{
    protected static string $resource = NewsCandidateResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Manuel haber ekle')];
    }
}
