<?php

namespace App\Filament\Resources\FaqTopics;

use App\Filament\Resources\FaqTopics\Pages\CreateFaqTopic;
use App\Filament\Resources\FaqTopics\Pages\EditFaqTopic;
use App\Filament\Resources\FaqTopics\Pages\ListFaqTopics;
use App\Filament\Resources\FaqTopics\Schemas\FaqTopicForm;
use App\Filament\Resources\FaqTopics\Tables\FaqTopicsTable;
use App\Models\FaqTopic;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FaqTopicResource extends Resource
{
    protected static ?string $model = FaqTopic::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;
    protected static ?string $navigationLabel = 'SSS Konuları';
    protected static ?string $modelLabel = 'SSS Konusu';
    protected static ?string $pluralModelLabel = 'SSS Konuları';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 6;
    protected static string|\UnitEnum|null $navigationGroup = 'İçerik';

    public static function form(Schema $schema): Schema
    {
        return FaqTopicForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FaqTopicsTable::configure($table);
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
            'index' => ListFaqTopics::route('/'),
            'create' => CreateFaqTopic::route('/create'),
            'edit' => EditFaqTopic::route('/{record}/edit'),
        ];
    }
}
