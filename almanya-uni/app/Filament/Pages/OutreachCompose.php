<?php

namespace App\Filament\Pages;

use App\Mail\OutreachMail;
use App\Models\EmailTemplate;
use App\Models\HousingProvider;
use App\Models\OutreachContact;
use App\Services\Mail\MailBody;
use App\Services\Mail\Outbox;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;

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

    /** Kontak defteri migrate edildi mi? (deploy > migrate sırasında panel 500 olmasın) */
    protected static function contactsReady(): bool
    {
        return \Illuminate\Support\Facades\Schema::hasTable('outreach_contacts');
    }

    public function mount(): void
    {
        // Firma Kontakları tablosundan "Mail Gönder" ile gelindiyse alıcıyı doldur.
        $contact = ($id = request()->query('contact')) && self::contactsReady()
            ? OutreachContact::find($id)
            : null;

        $this->form->fill(array_merge([
            'layout'         => 'personal',
            'show_signature' => true,
        ], $contact ? [
            'contact_id' => $contact->id,
            'to_email'   => $contact->email,
            'to_name'    => $contact->contact_name ?: $contact->organization,
        ] : []));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                Section::make('Alıcı')
                    ->columns(2)
                    ->components([
                        Select::make('mailbox')
                            ->label('Gönderen kutu')
                            ->options(Outbox::options())
                            ->default('partnerships')
                            ->required()
                            ->helperText('Yanıtlar o kutunun gelen kutusuna düşer.'),
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
                                $contact = ($cid = $get('contact_id')) && self::contactsReady() ? OutreachContact::find($cid) : null;
                                $vars = [
                                    // Şablon "{{provider_name}}" yer tutucusunu firma kontağı da doldurabilir.
                                    'provider_name' => $provider?->name ?? $contact?->organization ?? '',
                                    'city' => $provider?->cities[0] ?? '',
                                    'sender_name' => auth()->user()?->name ?? '',
                                ];
                                $rendered = $template->rendered($vars);
                                $set('subject', $rendered['subject']);
                                // Düz metin şablonlar editöre HTML olarak girmeli;
                                // aksi hâlde TipTap satır sonlarını yutar.
                                $set('body', MailBody::toEditorHtml($rendered['body']));
                                $set('layout', $rendered['layout']);
                            }),
                        Select::make('contact_id')
                            ->label('Firma kontağı (opsiyonel)')
                            ->visible(fn () => self::contactsReady())
                            ->options(fn () => OutreachContact::whereNotNull('email')
                                ->orderBy('organization')
                                ->get()
                                ->mapWithKeys(fn ($c) => [$c->id => $c->organization . ' — ' . $c->email])
                                ->all())
                            ->searchable()
                            ->live()
                            ->helperText('Seçilirse mail, kontağın yazışma geçmişine düşer ve durumu "mail atıldı" olur.')
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    return;
                                }
                                $contact = OutreachContact::find($state);
                                if ($contact) {
                                    $set('to_email', $contact->email);
                                    $set('to_name', $contact->contact_name ?: $contact->organization);
                                }
                            }),
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
                    ]),

                Section::make('Düzen')
                    ->description('Sade düzen gerçek bir kişiden gelmiş gibi görünür; soğuk kurumsal temasta yanıt oranı belirgin biçimde daha yüksektir. Şablonlu düzen duyuru, medya kiti ve tanıdık kontaklar için.')
                    ->columns(2)
                    ->components([
                        Select::make('layout')
                            ->label('Mail düzeni')
                            ->options([
                                'personal' => 'Sade — kişiden gelmiş gibi (önerilen)',
                                'rich'     => 'Şablonlu — logolu başlık, görsel, buton',
                            ])
                            ->default('personal')
                            ->required()
                            ->live()
                            ->native(false),
                        Toggle::make('show_signature')
                            ->label('İmza bloğu ekle')
                            ->default(true)
                            ->helperText('Ad, ünvan, adres ve site linki. config/services.php > mail_signature'),
                        TextInput::make('preheader')
                            ->label('Ön izleme yazısı')
                            ->maxLength(140)
                            ->columnSpanFull()
                            ->helperText('Gelen kutusunda konu başlığının yanında görünen gri satır. Boş bırakılırsa istemci mailin ilk cümlesini gösterir.'),
                        FileUpload::make('hero')
                            ->label('Üst görsel')
                            ->image()
                            ->disk('public')
                            ->directory('mail/hero')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->visible(fn (callable $get) => $get('layout') === 'rich')
                            ->columnSpanFull()
                            ->helperText('600 px genişlik önerilir · 2 MB max. Birçok istemci görselleri varsayılan olarak engeller — maili görselsiz de anlaşılır tut.'),
                        TextInput::make('cta_label')
                            ->label('Buton yazısı')
                            ->maxLength(40)
                            ->visible(fn (callable $get) => $get('layout') === 'rich'),
                        TextInput::make('cta_url')
                            ->label('Buton linki')
                            ->url()
                            ->visible(fn (callable $get) => $get('layout') === 'rich')
                            ->helperText('Tam adres: https://applytogerman.com/...'),
                    ]),

                Section::make('Mesaj')
                    ->components([
                        TextInput::make('subject')
                            ->label('Konu')
                            ->required()
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->label('Gövde')
                            ->required()
                            ->columnSpanFull()
                            // Araç çubuğundaki ataç düğmesi görsel yükler; dosya
                            // public diskine düşer ve maile mutlak adresle girer.
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('mail/inline')
                            ->fileAttachmentsVisibility('public')
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'link'],
                                ['h2', 'h3'],
                                ['bulletList', 'orderedList', 'blockquote'],
                                ['attachFiles'],
                                ['undo', 'redo'],
                            ])
                            ->helperText('Eski düz metin şablonları da çalışır; paragraf ve maddeler otomatik biçimlenir.'),
                        FileUpload::make('attachments')
                            ->label('Ek dosyalar')
                            ->multiple()
                            ->disk('public')
                            ->directory('mail/attachments')
                            ->visibility('public')
                            ->maxSize(8192)
                            ->columnSpanFull()
                            ->helperText('PDF, medya kiti vb. · dosya başına 8 MB max. İlk temasta ek göndermek spam riskini artırır.'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Önizle')
                ->icon(Heroicon::OutlinedEye)
                ->color('gray')
                ->modalHeading('Mail önizleme')
                ->modalDescription('Alıcının göreceği hâli. Gerçek mail şablonundan üretilir.')
                ->modalWidth(Width::FiveExtraLarge)
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Kapat')
                ->modalContent(fn () => view('filament.pages.partials.mail-preview', [
                    'html'    => $this->previewHtml(),
                    'subject' => $this->data['subject'] ?? '',
                ])),

            Action::make('sendTest')
                ->label('Kendime test gönder')
                ->icon(Heroicon::OutlinedBeaker)
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Test maili')
                ->modalDescription(fn () => (auth()->user()?->email ?: 'hesabındaki adrese') . ' adresine bir kopya gönderilecek. Kontak geçmişine işlenmez.')
                ->action('sendTest'),

            Action::make('send')
                ->label('Gönder')
                ->color('success')
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->action('send'),
        ];
    }

    /** Formdaki hâliyle gerçek mail HTML'i — önizleme penceresi bunu gösterir. */
    protected function previewHtml(): string
    {
        $state = $this->data;
        $box   = Outbox::get($state['mailbox'] ?? 'partnerships') ?? [];

        $mail = new OutreachMail(
            subjectLine: $state['subject'] ?? '(konu yok)',
            bodyText: $state['body'] ?? '',
            fromEmail: $box['email'] ?? 'partnerships@applytogerman.com',
            fromName: $box['name'] ?? 'ApplyToGerman',
            layout: $state['layout'] ?? 'personal',
            preheader: $state['preheader'] ?? null,
            heroUrl: $this->publicUrl($state['hero'] ?? null),
            ctaLabel: $state['cta_label'] ?? null,
            ctaUrl: $state['cta_url'] ?? null,
            signature: Outbox::signature(auth()->user()?->name),
            showSignature: (bool) ($state['show_signature'] ?? true),
        );

        return $mail->render();
    }

    /**
     * FileUpload durumu (dizi ya da metin) → tarayıcıdan erişilebilir mutlak adres.
     * Yeni yüklenen dosya henüz geçici ise onun geçici adresi döner.
     */
    protected function publicUrl(mixed $state): ?string
    {
        $path = is_array($state) ? (reset($state) ?: null) : $state;

        if (blank($path)) {
            return null;
        }

        if ($path instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            return $path->temporaryUrl();
        }

        return is_string($path) && str_starts_with($path, 'http')
            ? $path
            : Storage::disk('public')->url((string) $path);
    }

    /** Ek dosyaların diskteki mutlak yolları. */
    protected function attachmentPaths(mixed $state): array
    {
        return collect((array) $state)
            ->filter(fn ($p) => is_string($p) && $p !== '')
            ->map(fn ($p) => Storage::disk('public')->path($p))
            ->filter(fn ($p) => is_file($p))
            ->values()
            ->all();
    }

    /** Gönderim seçenekleri — hem gerçek gönderimde hem testte aynı. */
    protected function optionsFrom(array $state): array
    {
        return [
            'layout'         => $state['layout'] ?? 'personal',
            'preheader'      => $state['preheader'] ?? null,
            'hero_url'       => $this->publicUrl($state['hero'] ?? null),
            'cta_label'      => $state['cta_label'] ?? null,
            'cta_url'        => $state['cta_url'] ?? null,
            'signature_name' => auth()->user()?->name,
            'show_signature' => (bool) ($state['show_signature'] ?? true),
            'attachments'    => $this->attachmentPaths($state['attachments'] ?? []),
        ];
    }

    public function sendTest(): void
    {
        $state = $this->form->getState();
        $to    = auth()->user()?->email;

        if (blank($to)) {
            Notification::make()->title('Hesabında kayıtlı e-posta adresi yok')->danger()->send();

            return;
        }

        // Kontak/sağlayıcı bağı bilerek verilmiyor: test maili yazışma
        // geçmişini kirletmesin, kontağı "mail atıldı" yapmasın.
        $msg = Outbox::send(
            $state['mailbox'] ?? 'partnerships',
            $to,
            auth()->user()?->name,
            '[TEST] ' . $state['subject'],
            $state['body'],
            [],
            $this->optionsFrom($state),
        );

        if ($msg->status === 'sent') {
            Notification::make()->title('Test maili gönderildi → ' . $to)->success()->send();
        } else {
            Notification::make()->title('Test maili gönderilemedi')
                ->body($msg->error ?: 'Bilinmeyen hata')->danger()->persistent()->send();
        }
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
                'contact_id'   => $state['contact_id'] ?? null,
                'template_key' => $state['template_key'] ?? null,
            ],
            $this->optionsFrom($state),
        );

        if ($msg->status === 'sent') {
            Notification::make()->title('Mail gönderildi (' . $msg->from_email . ')')->success()->send();
        } else {
            Notification::make()->title('Mail gönderilemedi')
                ->body($msg->error ?: 'Bilinmeyen hata')->danger()->persistent()->send();
        }
    }
}
