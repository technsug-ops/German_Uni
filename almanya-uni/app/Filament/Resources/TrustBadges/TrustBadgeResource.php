<?php

namespace App\Filament\Resources\TrustBadges;

use App\Filament\Resources\TrustBadges\Pages\CreateTrustBadge;
use App\Filament\Resources\TrustBadges\Pages\EditTrustBadge;
use App\Filament\Resources\TrustBadges\Pages\ListTrustBadges;
use App\Models\TrustBadge;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TrustBadgeResource extends Resource
{
    protected static ?string $model = TrustBadge::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;
    protected static ?string $navigationLabel = 'Güven Rozetleri';
    protected static ?string $modelLabel = 'Güven Rozeti';
    protected static ?string $pluralModelLabel = 'Güven Rozetleri';
    protected static ?int $navigationSort = 39;
    protected static string|\UnitEnum|null $navigationGroup = 'Pazarlama';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Platform')->schema([
                Select::make('platform')
                    ->label('Platform')
                    ->options(TrustBadge::PLATFORM_PRESETS)
                    ->required()
                    ->searchable()
                    ->helperText('Mevcut listede yoksa, key olarak custom yaz (örn. "youtube_subscribers")'),
                TextInput::make('display_name')
                    ->label('Görünür Ad')
                    ->required()
                    ->placeholder('Trustpilot')
                    ->maxLength(100),
            ])->columns(2),

            Section::make('Görsel + Link')->schema([
                TextInput::make('logo_url')
                    ->label('Logo URL (SVG/PNG)')
                    ->url()
                    ->placeholder('https://cdn.trustpilot.net/.../logo.svg')
                    ->maxLength(500),
                TextInput::make('profile_url')
                    ->label('Profil/Review Sayfası')
                    ->url()
                    ->placeholder('https://tr.trustpilot.com/review/applytogerman.com')
                    ->maxLength(500),
            ])->columns(1),

            Section::make('Skor + Yorum Sayısı (varsa)')->schema([
                TextInput::make('rating')
                    ->label('Skor (örn. 4.7)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(5)
                    ->step(0.1),
                TextInput::make('review_count')
                    ->label('Yorum sayısı')
                    ->numeric()
                    ->minValue(0)
                    ->helperText('Boş = sadece logo göster, skor pill\'i çıkar'),
                Textarea::make('badge_html')
                    ->label('Embed kodu (opsiyonel)')
                    ->rows(4)
                    ->columnSpanFull()
                    ->helperText('Trustpilot/G2 vb. resmi embed snippet\'i — kullanılırsa logo + skor görmezden gelinir, doğrudan widget render edilir.'),
            ])->columns(2)->collapsible(),

            Section::make('Yerleşim')->schema([
                Select::make('slot')
                    ->label('Hangi bölgede göster?')
                    ->options(TrustBadge::SLOTS)
                    ->default('footer')
                    ->required(),
                TextInput::make('sort_order')
                    ->label('Sıralama (düşük = solda/önce)')
                    ->numeric()
                    ->default(10),
                Toggle::make('is_active')
                    ->label('Aktif (siteye yansıt)')
                    ->default(false)
                    ->helperText('Henüz platforma kayıt olmadıysan kapalı tut — placeholder olur ama görünmez.'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_url')->label('Logo')->circular()->size(40),
                TextColumn::make('display_name')->label('Ad')->searchable(),
                TextColumn::make('platform')->label('Platform')->badge(),
                TextColumn::make('rating')->label('⭐')->sortable(),
                TextColumn::make('review_count')->label('💬')->sortable(),
                TextColumn::make('slot')->label('Slot')->badge(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('sort_order')->label('Sıra'),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                SelectFilter::make('platform')->options(TrustBadge::PLATFORM_PRESETS),
                SelectFilter::make('slot')->options(TrustBadge::SLOTS),
                TernaryFilter::make('is_active')->label('Aktif'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTrustBadges::route('/'),
            'create' => CreateTrustBadge::route('/create'),
            'edit'   => EditTrustBadge::route('/{record}/edit'),
        ];
    }
}
