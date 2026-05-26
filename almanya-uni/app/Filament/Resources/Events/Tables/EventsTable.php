<?php

namespace App\Filament\Resources\Events\Tables;

use App\Models\Event;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        $typeOptions = [];
        foreach (Event::TYPES as $key => $meta) {
            $typeOptions[$key] = $meta['emoji'] . ' ' . $meta['label'];
        }

        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => Event::TYPES[$state]['emoji'] . ' ' . (Event::TYPES[$state]['label'] ?? $state))
                    ->color(fn (?string $state) => match ($state) {
                        'webinar' => 'info',
                        'workshop' => 'warning',
                        'meetup', 'open_day' => 'success',
                        'deadline' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('title_tr')->label('Başlık')->limit(50)->searchable()->wrap(),

                TextColumn::make('starts_at')->label('Başlangıç')->dateTime('d.m.Y H:i')->sortable(),

                TextColumn::make('mode')->label('Mod')->badge()->formatStateUsing(fn (?string $state) => match ($state) {
                    'online' => '💻 Online',
                    'offline' => '📍 Yüz yüze',
                    'hybrid' => '🔄 Hibrit',
                    default => $state,
                }),

                TextColumn::make('registered_count')->label('Kayıt')->numeric()
                    ->formatStateUsing(fn ($state, $record) => $record->max_attendees ? "$state / $record->max_attendees" : (string) $state),

                IconColumn::make('is_featured')->label('Banner')->boolean(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')->options($typeOptions),
                SelectFilter::make('mode')->options([
                    'online' => '💻 Online',
                    'offline' => '📍 Yüz yüze',
                    'hybrid' => '🔄 Hibrit',
                ]),
                TernaryFilter::make('is_featured')->label('Üst banner'),
                TernaryFilter::make('is_active')->label('Aktif')->default(true),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('starts_at', 'desc');
    }
}
