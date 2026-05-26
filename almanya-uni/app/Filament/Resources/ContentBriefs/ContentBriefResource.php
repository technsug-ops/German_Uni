<?php

namespace App\Filament\Resources\ContentBriefs;

use App\Filament\Resources\ContentBriefs\Pages\CreateContentBrief;
use App\Filament\Resources\ContentBriefs\Pages\EditContentBrief;
use App\Filament\Resources\ContentBriefs\Pages\ListContentBriefs;
use App\Filament\Resources\ContentBriefs\Schemas\ContentBriefForm;
use App\Filament\Resources\ContentBriefs\Tables\ContentBriefsTable;
use App\Models\ContentBrief;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContentBriefResource extends Resource
{
    protected static ?string $model = ContentBrief::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;
    protected static ?string $navigationLabel = 'İçerik Brief\'leri';
    protected static ?string $modelLabel = 'Brief';
    protected static ?string $pluralModelLabel = 'Brief\'ler';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?int $navigationSort = 30;
    protected static string|\UnitEnum|null $navigationGroup = 'İçerik';

    public static function form(Schema $schema): Schema
    {
        return ContentBriefForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentBriefsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\ContentBriefs\RelationManagers\AssetsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContentBriefs::route('/'),
            'create' => CreateContentBrief::route('/create'),
            'edit' => EditContentBrief::route('/{record}/edit'),
        ];
    }
}
