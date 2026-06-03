<?php

namespace App\Filament\Resources\TranslationOffices;

use App\Filament\Resources\TranslationOffices\Pages\CreateTranslationOffice;
use App\Filament\Resources\TranslationOffices\Pages\EditTranslationOffice;
use App\Filament\Resources\TranslationOffices\Pages\ListTranslationOffices;
use App\Filament\Resources\TranslationOffices\Schemas\TranslationOfficeForm;
use App\Filament\Resources\TranslationOffices\Tables\TranslationOfficesTable;
use App\Models\TranslationOffice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TranslationOfficeResource extends Resource
{
    protected static ?string $model = TranslationOffice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;
    protected static ?string $navigationLabel = '📜 Yeminli Tercüme';
    protected static ?string $modelLabel = 'Tercüme Bürosu';
    protected static ?string $pluralModelLabel = 'Tercüme Büroları';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 41;
    protected static string|\UnitEnum|null $navigationGroup = 'Kaynaklar';

    public static function form(Schema $schema): Schema
    {
        return TranslationOfficeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TranslationOfficesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTranslationOffices::route('/'),
            'create' => CreateTranslationOffice::route('/create'),
            'edit' => EditTranslationOffice::route('/{record}/edit'),
        ];
    }
}
