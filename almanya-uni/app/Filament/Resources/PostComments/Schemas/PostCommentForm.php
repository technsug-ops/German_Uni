<?php

namespace App\Filament\Resources\PostComments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostCommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Yorum')->schema([
                Select::make('post_id')
                    ->label('Yazı')
                    ->relationship('post', 'title')
                    ->searchable()
                    ->required(),
                Select::make('user_id')
                    ->label('Kullanıcı (üye)')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->placeholder('Anonim'),
                TextInput::make('author_name')
                    ->label('Anonim isim')
                    ->visible(fn ($get) => ! $get('user_id'))
                    ->maxLength(80),
                TextInput::make('author_email')
                    ->label('Anonim e-posta')
                    ->visible(fn ($get) => ! $get('user_id'))
                    ->email()
                    ->maxLength(150),
                Textarea::make('body')
                    ->label('Yorum metni')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make('Moderation')->schema([
                Select::make('status')
                    ->label('Durum')
                    ->options([
                        'pending'  => '⏳ Beklemede',
                        'approved' => '✅ Onaylı',
                        'spam'     => '🚫 Spam',
                        'rejected' => '❌ Reddedildi',
                    ])
                    ->required(),
                Toggle::make('is_pinned')
                    ->label('📌 Sabit (öne çıkar)')
                    ->inline(false),
            ])->columns(2),

            Section::make('Meta (read-only)')->schema([
                TextInput::make('ip_address')->label('IP')->disabled(),
                TextInput::make('user_agent')->label('User Agent')->disabled(),
                TextInput::make('helpful_count')->label('Yararlı oy sayısı')->numeric()->disabled(),
            ])->columns(3)->collapsed(),
        ]);
    }
}
