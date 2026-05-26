<?php

namespace App\Filament\Resources\ApiClients\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use App\Models\ApiClient;

class ApiClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Firma')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contact_email')
                    ->label('E-posta')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('plan')
                    ->label('Plan')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'free' => 'gray',
                        'partner' => 'success',
                        'enterprise' => 'warning',
                    }),
                TextColumn::make('rate_limit_per_minute')
                    ->label('Limit / dk')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('last_used_at')
                    ->label('Son kullanım')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Kayıt')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('plan')->options([
                    'free' => 'Free', 'partner' => 'Partner', 'enterprise' => 'Enterprise',
                ]),
                TernaryFilter::make('is_active')->label('Aktif mi?'),
            ])
            ->recordActions([
                Action::make('generateToken')
                    ->label('🔑 Token üret')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription('Yeni token üretildiğinde bir önceki token iptal olmaz (manuel sil). Token yalnızca BİR KEZ gösterilir.')
                    ->action(function (ApiClient $record) {
                        $token = $record->createToken('manual-' . now()->format('Ymd-His'), $record->defaultAbilities());

                        Notification::make()
                            ->title('🔑 Yeni Token')
                            ->body("\n" . $token->plainTextToken . "\n\nKopyala, bir daha gösterilmeyecek.")
                            ->persistent()
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
