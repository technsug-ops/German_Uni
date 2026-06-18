<?php

namespace App\Filament\Resources\EmailTemplates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmailTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Şablon')->columns(2)->components([
                TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('kısa kod, örn: partnership-stw-de'),
                TextInput::make('name')
                    ->required(),
                Select::make('category')
                    ->options([
                        'partnership' => 'Partnerlik',
                        'affiliate' => 'Affiliate',
                        'general' => 'Genel',
                    ])
                    ->default('general'),
                Select::make('locale')
                    ->options([
                        'de' => 'Almanca',
                        'en' => 'İngilizce',
                        'tr' => 'Türkçe',
                    ])
                    ->default('de'),
                TextInput::make('subject')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('body')
                    ->required()
                    ->rows(16)
                    ->columnSpanFull()
                    ->helperText('Yer tutucular: {{provider_name}}, {{city}}, {{sender_name}}'),
                Toggle::make('is_active')
                    ->default(true),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]),
        ]);
    }
}
