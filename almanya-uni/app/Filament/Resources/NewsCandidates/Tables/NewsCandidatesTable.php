<?php

namespace App\Filament\Resources\NewsCandidates\Tables;

use App\Models\NewsCandidate;
use App\Services\Content\NewsService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NewsCandidatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('origin')
                    ->label('Mod')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ['auto' => '1️⃣ Oto', 'link' => '2️⃣ Link', 'manual' => '3️⃣ Manuel'][$state] ?? $state)
                    ->color(fn ($state) => ['auto' => 'info', 'link' => 'warning', 'manual' => 'success'][$state] ?? 'gray'),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn ($state) => [
                        'pending' => '⏳ Beklemede', 'approved' => '👍 Onaylı',
                        'rejected' => '🚫 Reddedildi', 'published' => '✅ Yayında',
                    ][$state] ?? $state)
                    ->color(fn ($state) => [
                        'pending' => 'gray', 'approved' => 'info',
                        'rejected' => 'danger', 'published' => 'success',
                    ][$state] ?? 'gray'),
                TextColumn::make('draft_title')
                    ->label('Başlık')
                    ->state(fn (NewsCandidate $r) => $r->draft_title ?: $r->orig_title ?: '—')
                    ->limit(60)->wrap()->weight('bold')->searchable(['draft_title', 'orig_title']),
                TextColumn::make('suggestedCategory.name_tr')->label('Kategori')->badge()->toggleable(),
                TextColumn::make('source_name')->label('Kaynak')->toggleable(),
                TextColumn::make('priority')->label('Öncelik')->badge()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray')->toggleable(),
                TextColumn::make('created_at')->label('Eklendi')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->label('Durum')->options([
                    'pending' => 'Beklemede', 'approved' => 'Onaylı',
                    'rejected' => 'Reddedildi', 'published' => 'Yayında',
                ]),
                SelectFilter::make('origin')->label('Mod')->options([
                    'auto' => 'Otomatik', 'link' => 'Link', 'manual' => 'Manuel',
                ]),
            ])
            ->recordActions([
                // 2/1) Link veya otomatik: kaynaktan içeriği çek
                Action::make('fetch')
                    ->label('İçeriği Çek')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->visible(fn (NewsCandidate $r) => $r->source_url && $r->status !== NewsCandidate::STATUS_PUBLISHED)
                    ->action(function (NewsCandidate $r) {
                        $data = app(NewsService::class)->fetchUrl($r->source_url);
                        if (! $data['title'] && ! $data['text']) {
                            Notification::make()->title('Çekilemedi — URL erişilemedi/boş')->danger()->send();
                            return;
                        }
                        $r->update([
                            'orig_title'      => $data['title'] ?: $r->orig_title,
                            'raw_excerpt'     => $data['excerpt'] ?: $r->raw_excerpt,
                            'image_url'       => $r->image_url ?: $data['image'],
                            'fetched_content' => $data['text'] ?: $r->fetched_content,
                        ]);
                        Notification::make()->title('İçerik çekildi — şimdi "AI Taslak Üret"')->success()->send();
                    }),

                // AI editöryel taslak üret (özgün, telif-güvenli)
                Action::make('draft')
                    ->label('AI Taslak Üret')
                    ->icon('heroicon-o-sparkles')
                    ->color('info')
                    ->visible(fn (NewsCandidate $r) => $r->status !== NewsCandidate::STATUS_PUBLISHED && ($r->orig_title || $r->raw_excerpt || $r->fetched_content))
                    ->requiresConfirmation()
                    ->modalDescription('Kaynaktan ÖZGÜN bir editöryel taslak üretilir (birincil dilde). Token harcar.')
                    ->action(function (NewsCandidate $r) {
                        try {
                            $ok = app(NewsService::class)->generateDraft($r);
                        } catch (\Throwable $e) {
                            Notification::make()->title('Hata: ' . $e->getMessage())->danger()->send();
                            return;
                        }
                        $ok
                            ? Notification::make()->title('Taslak hazır — kontrol et, sonra "Paylaş"')->success()->send()
                            : Notification::make()->title('Taslak üretilemedi (Gemini)')->danger()->send();
                    }),

                // AI görsel üret (resim yoksa) — habere uygun illüstrasyon
                Action::make('image')
                    ->label('AI Görsel')
                    ->icon('heroicon-o-photo')
                    ->color('gray')
                    ->visible(fn (NewsCandidate $r) => empty($r->image_url) && $r->status !== NewsCandidate::STATUS_PUBLISHED)
                    ->requiresConfirmation()
                    ->modalDescription('Habere uygun ÖZGÜN bir illüstrasyon üretilir (Imagen, token harcar). Paylaşırken resim yoksa zaten otomatik üretilir.')
                    ->action(function (NewsCandidate $r) {
                        $img = app(NewsService::class)->generateImage($r);
                        if ($img) {
                            $r->update(['image_url' => $img]);
                            Notification::make()->title('Görsel üretildi')->success()->send();
                        } else {
                            Notification::make()->title('Görsel üretilemedi (log\'a bak — yazma izni/Imagen).')->danger()->send();
                        }
                    }),

                // Paylaş: çoklu dilde yayınla
                Action::make('publish')
                    ->label('Paylaş')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn (NewsCandidate $r) => $r->hasDraft() && $r->status !== NewsCandidate::STATUS_PUBLISHED)
                    ->requiresConfirmation()
                    ->modalHeading('Paylaşalım mı?')
                    ->modalDescription('Birincil dilde yayınlanır + diğer aktif dillere çevrilip yayınlanır (token harcar).')
                    ->action(function (NewsCandidate $r) {
                        try {
                            $res = app(NewsService::class)->publish($r);
                        } catch (\Throwable $e) {
                            Notification::make()->title('Yayınlanamadı: ' . $e->getMessage())->danger()->send();
                            return;
                        }
                        Notification::make()
                            ->title('Yayında! Diller: ' . strtoupper(implode(', ', $res['locales'])))
                            ->success()->send();
                    }),

                // Reddet
                Action::make('reject')
                    ->label('Reddet')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (NewsCandidate $r) => ! in_array($r->status, [NewsCandidate::STATUS_PUBLISHED, NewsCandidate::STATUS_REJECTED], true))
                    ->action(fn (NewsCandidate $r) => $r->update(['status' => NewsCandidate::STATUS_REJECTED, 'decided_at' => now()])),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
