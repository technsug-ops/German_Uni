<?php

namespace App\Filament\Resources\HealthInsuranceProviders;

use App\Filament\Resources\HealthInsuranceProviders\Pages\CreateHealthInsuranceProvider;
use App\Filament\Resources\HealthInsuranceProviders\Pages\EditHealthInsuranceProvider;
use App\Filament\Resources\HealthInsuranceProviders\Pages\ListHealthInsuranceProviders;
use App\Filament\Resources\HealthInsuranceProviders\Schemas\HealthInsuranceProviderForm;
use App\Filament\Resources\HealthInsuranceProviders\Tables\HealthInsuranceProvidersTable;
use App\Models\HealthInsuranceProvider;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HealthInsuranceProviderResource extends Resource
{
    protected static ?string $model = HealthInsuranceProvider::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;
    protected static ?string $navigationLabel = 'Sağlık Sigortaları';
    protected static ?string $modelLabel = 'Sağlık Sigortası';
    protected static ?string $pluralModelLabel = 'Sağlık Sigortaları';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 37;
    protected static string|\UnitEnum|null $navigationGroup = 'Kaynaklar';

    public static function form(Schema $schema): Schema
    {
        return HealthInsuranceProviderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HealthInsuranceProvidersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListHealthInsuranceProviders::route('/'),
            'create' => CreateHealthInsuranceProvider::route('/create'),
            'edit'   => EditHealthInsuranceProvider::route('/{record}/edit'),
        ];
    }
}
