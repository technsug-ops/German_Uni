<?php

namespace App\Filament\Resources\BlockedAccountProviders;

use App\Filament\Resources\BlockedAccountProviders\Pages\CreateBlockedAccountProvider;
use App\Filament\Resources\BlockedAccountProviders\Pages\EditBlockedAccountProvider;
use App\Filament\Resources\BlockedAccountProviders\Pages\ListBlockedAccountProviders;
use App\Filament\Resources\BlockedAccountProviders\Schemas\BlockedAccountProviderForm;
use App\Filament\Resources\BlockedAccountProviders\Tables\BlockedAccountProvidersTable;
use App\Models\BlockedAccountProvider;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BlockedAccountProviderResource extends Resource
{
    protected static ?string $model = BlockedAccountProvider::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected static ?string $navigationLabel = 'Bloke Hesap Sağlayıcılar';
    protected static ?string $modelLabel = 'Bloke Hesap Sağlayıcı';
    protected static ?string $pluralModelLabel = 'Bloke Hesap Sağlayıcılar';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 36;
    protected static string|\UnitEnum|null $navigationGroup = 'İçerik';

    public static function form(Schema $schema): Schema
    {
        return BlockedAccountProviderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BlockedAccountProvidersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListBlockedAccountProviders::route('/'),
            'create' => CreateBlockedAccountProvider::route('/create'),
            'edit'   => EditBlockedAccountProvider::route('/{record}/edit'),
        ];
    }
}
