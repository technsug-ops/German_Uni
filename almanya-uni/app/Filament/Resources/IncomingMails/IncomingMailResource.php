<?php

namespace App\Filament\Resources\IncomingMails;

use App\Filament\Resources\IncomingMails\Pages\ListIncomingMails;
use App\Filament\Resources\IncomingMails\Tables\IncomingMailsTable;
use App\Models\EmailMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class IncomingMailResource extends Resource
{
    protected static ?string $model = EmailMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;
    protected static ?string $navigationLabel = 'Gelen Kutusu';
    protected static ?string $modelLabel = 'Gelen Mail';
    protected static ?string $recordTitleAttribute = 'subject';
    protected static ?int $navigationSort = 4;
    protected static string|\UnitEnum|null $navigationGroup = 'Mail';

    public static function canAccess(): bool
    {
        return auth()->user()?->isFullAdmin() === true;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('direction', 'inbound');
    }

    public static function table(Table $table): Table
    {
        return IncomingMailsTable::configure($table);
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
            'index' => ListIncomingMails::route('/'),
        ];
    }
}
