<?php

namespace App\Filament\Resources\LanguageCourses;

use App\Filament\Resources\LanguageCourses\Pages\CreateLanguageCourse;
use App\Filament\Resources\LanguageCourses\Pages\EditLanguageCourse;
use App\Filament\Resources\LanguageCourses\Pages\ListLanguageCourses;
use App\Filament\Resources\LanguageCourses\Schemas\LanguageCourseForm;
use App\Filament\Resources\LanguageCourses\Tables\LanguageCoursesTable;
use App\Models\LanguageCourse;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LanguageCourseResource extends Resource
{
    protected static ?string $model = LanguageCourse::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;
    protected static ?string $navigationLabel = '🗣️ Dil Kursları';
    protected static ?string $modelLabel = 'Dil Kursu';
    protected static ?string $pluralModelLabel = 'Dil Kursları';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 40;
    protected static string|\UnitEnum|null $navigationGroup = 'Kaynaklar';

    public static function form(Schema $schema): Schema
    {
        return LanguageCourseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LanguageCoursesTable::configure($table);
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
            'index' => ListLanguageCourses::route('/'),
            'create' => CreateLanguageCourse::route('/create'),
            'edit' => EditLanguageCourse::route('/{record}/edit'),
        ];
    }
}
