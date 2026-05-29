<?php

namespace App\Filament\Resources\EventReviews\Pages;

use App\Filament\Resources\EventReviews\EventReviewResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEventReview extends EditRecord
{
    protected static string $resource = EventReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
