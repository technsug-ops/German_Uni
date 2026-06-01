<?php

namespace App\Filament\Resources\NewsCandidates;

use App\Filament\Resources\NewsCandidates\Pages\CreateNewsCandidate;
use App\Filament\Resources\NewsCandidates\Pages\EditNewsCandidate;
use App\Filament\Resources\NewsCandidates\Pages\ListNewsCandidates;
use App\Filament\Resources\NewsCandidates\Schemas\NewsCandidateForm;
use App\Filament\Resources\NewsCandidates\Tables\NewsCandidatesTable;
use App\Models\NewsCandidate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NewsCandidateResource extends Resource
{
    protected static ?string $model = NewsCandidate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;
    protected static ?string $navigationLabel = 'Haber Akışı';
    protected static ?string $modelLabel = 'Haber Adayı';
    protected static ?string $pluralModelLabel = 'Haber Adayları';
    protected static ?string $recordTitleAttribute = 'draft_title';
    protected static ?int $navigationSort = 20;
    protected static string|\UnitEnum|null $navigationGroup = 'İçerik';

    public static function form(Schema $schema): Schema
    {
        return NewsCandidateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NewsCandidatesTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        $n = NewsCandidate::whereIn('status', ['pending', 'approved'])->count();
        return $n ? (string) $n : null;
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListNewsCandidates::route('/'),
            'create' => CreateNewsCandidate::route('/create'),
            'edit'   => EditNewsCandidate::route('/{record}/edit'),
        ];
    }
}
