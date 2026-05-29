<?php

namespace App\Filament\Resources\ApiClients;

use App\Filament\Resources\ApiClients\Pages\CreateApiClient;
use App\Filament\Resources\ApiClients\Pages\EditApiClient;
use App\Filament\Resources\ApiClients\Pages\ListApiClients;
use App\Filament\Resources\ApiClients\Schemas\ApiClientForm;
use App\Filament\Resources\ApiClients\Tables\ApiClientsTable;
use App\Models\ApiClient;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ApiClientResource extends Resource
{
    /** Hassas kaynak — yalnızca tam admin (editör göremez). */
    public static function canViewAny(): bool
    {
        return auth()->user()?->isFullAdmin() ?? false;
    }

    protected static ?string $model = ApiClient::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;
    protected static ?string $navigationLabel = 'API İstemcileri';
    protected static ?string $modelLabel = 'API İstemcisi';
    protected static ?string $pluralModelLabel = 'API İstemcileri';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 60;
    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';

    public static function form(Schema $schema): Schema
    {
        return ApiClientForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApiClientsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApiClients::route('/'),
            'create' => CreateApiClient::route('/create'),
            'edit' => EditApiClient::route('/{record}/edit'),
        ];
    }
}
