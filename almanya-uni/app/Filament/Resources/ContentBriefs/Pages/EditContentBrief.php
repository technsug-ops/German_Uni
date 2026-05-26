<?php

namespace App\Filament\Resources\ContentBriefs\Pages;

use App\Filament\Resources\ContentBriefs\ContentBriefResource;
use App\Models\ContentAsset;
use App\Models\ContentBrief;
use App\Services\Content\ContentGenerationService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditContentBrief extends EditRecord
{
    protected static string $resource = ContentBriefResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateAssets')
                ->label('⚡ AI ile Asset Üret')
                ->color('success')
                ->schema([
                    CheckboxList::make('asset_types')
                        ->label('Platformlar')
                        ->options(ContentAsset::TYPES)
                        ->required()
                        ->columns(2)
                        ->default(['blog', 'youtube_short', 'instagram', 'twitter']),
                ])
                ->action(function (array $data) {
                    /** @var ContentBrief $brief */
                    $brief = $this->record;
                    $service = app(ContentGenerationService::class);
                    if (!$service->isConfigured()) {
                        Notification::make()->title('❌ Gemini API key yok')->danger()->send();
                        return;
                    }
                    $results = $service->generateAll($brief, $data['asset_types']);
                    $ok = collect($results)->filter(fn ($r) => $r['success'] ?? false)->count();
                    $fail = collect($results)->filter(fn ($r) => !($r['success'] ?? false))->count();
                    $brief->update(['status' => $ok > 0 ? 'in_progress' : $brief->status]);

                    Notification::make()
                        ->title($fail === 0 ? '✅ Üretildi' : '⚠ Kısmen')
                        ->body("$ok başarılı, $fail hata")
                        ->color($fail === 0 ? 'success' : 'warning')
                        ->persistent()->send();
                }),
            DeleteAction::make(),
        ];
    }
}
