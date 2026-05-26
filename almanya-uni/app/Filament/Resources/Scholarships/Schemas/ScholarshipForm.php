<?php

namespace App\Filament\Resources\Scholarships\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ScholarshipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kimlik')
                    ->columns(3)
                    ->components([
                        TextInput::make('sap_objid')->disabled()->numeric()->label('SAP ObjID'),
                        TextInput::make('daad_id')->disabled()->numeric()->label('DAAD ID'),
                        TextInput::make('slug')->required()->maxLength(191),
                    ]),

                Section::make('İsim')
                    ->columns(2)
                    ->components([
                        TextInput::make('name_en')->label('Name (EN)'),
                        TextInput::make('name_de')->label('Name (DE)'),
                        TextInput::make('programmname_en')->label('Programmname (EN)'),
                        TextInput::make('programmname_de')->label('Programmname (DE)'),
                    ]),

                Section::make('İçerik')
                    ->columns(1)
                    ->components([
                        Textarea::make('introduction_json')
                            ->label('Giriş (JSON veya düz metin)')
                            ->rows(4)
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : $state)
                            ->dehydrateStateUsing(fn ($state) => self::decodeMaybe($state)),
                        Textarea::make('q_en_json')
                            ->label('Koşullar (EN)')
                            ->rows(4)
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : $state)
                            ->dehydrateStateUsing(fn ($state) => self::decodeMaybe($state)),
                        Textarea::make('q_de_json')
                            ->label('Koşullar (DE)')
                            ->rows(4)
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : $state)
                            ->dehydrateStateUsing(fn ($state) => self::decodeMaybe($state)),
                    ]),

                Section::make('Durum')
                    ->columns(3)
                    ->components([
                        Toggle::make('is_daad')->label('DAAD\'in kendi bursu'),
                        Toggle::make('is_move')->label('isMove'),
                        TextInput::make('detail_url')->label('DAAD canonical URL')->url()->maxLength(500),
                    ]),
            ]);
    }

    private static function decodeMaybe($state)
    {
        if ($state === null || $state === '') return null;
        if (is_array($state)) return $state;
        $decoded = json_decode((string) $state, true);
        return $decoded !== null ? $decoded : $state;
    }
}
