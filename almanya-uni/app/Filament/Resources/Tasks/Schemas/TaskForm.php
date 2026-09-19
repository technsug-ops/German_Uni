<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Models\Task;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('İş')->schema([
                TextInput::make('title')
                    ->label('Başlık')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('details')
                    ->label('Ne yapılacak')
                    ->rows(3)
                    ->columnSpanFull(),

                Select::make('playbook')
                    ->label('Oyun kitabı')
                    ->options(Task::PLAYBOOKS)
                    ->default('backlink')
                    ->required(),

                TextInput::make('group')
                    ->label('Bölüm')
                    ->helperText('Tabloda bu başlığa göre gruplanır.')
                    ->maxLength(80),

                TextInput::make('sort_order')
                    ->label('Sıra')
                    ->numeric()
                    ->default(0),
            ])->columns(3),

            Section::make('Takip')->schema([
                Select::make('status')
                    ->label('Durum')
                    ->options(Task::STATUSES)
                    ->default('todo')
                    ->required(),

                Select::make('priority')
                    ->label('Öncelik')
                    ->options(Task::PRIORITIES)
                    ->default('normal')
                    ->required(),

                DatePicker::make('due_date')
                    ->label('Hedef tarih')
                    ->native(false)
                    ->displayFormat('d.m.Y'),

                TextInput::make('target_url')
                    ->label('Hedef bağlantı')
                    ->url()
                    ->maxLength(500)
                    ->columnSpanFull(),

                Textarea::make('notes')
                    ->label('Not')
                    ->helperText('Kiminle konuşuldu, hangi cevabı aldın, sonraki adım.')
                    ->rows(3)
                    ->columnSpanFull(),
            ])->columns(3),
        ]);
    }
}
