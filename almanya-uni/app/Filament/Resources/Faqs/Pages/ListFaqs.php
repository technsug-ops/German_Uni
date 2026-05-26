<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Filament\Resources\Faqs\FaqResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListFaqs extends ListRecords
{
    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateAi')
                ->label('🤖 Topluluk\'tan SSS Üret')
                ->color('success')
                ->icon('heroicon-o-sparkles')
                ->modalHeading('AI ile SSS üret')
                ->modalDescription('Telegram + Forum topluluk havuzundaki gerçek sorular AI ile genel, tekrar sorulabilir SSS\'lere dönüştürülür. Halüsinasyon önlemi: kritik rakam/tarih için "resmi kaynaktan doğrula" denir.')
                ->form([
                    Select::make('batches')
                        ->label('Üretim miktarı')
                        ->options([
                            1 => 'Az (~6-8 SSS, ~30 sn)',
                            2 => 'Orta (~12-16 SSS, ~1 dk)',
                            3 => 'Çok (~18-24 SSS, ~1.5 dk)',
                        ])
                        ->default(2)
                        ->required(),
                    Toggle::make('publish')
                        ->label('Direkt yayınla')
                        ->helperText('Kapalı: taslak olarak kaydedilir, sen onaylarsın (önerilen). Açık: anında yayında.')
                        ->default(false),
                ])
                ->action(function (array $data) {
                    @set_time_limit(300);
                    $args = ['--batch' => 12, '--batches' => (int) $data['batches']];
                    if ($data['publish'] ?? false) {
                        $args['--publish'] = true;
                    }
                    Artisan::call('faq:generate-ai', $args);
                    $output = Artisan::output();

                    // Çıktıdan üretilen sayıyı çek
                    preg_match('/✅\s+(\d+)\s+SSS üretildi/u', $output, $m);
                    $count = $m[1] ?? '?';
                    $state = ($data['publish'] ?? false) ? 'yayınlandı' : 'taslak olarak kaydedildi (onay bekliyor)';

                    Notification::make()
                        ->title("🤖 {$count} yeni SSS {$state}")
                        ->body(($data['publish'] ?? false) ? 'Listede görünüyor.' : 'is_published filtresini kaldırıp gözden geçir.')
                        ->success()
                        ->duration(10000)
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}
