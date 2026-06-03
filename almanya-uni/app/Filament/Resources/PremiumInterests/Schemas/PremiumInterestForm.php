<?php

namespace App\Filament\Resources\PremiumInterests\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PremiumInterestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Lead Bilgileri')
                ->columns(2)
                ->components([
                    TextInput::make('email')
                        ->label('E-posta')
                        ->email()
                        ->required()
                        ->maxLength(150)
                        ->copyable(),

                    TextInput::make('name')
                        ->label('Ad')
                        ->maxLength(150)
                        ->placeholder('—'),

                    Select::make('tier_interest')
                        ->label('İlgilendiği tier')
                        ->options([
                            'premium'   => 'Premium (€14/ay)',
                            'pro'       => 'Pro (€49 tek)',
                            'undecided' => 'Henüz emin değil',
                        ])
                        ->required(),

                    Select::make('locale')
                        ->label('Dil')
                        ->options(['tr' => 'Türkçe', 'en' => 'English', 'de' => 'Deutsch'])
                        ->placeholder('—'),

                    TextInput::make('country')
                        ->label('Ülke (varsa)')
                        ->maxLength(80)
                        ->placeholder('—'),

                    TextInput::make('source_page')
                        ->label('Geldiği sayfa')
                        ->maxLength(500)
                        ->placeholder('—')
                        ->disabled(),
                ]),

            Section::make('Mesaj / Not')
                ->components([
                    Textarea::make('note')
                        ->label('Lead\'in eklediği not')
                        ->rows(3)
                        ->placeholder('—')
                        ->disabled()
                        ->columnSpanFull(),
                ]),

            Section::make('İletişim Durumu')
                ->columns(2)
                ->components([
                    Toggle::make('contacted')
                        ->label('İletişime geçildi')
                        ->helperText('İşaretledikten sonra timestamp otomatik kaydedilir')
                        ->live()
                        ->afterStateUpdated(function ($state, $set, $record) {
                            if ($state && $record && ! $record->contacted_at) {
                                $set('contacted_at', now());
                            }
                        }),

                    DateTimePicker::make('contacted_at')
                        ->label('İletişim tarihi')
                        ->placeholder('—'),
                ]),

            Section::make('Tracking (read-only)')
                ->columns(2)
                ->collapsed()
                ->components([
                    DateTimePicker::make('created_at')
                        ->label('İlk kayıt')
                        ->disabled(),
                    DateTimePicker::make('updated_at')
                        ->label('Son güncelleme')
                        ->disabled(),
                ]),
        ]);
    }
}
