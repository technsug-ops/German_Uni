<?php

namespace App\Filament\Resources\FaqTopics\Pages;

use App\Filament\Resources\FaqTopics\FaqTopicResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFaqTopic extends EditRecord
{
    protected static string $resource = FaqTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
