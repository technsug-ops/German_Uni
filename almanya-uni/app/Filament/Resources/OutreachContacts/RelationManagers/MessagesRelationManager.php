<?php

namespace App\Filament\Resources\OutreachContacts\RelationManagers;

use App\Models\EmailMessage;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Kontağın altındaki yazışma geçmişi (giden + gelen). Salt okunur —
 * kayıtlar Outbox (gönderim) ve ImapInbox (çekim) tarafından oluşturulur.
 */
class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    protected static ?string $title = 'Yazışma geçmişi';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Tarih')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('direction')
                    ->label('Yön')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'inbound' ? '← Gelen' : '→ Giden')
                    ->color(fn ($state) => $state === 'inbound' ? 'warning' : 'info'),
                TextColumn::make('subject')->label('Konu')->limit(60)->wrap()->searchable(),
                TextColumn::make('mailbox')->label('Kutu')->badge()->color('gray'),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'sent'   => 'success',
                        'failed' => 'danger',
                        default  => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('direction')->label('Yön')->options([
                    'outbound' => 'Giden',
                    'inbound'  => 'Gelen',
                ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Oku')
                    ->schema([
                        TextEntry::make('direction')->label('Yön')
                            ->formatStateUsing(fn ($state) => $state === 'inbound' ? 'Gelen' : 'Giden'),
                        TextEntry::make('from_email')->label('Gönderen'),
                        TextEntry::make('to_email')->label('Alıcı'),
                        TextEntry::make('sent_at')->label('Tarih')->dateTime('d.m.Y H:i')->placeholder('—'),
                        TextEntry::make('subject')->label('Konu')->columnSpanFull(),
                        TextEntry::make('body')->label('İçerik')->prose()->columnSpanFull(),
                        TextEntry::make('error')->label('Hata')->color('danger')
                            ->visible(fn (EmailMessage $record) => filled($record->error))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
