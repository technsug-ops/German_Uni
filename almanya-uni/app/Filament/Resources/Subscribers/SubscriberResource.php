<?php

namespace App\Filament\Resources\Subscribers;

use App\Filament\Resources\Subscribers\Pages\ListSubscribers;
use App\Filament\Resources\Subscribers\Pages\EditSubscriber;
use App\Filament\Resources\Subscribers\Schemas\SubscriberForm;
use App\Filament\Resources\Subscribers\Tables\SubscribersTable;
use App\Models\Subscriber;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubscriberResource extends Resource
{
    /** Hassas kaynak — yalnızca tam admin (editör göremez). */
    public static function canViewAny(): bool
    {
        return auth()->user()?->isFullAdmin() ?? false;
    }

    protected static ?string $model = Subscriber::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;
    protected static ?string $navigationLabel = 'Newsletter Aboneleri';
    protected static ?string $modelLabel = 'Abone';
    protected static ?string $pluralModelLabel = 'Aboneler';
    protected static ?string $recordTitleAttribute = 'email';
    protected static ?int $navigationSort = 80;
    protected static string|\UnitEnum|null $navigationGroup = 'Topluluk';

    public static function form(Schema $schema): Schema
    {
        return SubscriberForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubscribersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubscribers::route('/'),
            'edit'  => EditSubscriber::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Subscriber::confirmed()->count();
    }
}
