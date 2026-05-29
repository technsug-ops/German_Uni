<?php

namespace App\Filament\Resources\EventReviews\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EventReviewForm
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
                TextInput::make('rating')
                    ->required()
                    ->numeric(),
                TextInput::make('attendee_name'),
                TextInput::make('attendee_email')
                    ->email(),
                Textarea::make('body')
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                Toggle::make('is_pinned')
                    ->required(),
                TextInput::make('helpful_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('ip_address'),
                TextInput::make('user_agent'),
                DateTimePicker::make('approved_at'),
                TextInput::make('approved_by')
                    ->numeric(),
            ]);
    }
}
