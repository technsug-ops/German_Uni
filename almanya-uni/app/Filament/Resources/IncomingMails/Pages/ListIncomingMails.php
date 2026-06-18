<?php

namespace App\Filament\Resources\IncomingMails\Pages;

use App\Filament\Resources\IncomingMails\IncomingMailResource;
use App\Services\Mail\ImapInbox;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListIncomingMails extends ListRecords
{
    protected static string $resource = IncomingMailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('fetch')
                ->label("IMAP'ten Çek")
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->action(function () {
                    if (! ImapInbox::available()) {
                        Notification::make()->title('IMAP kullanılamıyor')
                            ->body(ImapInbox::unavailableReason())->warning()->persistent()->send();

                        return;
                    }
                    try {
                        $n = app(ImapInbox::class)->sync();
                        Notification::make()->title("✅ {$n} yeni mail")->success()->send();
                    } catch (\Throwable $e) {
                        report($e);
                        Notification::make()->title('❌ Çekme hatası')->body($e->getMessage())->danger()->persistent()->send();
                    }
                }),
        ];
    }
}
