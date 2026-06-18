<?php

namespace App\Filament\Pages;

use App\Models\EmailTemplate;
use App\Models\HousingProvider;
use App\Services\Mail\Outbox;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class OutreachCompose extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaperAirplane;

    protected static ?string $navigationLabel = 'Mail Gönder';

    protected static ?string $title = 'Outreach / Mail Gönder';

    protected static ?int $navigationSort = 2;

    protected static string|\UnitEnum|null $navigationGroup = 'Mail';

    protected string $view = 'filament.pages.outreach-compose';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isFullAdmin() === true;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Select::make('mailbox')
                    ->label('Gönderen kutu')
                    ->options(Outbox::options())
                    ->default('partnerships')
                    ->required()
                    ->helperText('Hangi adresten gönderilsin? Yanıtlar o kutunun gelen kutusuna düşer.'),
                Select::make('provider_id')
                    ->label('Sağlayıcı (opsiyonel)')
                    ->options(
                        HousingProvider::whereNotNull('email')
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(fn ($p) => [$p->id => $p->name . ' — ' . $p->email])
                            ->all()
                    )
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (! $state) {
                            return;
                        }
                        $provider = HousingProvider::find($state);
                        if ($provider) {
                            $set('to_email', $provider->email);
                            $set('to_name', $provider->name);
                        }
                    }),
                TextInput::make('to_email')
                    ->label('Alıcı e-posta')
                    ->email()
                    ->required(),
                TextInput::make('to_name')
                    ->label('Alıcı adı'),
                Select::make('template_key')
                    ->label('Şablon')
                    ->options(
                        EmailTemplate::where('is_active', true)
                            ->orderBy('sort_order')
                            ->get()
                            ->mapWithKeys(fn ($t) => [$t->key => $t->name . ' (' . $t->locale . ')'])
                            ->all()
                    )
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if (! $state) {
                            return;
                        }
                        $template = EmailTemplate::where('key', $state)->first();
                        if (! $template) {
                            return;
                        }
                        $provider = ($pid = $get('provider_id')) ? HousingProvider::find($pid) : null;
                        $vars = [
                            'provider_name' => $provider?->name ?? '',
                            'city' => $provider?->cities[0] ?? '',
                            'sender_name' => '',
                        ];
                        $rendered = $template->rendered($vars);
                        $set('subject', $rendered['subject']);
                        $set('body', $rendered['body']);
                    }),
                TextInput::make('subject')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('body')
                    ->required()
                    ->rows(16)
                    ->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send')
                ->label('Gönder')
                ->color('success')
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->action('send'),
        ];
    }

    public function send(): void
    {
        $state = $this->form->getState();

        $msg = Outbox::send(
            $state['mailbox'] ?? 'partnerships',
            $state['to_email'],
            $state['to_name'] ?? null,
            $state['subject'],
            $state['body'],
            [
                'provider_id'  => $state['provider_id'] ?? null,
                'template_key' => $state['template_key'] ?? null,
            ],
        );

        if ($msg->status === 'sent') {
            Notification::make()->title('Mail gönderildi (' . $msg->from_email . ')')->success()->send();
        } else {
            Notification::make()->title('Mail gönderilemedi')
                ->body($msg->error ?: 'Bilinmeyen hata')->danger()->persistent()->send();
        }
    }
}
