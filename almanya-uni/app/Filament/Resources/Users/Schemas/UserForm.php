<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profil')
                    ->columns(2)
                    ->components([
                        TextInput::make('name')
                            ->label('Ad')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('E-posta')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('avatar_url')
                            ->label('Profil Fotoğrafı')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('avatars')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios(['1:1'])
                            ->maxSize(2048)
                            ->helperText('JPG / PNG · 2 MB max · 1:1 oran önerilir')
                            ->columnSpanFull(),

                        TextInput::make('role_label')
                            ->label('Rol / Pozisyon')
                            ->maxLength(60)
                            ->placeholder('Örn: Kurucu, Editör, Gönüllü Yazar'),

                        Textarea::make('bio')
                            ->label('Yazar Biyografisi')
                            ->rows(4)
                            ->maxLength(1000)
                            ->helperText('Blog yazılarının altında görünür. 150-300 karakter ideal.')
                            ->columnSpanFull(),

                        KeyValue::make('social_links')
                            ->label('Sosyal Linkler')
                            ->keyLabel('Tür')
                            ->valueLabel('Değer')
                            ->keyPlaceholder('email / twitter / linkedin / github')
                            ->valuePlaceholder('handle veya URL')
                            ->helperText('Örn: email → user@x.com, twitter → username (@ olmadan)')
                            ->columnSpanFull(),
                    ]),

                Section::make('Yetkiler')
                    ->columns(2)
                    ->components([
                        Toggle::make('is_admin')
                            ->label('Admin (tam yetki — sistem + kullanıcı + içerik)')
                            ->default(false),

                        Toggle::make('is_editor')
                            ->label('Editör/Moderatör (sınırlı panel)')
                            ->helperText('İçerik üretir + denetler. Kullanıcı/sistem/entegrasyona giremez.')
                            ->default(false),

                        Toggle::make('is_author')
                            ->label('Yazar (blog\'da görünür)')
                            ->default(false),

                        Toggle::make('is_contributor')
                            ->label('🌱 Topluluk Katkıcısı')
                            ->helperText('Onaylı katkıda otomatik verilir; elle de atanabilir.')
                            ->default(false),
                    ]),

                Section::make('Şifre')
                    ->columns(1)
                    ->collapsed()
                    ->components([
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                            ->helperText('Boş bırak: mevcut şifre korunur.'),
                    ]),
            ]);
    }
}
