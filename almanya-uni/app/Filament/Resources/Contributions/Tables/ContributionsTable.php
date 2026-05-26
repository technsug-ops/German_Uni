<?php

namespace App\Filament\Resources\Contributions\Tables;

use App\Models\Contribution;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContributionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ['pending' => '⏳ Bekliyor', 'approved' => '✅ Yayında', 'rejected' => '❌ Red'][$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'approved' => 'success', 'rejected' => 'danger', default => 'warning',
                    }),
                TextColumn::make('type')
                    ->label('Tür')
                    ->formatStateUsing(fn ($state) => Contribution::TYPES[$state] ?? $state)
                    ->badge()->color('gray'),
                TextColumn::make('user.name')->label('Yazan')->searchable(),
                TextColumn::make('title')->label('Başlık')->limit(50)->wrap()->searchable(),
                TextColumn::make('target_label')->label('Konu')->placeholder('Genel')->toggleable(),
                TextColumn::make('content')->label('İçerik')->limit(80)->wrap()->toggleable(),
                TextColumn::make('created_at')->label('Gönderim')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'pending' => '⏳ Bekleyen', 'approved' => '✅ Yayında', 'rejected' => '❌ Red',
                ])->default('pending'),
                SelectFilter::make('type')->options(Contribution::TYPES),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Onayla')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Contribution $r) => $r->status !== 'approved')
                    ->requiresConfirmation()
                    ->modalDescription('Onaylanınca katkı yayınlanır ve yazara "Topluluk Katkıcısı" rozeti verilir.')
                    ->action(function (Contribution $record) {
                        $record->update(['status' => 'approved', 'approved_at' => now()]);
                        // Yazara katkıcı rozeti
                        User::where('id', $record->user_id)->update(['is_contributor' => true]);
                        Notification::make()->title('✅ Onaylandı + katkıcı rozeti verildi')->success()->send();
                    }),
                Action::make('reject')
                    ->label('Reddet')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (Contribution $r) => $r->status !== 'rejected')
                    ->requiresConfirmation()
                    ->action(function (Contribution $record) {
                        $record->update(['status' => 'rejected']);
                        // Başka onaylı katkısı kalmadıysa rozeti kaldır
                        $stillHas = Contribution::where('user_id', $record->user_id)->where('status', 'approved')->exists();
                        if (! $stillHas) {
                            User::where('id', $record->user_id)->update(['is_contributor' => false]);
                        }
                        Notification::make()->title('Reddedildi')->warning()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
