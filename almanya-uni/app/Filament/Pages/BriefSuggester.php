<?php

namespace App\Filament\Pages;

use App\Models\ContentBrief;
use App\Services\Content\BriefSuggestionService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;

class BriefSuggester extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;
    protected static ?string $navigationLabel = '🪄 Brief Önerileri (AI)';
    protected static ?string $title = 'AI Brief Önerileri';
    protected static ?int $navigationSort = 31;
    protected static string|\UnitEnum|null $navigationGroup = 'İçerik';

    protected string $view = 'filament.pages.brief-suggester';

    #[Url]
    public ?string $topic = 'vize';

    #[Url]
    public ?string $audience = 'aday_ogrenci';

    public int $count = 10;

    public ?string $extraInstructions = null;

    public bool $liveSearch = false;

    public array $suggestions = [];

    public ?string $errorMessage = null;

    public ?array $tokens = null;

    public ?string $modelUsed = null;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Parametreler')
                ->columns(3)
                ->components([
                    Select::make('topic')
                        ->label('Topic')
                        ->required()
                        ->options([
                            'vize' => 'Vize (8.5K mesaj)',
                            'dil' => 'Dil (7.7K)',
                            'para' => 'Para & Finansman (2.8K)',
                            'randevu' => 'Randevu (2.4K)',
                            'uni_assist' => 'Uni-Assist (1.7K)',
                            'yurt' => 'Yurt (1.2K)',
                            'sehir' => 'Şehir (1K)',
                            'master' => 'Master (960)',
                            'sigorta' => 'Sigorta (734)',
                            'studienkolleg' => 'Studienkolleg (433)',
                            'denklik' => 'Denklik (432)',
                            'is' => 'İş & Werkstudent (430)',
                            'anmeldung' => 'Anmeldung (407)',
                            'burs' => 'Burs (264)',
                        ]),
                    Select::make('audience')
                        ->label('Hedef kitle')
                        ->required()
                        ->options(ContentBrief::AUDIENCES)
                        ->default('aday_ogrenci'),
                    Select::make('count')
                        ->label('Kaç öneri?')
                        ->required()
                        ->options([
                            5 => '5 (hızlı test)',
                            10 => '10 (standart)',
                            15 => '15 (geniş)',
                            20 => '20 (maksimum)',
                        ])
                        ->default(10),
                ]),
            Section::make('Gelişmiş')
                ->collapsed()
                ->collapsible()
                ->components([
                    Textarea::make('extraInstructions')
                        ->label('⚙️ Ek Talimat (AI\'a yön ver)')
                        ->rows(3)
                        ->placeholder('Örn: "Sayısal data + somut ücret rakamı dahil et", "Gen-Z dili kullan", "Sadece master/PhD odaklı", "Veli için kaygı azaltıcı ton"')
                        ->helperText('Bu talimat AI\'ın tüm önerilerini şekillendirir. Format/ton/derinlik için kullan.'),
                    Toggle::make('liveSearch')
                        ->label('🌐 Live Google Search (grounding)')
                        ->helperText('Aktif edilirse AI gerçek-zamanlı Google sonuçlarına bakarak güncel veri/regülasyon kullanır. Daha yavaş ama güncel.')
                        ->default(false),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate')
                ->label('🪄 AI ile Öner')
                ->color('success')
                ->size('lg')
                ->action(function () {
                    $svc = app(BriefSuggestionService::class);
                    if (!$svc->isConfigured()) {
                        Notification::make()->title('❌ Gemini API key yok')->danger()->send();
                        return;
                    }
                    $r = $svc->suggest(
                        $this->topic,
                        $this->audience,
                        (int) $this->count,
                        $this->extraInstructions,
                        (bool) $this->liveSearch,
                    );
                    if ($r['success']) {
                        $this->suggestions = $r['suggestions'];
                        $this->tokens = $r['tokens'];
                        $this->modelUsed = $r['model_used'] ?? null;
                        $this->errorMessage = null;
                        Notification::make()
                            ->title('✅ ' . count($r['suggestions']) . ' öneri üretildi')
                            ->body('Model: ' . ($r['model_used'] ?? '?') . ' · ' . $r['tokens']['input'] . ' in / ' . $r['tokens']['output'] . ' out')
                            ->success()
                            ->send();
                    } else {
                        $this->suggestions = [];
                        $this->errorMessage = $r['error'];
                        Notification::make()
                            ->title('❌ Hata')
                            ->body($r['error'])
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                }),
            Action::make('refineWithFeedback')
                ->label('🔁 Önerileri Geliştir')
                ->color('warning')
                ->visible(fn () => !empty($this->suggestions))
                ->schema([
                    Textarea::make('feedback')
                        ->label('Geri bildirim')
                        ->required()
                        ->rows(3)
                        ->placeholder('Örn: "Hepsi çok genel, daha niş alt konular istiyorum", "Sayısal data ekle", "Aday öğrenci için fazla teknik kaldı"'),
                ])
                ->action(function (array $data) {
                    $extra = trim(($this->extraInstructions ?? '') . "\n\nÖNCEKİ ÖNERİLERİME GERİ BİLDİRİM: " . $data['feedback']);
                    $this->extraInstructions = $extra;

                    $svc = app(BriefSuggestionService::class);
                    $r = $svc->suggest($this->topic, $this->audience, (int) $this->count, $extra, (bool) $this->liveSearch);
                    if ($r['success']) {
                        $this->suggestions = $r['suggestions'];
                        $this->tokens = $r['tokens'];
                        Notification::make()->title('🔁 Yeniden üretildi')->success()->send();
                    } else {
                        Notification::make()->title('❌ Hata')->body($r['error'])->danger()->send();
                    }
                }),
        ];
    }

    public function acceptSuggestion(int $index): void
    {
        $s = $this->suggestions[$index] ?? null;
        if (!$s) return;

        $slug = $s['slug'] ?? Str::slug($s['title']);
        $original = $slug;
        $i = 1;
        while (ContentBrief::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }

        $notes = '';
        if (!empty($s['content_format'])) {
            $notes .= "Format: " . $s['content_format'] . "\n";
        }
        if (!empty($s['unique_angle'])) {
            $notes .= "Unique angle: " . $s['unique_angle'] . "\n";
        }
        if (!empty($s['search_intent'])) {
            $notes .= "Search intent: " . $s['search_intent'];
        }

        $brief = ContentBrief::create([
            'title' => $s['title'],
            'slug' => $slug,
            'audience' => $this->audience,
            'topic' => $this->topic,
            'primary_keyword' => $s['primary_keyword'] ?? null,
            'secondary_keywords' => $s['secondary_keywords'] ?? [],
            'pain_point' => $s['pain_point'] ?? null,
            'source_questions' => $s['source_questions'] ?? [],
            'target_word_count' => (int) ($s['target_word_count'] ?? 1500),
            'brand_tone' => 'instructive',
            'status' => 'draft',
            'notes' => $notes ?: null,
        ]);

        unset($this->suggestions[$index]);
        $this->suggestions = array_values($this->suggestions);

        Notification::make()
            ->title('✅ Brief #' . $brief->id . ' oluşturuldu')
            ->body($brief->title)
            ->success()
            ->actions([
                \Filament\Notifications\Actions\Action::make('edit')
                    ->label('Brief\'i düzenle')
                    ->url(route('filament.admin.resources.content-briefs.edit', $brief->id)),
            ])
            ->send();
    }

    public function acceptAll(): void
    {
        $count = count($this->suggestions);
        foreach (array_keys($this->suggestions) as $i) {
            $this->acceptSuggestion(0);
        }
        Notification::make()
            ->title("✅ $count brief oluşturuldu")
            ->success()
            ->send();
    }

    public function dismissSuggestion(int $index): void
    {
        unset($this->suggestions[$index]);
        $this->suggestions = array_values($this->suggestions);
    }
}
