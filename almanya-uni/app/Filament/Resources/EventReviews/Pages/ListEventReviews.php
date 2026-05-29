<?php

namespace App\Filament\Resources\EventReviews\Pages;

use App\Filament\Resources\EventReviews\EventReviewResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEventReviews extends ListRecords
{
    protected static string $resource = EventReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
