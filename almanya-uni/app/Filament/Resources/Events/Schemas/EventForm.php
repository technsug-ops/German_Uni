<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Models\Event;
use App\Models\EventCategory;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        // Tipleri kategoriye göre grupla
        $typeOptions = [];
        $categoryLabels = [
            'networking'         => '🤝 Networking & Kariyer',
            'skill'              => '🛠️ Beceri Geliştirme',
            'peer-learning'      => '🌍 Topluluk & Tanışma',
            'personal-growth'    => '🧠 Kişisel Gelişim',
            'adventure'          => '🏔️ Macera & Sosyal',
            'industry-immersion' => '🏭 Sektör Keşfi',
            'special-format'    => '🎤 Özel Format',
        ];

        foreach ($categoryLabels as $catSlug => $catLabel) {
            $group = [];
            foreach (Event::TYPES as $key => $meta) {
                if (($meta['category'] ?? null) === $catSlug) {
                    $group[$key] = $meta['emoji'] . ' ' . $meta['label'];
                }
            }
            if (! empty($group)) {
                $typeOptions[$catLabel] = $group;
            }
        }

        $categoryOptions = EventCategory::active()->orderBy('sort_order')->pluck('name_tr', 'id')->toArray();

        return $schema->components([
            Section::make('Temel Bilgi')->schema([
                Select::make('category_id')
                    ->label('Kategori')
                    ->options($categoryOptions)
                    ->helperText('Tipi seçince otomatik atanır, manuel override edebilirsin'),

                Select::make('type')
                    ->label('Etkinlik Tipi (30+ seçenek, kategoriye göre gruplandı)')
                    ->options($typeOptions)
                    ->default('webinar')
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Tip seçilince kategoriyi otomatik ata
                        $cat = Event::TYPES[$state]['category'] ?? null;
                        if ($cat) {
                            $catId = EventCategory::where('slug', $cat)->value('id');
                            if ($catId) $set('category_id', $catId);
                        }
                    }),

                TextInput::make('title_tr')
                    ->label('Başlık (TR)')
                    ->required()
                    ->maxLength(200)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state ?? '') . '-' . now()->format('ymdHi'))),

                TextInput::make('title_de')
                    ->label('Başlık (DE)')
                    ->maxLength(200),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(220)
                    ->unique(ignoreRecord: true)
                    ->helperText('Otomatik oluşur — değiştirebilirsin'),

                Textarea::make('description_md')
                    ->label('Açıklama (Markdown)')
                    ->rows(6)
                    ->helperText('Markdown destekli: **kalın**, *italic*, [link](url), - liste'),

                TextInput::make('host')
                    ->label('Sunucu / Konuşmacı')
                    ->maxLength(150),
            ])->columns(2),

            Section::make('Sponsor + Ödül + Hedef Kitle')->schema([
                TextInput::make('sponsor')
                    ->label('Sponsor (Google, SAP, Zalando, DAAD, vb.)')
                    ->maxLength(200),
                TextInput::make('sponsor_logo_url')
                    ->label('Sponsor logo URL')
                    ->url()
                    ->maxLength(500),
                Textarea::make('reward')
                    ->label('Ödül / Sertifika')
                    ->rows(2)
                    ->placeholder('Örnek: Sertifika + LinkedIn badge + En iyi 3 takım için €1.000 ödül')
                    ->columnSpanFull(),
                Select::make('target_audience')
                    ->label('Hedef Kitle')
                    ->options([
                        'all'      => 'Herkes',
                        'bachelor' => 'Bachelor öğrencileri',
                        'master'   => 'Master öğrencileri',
                        'phd'      => 'PhD / Araştırmacılar',
                        'alumni'   => 'Mezunlar',
                        'startup'  => 'Girişimciler',
                        'mentor'   => 'Mentor adayları',
                    ])
                    ->default('all'),
                Select::make('difficulty')
                    ->label('Seviye')
                    ->options([
                        'beginner'     => '🌱 Başlangıç',
                        'intermediate' => '🌿 Orta',
                        'advanced'     => '🌳 İleri',
                    ]),
                TextInput::make('duration_minutes')
                    ->label('Süre (dakika)')
                    ->numeric()
                    ->placeholder('60, 90, 180...'),
                TagsInput::make('tags')
                    ->label('Etiketler')
                    ->placeholder('AI, Python, Career, Startup, DAAD...')
                    ->columnSpanFull(),
            ])->columns(3)->collapsed(),

            Section::make('Tarih + Lokasyon')->schema([
                DateTimePicker::make('starts_at')->label('Başlangıç')->required()->seconds(false),
                DateTimePicker::make('ends_at')->label('Bitiş')->seconds(false),
                TextInput::make('timezone')->default('Europe/Berlin')->maxLength(32),

                Select::make('mode')
                    ->label('Tip')
                    ->options([
                        'online'  => '💻 Online',
                        'offline' => '📍 Yüz yüze',
                        'hybrid'  => '🔄 Hibrit',
                    ])
                    ->default('online')
                    ->required(),

                TextInput::make('online_url')->label('Online URL (Zoom/Meet/YouTube)')->url()->maxLength(500),
                TextInput::make('location_name')->label('Mekan Adı (offline)')->maxLength(200),
                TextInput::make('location_city')->label('Şehir')->maxLength(100),
            ])->columns(2),

            Section::make('Kayıt + Ücret')->schema([
                TextInput::make('registration_url')->label('Kayıt URL')->url()->maxLength(500),
                Toggle::make('registration_required')->label('Kayıt zorunlu')->default(true),
                TextInput::make('max_attendees')->label('Maks Katılımcı')->numeric()->minValue(1),
                TextInput::make('registered_count')->label('Kayıtlı Sayısı')->numeric()->default(0),
                TextInput::make('price_eur')->label('Ücret (€)')->numeric()->default(0)->step(0.01),
            ])->columns(3),

            Section::make('Görsel + Banner')->schema([
                TextInput::make('banner_url')->label('Banner URL')->url()->maxLength(500),
                ColorPicker::make('banner_color')->label('Banner Rengi')->default('#1E40AF'),
                Toggle::make('is_featured')
                    ->label('Üst banner\'da göster')
                    ->helperText('Aktif olunca header üstünde countdown ile sayfa açanlara görünür')
                    ->default(false),
                Toggle::make('is_active')->label('Aktif')->default(true),
            ])->columns(2),

            Section::make('SEO')->schema([
                TextInput::make('meta_title')->label('Meta Title')->maxLength(255),
                TextInput::make('meta_description')->label('Meta Description')->maxLength(500),
            ])->columns(1)->collapsed(),
        ]);
    }
}
