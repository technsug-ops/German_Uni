<?php

namespace App\Filament\Resources\EventReviews;

use App\Filament\Resources\EventReviews\Pages\CreateEventReview;
use App\Filament\Resources\EventReviews\Pages\EditEventReview;
use App\Filament\Resources\EventReviews\Pages\ListEventReviews;
use App\Filament\Resources\EventReviews\Schemas\EventReviewForm;
use App\Filament\Resources\EventReviews\Tables\EventReviewsTable;
use App\Models\EventReview;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EventReviewResource extends Resource
{
    protected static ?string $model = EventReview::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static string|UnitEnum|null $navigationGroup = 'Topluluk';

    protected static ?string $navigationLabel = 'Etkinlik Yorumları';

    protected static ?string $modelLabel = 'Etkinlik Yorumu';

    protected static ?string $pluralModelLabel = 'Etkinlik Yorumları';

    protected static ?string $recordTitleAttribute = 'body';

    public static function getNavigationBadge(): ?string
    {
        $count = EventReview::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return EventReviewForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventReviewsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListEventReviews::route('/'),
            'create' => CreateEventReview::route('/create'),
            'edit'   => EditEventReview::route('/{record}/edit'),
        ];
    }
}
