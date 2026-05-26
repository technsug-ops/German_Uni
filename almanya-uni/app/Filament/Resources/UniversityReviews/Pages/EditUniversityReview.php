<?php

namespace App\Filament\Resources\UniversityReviews\Pages;

use App\Filament\Resources\UniversityReviews\UniversityReviewResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUniversityReview extends EditRecord
{
    protected static string $resource = UniversityReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
