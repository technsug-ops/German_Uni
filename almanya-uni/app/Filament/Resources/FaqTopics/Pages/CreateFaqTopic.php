<?php

namespace App\Filament\Resources\FaqTopics\Pages;

use App\Filament\Resources\FaqTopics\FaqTopicResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFaqTopic extends CreateRecord
{
    protected static string $resource = FaqTopicResource::class;
}
