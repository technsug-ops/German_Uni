<?php

namespace App\Filament\Resources\EventRsvps\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EventRsvpForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')
                    ->relationship('event', 'id')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                TextInput::make('attendee_name'),
                TextInput::make('attendee_email')
                    ->email(),
                TextInput::make('status')
                    ->required()
                    ->default('going'),
                Textarea::make('note')
                    ->columnSpanFull(),
                TextInput::make('ip_address'),
                TextInput::make('user_agent'),
            ]);
    }
}
