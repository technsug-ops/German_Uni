<?php

namespace App\Filament\Resources\Mentors\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class MentorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Kimlik')->schema([
                TextInput::make('name')
                    ->label('Ad Soyad')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                TextInput::make('avatar_url')->label('Avatar URL')->url(),
                TextInput::make('headline')->label('Tek satır tanıtım')
                    ->placeholder('Örnek: AI Engineer @ Google · TUM Mezunu')
                    ->maxLength(200)
                    ->columnSpanFull(),
                TextInput::make('current_role')->label('Şu anki rolü'),
                TextInput::make('current_company')->label('Şirket'),
                TextInput::make('city')->label('Şehir')->placeholder('Berlin'),
            ])->columns(2),

            Section::make('Geçmiş + Bio')->schema([
                Textarea::make('bio')->label('Hakkında (uzun)')->rows(6)->columnSpanFull(),
                TextInput::make('university')->label('Üniversite'),
                TextInput::make('field_of_study')->label('Alanı'),
                TextInput::make('graduation_year')->label('Mezuniyet yılı')->maxLength(8),
            ])->columns(3),

            Section::make('Sosyal + İletişim')->schema([
                TextInput::make('linkedin_url')->label('LinkedIn URL')->url(),
                TextInput::make('twitter_url')->label('Twitter URL')->url(),
                TextInput::make('github_url')->label('GitHub URL')->url(),
                TextInput::make('website_url')->label('Website')->url(),
                TextInput::make('calendly_url')->label('Calendly / Booking link')->url()
                    ->helperText('Eğer dolu ise "Randevu Al" butonu açılır'),
                TextInput::make('contact_email')->label('E-posta')->email(),
            ])->columns(2),

            Section::make('Mentorluk Detayı')->schema([
                TagsInput::make('topics')->label('Konular')
                    ->placeholder('Career, AI, Startup, CV Review, Uni Application, Vize, Sperrkonto')
                    ->columnSpanFull(),
                TagsInput::make('languages')->label('Diller (tr, de, en)')
                    ->placeholder('tr, de, en'),
                TextInput::make('availability')->label('Müsaitlik')
                    ->placeholder('Örnek: Haftada 2 saat / Pazar akşamları'),
                TextInput::make('rate_eur')->label('Ücret (€/seans)')->numeric()->default(0)
                    ->helperText('0 = ücretsiz'),
                TextInput::make('session_duration')->label('Seans süresi')
                    ->placeholder('30 dk / 1 saat'),
            ])->columns(2),

            Section::make('Yönetim')->schema([
                Toggle::make('is_featured')->label('Öne Çıkan')->helperText('Liste başında gösterilir'),
                Toggle::make('is_active')->label('Aktif')->default(true),
                TextInput::make('sort_order')->numeric()->default(0),
            ])->columns(3)->collapsed(),
        ]);
    }
}
