<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parent_id')
                    ->label('Üst Kategori')
                    ->placeholder('Yok — bu bir ANA kategori')
                    ->relationship(
                        name: 'parent',
                        titleAttribute: 'name',
                        // Kendini ve alt kategorisi olanları seçeneklerden çıkar (2 seviye derinlik)
                        modifyQueryUsing: fn ($query, ?Category $record) => $query
                            ->whereNull('parent_id')
                            ->when($record, fn ($q) => $q->whereKeyNot($record->id)),
                    )
                    ->searchable()
                    ->preload()
                    ->helperText('Boş bırakırsan ana kategori olur. Bir ana kategori seçersen alt kategori olur.'),

                TextInput::make('name')
                    ->label('Ad')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state ?? ''))),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                Textarea::make('description')
                    ->label('Açıklama')
                    ->rows(2)
                    ->columnSpanFull(),

                ColorPicker::make('color')->label('Renk'),

                TextInput::make('sort_order')
                    ->label('Sıra')
                    ->required()
                    ->numeric()
                    ->default(0),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
