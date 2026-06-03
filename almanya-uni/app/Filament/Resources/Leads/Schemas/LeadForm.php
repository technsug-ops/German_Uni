<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\Lead;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Kaynak')->schema([
                Select::make('source_type')->label('Tür')->options(Lead::SOURCES)->disabled(),
                TextInput::make('source_name')->label('Firma')->disabled(),
                Select::make('status')->label('Durum')->options(Lead::STATUSES)->default('new')->required(),
            ])->columns(3),

            Section::make('İletişim')->schema([
                TextInput::make('name')->label('Ad'),
                TextInput::make('email')->label('E-posta')->email(),
                TextInput::make('phone')->label('Telefon'),
                Textarea::make('message')->label('Mesaj')->rows(4)->columnSpanFull(),
            ])->columns(3),
        ]);
    }
}
