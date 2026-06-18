<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Mail\OutreachMail;
use App\Models\EmailMessage;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;

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
                        ->helperText('partnerships@applytogerman.com adresinden gönderilir; yanıtlar aynı kutuya düşer.'),
                ])
                ->action(function (array $data, Lead $record) {
                    $msg = EmailMessage::create([
                        'direction'  => 'outbound',
                        'to_email'   => $data['to_email'],
                        'to_name'    => $record->name,
                        'from_email' => 'partnerships@applytogerman.com',
                        'subject'    => $data['subject'],
                        'body'       => $data['body'],
                        'status'     => 'queued',
                    ]);

                    try {
                        Mail::to($data['to_email'], $record->name)
                            ->send(new OutreachMail($data['subject'], $data['body'], 'partnerships@applytogerman.com'));

                        $msg->update(['status' => 'sent', 'sent_at' => now()]);
                        $record->update(['status' => 'contacted']);

                        Notification::make()->title('✅ Yanıt gönderildi')
                            ->body('Lead durumu "İletişime geçildi" olarak güncellendi.')->success()->send();
                    } catch (\Throwable $e) {
                        report($e);
                        $msg->update(['status' => 'failed', 'error' => $e->getMessage()]);

                        Notification::make()->title('❌ Gönderilemedi')
                            ->body($e->getMessage())->danger()->persistent()->send();
                    }
                }),

            DeleteAction::make(),
        ];
    }
}
