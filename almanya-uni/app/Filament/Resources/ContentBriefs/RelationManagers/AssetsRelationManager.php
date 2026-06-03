<?php

namespace App\Filament\Resources\ContentBriefs\RelationManagers;

use App\Models\Category;
use App\Models\ContentAsset;
use App\Models\ContentBrief;
use App\Models\Post;
use App\Services\Content\ContentGenerationService;
use App\Services\Content\ContentTranslator;
use App\Services\Content\ImageGenerationService;
use App\Services\Content\TextToSpeechService;
use App\Services\Content\VideoComposerService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'assets';

    protected static ?string $title = 'Asset\'ler';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('asset_type')
                ->label('Platform')
                ->required()
                ->options(ContentAsset::TYPES),
            Select::make('language')
                ->label('Dil')
                ->required()
                ->default('tr')
                ->options(ContentAsset::LANGUAGES),
            Select::make('status')
                ->required()
                ->options(ContentAsset::STATUSES)
                ->default('draft'),
            Textarea::make('body_md')
                ->label('İçerik (Markdown)')
                ->rows(20)
                ->columnSpanFull(),
            ViewField::make('media_preview')
                ->view('filament.content.asset-media-preview')
                ->columnSpanFull()
                ->dehydrated(false)
                ->visible(fn ($record) => $record && !empty($record->media)),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset_type')
                    ->label('Platform')
                    ->formatStateUsing(fn (string $state) => ContentAsset::TYPES[$state] ?? $state)
                    ->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft' => 'gray',
                        'ready' => 'info',
                        'scheduled' => 'warning',
                        'published' => 'success',
                    })
                    ->formatStateUsing(fn (string $state) => ContentAsset::STATUSES[$state] ?? $state),
                TextColumn::make('generated_by')->badge(),
                TextColumn::make('body_md')
                    ->label('İçerik')
                    ->limit(80)
                    ->wrap(),
                TextColumn::make('updated_at')->dateTime('d.m H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('asset_type')->options(ContentAsset::TYPES),
                SelectFilter::make('language')->label('Dil')->options(ContentAsset::LANGUAGES),
                SelectFilter::make('status')->options(ContentAsset::STATUSES),
            ])
            ->headerActions([
                CreateAction::make(),
                Action::make('regenerateAll')
                    ->label('🔄 Eksikleri AI ile üret')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function () {
                        /** @var ContentBrief $brief */
                        $brief = $this->ownerRecord;
                        $service = app(ContentGenerationService::class);
                        if (!$service->isConfigured()) {
                            Notification::make()->title('❌ Gemini API key yok')->danger()->send();
                            return;
                        }
                        $existing = $brief->assets()->pluck('asset_type')->all();
                        $missing = array_diff(array_keys(ContentAsset::TYPES), $existing);
                        if (empty($missing)) {
                            Notification::make()->title('Tüm asset\'ler zaten var')->send();
                            return;
                        }
                        $results = $service->generateAll($brief, $missing);
                        $ok = collect($results)->filter(fn ($r) => $r['success'] ?? false)->count();
                        Notification::make()
                            ->title("$ok asset üretildi")
                            ->success()
                            ->send();
                    }),
                Action::make('generateAllImages')
                    ->label('🎨 Tüm Asset\'lere Görsel')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalDescription('Görseli olmayan tüm asset\'lere otomatik görsel üretir (asset türüne göre 1-8 görsel). Ücretsiz (Pollinations) ama 1-3 dk sürebilir.')
                    ->action(function () {
                        $brief = $this->ownerRecord;
                        $service = app(ImageGenerationService::class);
                        $assets = $brief->assets()->where(function ($q) {
                            $q->whereNull('media')->orWhereRaw("JSON_LENGTH(media) = 0");
                        })->get();
                        $totalImages = 0;
                        foreach ($assets as $a) {
                            $r = $service->generateForAsset($a);
                            if ($r['success']) $totalImages += $r['count'];
                        }
                        Notification::make()
                            ->title("🎨 $totalImages görsel üretildi")
                            ->success()->persistent()->send();
                    }),
            ])
            ->recordActions([
                Action::make('translate10')
                    ->label('🌍 10 dile çevir')
                    ->color('info')
                    ->visible(fn (ContentAsset $record) => empty($record->source_asset_id))
                    ->requiresConfirmation()
                    ->modalHeading('10 dile çevir')
                    ->modalDescription('Bu asset TR/EN/DE/FR/ES/IT/PL/RU/AR/FA dillerine Gemini ile çevirilir. Mevcut çeviriler atlanır. ~3-5 dk sürer.')
                    ->action(function (ContentAsset $record) {
                        try {
                            $translator = app(ContentTranslator::class);
                            $results = $translator->translateToAll($record, false, 2);
                            $ok = collect($results)->reject(fn ($r) => $r instanceof \Throwable)->count();
                            $fail = count($results) - $ok;
                            Notification::make()
                                ->title("🌍 {$ok} dile çevrildi" . ($fail ? " ({$fail} hata)" : ''))
                                ->color($ok > 0 ? 'success' : 'danger')
                                ->persistent()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('❌ Çeviri hatası')
                                ->body(substr($e->getMessage(), 0, 200))
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    }),
                Action::make('translateOne')
                    ->label('🌐 Tek dile çevir')
                    ->color('gray')
                    ->visible(fn (ContentAsset $record) => empty($record->source_asset_id))
                    ->schema([
                        Select::make('target_language')
                            ->label('Hedef Dil')
                            ->required()
                            ->options(collect(ContentAsset::LANGUAGES)->except('tr')->toArray()),
                    ])
                    ->action(function (ContentAsset $record, array $data) {
                        try {
                            $translator = app(ContentTranslator::class);
                            $new = $translator->translate($record, $data['target_language']);
                            Notification::make()
                                ->title('✅ ' . ($new->language) . ' çevirisi hazır (#' . $new->id . ')')
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('❌ Çeviri hatası')
                                ->body(substr($e->getMessage(), 0, 200))
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('regenerate')
                    ->label('🔄 Yeniden üret')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (ContentAsset $record) {
                        $service = app(ContentGenerationService::class);
                        $result = $service->generateAsset($record->brief, $record->asset_type);
                        Notification::make()
                            ->title($result['success'] ? '✅ Üretildi' : '❌ Hata')
                            ->body($result['error'] ?? 'OK')
                            ->color($result['success'] ? 'success' : 'danger')
                            ->send();
                    }),
                Action::make('generateImages')
                    ->label('🎨 Görsel Üret')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalDescription('Pollinations.ai FLUX modeli ile görsel(ler) üretilecek. Asset türüne göre 1-8 görsel.')
                    ->action(function (ContentAsset $record) {
                        $service = app(ImageGenerationService::class);
                        $result = $service->generateForAsset($record);
                        Notification::make()
                            ->title($result['success'] ? '🎨 ' . $result['count'] . ' görsel üretildi' : '❌ Hata')
                            ->body($result['error'] ?? 'Asset detayında galeri sekmesinde gör')
                            ->color($result['success'] ? 'success' : 'danger')
                            ->persistent()
                            ->send();
                    }),
                Action::make('composeVideo')
                    ->label('🎬 Video Üret')
                    ->color('success')
                    ->visible(fn (ContentAsset $record) => in_array($record->asset_type, ['tiktok', 'youtube_short', 'instagram', 'youtube_long', 'podcast']))
                    ->requiresConfirmation()
                    ->modalDescription('Görsel + Ses birleştirilerek slideshow video üretilir. FFmpeg yoksa CapCut/Pictory için scene_script.json üretilir.')
                    ->action(function (ContentAsset $record) {
                        $service = app(VideoComposerService::class);
                        $result = $service->composeForAsset($record);

                        $msg = '';
                        if (!empty($result['video_url'])) {
                            $msg .= 'Video: ' . $result['video_url'] . "\n";
                        }
                        if (!empty($result['scene_script_url'])) {
                            $msg .= 'Scene script: ' . $result['scene_script_url'] . "\n";
                        }
                        if (!empty($result['note'])) {
                            $msg .= $result['note'];
                        }

                        Notification::make()
                            ->title($result['success'] ? '🎬 Üretildi' : '❌ Hata')
                            ->body($result['error'] ?? $msg)
                            ->color($result['success'] ? 'success' : 'danger')
                            ->persistent()
                            ->send();
                    }),
                Action::make('generateVoice')
                    ->label('🎙️ Ses Üret')
                    ->color('warning')
                    ->visible(fn (ContentAsset $record) => in_array($record->asset_type, ['podcast', 'youtube_long', 'youtube_short', 'tiktok', 'instagram']))
                    ->requiresConfirmation()
                    ->modalDescription('ElevenLabs ile Türkçe ses (MP3) üretilir. Free tier 10K karakter/ay limiti.')
                    ->action(function (ContentAsset $record) {
                        $service = app(TextToSpeechService::class);
                        if (!$service->isConfigured()) {
                            Notification::make()->title('❌ ELEVENLABS_API_KEY eksik')->body('.env\'e ekle: ELEVENLABS_API_KEY=...')->danger()->persistent()->send();
                            return;
                        }
                        $result = $service->generateForAsset($record);
                        Notification::make()
                            ->title($result['success'] ? '🎙️ Ses üretildi' : '❌ Hata')
                            ->body($result['success'] ? round($result['audio']['size_bytes']/1024,1).' KB MP3' : $result['error'])
                            ->color($result['success'] ? 'success' : 'danger')
                            ->persistent()
                            ->send();
                    }),
                Action::make('publishToBlog')
                    ->label('📤 Blog\'a Aktar')
                    ->color('success')
                    ->visible(fn (ContentAsset $record) => $record->asset_type === 'blog' && ! empty($record->body_md))
                    ->schema([
                        Toggle::make('go_live')
                            ->label('Hemen yayınla (canlı)')
                            ->default(true)
                            ->helperText('Açık: anında /blog\'da canlı. Kapalı: Blog Yazıları\'na TASLAK düşer, sen yayına alırsın.'),
                        Toggle::make('translate_all')
                            ->label('EN + DE\'ye çevir & yayınla (haber gibi)')
                            ->default(true)
                            ->helperText('Açık: TR yayınlanırken İngilizce + Almanca\'ya da çevrilip yayınlanır (Gemini, ~15-40 sn). Yalnız "Hemen yayınla" açıkken çalışır.'),
                    ])
                    ->modalHeading('Blog yazısına aktar')
                    ->modalDescription('Bu asset bir Blog Yazısı (Post) olarak oluşturulur/güncellenir. Markdown frontmatter (title/slug/excerpt) gerekir.')
                    ->action(function (ContentAsset $record, array $data) {
                        @set_time_limit(180);
                        $parsed = self::parseAssetMarkdown((string) $record->body_md);
                        if (! $parsed) {
                            Notification::make()
                                ->title('❌ Markdown aktarılamadı')
                                ->body('Frontmatter (--- title: ... ---) veya başlık eksik. Asset\'i "Yeniden üret" ile tazele.')
                                ->danger()->persistent()->send();
                            return;
                        }

                        $brief = $record->brief;
                        $slug = Str::limit($parsed['slug'] ?: Str::slug($parsed['title']), 250, '');
                        $contentHtml = Str::markdown($parsed['body'], [
                            'html_input' => 'allow',
                            'allow_unsafe_links' => false,
                        ]);
                        $excerpt = Str::limit($parsed['excerpt'] ?: strip_tags($contentHtml), 250, '...');
                        $goLive = (bool) ($data['go_live'] ?? false);
                        $translateAll = (bool) ($data['translate_all'] ?? false);

                        $existing = Post::where('slug', $slug)->first();
                        $payload = [
                            'locale'               => $record->language ?: 'tr',
                            'translation_group_id' => $existing?->translation_group_id ?? (string) Str::uuid(),
                            'user_id'              => auth()->id() ?? 1,
                            'category_id'          => self::resolveCategoryId($brief?->topic),
                            'title'                => Str::limit($parsed['title'], 250, ''),
                            'slug'                 => $slug,
                            'excerpt'              => $excerpt,
                            'content_md'           => $parsed['body'],
                            'content_html'         => $contentHtml,
                            'meta_title'           => Str::limit($parsed['title'], 250, ''),
                            'meta_description'     => $excerpt,
                            'reading_minutes'      => max(1, (int) round(str_word_count(strip_tags($contentHtml)) / 220)),
                            'is_published'         => $goLive,
                            'published_at'         => $existing?->published_at ?? now(),
                        ];

                        $post = $existing ? tap($existing)->update($payload) : Post::create($payload);
                        $record->update(['status' => $goLive ? 'published' : 'ready']);

                        // Haber paritesi: yayınlanırken TR → EN + DE çevir & yayınla (aynı grup).
                        // Yalnız birincil dil TR ise ve yayına alınıyorsa.
                        $translated = [];
                        if ($goLive && $translateAll && ($record->language ?: 'tr') === 'tr') {
                            try {
                                Artisan::call('content:translate-posts', [
                                    '--post'  => $post->id,
                                    '--force' => true,
                                    '--sleep' => 0,
                                ]);
                                $out = Artisan::output();
                                foreach (['en' => 'EN', 'de' => 'DE'] as $loc => $lbl) {
                                    if (str_contains($out, $loc)) {
                                        $translated[] = $lbl;
                                    }
                                }
                            } catch (\Throwable $e) {
                                Notification::make()
                                    ->title('⚠️ TR yayında ama çeviri başarısız')
                                    ->body('EN/DE çevirisini sonra "Eksik çevirileri tamamla" ile dene. ' . mb_substr($e->getMessage(), 0, 120))
                                    ->warning()->persistent()->send();
                            }
                        }

                        $langNote = $translated ? ' + ' . implode(' & ', $translated) . ' çevrildi' : '';
                        Notification::make()
                            ->title($goLive ? '✅ Yayında!' . $langNote : '📝 Taslak Post oluşturuldu')
                            ->body(($existing ? 'Güncellendi' : 'Oluşturuldu') . ': ' . mb_substr($post->title, 0, 50)
                                . ($goLive ? '' : ' — Blog Yazıları\'ndan yayına al.'))
                            ->success()->persistent()->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ;
    }

    /** brief.topic → blog kategori id. Kültür konuları yeni üst kategoriye gider. */
    private static function resolveCategoryId(?string $topic): int
    {
        $map = [
            'vize' => 6, 'randevu' => 6, 'uni_assist' => 8, 'dil' => 7, 'sinav' => 7,
            'yurt' => 9, 'sehir' => 9, 'studienkolleg' => 1, 'master' => 1,
            'sigorta' => 5, 'para' => 5, 'sperrkonto' => 5,
        ];
        if ($topic && isset($map[$topic])) {
            return $map[$topic];
        }
        // Kültür/yaşam konuları → "Almanya'da Yaşam & Kültür"
        if (in_array($topic, ['yasam', 'konut', 'kultur'], true)) {
            $id = Category::where('slug', 'german-life-culture')->value('id');
            if ($id) {
                return (int) $id;
            }
        }
        return 1; // güvenli default
    }

    /**
     * Asset body_md'sinden YAML frontmatter + body ayıklar (PublishBlogAssets ile aynı kural).
     * @return array{title:string,slug:?string,excerpt:?string,body:string}|null
     */
    private static function parseAssetMarkdown(string $md): ?array
    {
        $md = trim($md);
        if (preg_match('/^```(?:markdown|md)?\s*\n(.+)\n```\s*$/s', $md, $w)) {
            $md = trim($w[1]);
        }
        if (! preg_match('/^---\s*\n(.+?)\n---\s*\n(.+)$/s', $md, $m)) {
            return null;
        }
        $body = trim($m[2]);
        $meta = [];
        foreach (preg_split('/\n/', $m[1]) as $line) {
            if (preg_match('/^(\w+):\s*(.+)$/', trim($line), $kv)) {
                $val = trim($kv[2]);
                if (preg_match('/^"(.+)"$/', $val, $q) || preg_match("/^'(.+)'$/", $val, $q)) {
                    $val = $q[1];
                }
                $meta[$kv[1]] = $val;
            }
        }
        if (empty($meta['title'])) {
            return null;
        }
        return [
            'title'   => $meta['title'],
            'slug'    => $meta['slug'] ?? null,
            'excerpt' => $meta['excerpt'] ?? null,
            'body'    => $body,
        ];
    }
}
