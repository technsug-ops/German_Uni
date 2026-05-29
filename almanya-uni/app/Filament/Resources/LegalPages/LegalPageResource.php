<?php

namespace App\Filament\Resources\LegalPages;

use App\Filament\Resources\LegalPages\Pages\CreateLegalPage;
use App\Filament\Resources\LegalPages\Pages\EditLegalPage;
use App\Filament\Resources\LegalPages\Pages\ListLegalPages;
use App\Filament\Resources\LegalPages\Schemas\LegalPageForm;
use App\Filament\Resources\LegalPages\Tables\LegalPagesTable;
use App\Models\LegalPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LegalPageResource extends Resource
{
    protected static ?string $model = LegalPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;
    protected static ?string $navigationLabel = 'Yasal Sayfalar';
    protected static ?string $modelLabel = 'Yasal Sayfa';
    protected static ?string $pluralModelLabel = 'Yasal Sayfalar';
    protected static ?string $recordTitleAttribute = 'key';
    protected static ?int $navigationSort = 90;
    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';

    public static function form(Schema $schema): Schema
    {
        return LegalPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LegalPagesTable::configure($table);
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
            'index' => ListLegalPages::route('/'),
            'create' => CreateLegalPage::route('/create'),
            'edit' => EditLegalPage::route('/{record}/edit'),
        ];
    }
}
