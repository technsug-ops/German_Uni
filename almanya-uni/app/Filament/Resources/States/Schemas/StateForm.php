<?php

namespace App\Filament\Resources\States\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('wikidata_id'),
                TextInput::make('name_tr')
                    ->required(),
                TextInput::make('name_de')
                    ->required(),
                TextInput::make('name_en'),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->numeric(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
