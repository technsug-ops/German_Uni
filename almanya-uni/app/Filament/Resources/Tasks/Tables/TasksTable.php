<?php

namespace App\Filament\Resources\Tasks\Tables;

use App\Models\Task;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->defaultGroup('group')
            ->paginated([25, 50, 100, 'all'])
            ->defaultPaginationPageOption(50)
            ->columns([
                // Asıl "checklist" davranışı: tek tıkla tamam / geri al.
                CheckboxColumn::make('is_done')
                    ->label('')
                    ->alignCenter(),

                TextColumn::make('title')
                    ->label('İş')
                    ->searchable()
                    ->wrap()
                    ->weight(fn (Task $r) => $r->status === 'done' ? null : 'bold')
                    ->color(fn (Task $r) => $r->status === 'done' ? 'gray' : null)
                    ->description(fn (Task $r) => $r->details),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Task::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'todo'    => 'gray',
                        'doing'   => 'info',
                        'done'    => 'success',
                        'skipped' => 'warning',
                        default   => 'gray',
                    }),

                TextColumn::make('priority')
                    ->label('Öncelik')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Task::PRIORITIES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'high'  => 'danger',
                        'low'   => 'gray',
                        default => 'info',
                    })
                    ->toggleable(),

                TextColumn::make('due_date')
                    ->label('Hedef tarih')
                    ->date('d.m.Y')
                    ->sortable()
                    ->placeholder('—')
                    ->color(fn (Task $r) => $r->isOverdue() ? 'danger' : null)
                    ->weight(fn (Task $r) => $r->isOverdue() ? 'bold' : null),

                TextColumn::make('target_url')
                    ->label('Hedef')
                    ->url(fn (Task $r) => $r->target_url, true)
                    ->limit(32)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('notes')
                    ->label('Not')
                    ->limit(40)
                    ->wrap()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('completed_at')
                    ->label('Tamamlandı')
                    ->dateTime('d.m.Y')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('playbook')
                    ->label('Oyun kitabı')
                    ->options(Task::PLAYBOOKS),

                SelectFilter::make('status')
                    ->label('Durum')
                    ->options(Task::STATUSES),

                SelectFilter::make('priority')
                    ->label('Öncelik')
                    ->options(Task::PRIORITIES),

                Filter::make('open')
                    ->label('Sadece açık işler')
                    ->query(fn ($query) => $query->open())
                    ->default(),

                Filter::make('overdue')
                    ->label('Tarihi geçenler')
                    ->query(fn ($query) => $query->overdue()),
            ])
            ->recordActions([
                Action::make('start')
                    ->label('Başla')
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->visible(fn (Task $r) => $r->status === 'todo')
                    ->action(fn (Task $r) => $r->update(['status' => 'doing'])),

                Action::make('skip')
                    ->label('Atla')
                    ->icon('heroicon-o-minus-circle')
                    ->color('gray')
                    ->visible(fn (Task $r) => ! in_array($r->status, ['done', 'skipped'], true))
                    ->requiresConfirmation()
                    ->action(fn (Task $r) => $r->update(['status' => 'skipped'])),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_done')
                        ->label('Tamam işaretle')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'done']))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('mark_todo')
                        ->label('Yapılacağa al')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('gray')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'todo']))
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Görev yok')
            ->emptyStateDescription('Oyun kitabı görevleri buraya gelir; yeni iş eklemek için sağ üstteki düğmeyi kullan.');
    }
}
