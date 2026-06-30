<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateBlog')
                ->label('🤖 Topluluk\'tan Blog Üret')
                ->color('success')
                ->icon('heroicon-o-sparkles')
                ->modalHeading('AI ile blog taslağı üret')
                ->modalDescription('Türk öğrenci pain-point\'lerinden (Forum + Telegram + Reddit) SEO uyumlu Türkçe blog taslakları üretilir (~1500-2000 kelime, taslak/yayınlanmamış). Onayladıktan sonra DE/EN çevirisi yapılır. Halüsinasyon önlemi: kritik rakam/tarih için "resmi kaynaktan doğrula" denir.')
                ->modalSubmitActionLabel('Üret')
                ->form([
                    Select::make('limit')
                        ->label('Kaç yazı')
                        ->options([
                            1 => '1 yazı (~1 dk)',
                            2 => '2 yazı (~2 dk)',
                            3 => '3 yazı (~3 dk)',
                            5 => '5 yazı (~5 dk)',
                            10 => '10 yazı — tüm başlangıç seti (~10 dk)',
                        ])
                        ->default(2)
                        ->required()
                        ->helperText('Az başla. Her yazı tek tek kaydedilir — sayfa zaman aşımına uğrasa bile o ana kadar üretilenler taslak olarak kalır.'),
                ])
                ->action(function (array $data) {
                    @set_time_limit(900);
                    @ini_set('max_execution_time', '900');
                    Artisan::call('blog:generate-starter', ['--limit' => (int) $data['limit']]);
                    $output = Artisan::output();

                    // Çıktıdan üretilen yazı sayısını kabaca çek
                    preg_match_all('/(?:✅|kaydedildi|created|üretildi)/iu', $output, $m);
                    $count = count($m[0]) ?: (int) $data['limit'];

                    Notification::make()
                        ->title("🤖 {$count} blog taslağı üretildi")
                        ->body('Taslak olarak kaydedildi (yayınlanmadı). Gözden geçir → yayınla → sonra DE/EN çevirisini çalıştır.')
                        ->success()
                        ->duration(12000)
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}
