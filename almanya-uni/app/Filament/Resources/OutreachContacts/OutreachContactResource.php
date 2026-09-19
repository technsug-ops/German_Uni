<?php

namespace App\Filament\Resources\OutreachContacts;

use App\Filament\Resources\OutreachContacts\Pages\CreateOutreachContact;
use App\Filament\Resources\OutreachContacts\Pages\EditOutreachContact;
use App\Filament\Resources\OutreachContacts\Pages\ListOutreachContacts;
use App\Filament\Resources\OutreachContacts\RelationManagers\MessagesRelationManager;
use App\Filament\Resources\OutreachContacts\Schemas\OutreachContactForm;
use App\Filament\Resources\OutreachContacts\Tables\OutreachContactsTable;
use App\Models\OutreachContact;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OutreachContactResource extends Resource
{
    protected static ?string $model = OutreachContact::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;
    protected static ?string $navigationLabel = 'Firma Kontakları';
    protected static ?string $modelLabel = 'Firma Kontağı';
    protected static ?string $pluralModelLabel = 'Firma Kontakları';
    protected static ?string $recordTitleAttribute = 'organization';
    protected static ?int $navigationSort = 0;
    protected static string|\UnitEnum|null $navigationGroup = 'Mail';

    public static function canAccess(): bool
    {
        return auth()->user()?->isFullAdmin() === true;
    }

    /** Takip tarihi gelmiş/geçmiş kontak sayısı. */
    public static function getNavigationBadge(): ?string
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('outreach_contacts')) {
            return null;
        }

        $due = OutreachContact::whereNotNull('next_followup_at')
            ->whereDate('next_followup_at', '<=', now())
            ->whereNotIn('status', ['partner', 'declined'])
            ->count();

        return $due > 0 ? (string) $due : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return OutreachContactForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OutreachContactsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOutreachContacts::route('/'),
            'create' => CreateOutreachContact::route('/create'),
            'edit'   => EditOutreachContact::route('/{record}/edit'),
        ];
    }
}
