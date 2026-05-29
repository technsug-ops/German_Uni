<?php

namespace App\Filament\Resources\SocialLinks;

use App\Filament\Resources\SocialLinks\Pages\ListSocialLinks;
use App\Models\SocialLink;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SocialLinkResource extends Resource
{
    protected static ?string $model = SocialLink::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShare;
    protected static ?string $navigationLabel = '🔗 Sosyal Medya';
    protected static ?string $modelLabel = 'Sosyal Link';
    protected static ?string $pluralModelLabel = 'Sosyal Medya';
    protected static ?int $navigationSort = 80;
    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';

    /** Yalnızca tam admin. */
    public static function canViewAny(): bool
    {
        return auth()->user()?->isFullAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('label')->label('Ad')->required(),
            TextInput::make('url')->label('URL')->url()
                ->placeholder('https://instagram.com/almanyauni')
                ->helperText('Boş bırakırsan footer\'da gösterilmez.'),
            Select::make('group')->label('Grup')->options(SocialLink::GROUPS)->required(),
            TextInput::make('sort_order')->label('Sıra')->numeric()->default(0),
            Toggle::make('is_active')->label('Aktif (footer\'da göster)')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')->label('Platform')->weight('bold'),
                TextColumn::make('url')->label('URL')->limit(40)->placeholder('— boş —')->color('gray'),
                TextColumn::make('group')->label('Grup')->badge()
                    ->formatStateUsing(fn ($s) => SocialLink::GROUPS[$s] ?? $s),
                TextColumn::make('sort_order')->label('Sıra')->sortable(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return ['index' => ListSocialLinks::route('/')];
    }
}
