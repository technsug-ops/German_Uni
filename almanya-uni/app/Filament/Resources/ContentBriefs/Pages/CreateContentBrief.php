<?php

namespace App\Filament\Resources\ContentBriefs\Pages;

use App\Filament\Resources\ContentBriefs\ContentBriefResource;
use App\Models\ContentBrief;
use App\Services\Content\BriefSuggestionService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateContentBrief extends CreateRecord
{
    protected static string $resource = ContentBriefResource::class;

    /**
     * Save'den HEMEN önce: title'a göre AI ile tüm alanları otomatik doldur.
     * Kullanıcı sadece "Başlık" yazıyor — geri kalanı topluluk verisinden geliyor.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $title = trim($data['title'] ?? '');
        if (!$title) return $data;

        $svc = app(BriefSuggestionService::class);
        if (!$svc->isConfigured()) {
            Notification::make()
                ->title('⚠ Gemini API key yok — AI doldurma atlandı')
                ->body('Brief yine de oluşturuldu ama alanlar boş. Edit\'te manuel doldur.')
                ->warning()->send();
            return $data;
        }

        $topic = $data['topic'] ?? null;
        $audience = $data['audience'] ?? 'aday_ogrenci';

        try {
            $r = $svc->suggestFromTitle($title, $topic, $audience);
        } catch (\Throwable $e) {
            Notification::make()
                ->title('⚠ AI hata verdi — brief boş kaydedildi')
                ->body($e->getMessage())
                ->warning()->send();
            return $data;
        }

        if (!$r['success']) {
            Notification::make()
                ->title('⚠ AI başarısız')
                ->body($r['error'] . ' — Brief boş kaydedildi, edit\'te elle doldurabilirsin.')
                ->warning()->send();
            return $data;
        }

        $b = $r['brief'];

        // AI bazen pipe-separated dönüyor ("instructive|casual"); ilkini al + enum'da olduğunu doğrula
        $pickEnum = function (?string $value, array $allowed, string $default): string {
            if (!$value) return $default;
            foreach (preg_split('/[|,;\/]/u', $value) as $candidate) {
                $c = trim($candidate);
                if (isset($allowed[$c]) || in_array($c, $allowed, true)) return $c;
            }
            return $default;
        };

        $data['topic'] = $data['topic'] ?: ($b['topic_suggestion'] ?? null);
        $data['audience'] = $pickEnum($data['audience'] ?: ($b['audience_suggestion'] ?? null), ContentBrief::AUDIENCES, 'aday_ogrenci');
        $data['primary_keyword'] = $b['primary_keyword'] ?? null;
        $data['secondary_keywords'] = is_array($b['secondary_keywords'] ?? null) ? $b['secondary_keywords'] : [];
        $data['pain_point'] = $b['pain_point'] ?? null;
        $data['source_questions'] = is_array($b['source_questions'] ?? null) ? $b['source_questions'] : [];
        $data['target_word_count'] = max(300, min(5000, (int) ($b['target_word_count'] ?? 1500)));
        $data['brand_tone'] = $pickEnum($b['brand_tone'] ?? null, ContentBrief::TONES, 'instructive');
        $data['status'] = 'draft';
        $data['notes'] = trim(
            ($b['notes'] ?? '') .
            (!empty($b['content_format']) ? "\nContent format: " . $b['content_format'] : '')
        ) ?: null;

        $this->aiTokensUsed = $r['tokens'] ?? null;
        $this->aiContentFormat = $b['content_format'] ?? null;
        $this->aiSourcesUsed = $r['sources_used'] ?? null;

        return $data;
    }

    public ?array $aiTokensUsed = null;
    public ?string $aiContentFormat = null;
    public ?array $aiSourcesUsed = null;

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        $body = "🪄 AI otomatik doldurdu · Format: " . ($this->aiContentFormat ?? '?');
        if ($this->aiTokensUsed) {
            $body .= " · {$this->aiTokensUsed['input']} in / {$this->aiTokensUsed['output']} out tokens";
        }
        if ($this->aiSourcesUsed) {
            $s = $this->aiSourcesUsed;
            $get = fn ($k) => $s[$k] ?? 0;
            $body .= "\n\n📚 Taranan kaynaklar:";
            $body .= "\n• Telegram: {$get('telegram_pool_size')} havuz → {$get('telegram_in_prompt')} alakalı soru";
            $body .= "\n• Forum top: {$get('forum_top_topics')} konu + {$get('forum_anchor_msg_count')} anchor";
            $body .= "\n• 🔥 Isı haritası: {$get('forum_heatmap_pairs')} çift";
            $body .= "\n• Başlık ngram: {$get('forum_title_trigrams')} 3gram + {$get('forum_title_bigrams')} 2gram";
            $body .= "\n• Gövde 3gram: {$get('forum_all_trigrams')}";
            $body .= "\n• Trending: {$get('forum_trending_bigrams')} bigram + {$get('forum_trending_unigrams')} unigram";
            $body .= "\n• AlmanyaUni: {$get('almanyauni_faqs_context')} FAQ + {$get('almanyauni_posts_context')} blog";
            if (!empty($s['title_keywords'])) {
                $body .= "\n• Title kw: " . implode(', ', $s['title_keywords']);
            }
        }
        return Notification::make()
            ->title('✅ Brief oluşturuldu')
            ->body($body)
            ->success()
            ->persistent();
    }
}
