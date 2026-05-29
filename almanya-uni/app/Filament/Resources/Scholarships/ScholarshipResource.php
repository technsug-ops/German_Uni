<?php

namespace App\Filament\Resources\Scholarships;

use App\Filament\Resources\Scholarships\Pages\EditScholarship;
use App\Filament\Resources\Scholarships\Pages\ListScholarships;
use App\Filament\Resources\Scholarships\Pages\ViewScholarship;
use App\Filament\Resources\Scholarships\Schemas\ScholarshipForm;
use App\Filament\Resources\Scholarships\Tables\ScholarshipsTable;
use App\Models\Scholarship;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ScholarshipResource extends Resource
{
    protected static ?string $model = Scholarship::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;
    protected static ?string $navigationLabel = 'DAAD Bursları';
    protected static ?string $modelLabel = 'Burs';
    protected static ?string $pluralModelLabel = 'DAAD Bursları';
    protected static ?string $recordTitleAttribute = 'name_en';
    protected static ?int $navigationSort = 30;
    protected static string|\UnitEnum|null $navigationGroup = 'Akademik Veri';

    public static function form(Schema $schema): Schema
    {
        return ScholarshipForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScholarshipsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListScholarships::route('/'),
            'view'  => ViewScholarship::route('/{record}'),
            'edit'  => EditScholarship::route('/{record}/edit'),
        ];
    }
}
