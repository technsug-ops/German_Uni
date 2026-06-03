<?php

namespace App\Filament\Resources\ContentBriefs\Tables;

use App\Models\ContentAsset;
use App\Models\ContentBrief;
use App\Services\Content\ContentGenerationService;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ContentBriefsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->limit(45)->wrap(),
                TextColumn::make('topic')->badge()->color('info'),
                TextColumn::make('audience')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => ContentBrief::AUDIENCES[$state] ?? $state),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft' => 'gray',
                        'in_progress' => 'warning',
                        'ready' => 'info',
                        'published' => 'success',
                        'archived' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state) => ContentBrief::STATUSES[$state] ?? $state),
                TextColumn::make('assets_count')
                    ->label('Asset')
                    ->counts('assets')
                    ->badge()
                    ->color('success'),
                TextColumn::make('target_word_count')->label('Kelime')->numeric(),
                TextColumn::make('created_at')->dateTime('d.m H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('audience')->options(ContentBrief::AUDIENCES),
                SelectFilter::make('status')->options(ContentBrief::STATUSES),
                SelectFilter::make('topic')->options([
                    'vize' => 'Vize', 'dil' => 'Dil', 'para' => 'Para', 'randevu' => 'Randevu',
                    'uni_assist' => 'Uni-Assist', 'yurt' => 'Yurt', 'sehir' => 'Şehir',
                    'master' => 'Master', 'sigorta' => 'Sigorta', 'studienkolleg' => 'Studienkolleg',
                    'denklik' => 'Denklik', 'is' => 'İş', 'anmeldung' => 'Anmeldung', 'burs' => 'Burs',
                ]),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Çoklu brief → seçili platform asset'lerini AI ile üret (zaman bütçeli, mevcut atlanır).
                    BulkAction::make('bulkGenerate')
                        ->label('🤖 Toplu Asset Üret')
                        ->color('info')
                        ->icon('heroicon-o-sparkles')
                        ->schema([
                            CheckboxList::make('types')
                                ->label('Platformlar')
                                ->options(ContentAsset::TYPES)
                                ->default(['blog'])
                                ->columns(2)
                                ->required(),
                        ])
                        ->modalDescription('Seçili brief\'lerin EKSİK asset türleri üretilir (mevcut atlanır). Gemini token harcar; ~35 sn bütçe — kalan brief olursa tekrar bas.')
                        ->action(function (Collection $records, array $data) {
                            @set_time_limit(180);
                            $svc = app(ContentGenerationService::class);
                            if (! $svc->isConfigured()) {
                                Notification::make()->title('❌ Gemini API key yok')->danger()->send();
                                return;
                            }
                            $types = (array) ($data['types'] ?? []);
                            $started = microtime(true);
                            $ok = 0; $skip = 0; $fail = 0; $doneBriefs = 0; $leftBriefs = 0;
                            foreach ($records as $brief) {
                                if (microtime(true) - $started >= 35) { $leftBriefs++; continue; }
                                $existing = $brief->assets()->pluck('asset_type')->all();
                                foreach ($types as $type) {
                                    if (in_array($type, $existing, true)) { $skip++; continue; }
                                    if (microtime(true) - $started >= 35) { break; }
                                    try {
                                        $r = $svc->generateAsset($brief, $type);
                                        ($r['success'] ?? false) ? $ok++ : $fail++;
                                    } catch (\Throwable $e) {
                                        $fail++;
                                    }
                                }
                                $doneBriefs++;
                            }
                            Notification::make()
                                ->title("🤖 {$ok} asset üretildi" . ($skip ? " · {$skip} mevcut" : '') . ($fail ? " · {$fail} hata" : ''))
                                ->body("{$doneBriefs} brief işlendi" . ($leftBriefs ? " · {$leftBriefs} kaldı (tekrar bas)" : ''))
                                ->color($fail ? 'warning' : 'success')->persistent()->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
