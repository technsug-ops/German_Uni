<?php

namespace App\Filament\Resources\MentorSessions;

use App\Filament\Resources\MentorSessions\Pages\ListMentorSessions;
use App\Models\MentorSession;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MentorSessionResource extends Resource
{
    protected static ?string $model = MentorSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedVideoCamera;
    protected static ?string $navigationLabel = 'Mentor Seansları';
    protected static ?string $modelLabel = 'Mentor Seansı';
    protected static ?string $pluralModelLabel = 'Mentor Seansları';
    protected static ?int $navigationSort = 22;
    protected static string|\UnitEnum|null $navigationGroup = 'Topluluk';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]); // read-only — bookings come from public form
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['mentor:id,name,slug', 'user:id,name,email']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scheduled_at')->dateTime('d.m.Y H:i')->sortable()->label('Zaman'),
                TextColumn::make('mentor.name')->label('Mentor')->searchable(),
                TextColumn::make('user.name')->label('Mentee')->searchable(),
                TextColumn::make('user.email')->label('Email')->toggleable()->copyable(),
                TextColumn::make('duration_minutes')->label('Süre')->suffix(' dk'),
                TextColumn::make('topic')->label('Konu')->limit(40)->wrap(),
                TextColumn::make('status')
                    ->label('Durum')
                    ->formatStateUsing(fn ($state) => MentorSession::STATUSES[$state] ?? $state)
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'   => 'warning',
                        'confirmed' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'gray',
                        'no_show'   => 'danger',
                        default     => 'gray',
                    }),
                TextColumn::make('jitsi_room_id')
                    ->label('Jitsi')
                    ->copyable()
                    ->limit(20)
                    ->formatStateUsing(fn ($state) => '🎥 ' . substr($state, 0, 16) . '...')
                    ->toggleable(),
                TextColumn::make('rating')
                    ->label('⭐')
                    ->formatStateUsing(fn ($state) => $state ? str_repeat('⭐', $state) : '—')
                    ->toggleable(),
            ])
            ->defaultSort('scheduled_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options(MentorSession::STATUSES),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('openJitsi')
                        ->label('🎥 Jitsi\'yi aç')
                        ->url(fn (MentorSession $r) => $r->jitsiUrl())
                        ->openUrlInNewTab(),
                    Action::make('markConfirmed')
                        ->label('✅ Onayla')
                        ->visible(fn (MentorSession $r) => $r->status === 'pending')
                        ->action(fn (MentorSession $r) => $r->update(['status' => 'confirmed'])),
                    Action::make('markCompleted')
                        ->label('🎓 Tamamlandı')
                        ->visible(fn (MentorSession $r) => $r->status === 'confirmed')
                        ->action(fn (MentorSession $r) => $r->update(['status' => 'completed'])),
                    Action::make('markCancelled')
                        ->label('❌ İptal')
                        ->color('danger')
                        ->visible(fn (MentorSession $r) => in_array($r->status, ['pending','confirmed']))
                        ->action(fn (MentorSession $r) => $r->update(['status' => 'cancelled'])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMentorSessions::route('/'),
        ];
    }
}
