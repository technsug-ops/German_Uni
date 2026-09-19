<?php

namespace App\Filament\Resources\OutreachContacts\Schemas;

use App\Models\OutreachContact;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OutreachContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Kurum')->schema([
                TextInput::make('organization')
                    ->label('Firma / Kurum')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                Select::make('category')
                    ->label('Kategori')
                    ->options(OutreachContact::CATEGORIES)
                    ->default('other')
                    ->required(),
                TextInput::make('website')
                    ->label('Web sitesi')
                    ->url()
                    ->prefix('https://')
                    ->maxLength(255)
                    ->columnSpan(2),
                Select::make('priority')
                    ->label('Öncelik')
                    ->options(OutreachContact::PRIORITIES)
                    ->default('normal')
                    ->required(),
            ])->columns(3),

            Section::make('Muhatap')->schema([
                TextInput::make('contact_name')->label('Kişi')->maxLength(255),
                TextInput::make('email')
                    ->label('E-posta')
                    ->email()
                    ->maxLength(255)
                    ->helperText('Gelen yanıtlar bu adrese göre otomatik eşleşir.'),
                TextInput::make('phone')->label('Telefon')->maxLength(64),
            ])->columns(3),

            Section::make('Takip')->schema([
                Select::make('status')
                    ->label('Durum')
                    ->options(OutreachContact::STATUSES)
                    ->default('new')
                    ->required(),
                DatePicker::make('next_followup_at')
                    ->label('Sonraki takip')
                    ->native(false)
                    ->displayFormat('d.m.Y')
                    ->helperText('Tarih gelince menüde rozet çıkar.'),
                TextInput::make('last_contacted_at')
                    ->label('Son temas')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn ($state) => $state ? \Illuminate\Support\Carbon::parse($state)->format('d.m.Y H:i') : 'henüz yok'),
                Textarea::make('notes')
                    ->label('Notlar')
                    ->rows(6)
                    ->columnSpanFull()
                    ->helperText('Görüşme özeti, şartlar, kimin ne dediği.'),
            ])->columns(3),
        ]);
    }
}
