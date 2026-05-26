<?php

namespace App\Filament\Resources\Feedbacks\Schemas;

use App\Models\Feedback;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FeedbackForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Mesaj')
                ->columns(2)
                ->components([
                    Select::make('type')->label('Tip')->options(Feedback::TYPES)->required()->native(false),
                    Select::make('status')->label('Durum')->options(Feedback::STATUSES)->required()->native(false),
                    TextInput::make('name')->label('İsim'),
                    TextInput::make('email')->label('E-posta')->email(),
                    TextInput::make('subject')->label('Konu')->columnSpanFull(),
                    Textarea::make('message')->label('Mesaj')->rows(6)->columnSpanFull()->required(),
                    TextInput::make('page_url')->label('Geldiği sayfa')->columnSpanFull(),
                ]),

            Section::make('Admin notu')
                ->collapsible()
                ->components([
                    Textarea::make('admin_note')->label('Not (sadece admin)')->rows(3),
                    DateTimePicker::make('resolved_at')->label('Çözüldü tarihi'),
                ]),
        ]);
    }
}
