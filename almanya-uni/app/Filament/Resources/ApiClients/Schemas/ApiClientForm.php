<?php

namespace App\Filament\Resources\ApiClients\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ApiClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Firma adı')
                ->required()
                ->maxLength(120)
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, $set, $get) =>
                    blank($get('slug')) ? $set('slug', Str::slug($state)) : null
                ),
            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(120),
            TextInput::make('contact_email')
                ->label('Kontak e-posta')
                ->required()
                ->email(),
            TextInput::make('contact_name')
                ->label('Kontak kişi')
                ->maxLength(120),
            TextInput::make('website')
                ->label('Web sitesi')
                ->url(),
            Select::make('plan')
                ->label('Plan')
                ->required()
                ->default('free')
                ->options([
                    'free' => 'Free (60 req/dk)',
                    'partner' => 'Partner (1.000 req/dk)',
                    'enterprise' => 'Enterprise (10.000 req/dk)',
                ])
                ->live()
                ->afterStateUpdated(fn ($state, $set) =>
                    $set('rate_limit_per_minute', [
                        'free' => 60, 'partner' => 1000, 'enterprise' => 10000,
                    ][$state] ?? 60)
                ),
            TextInput::make('rate_limit_per_minute')
                ->label('Dakikalık limit (özelleştirme)')
                ->numeric()
                ->minValue(1)
                ->maxValue(100000)
                ->default(60),
            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
            Textarea::make('notes')
                ->label('Notlar')
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }
}
