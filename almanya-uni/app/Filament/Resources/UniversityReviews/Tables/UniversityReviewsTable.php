<?php

namespace App\Filament\Resources\UniversityReviews\Tables;

use Filament\Actions\Action;
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

class UniversityReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('university.name_de')
                    ->label('Üniversite')
                    ->searchable()
                    ->limit(30)
                    ->wrap(),

                TextColumn::make('rating')
                    ->label('Puan')
                    ->formatStateUsing(fn (int $state): string => str_repeat('★', $state) . str_repeat('☆', 5 - $state))
                    ->color(fn (int $state): string => $state >= 4 ? 'success' : ($state >= 3 ? 'warning' : 'danger')),

                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->limit(40)
                    ->wrap()
                    ->tooltip(fn ($record) => $record->body),

                TextColumn::make('author_display_name')
                    ->label('Yazar')
                    ->getStateUsing(fn ($record) => $record->author_display_name)
                    ->searchable(['author_name', 'author_email']),

                TextColumn::make('author_status')
                    ->label('Durum')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'current_student' => 'Öğrenci',
                        'alumni'          => 'Mezun',
                        'admitted'        => 'Kabul',
                        'applicant'       => 'Başvuran',
                        default           => '—',
                    })
                    ->toggleable(),

                TextColumn::make('locale')
                    ->label('Dil')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => strtoupper((string) $state))
                    ->toggleable(),

                IconColumn::make('is_verified')
                    ->label('Doğrulandı')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('status')
                    ->label('Mod')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending'  => 'warning',
                        'rejected' => 'danger',
                        'spam'     => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'approved' => '✅ Onaylı',
                        'pending'  => '⏳ Beklemede',
                        'rejected' => '❌ Red',
                        'spam'     => '🚫 Spam',
                        default    => $state,
                    }),

                TextColumn::make('helpful_count')
                    ->label('👍')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('reported_count')
                    ->label('🚩')
                    ->numeric()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Kayıt')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('moderation_queue')
                    ->label('Moderation queue (verified + pending)')
                    ->query(fn (Builder $q) => $q->where('status', 'pending')->where('is_verified', true))
                    ->default(),

                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending'  => '⏳ Beklemede',
                        'approved' => '✅ Onaylı',
                        'rejected' => '❌ Red',
                        'spam'     => '🚫 Spam',
                    ]),

                Filter::make('reported')
                    ->label('Rapor edilenler')
                    ->query(fn (Builder $q) => $q->where('reported_count', '>', 0)),

                Filter::make('unverified')
                    ->label('Doğrulanmamışlar')
                    ->query(fn (Builder $q) => $q->where('is_verified', false)),

                SelectFilter::make('locale')
                    ->label('Dil')
                    ->options(['tr' => 'TR', 'en' => 'EN', 'de' => 'DE']),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('✅ Onayla')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'approved')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status'         => 'approved',
                            'moderated_by'   => auth()->id(),
                            'moderated_at'   => now(),
                        ]);
                        Notification::make()->title('Yorum onaylandı')->success()->send();
                    }),

                Action::make('reject')
                    ->label('❌ Reddet')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status'         => 'rejected',
                            'moderated_by'   => auth()->id(),
                            'moderated_at'   => now(),
                        ]);
                        Notification::make()->title('Yorum reddedildi')->warning()->send();
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approveBulk')
                        ->label('✅ Toplu onayla')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $n = 0;
                            foreach ($records as $r) {
                                if ($r->status !== 'approved') {
                                    $r->update([
                                        'status'       => 'approved',
                                        'moderated_by' => auth()->id(),
                                        'moderated_at' => now(),
                                    ]);
                                    $n++;
                                }
                            }
                            Notification::make()->title("{$n} yorum onaylandı")->success()->send();
                        }),

                    BulkAction::make('markSpam')
                        ->label('🚫 Spam olarak işaretle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $n = 0;
                            foreach ($records as $r) {
                                $r->update([
                                    'status'       => 'spam',
                                    'moderated_by' => auth()->id(),
                                    'moderated_at' => now(),
                                ]);
                                $n++;
                            }
                            Notification::make()->title("{$n} yorum spam olarak işaretlendi")->danger()->send();
                        }),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Henüz yorum yok')
            ->emptyStateDescription('Üniversite sayfalarındaki form aktif olduktan sonra burada görünecek.');
    }
}
