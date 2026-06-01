<?php

namespace App\Filament\Resources\NewsCandidates\Pages;

use App\Filament\Resources\NewsCandidates\NewsCandidateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsCandidate extends CreateRecord
{
    protected static string $resource = NewsCandidateResource::class;
}
