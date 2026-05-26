<?php

namespace App\Filament\Resources\FaqTopics\Pages;

use App\Filament\Resources\FaqTopics\FaqTopicResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFaqTopics extends ListRecords
{
    protected static string $resource = FaqTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
