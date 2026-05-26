<?php

namespace App\Filament\Resources\HousingProviders;

use App\Filament\Resources\HousingProviders\Pages\CreateHousingProvider;
use App\Filament\Resources\HousingProviders\Pages\EditHousingProvider;
use App\Filament\Resources\HousingProviders\Pages\ListHousingProviders;
use App\Filament\Resources\HousingProviders\Schemas\HousingProviderForm;
use App\Filament\Resources\HousingProviders\Tables\HousingProvidersTable;
use App\Models\HousingProvider;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HousingProviderResource extends Resource
{
    protected static ?string $model = HousingProvider::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;
    protected static ?string $navigationLabel = 'Yurt Sağlayıcılar';
    protected static ?string $modelLabel = 'Yurt Sağlayıcı';
    protected static ?string $pluralModelLabel = 'Yurt Sağlayıcılar';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 35;
    protected static string|\UnitEnum|null $navigationGroup = 'İçerik';

    public static function form(Schema $schema): Schema
    {
        return HousingProviderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HousingProvidersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListHousingProviders::route('/'),
            'create' => CreateHousingProvider::route('/create'),
            'edit'   => EditHousingProvider::route('/{record}/edit'),
        ];
    }
}
