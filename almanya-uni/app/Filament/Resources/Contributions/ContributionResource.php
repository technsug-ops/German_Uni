<?php

namespace App\Filament\Resources\Contributions;

use App\Filament\Resources\Contributions\Pages\ListContributions;
use App\Models\Contribution;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContributionResource extends Resource
{
    protected static ?string $model = Contribution::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?string $navigationLabel = '🌱 Topluluk Katkıları';
    protected static ?string $modelLabel = 'Katkı';
    protected static ?string $pluralModelLabel = 'Topluluk Katkıları';
    protected static ?int $navigationSort = 37;
    protected static string|\UnitEnum|null $navigationGroup = 'Topluluk';

    public static function getNavigationBadge(): ?string
    {
        $pending = Contribution::pending()->count();
        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema; // read-only — moderasyon aksiyonlarla
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Resources\Contributions\Tables\ContributionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContributions::route('/'),
        ];
    }
}
