<?php

namespace App\Filament\Resources\Subscribers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SubscriberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Abone Bilgileri')
                ->columns(2)
                ->components([
                    TextInput::make('email')
                        ->label('E-posta')
                        ->email()
                        ->required()
                        ->maxLength(191),

                    TextInput::make('name')
                        ->label('Ad')
                        ->maxLength(100),

                    Select::make('language')
                        ->label('Dil')
                        ->options(['tr' => 'Türkçe', 'en' => 'English', 'de' => 'Deutsch'])
                        ->default('tr'),

                    Select::make('source')
                        ->label('Kaynak')
                        ->options([
                            'home'    => 'Ana Sayfa',
                            'footer'  => 'Footer',
                            'blog'    => 'Blog (genel)',
                            'about'   => 'Biz Kimiz',
                            'popup'   => 'Popup',
                            'unknown' => 'Bilinmiyor',
                        ])
                        ->searchable(),
                ]),

            Section::make('Durum')
                ->columns(2)
                ->components([
                    DateTimePicker::make('confirmed_at')
                        ->label('Onaylandı'),

                    DateTimePicker::make('unsubscribed_at')
                        ->label('Aboneliği iptal etti'),

                    Textarea::make('unsubscribe_reason')
                        ->label('İptal nedeni')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

            Section::make('Tracking (read-only)')
                ->columns(2)
                ->collapsed()
                ->components([
                    TextInput::make('ip_address')->label('IP')->disabled(),
                    TextInput::make('user_agent')->label('Tarayıcı')->disabled(),
                    TextInput::make('referrer_url')->label('Referrer')->disabled()->columnSpanFull(),
                ]),
        ]);
    }
}
