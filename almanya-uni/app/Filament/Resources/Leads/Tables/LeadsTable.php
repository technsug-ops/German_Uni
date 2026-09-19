<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Models\Lead;
use App\Services\Mail\Outbox;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Lead listesi.
 *
 * Tasarım notu: tablo 8 sütunla yatay kaydırma gerektiriyordu ve "Yanıtla"ya
 * ulaşmak için hem sağa kaydırmak hem kaydı açmak gerekiyordu. İki değişiklik:
 *   1) İlişkili alanlar tek sütunda birleştirildi (tür+firma, ad+e-posta) →
 *      tablo ekrana sığıyor.
 *   2) Aksiyonlar satırın BAŞINA alındı ve "Yanıtla" doğrudan listeye kondu →
 *      yanıt için kaydı açmak gerekmiyor.
 */
class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordActionsPosition(RecordActionsPosition::BeforeCells)
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->since()
                    ->tooltip(fn (Lead $r) => $r->created_at?->format('d.m.Y H:i'))
                    ->sortable(),

                // Tür + firma tek sütunda
                TextColumn::make('source_type')
                    ->label('Kaynak')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Lead::SOURCES[$state] ?? $state)
                    ->color(fn ($state) => $state === 'language_course' ? 'info' : 'warning')
                    ->description(fn (Lead $r) => $r->source_name)
                    ->searchable(['source_name']),

                // Ad + e-posta tek sütunda; telefon isteğe bağlı
                TextColumn::make('email')
                    ->label('Kişi')
                    ->formatStateUsing(fn (Lead $r) => $r->name ?: $r->email)
                    ->description(fn (Lead $r) => $r->name ? $r->email : null)
                    ->copyable()
                    ->copyableState(fn (Lead $r) => $r->email)
                    ->searchable(['name', 'email'])
                    ->wrap(),

                TextColumn::make('phone')
                    ->label('Telefon')
                    ->copyable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('message')
                    ->label('Mesaj')
                    ->limit(60)
                    ->wrap()
                    ->tooltip(fn (Lead $r) => $r->message),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Lead::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'new' => 'warning', 'contacted' => 'info', 'converted' => 'success', default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('source_type')->label('Tür')->options(Lead::SOURCES),
                SelectFilter::make('status')->label('Durum')->options(Lead::STATUSES),
            ])
            ->recordActions([
                // Kaydı açmadan yanıtla — EditLead'deki aksiyonun aynısı.
                Action::make('reply')
                    ->label('Yanıtla')
                    ->icon('heroicon-o-envelope')
                    ->color('success')
                    ->visible(fn (Lead $record) => filled($record->email))
                    ->modalHeading('E-posta ile yanıtla')
                    ->modalSubmitActionLabel('Gönder')
                    ->fillForm(fn (Lead $record) => [
                        'to_email' => $record->email,
                        'subject'  => 'Re: ' . ($record->source_name ?: 'Talebiniz'),
                    ])
                    ->schema([
                        TextInput::make('to_email')->label('Alıcı')->email()->required(),
                        TextInput::make('subject')->label('Konu')->required(),
                        Textarea::make('body')->label('Mesaj')->rows(12)->required()
                            ->helperText('admin@applytogerman.com adresinden gönderilir; yanıtlar aynı kutuya düşer.'),
                    ])
                    ->action(function (array $data, Lead $record) {
                        $msg = Outbox::send('admin', $data['to_email'], $record->name, $data['subject'], $data['body']);

                        if ($msg->status === 'sent') {
                            $record->update(['status' => 'contacted']);
                            Notification::make()->title('✅ Yanıt gönderildi (admin@)')
                                ->body('Lead durumu "İletişime geçildi" olarak güncellendi.')->success()->send();
                        } else {
                            Notification::make()->title('❌ Gönderilemedi')
                                ->body($msg->error ?: 'Bilinmeyen hata')->danger()->persistent()->send();
                        }
                    }),

                EditAction::make()->label('Düzenle'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
