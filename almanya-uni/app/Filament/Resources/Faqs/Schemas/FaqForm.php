<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('faq_topic_id')
                    ->required()
                    ->numeric(),
                TextInput::make('question')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('answer_md')
                    ->columnSpanFull(),
                Textarea::make('answer_html')
                    ->columnSpanFull(),
                TextInput::make('intent'),
                TextInput::make('answer_minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('has_answer')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
                TextInput::make('view_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_published')
                    ->required(),
            ]);
    }
}
