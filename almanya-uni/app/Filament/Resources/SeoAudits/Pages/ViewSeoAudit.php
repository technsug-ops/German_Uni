<?php

namespace App\Filament\Resources\SeoAudits\Pages;

use App\Filament\Resources\SeoAudits\SeoAuditResource;
use App\Services\Seo\SeoAuditorService;
use App\Services\Seo\SeoActivationService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewSeoAudit extends ViewRecord
{
    protected static string $resource = SeoAuditResource::class;

    protected string $view = 'filament.seo.audit-view';

    protected function getHeaderActions(): array
    {
        $entityMap = [
            'city_detail'       => ['cities', 'şehir'],
            'university_detail' => ['universities', 'üniversite'],
            'program_detail'    => ['programs', 'program'],
            'field_detail'      => ['fields', 'alan'],
            'blog_detail'       => ['posts', 'blog yazısı'],
        ];
        $entityInfo = $entityMap[$this->record->template] ?? null;

        $actions = [
            Action::make('reaudit')
                ->label('🔄 Yeniden Audit')
                ->color('gray')
                ->action(function () {
                    $svc = app(SeoAuditorService::class);
                    $svc->audit($this->record->template, $this->record->sample_url, false);
                    Notification::make()->title('Audit yenilendi')->success()->send();
                    $this->refreshFormData(['record']);
                }),
            Action::make('aiSuggest')
                ->label('🪄 AI Öneri Üret')
                ->color('warning')
                ->action(function () {
                    $svc = app(SeoAuditorService::class);
                    $svc->audit($this->record->template, $this->record->sample_url, true);
                    Notification::make()->title('AI önerisi üretildi')->success()->send();
                    $this->refreshFormData(['record']);
                }),
        ];

        // Entity-based template ise: aktivasyon aksiyonu
        if ($entityInfo) {
            $actions[] = Action::make('activate')
                ->label('⚡ İçerik Üret & Aktive Et')
                ->color('success')
                ->icon('heroicon-o-bolt')
                ->modalHeading('İçerik üretip entity\'ye aktive et')
                ->modalDescription("Bu template bir {$entityInfo[1]} entity'sine bağlı. Audit'in eksik keyword listesi + topluluk içgörüleri (Forum + Telegram) + AI ile content_blocks üretilip seçtiğin {$entityInfo[1]} record'una uygulanır. Sayfada anında görünür.")
                ->form([
                    TextInput::make('entity_slug')
                        ->label("{$entityInfo[1]} slug'ı")
                        ->placeholder($entityInfo[0] === 'cities' ? 'berlin-q64' : ($entityInfo[0] === 'universities' ? 'tu-berlin' : 'slug'))
                        ->required()
                        ->helperText("Hangi {$entityInfo[1]} için içerik üretileceğini gir. Örn: berlin-q64, tu-berlin, muhendislik"),
                ])
                ->requiresConfirmation()
                ->action(function (array $data) use ($entityInfo) {
                    $svc = app(SeoActivationService::class);
                    $result = $svc->activate($this->record, $entityInfo[0], $data['entity_slug']);

                    if ($result['success']) {
                        Notification::make()
                            ->title('⚡ İçerik üretildi + aktive edildi')
                            ->body($result['summary'])
                            ->success()
                            ->duration(10000)
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Aktivasyon başarısız')
                            ->body($result['error'] ?? 'Bilinmeyen hata')
                            ->danger()
                            ->send();
                    }
                    $this->refreshFormData(['record']);
                });
        }

        return $actions;
    }

    /**
     * Her template için "olması gereken" referans rehberi.
     */
    public function getTemplateRequirements(): array
    {
        $tpl = $this->record->template;

        $base = [
            ['icon' => '🎯', 'title' => '1 adet H1', 'desc' => 'Sayfa başlığını H1 olarak ver. Birden fazla H1 = kafa karışıklığı.'],
            ['icon' => '📑', 'title' => '6+ H2 bölüm', 'desc' => 'Topic coverage için içeriği bölümle. Her H2 ayrı bir alt-konu.'],
            ['icon' => '📝', 'title' => '2.500+ karakter', 'desc' => 'Min eşik. Topical authority için 4.000+ önerilir.'],
            ['icon' => '🔗', 'title' => '5+ iç link', 'desc' => 'İlgili entity\'lere (şehir/üni/program) link ver. Crawl + topical relevance.'],
            ['icon' => '🖼️', 'title' => '3+ görsel', 'desc' => 'Kapak + içerik + diyagram. Alt text Türkçe + keyword içersin.'],
            ['icon' => '🏷️', 'title' => 'Meta title + description', 'desc' => 'Title 50-60 karakter, description 150-160. Hepsi keyword içermeli.'],
        ];

        $specific = match ($tpl) {
            'city_detail' => [
                ['icon' => '💰', 'title' => 'Yaşam maliyeti bölümü', 'desc' => 'Kira + market + ulaşım + öğrenci hizmetleri tablosu.'],
                ['icon' => '🏛️', 'title' => 'Studierendenwerk + yurt seçenekleri', 'desc' => 'STW + özel chain listesi (community insights kullan).'],
                ['icon' => '🎓', 'title' => 'Üniversite + program listesi', 'desc' => 'Şehirdeki üniler + öne çıkan programlar (DB\'den).'],
                ['icon' => '🇹🇷', 'title' => 'Türk topluluğu bilgisi', 'desc' => 'Forum + Telegram\'dan Türk öğrenci içgörüleri (gerçek soru/cevap).'],
                ['icon' => '🚌', 'title' => 'Ulaşım + Semester ticket', 'desc' => 'Deutschlandticket + şehir içi metro/tram bilgisi.'],
                ['icon' => '❓', 'title' => 'FAQ bölümü (8-12 soru)', 'desc' => 'Topluluk verisinden gerçek sorulan sorular.'],
            ],
            'university_detail' => [
                ['icon' => '📊', 'title' => 'Üniversite künyesi', 'desc' => 'Kuruluş yılı + öğrenci sayısı + tipi + Trägerschaft.'],
                ['icon' => '📚', 'title' => 'Program listesi (Bachelor/Master)', 'desc' => 'Her program için: dil + NC + ücret + son başvuru.'],
                ['icon' => '🌐', 'title' => 'Uni-Assist gerekli mi?', 'desc' => 'VPD + APS + başvuru süreci açık yazılmalı.'],
                ['icon' => '💶', 'title' => 'Harç + Semesterbeitrag', 'desc' => 'Açık rakamla. Özel uniler için tuition fee tablosu.'],
                ['icon' => '🏠', 'title' => 'Şehir + yurt bilgisi', 'desc' => 'Üninin bulunduğu şehir + Studierendenwerk linki.'],
                ['icon' => '❓', 'title' => 'FAQ + alumni deneyimi', 'desc' => 'Forum başlıklarından gerçek sorular.'],
            ],
            'program_detail' => [
                ['icon' => '🎓', 'title' => 'Program künyesi', 'desc' => 'Derece + süre + dil + ücret + start semester.'],
                ['icon' => '📋', 'title' => 'Müfredat / ders listesi', 'desc' => 'Modül başlıkları + opsiyonel ders açıklamaları.'],
                ['icon' => '✔️', 'title' => 'Başvuru gereksinimleri', 'desc' => 'NC + dil seviyesi + APS + Vorprüfungsdokumentation.'],
                ['icon' => '📅', 'title' => 'Son başvuru tarihleri', 'desc' => 'Summer + Winter semester ayrı ayrı.'],
                ['icon' => '💼', 'title' => 'Kariyer çıktısı + iş alanı', 'desc' => 'Mezun sonrası iş alanı + maaş range\'i.'],
                ['icon' => '🔗', 'title' => 'Üniversite + şehir cross-link', 'desc' => 'Üst entity\'lere bağlantı.'],
            ],
            'field_detail' => [
                ['icon' => '🎯', 'title' => 'Alan tanımı + kapsam', 'desc' => 'Bu alan Almanya\'da neyi kapsar, hangi alt-disiplinler var?'],
                ['icon' => '🏫', 'title' => 'Top üniler bu alanda', 'desc' => 'CHE / DAAD ranking + community rep.'],
                ['icon' => '📚', 'title' => 'Program sayısı + dil dağılımı', 'desc' => 'DB\'den real-time istatistik.'],
                ['icon' => '💼', 'title' => 'Mezuniyet sonrası kariyer', 'desc' => 'Berufsbild + maaş + endüstri.'],
                ['icon' => '🌆', 'title' => 'En güçlü şehirler', 'desc' => 'Bu alan için endüstri merkezi şehirler.'],
                ['icon' => '❓', 'title' => 'FAQ — alan-spesifik', 'desc' => '"X alanı için Almanya iyi mi?" tarzı sorular.'],
            ],
            'blog_detail' => [
                ['icon' => '📰', 'title' => 'TLDR / özet', 'desc' => 'İlk paragraf 2-3 cümle ile özet.'],
                ['icon' => '📑', 'title' => 'TOC + 6+ H2', 'desc' => 'Sticky TOC ile uzun yazıya navigasyon.'],
                ['icon' => '👤', 'title' => 'Yazar + E-A-T', 'desc' => 'Author bio + expertise + güncellik tarihi.'],
                ['icon' => '🔗', 'title' => 'İlgili yazılar bölümü', 'desc' => 'Related posts + content cluster siloing.'],
                ['icon' => '💬', 'title' => 'Feedback + paylaşım', 'desc' => 'Helpful/unhelpful + 4-5 social share.'],
                ['icon' => '🏷️', 'title' => 'Schema.org BlogPosting', 'desc' => 'JSON-LD ile author/date/image işaretle.'],
            ],
            default => [
                ['icon' => '🎯', 'title' => 'Sayfanın amacı net', 'desc' => 'İlk paragraf: bu sayfa kim için, ne için?'],
                ['icon' => '🔗', 'title' => 'İlgili sayfalara CTA', 'desc' => '"Buradan devam et" tarzı 2-3 CTA.'],
                ['icon' => '❓', 'title' => 'En az 1 FAQ bloğu', 'desc' => 'Community\'den gelen gerçek sorular.'],
            ],
        };

        return array_merge($base, $specific);
    }
}
