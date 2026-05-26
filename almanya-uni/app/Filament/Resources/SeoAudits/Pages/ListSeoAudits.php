<?php

namespace App\Filament\Resources\SeoAudits\Pages;

use App\Filament\Resources\SeoAudits\SeoAuditResource;
use App\Services\Seo\SeoAuditorService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\HtmlString;

class ListSeoAudits extends ListRecords
{
    protected static string $resource = SeoAuditResource::class;

    public function getHeader(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.seo._guide', ['compact' => false, 'defaultOpen' => false]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('runAuditAll')
                ->label('🔍 Tüm Template\'leri Audit Et')
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription('17 sayfa şablonu için audit çalıştırılır (AI YOK). 30 saniye sürebilir.')
                ->action(function () {
                    Artisan::call('seo:audit', ['--all' => true]);
                    Notification::make()
                        ->title('🔍 Audit tamamlandı')
                        ->body(Artisan::output())
                        ->success()
                        ->send();
                }),
            Action::make('runAuditAllAi')
                ->label('🪄 Hepsine AI Öneri Üret')
                ->color('success')
                ->requiresConfirmation()
                ->modalDescription('17 template için AI section önerisi üretilir. ~$0.05 maliyet, 2-3 dakika.')
                ->action(function () {
                    Artisan::call('seo:audit', ['--all' => true, '--with-ai' => true]);
                    Notification::make()
                        ->title('🪄 AI önerileri üretildi')
                        ->body(Artisan::output())
                        ->success()
                        ->persistent()
                        ->send();
                }),
        ];
    }
}
