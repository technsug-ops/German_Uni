<?php

namespace App\Filament\Resources\ContentBriefs\Pages;

use App\Filament\Resources\ContentBriefs\ContentBriefResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContentBriefs extends ListRecords
{
    protected static string $resource = ContentBriefResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
