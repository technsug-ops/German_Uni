<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use App\Services\Mail\Outbox;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditLead extends EditRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Lead'e doğrudan panelden e-posta ile yanıt ver (partnerships@ üzerinden,
            // OutreachMail + email_messages logu). Başarılıysa durum "İletişime geçildi" olur.
            Action::make('reply')
                ->label('Yanıtla')
                ->icon(Heroicon::OutlinedEnvelope)
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
                    // Lead yanıtları admin@ kutusundan gider.
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

            DeleteAction::make(),
        ];
    }
}
