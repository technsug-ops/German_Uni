<?php

namespace App\Filament\Resources\FieldOfStudies;

use App\Filament\Resources\FieldOfStudies\Pages\CreateFieldOfStudy;
use App\Filament\Resources\FieldOfStudies\Pages\EditFieldOfStudy;
use App\Filament\Resources\FieldOfStudies\Pages\ListFieldOfStudies;
use App\Filament\Resources\FieldOfStudies\Schemas\FieldOfStudyForm;
use App\Filament\Resources\FieldOfStudies\Tables\FieldOfStudiesTable;
use App\Models\FieldOfStudy;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FieldOfStudyResource extends Resource
{
    protected static ?string $model = FieldOfStudy::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;
    protected static ?string $navigationLabel = 'Alan Grupları';
    protected static ?string $modelLabel = 'Alan';
    protected static ?string $pluralModelLabel = 'Alan Grupları';
    protected static ?string $recordTitleAttribute = 'name_tr';
    protected static ?int $navigationSort = 22;
    protected static string|\UnitEnum|null $navigationGroup = 'Lügat';

    public static function form(Schema $schema): Schema
    {
        return FieldOfStudyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FieldOfStudiesTable::configure($table);
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
            'index' => ListFieldOfStudies::route('/'),
            'create' => CreateFieldOfStudy::route('/create'),
            'edit' => EditFieldOfStudy::route('/{record}/edit'),
        ];
    }
}
