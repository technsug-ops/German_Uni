<?php

namespace App\Filament\Resources\FieldOfStudies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FieldOfStudyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required(),
                TextInput::make('name_tr')
                    ->required(),
                TextInput::make('name_de')
                    ->required(),
                TextInput::make('name_en'),
                TextInput::make('icon'),
                TextInput::make('color'),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
