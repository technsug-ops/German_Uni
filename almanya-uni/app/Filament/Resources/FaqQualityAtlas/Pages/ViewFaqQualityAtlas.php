<?php

namespace App\Filament\Resources\FaqQualityAtlas\Pages;

use App\Filament\Resources\Faqs\FaqResource;
use App\Filament\Resources\FaqQualityAtlas\FaqQualityAtlasResource;
use App\Models\Faq;
use App\Models\FaqQualityAtlas;
use App\Services\FaqAtlas\LiveStatus;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

/** Küme detayı: TR / EN / DE yan yana (canlı veri) + denetim paneli (değişmez anlık görüntü). Salt okunur. */
class ViewFaqQualityAtlas extends ViewRecord
{
    protected static string $resource = FaqQualityAtlasResource::class;

    protected string $view = 'filament.faq-atlas.view';

    protected function resolveRecord(int|string $key): Model
    {
        return FaqQualityAtlas::query()->withLive()->with(['tr.topic', 'en.topic', 'de.topic'])->findOrFail($key);
    }

    public function getTitle(): string
    {
        return 'SSS kümesi — '.$this->getRecord()->tr_slug;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    /** @return array<string, array<string, mixed>> */
    public function locales(): array
    {
        /** @var FaqQualityAtlas $r */
        $r = $this->getRecord();
        $out = [];
        $baseline = LiveStatus::baseline($r->audited_at, $r->created_at);
        foreach (FaqQualityAtlas::LOCALES as $l) {
            /** @var Faq|null $faq */
            $faq = $r->{$l};
            $live = LiveStatus::classify($faq);
            $clean = trim((string) preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $faq?->answer_html), ENT_QUOTES, 'UTF-8')));
            $out[$l] = [
                'faq' => $faq,
                'question' => $faq?->question,
                'slug' => $faq?->slug ?? $r->{"{$l}_slug"},
                'path' => FaqQualityAtlas::faqPath($faq),
                'edit_url' => $faq ? FaqResource::getUrl('edit', ['record' => $faq]) : null,
                'atlas_status' => $r->{"{$l}_status"},
                'live_status' => $live,
                'differs' => LiveStatus::materiallyDiffers($r->{"{$l}_status"}, $live),
                'updated_after_audit' => $faq && $faq->updated_at && $baseline && $faq->updated_at->gt($baseline),
                'published' => $faq?->is_published,
                'chars' => LiveStatus::visibleLength($faq?->answer_html),
                'preview' => mb_substr($clean, 0, 600).(mb_strlen($clean) > 600 ? '…' : ''),
                'status_reason' => $r->stale_markers[$l.'_status_reason'] ?? null,
            ];
        }

        return $out;
    }

    /** Birleştirme hedeflerinin aynı denetimdeki atlas kayıtları (link için). */
    public function duplicateTargets(): array
    {
        /** @var FaqQualityAtlas $r */
        $r = $this->getRecord();
        $slugs = $r->duplicate_of ?? [];
        if (! $slugs) {
            return [];
        }

        return FaqQualityAtlas::query()->where('audit_label', $r->audit_label)->whereIn('tr_slug', $slugs)
            ->get(['id', 'tr_slug', 'tr_question_at_audit', 'tr_quality'])
            ->map(fn ($t) => ['slug' => $t->tr_slug, 'question' => $t->tr_question_at_audit, 'quality' => $t->tr_quality,
                'url' => FaqQualityAtlasResource::getUrl('view', ['record' => $t])])->all();
    }
}
