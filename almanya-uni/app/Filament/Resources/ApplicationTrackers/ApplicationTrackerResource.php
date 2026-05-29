<?php

namespace App\Filament\Resources\ApplicationTrackers;

use App\Filament\Resources\ApplicationTrackers\Pages\ListApplicationTrackers;
use App\Models\ApplicationTracker;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ApplicationTrackerResource extends Resource
{
    protected static ?string $model = ApplicationTracker::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;
    protected static ?string $navigationLabel = 'Yol Haritası Takipçileri';
    protected static ?string $modelLabel = 'Yol Haritası';
    protected static ?string $pluralModelLabel = 'Yol Haritası Takipçileri';
    protected static ?int $navigationSort = 23;
    protected static string|\UnitEnum|null $navigationGroup = 'Topluluk';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]); // read-only — DB-managed by user actions
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user:id,name,email', 'targetUniversity:id,name_de,slug']);
    }

    public static function table(Table $table): Table
    {
        $total = count(ApplicationTracker::STEPS);

        return $table
            ->columns([
                TextColumn::make('user.name')->label('Kullanıcı')->searchable()->limit(30),
                TextColumn::make('user.email')->label('Email')->toggleable()->copyable(),
                TextColumn::make('progress')
                    ->label('İlerleme')
                    ->formatStateUsing(fn ($state, ApplicationTracker $r) => $r->completedCount() . '/' . $total . '  (' . $r->progressPercent() . '%)')
                    ->badge()
                    ->color(fn ($state, ApplicationTracker $r) => match (true) {
                        $r->progressPercent() >= 100 => 'success',
                        $r->progressPercent() >= 50  => 'info',
                        $r->progressPercent() > 0    => 'warning',
                        default                       => 'gray',
                    }),
                TextColumn::make('target_degree')->label('Hedef')->badge()->toggleable(),
                TextColumn::make('target_intake')->label('Dönem')->toggleable(),
                TextColumn::make('targetUniversity.name_de')->label('Hedef Üni')->limit(30)->wrap()->toggleable(),
                TextColumn::make('started_at')->label('Başlangıç')->date()->sortable(),
                TextColumn::make('last_activity_at')
                    ->label('Son hareket')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->color(fn ($state) => $state && now()->diffInDays($state, false) < -14 ? 'danger' : ($state && now()->diffInDays($state, false) < -7 ? 'warning' : 'success')),
                TextColumn::make('email_reminders')
                    ->label('Bildirim')
                    ->formatStateUsing(fn ($state) => $state ? '✅ açık' : '❌ kapalı')
                    ->toggleable(),
            ])
            ->defaultSort('last_activity_at', 'desc')
            ->filters([
                TernaryFilter::make('email_reminders')->label('Bildirim açık'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApplicationTrackers::route('/'),
        ];
    }
}
