<?php

namespace App\Filament\Resources\Subscribers\Tables;

use App\Models\Subscriber;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscribersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->weight('semibold'),

                TextColumn::make('name')
                    ->label('Ad')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('language')
                    ->label('Dil')
                    ->badge()
                    ->formatStateUsing(fn ($s) => strtoupper((string) $s)),

                TextColumn::make('source')
                    ->label('Kaynak')
                    ->badge()
                    ->color('gray')
                    ->limit(20),

                IconColumn::make('confirmed_at')
                    ->label('Onaylı')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->confirmed_at !== null && $record->unsubscribed_at === null),

                IconColumn::make('unsubscribed_at')
                    ->label('İptal')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->unsubscribed_at !== null)
                    ->color('danger'),

                TextColumn::make('created_at')
                    ->label('Kayıt')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('confirmed_at')
                    ->label('Onay')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('confirmed')
                    ->label('Sadece onaylı')
                    ->query(fn (Builder $q) => $q->whereNotNull('confirmed_at')->whereNull('unsubscribed_at')),

                Filter::make('pending')
                    ->label('Sadece bekleyen')
                    ->query(fn (Builder $q) => $q->whereNull('confirmed_at')->whereNull('unsubscribed_at')),

                Filter::make('unsubscribed')
                    ->label('İptal edenler')
                    ->query(fn (Builder $q) => $q->whereNotNull('unsubscribed_at')),

                SelectFilter::make('language')
                    ->label('Dil')
                    ->options(['tr' => 'TR', 'en' => 'EN', 'de' => 'DE']),

                SelectFilter::make('source')
                    ->label('Kaynak')
                    ->options(fn () => Subscriber::query()->distinct()->pluck('source', 'source')->toArray()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('exportCsv')
                        ->label('📤 CSV indir')
                        ->color('info')
                        ->action(function (Collection $records) {
                            $filename = 'subscribers-' . now()->format('Y-m-d-His') . '.csv';

                            return response()->streamDownload(function () use ($records) {
                                $out = fopen('php://output', 'w');
                                fputcsv($out, ['email', 'name', 'language', 'source', 'confirmed_at', 'unsubscribed_at', 'created_at']);
                                foreach ($records as $r) {
                                    fputcsv($out, [
                                        $r->email,
                                        $r->name,
                                        $r->language,
                                        $r->source,
                                        $r->confirmed_at?->toIso8601String(),
                                        $r->unsubscribed_at?->toIso8601String(),
                                        $r->created_at?->toIso8601String(),
                                    ]);
                                }
                                fclose($out);
                            }, $filename);
                        }),

                    BulkAction::make('forceUnsubscribe')
                        ->label('🚫 Aboneliği iptal et')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $r) {
                                if (! $r->unsubscribed_at) {
                                    $r->update(['unsubscribed_at' => now(), 'unsubscribe_reason' => 'admin_action']);
                                    $count++;
                                }
                            }
                            Notification::make()->title("{$count} abonelik iptal edildi")->success()->send();
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
