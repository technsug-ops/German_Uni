<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use App\Models\Event;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateSeries')
                ->label('🔁 Seriyi N kopya ileri uzat')
                ->color('warning')
                ->visible(fn (Event $record) => $record->isRecurring() && ! $record->parent_event_id)
                ->form([
                    Select::make('rule_preview')
                        ->label('Tekrar kuralı')
                        ->disabled()
                        ->options([
                            'weekly'   => '🗓️ Her hafta',
                            'biweekly' => '🗓️ İki haftada bir',
                            'monthly'  => '🗓️ Ayda bir',
                        ])
                        ->default(fn (Event $record) => $record->recurrence_rule),
                    TextInput::make('count')
                        ->label('Kaç occurrence üretelim?')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->maxValue(52)
                        ->default(6)
                        ->helperText('1–52 arası. Her biri parent ile aynı veri taşır, sadece tarihler ileri kayar.'),
                ])
                ->action(function (Event $record, array $data): void {
                    $children = $record->generateSeriesOccurrences((int) $data['count']);
                    Notification::make()
                        ->title(count($children) . ' adet seri occurrence oluşturuldu')
                        ->success()
                        ->send();
                }),

            Action::make('detachFromSeries')
                ->label('🔗 Parenttan ayır (bağımsız yap)')
                ->color('gray')
                ->visible(fn (Event $record) => $record->isSeriesChild())
                ->requiresConfirmation()
                ->modalHeading('Bu seri parçasını parenttan ayır')
                ->modalDescription('Etkinlik silinmez, sadece serisi koparılır.')
                ->action(function (Event $record): void {
                    $record->update(['parent_event_id' => null]);
                    Notification::make()->title('Seri bağı koparıldı')->success()->send();
                }),

            DeleteAction::make(),
        ];
    }
}
