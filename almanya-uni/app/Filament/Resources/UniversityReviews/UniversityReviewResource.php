<?php

namespace App\Filament\Resources\UniversityReviews;

use App\Filament\Resources\UniversityReviews\Pages\EditUniversityReview;
use App\Filament\Resources\UniversityReviews\Pages\ListUniversityReviews;
use App\Filament\Resources\UniversityReviews\Schemas\UniversityReviewForm;
use App\Filament\Resources\UniversityReviews\Tables\UniversityReviewsTable;
use App\Models\UniversityReview;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UniversityReviewResource extends Resource
{
    protected static ?string $model = UniversityReview::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;
    protected static ?string $navigationLabel = 'Üniversite Yorumları';
    protected static ?string $modelLabel = 'Yorum';
    protected static ?string $pluralModelLabel = 'Yorumlar';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?int $navigationSort = 76;
    protected static string|\UnitEnum|null $navigationGroup = 'İçerik';

    public static function form(Schema $schema): Schema
    {
        return UniversityReviewForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UniversityReviewsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUniversityReviews::route('/'),
            'edit'  => EditUniversityReview::route('/{record}/edit'),
        ];
    }

    /** Badge: bekleyen + doğrulanmış yorum sayısı (gerçek moderation queue). */
    public static function getNavigationBadge(): ?string
    {
        $n = UniversityReview::where('status', 'pending')->where('is_verified', true)->count();
        return $n > 0 ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
