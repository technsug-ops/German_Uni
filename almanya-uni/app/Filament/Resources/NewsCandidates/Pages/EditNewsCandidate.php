<?php

namespace App\Filament\Resources\NewsCandidates\Pages;

use App\Filament\Resources\NewsCandidates\NewsCandidateResource;
use App\Models\NewsCandidate;
use App\Services\Content\NewsService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditNewsCandidate extends EditRecord
{
    protected static string $resource = NewsCandidateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('fetch')
                ->label('İçeriği Çek')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('warning')
                ->visible(fn () => $this->record->source_url && $this->record->status !== NewsCandidate::STATUS_PUBLISHED)
                ->action(function () {
                    $data = app(NewsService::class)->fetchUrl($this->record->source_url);
                    if (! $data['title'] && ! $data['text']) {
                        Notification::make()->title('Çekilemedi — URL erişilemedi/boş')->danger()->send();
                        return;
                    }
                    $this->record->update([
                        'orig_title'      => $data['title'] ?: $this->record->orig_title,
                        'raw_excerpt'     => $data['excerpt'] ?: $this->record->raw_excerpt,
                        'image_url'       => $this->record->image_url ?: $data['image'],
                        'fetched_content' => $data['text'] ?: $this->record->fetched_content,
                    ]);
                    $this->fillForm();
                    Notification::make()->title('İçerik çekildi')->success()->send();
                }),

            Action::make('draft')
                ->label('AI Taslak Üret')
                ->icon('heroicon-o-sparkles')
                ->color('info')
                ->visible(fn () => $this->record->status !== NewsCandidate::STATUS_PUBLISHED)
                ->requiresConfirmation()
                ->action(function () {
                    try {
                        $ok = app(NewsService::class)->generateDraft($this->record);
                    } catch (\Throwable $e) {
                        Notification::make()->title('Hata: ' . $e->getMessage())->danger()->send();
                        return;
                    }
                    $this->fillForm();
                    $ok
                        ? Notification::make()->title('Taslak hazır')->success()->send()
                        : Notification::make()->title('Taslak üretilemedi')->danger()->send();
                }),

            Action::make('publish')
                ->label('Paylaş')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->visible(fn () => $this->record->hasDraft() && $this->record->status !== NewsCandidate::STATUS_PUBLISHED)
                ->requiresConfirmation()
                ->modalHeading('Paylaşalım mı?')
                ->modalDescription('Birincil dilde + diğer aktif dillere çevrilip yayınlanır.')
                ->action(function () {
                    try {
                        $res = app(NewsService::class)->publish($this->record);
                    } catch (\Throwable $e) {
                        Notification::make()->title('Yayınlanamadı: ' . $e->getMessage())->danger()->send();
                        return;
                    }
                    Notification::make()->title('Yayında! Diller: ' . strtoupper(implode(', ', $res['locales'])))->success()->send();
                    $this->redirect(NewsCandidateResource::getUrl('index'));
                }),
        ];
    }
}
