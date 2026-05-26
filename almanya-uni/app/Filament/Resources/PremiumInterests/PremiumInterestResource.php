<?php

namespace App\Filament\Resources\PremiumInterests;

use App\Filament\Resources\PremiumInterests\Pages\EditPremiumInterest;
use App\Filament\Resources\PremiumInterests\Pages\ListPremiumInterests;
use App\Filament\Resources\PremiumInterests\Schemas\PremiumInterestForm;
use App\Filament\Resources\PremiumInterests\Tables\PremiumInterestsTable;
use App\Models\PremiumInterest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PremiumInterestResource extends Resource
{
    /** Hassas kaynak (lead listesi + e-posta) — yalnızca tam admin görür. */
    public static function canViewAny(): bool
    {
        return auth()->user()?->isFullAdmin() ?? false;
    }

    protected static ?string $model = PremiumInterest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;
    protected static ?string $navigationLabel = 'Premium İlgi Leadleri';
    protected static ?string $modelLabel = 'Premium Lead';
    protected static ?string $pluralModelLabel = 'Premium Leadleri';
    protected static ?string $recordTitleAttribute = 'email';
    protected static ?int $navigationSort = 78;
    protected static string|\UnitEnum|null $navigationGroup = 'İletişim';

    public static function form(Schema $schema): Schema
    {
        return PremiumInterestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PremiumInterestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPremiumInterests::route('/'),
            'edit'  => EditPremiumInterest::route('/{record}/edit'),
        ];
    }

    /** Badge: kaç lead henüz contacted=false */
    public static function getNavigationBadge(): ?string
    {
        $n = PremiumInterest::where('contacted', false)->count();
        return $n > 0 ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
