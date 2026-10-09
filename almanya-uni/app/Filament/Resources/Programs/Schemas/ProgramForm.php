<?php

namespace App\Filament\Resources\Programs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use App\Models\ProgramVerification;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Program Bilgileri')
                    ->columns(2)
                    ->components([
                        TextInput::make('name_de')->label('Program adı')->required()->columnSpan(2),
                        TextInput::make('name_en')->label('İngilizce adı'),
                        TextInput::make('name_tr')->label('Türkçe adı'),
                        TextInput::make('slug')->label('Slug')->required()->columnSpan(2),
                        Select::make('university_id')
                            ->label('Üniversite')
                            ->relationship('university', 'name_de')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(2),
                        Select::make('field_of_study_id')
                            ->label('Alan')
                            ->relationship('field', 'name_tr')
                            ->searchable()
                            ->preload(),
                        Select::make('degree')
                            ->label('Derece')
                            ->options([
                                'bachelor' => 'Bachelor',
                                'master'   => 'Master',
                                'phd'      => 'PhD',
                                'staatsexamen' => 'Staatsexamen',
                                'diplom'   => 'Diplom',
                                'magister' => 'Magister',
                                'other'    => 'Diğer',
                            ])
                            ->required()
                            ->native(false),
                        TextInput::make('degree_specification')->label('Derece detayı (örn. M.Sc.)'),
                        Select::make('language')
                            ->label('Dil')
                            ->options(['de' => 'Almanca', 'en' => 'İngilizce', 'both' => 'İki dilli'])
                            ->native(false),
                        TextInput::make('duration_semesters')->label('Süre (yarıyıl)')->numeric()->minValue(1)->maxValue(20),
                        Select::make('study_form')
                            ->label('Çalışma şekli')
                            ->options([
                                'full_time' => 'Tam zamanlı',
                                'part_time' => 'Yarı zamanlı',
                                'dual'      => 'Dual',
                                'distance'  => 'Uzaktan',
                                'online'    => 'Online',
                            ])
                            ->native(false),
                        TextInput::make('location')->label('Şehir / Yer'),
                        Toggle::make('is_active')->label('Aktif')->default(true),
                    ]),

                Section::make('Açıklamalar')
                    ->collapsible()
                    ->columns(1)
                    ->components([
                        Textarea::make('description_tr')->label('Türkçe açıklama')->rows(6),
                        Textarea::make('description_en')->label('İngilizce açıklama')->rows(6),
                    ]),

                Section::make('Başvuru Şartları (Türkçe)')
                    ->collapsible()
                    ->columns(1)
                    ->components([
                        Textarea::make('qualification_requirements_tr')->label('Başvuru şartları')->rows(4),
                        Textarea::make('language_requirements_tr')->label('Dil şartları')->rows(3),
                        Textarea::make('required_documents_tr')->label('Gerekli belgeler')->rows(3),
                    ]),

                Section::make('Mali & Başvuru')
                    ->columns(3)
                    ->components([
                        TextInput::make('tuition_fee_eur')->label('Harç (€/sem)')->numeric()->minValue(0),
                        TextInput::make('application_fee_eur')->label('Başvuru ücreti (€)')->numeric()->minValue(0),
                        TextInput::make('cost_per_semester_eur')->label('Semester Beitrag (€)')->numeric()->minValue(0),
                        DatePicker::make('application_deadline_winter')->label('Kış dönemi deadline'),
                        DatePicker::make('application_deadline_summer')->label('Yaz dönemi deadline'),
                        TextInput::make('admission_mode')->label('Kabul türü'),
                        TextInput::make('nc_value')->label('NC değeri')->numeric()->step(0.01),
                        TextInput::make('admission_summary')->label('Kabul özeti')->columnSpan(2),
                    ]),

                Section::make('Konular & Alanlar (raw)')
                    ->collapsed()
                    ->columns(1)
                    ->components([
                        TagsInput::make('subjects')->label('Konular')->reorderable(),
                        TagsInput::make('study_fields_raw')->label('Alan grupları (kaynak)')->reorderable(),
                    ]),

                Section::make('Kaynak & Meta')
                    ->description('IMPORT kaynağı: programın sisteme nereden geldiği. Bu, resmî üniversite doğrulaması DEĞİLDİR.')
                    ->collapsed()
                    ->columns(2)
                    ->components([
                        TextInput::make('partner_id')->label('Partner ID')->disabled(),
                        TextInput::make('partner_university_name')->label('Partner uni adı')->disabled(),
                        TextInput::make('source')->label('Import kaynağı'),
                        TextInput::make('source_url')->label('Import kaynak URL')->url(),
                        DateTimePicker::make('last_synced_at')->label('Veri çekildi (son senkron)'),
                    ]),

                Section::make('Kaynak ve Doğrulama')
                    ->description('Resmî program sayfası ve program düzeyinde başvuru bilgisi. Boş = BİLİNMİYOR (hayır değil). Alan bazında doğrulama kayıtları sayfanın altındaki "Doğrulamalar" tablosunda. Yalnız tam yetkili admin düzenleyebilir.')
                    ->collapsible()
                    ->columns(2)
                    ->components([
                        TextEntry::make('verification_summary')
                            ->label('Doğrulama kapsamı')
                            ->state(function ($record) {
                                if (! $record) {
                                    return 'Henüz doğrulama yok';
                                }
                                $s = $record->verificationSummary();
                                $txt = "{$s['total']} kritik alanın {$s['verified']}'i doğrulandı";
                                if ($s['needs_review']) {
                                    $txt .= " · {$s['needs_review']} alanda inceleme gerekli";
                                }
                                if ($s['conflict']) {
                                    $txt .= " · {$s['conflict']} alanda çelişki";
                                }

                                return $txt;
                            })
                            ->columnSpan(2),
                        TextInput::make('official_program_url')
                            ->label('Resmî program sayfası (üniversitenin kendi sitesi)')
                            ->helperText('Üniversite ana sayfası değil; programın kendi sayfası. URL eklemek hiçbir alanı doğrulanmış yapmaz — sitede bağlantı ancak "Program kimliği" doğrulanınca görünür.')
                            ->url()
                            ->maxLength(500)
                            ->columnSpan(2)
                            ->disabled(fn () => ! auth()->user()?->isFullAdmin())
                            ->dehydrated(fn () => (bool) auth()->user()?->isFullAdmin()),
                        Select::make('application_method')
                            ->label('Başvuru yöntemi')
                            ->options(ProgramVerification::APPLICATION_METHODS)
                            ->placeholder('Bilinmiyor')
                            ->native(false)
                            ->disabled(fn () => ! auth()->user()?->isFullAdmin())
                            ->dehydrated(fn () => (bool) auth()->user()?->isFullAdmin()),
                        TextInput::make('application_url')
                            ->label('Başvuru bağlantısı')
                            ->url()
                            ->maxLength(500)
                            ->disabled(fn () => ! auth()->user()?->isFullAdmin())
                            ->dehydrated(fn () => (bool) auth()->user()?->isFullAdmin()),
                        Select::make('uni_assist_required')
                            ->label('uni-assist kullanılıyor mu?')
                            ->options(['yes' => 'Evet', 'no' => 'Hayır'])
                            ->placeholder('Bilinmiyor')
                            ->helperText('Üniversitenin uni-assist üyeliği bu programın uni-assist kullandığı anlamına gelmez. Aday grubuna göre değişiyorsa ayrıntıyı doğrulama kaydına yazın.')
                            ->native(false)
                            ->disabled(fn () => ! auth()->user()?->isFullAdmin())
                            ->dehydrated(fn () => (bool) auth()->user()?->isFullAdmin()),
                        Select::make('vpd_required')
                            ->label('VPD gerekli mi?')
                            ->options(['yes' => 'Evet', 'no' => 'Hayır'])
                            ->placeholder('Bilinmiyor')
                            ->native(false)
                            ->disabled(fn () => ! auth()->user()?->isFullAdmin())
                            ->dehydrated(fn () => (bool) auth()->user()?->isFullAdmin()),
                    ]),
            ]);
    }
}
