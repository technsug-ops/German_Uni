<?php

namespace App\Filament\Resources\PostComments\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PostCommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'spam',
                        'gray'    => 'rejected',
                    ])
                    ->sortable(),
                TextColumn::make('display_name')
                    ->label('Yazar')
                    ->searchable(['author_name', 'author_email'])
                    ->description(fn ($record) => $record->author_email ?: $record->user?->email),
                TextColumn::make('body')
                    ->label('Yorum')
                    ->limit(120)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('post.title')
                    ->label('Yazı')
                    ->limit(40)
                    ->searchable()
                    ->url(fn ($record) => $record->post ? route('blog.show', $record->post->slug) : null, true),
                IconColumn::make('is_pinned')
                    ->label('📌')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'  => 'Beklemede',
                        'approved' => 'Onaylı',
                        'spam'     => 'Spam',
                        'rejected' => 'Reddedildi',
                    ])
                    ->default('pending'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Onayla')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'approved')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                        ]);
                    }),
                Action::make('spam')
                    ->label('Spam')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status !== 'spam')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'spam'])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approveAll')
                        ->label('Onayla')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records) {
                            foreach ($records as $r) {
                                $r->update([
                                    'status' => 'approved',
                                    'approved_at' => now(),
                                    'approved_by' => auth()->id(),
                                ]);
                            }
                        }),
                    BulkAction::make('spamAll')
                        ->label('Spam')
                        ->icon('heroicon-o-shield-exclamation')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each(fn ($r) => $r->update(['status' => 'spam']))),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
