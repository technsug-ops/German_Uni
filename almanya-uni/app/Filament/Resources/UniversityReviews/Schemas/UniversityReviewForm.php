<?php

namespace App\Filament\Resources\UniversityReviews\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UniversityReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('İçerik')
                ->columns(2)
                ->components([
                    Select::make('university_id')
                        ->label('Üniversite')
                        ->relationship('university', 'name_de')
                        ->searchable()
                        ->required()
                        ->disabled(),

                    Select::make('rating')
                        ->label('Puan')
                        ->options([1=>'★', 2=>'★★', 3=>'★★★', 4=>'★★★★', 5=>'★★★★★'])
                        ->required(),

                    TextInput::make('title')
                        ->label('Başlık')
                        ->required()
                        ->maxLength(200)
                        ->columnSpanFull(),

                    Textarea::make('body')
                        ->label('İçerik')
                        ->required()
                        ->rows(8)
                        ->maxLength(2500)
                        ->columnSpanFull(),

                    Select::make('locale')
                        ->label('Dil')
                        ->options(['tr' => 'Türkçe', 'en' => 'English', 'de' => 'Deutsch'])
                        ->required(),
                ]),

            Section::make('Yazar Bilgisi')
                ->columns(3)
                ->components([
                    TextInput::make('author_name')->label('Ad'),
                    TextInput::make('author_email')->label('E-posta')->email()->copyable(),
                    TextInput::make('author_program')->label('Program'),
                    Select::make('author_status')
                        ->label('Durum')
                        ->options([
                            'current_student' => 'Mevcut öğrenci',
                            'alumni'          => 'Mezun',
                            'admitted'        => 'Kabul almış',
                            'applicant'       => 'Başvuruda',
                        ]),
                    TextInput::make('study_year')->label('Başlangıç yılı')->numeric(),
                ]),

            Section::make('Moderation')
                ->columns(2)
                ->components([
                    Select::make('status')
                        ->label('Durum')
                        ->options([
                            'pending'  => '⏳ Beklemede',
                            'approved' => '✅ Onaylandı',
                            'rejected' => '❌ Reddedildi',
                            'spam'     => '🚫 Spam',
                        ])
                        ->required()
                        ->native(false),

                    Toggle::make('is_verified')
                        ->label('E-posta doğrulandı')
                        ->helperText('Kullanıcı doğrulama linkini tıklamış mı'),

                    Textarea::make('moderation_note')
                        ->label('Moderasyon notu')
                        ->placeholder('Neden onaylandı/reddedildi (internal)')
                        ->rows(2)
                        ->columnSpanFull(),

                    DateTimePicker::make('verified_at')->label('Doğrulandı')->disabled(),
                    DateTimePicker::make('moderated_at')->label('Moderate edildi')->disabled(),
                ]),

            Section::make('UGC + Tracking')
                ->columns(3)
                ->collapsed()
                ->components([
                    TextInput::make('helpful_count')->label('Faydalı oy')->disabled(),
                    TextInput::make('unhelpful_count')->label('Faydasız oy')->disabled(),
                    TextInput::make('reported_count')->label('Rapor sayısı')->disabled(),
                    TextInput::make('ip_address')->label('IP')->disabled(),
                    TextInput::make('user_agent')->label('Tarayıcı')->disabled()->columnSpanFull(),
                ]),
        ]);
    }
}
