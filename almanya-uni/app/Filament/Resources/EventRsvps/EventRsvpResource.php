<?php

namespace App\Filament\Resources\EventRsvps;

use App\Filament\Resources\EventRsvps\Pages\CreateEventRsvp;
use App\Filament\Resources\EventRsvps\Pages\EditEventRsvp;
use App\Filament\Resources\EventRsvps\Pages\ListEventRsvps;
use App\Filament\Resources\EventRsvps\Schemas\EventRsvpForm;
use App\Filament\Resources\EventRsvps\Tables\EventRsvpsTable;
use App\Models\EventRsvp;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EventRsvpResource extends Resource
{
    protected static ?string $model = EventRsvp::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static string|UnitEnum|null $navigationGroup = 'Topluluk';

    protected static ?string $navigationLabel = 'Etkinlik Kayıtları';

    protected static ?string $modelLabel = 'Etkinlik Kaydı';

    protected static ?string $pluralModelLabel = 'Etkinlik Kayıtları';

    protected static ?string $recordTitleAttribute = 'attendee_name';

    public static function getNavigationBadge(): ?string
    {
        $count = EventRsvp::whereHas('event', fn ($q) => $q->where('starts_at', '>', now()))
            ->where('status', 'going')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Schema $schema): Schema
    {
        return EventRsvpForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventRsvpsTable::configure($table);
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
            'index' => ListEventRsvps::route('/'),
            'create' => CreateEventRsvp::route('/create'),
            'edit' => EditEventRsvp::route('/{record}/edit'),
        ];
    }
}
