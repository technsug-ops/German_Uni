<?php

namespace App\Services\FlatReklam;

use App\Models\Event;
use App\Models\LegalPage;
use App\Models\Post;
use Illuminate\Support\Str;

/**
 * FlatReklam "Özel Site SEO API" sağlayıcı tarafı. Sitenin DÜZENLENEBİLİR SEO
 * meta'sına sahip kaynaklarını tek bir normalize şemada sunar:
 *   article → blog yazıları (Post, locale-row)
 *   event   → etkinlikler (Event)
 *   page    → yasal sayfalar (LegalPage, JSON locale)
 *
 * Katalog (üniversite/şehir/program) SEO meta'sı otomatik üretilir + düzenlenmez,
 * bu yüzden bu sözleşmeye dahil değildir (FlatReklam'da denetlenecek bir şey yok).
 *
 * Bileşik id: "{type}-{modelId}" (ör. article-42, event-5, page-privacy).
 */
class SeoResourceService
{
    private const TYPES = ['article', 'event', 'page'];

    public function siteInfo(): array
    {
        return [
            'ok' => true,
            'version' => '1.0.0',
            'siteName' => brand('name') ?? config('app.name'),
            'siteProfile' => 'hybrid',
            'supportedResourceTypes' => self::TYPES,
            'supportedLocales' => ['en', 'de', 'tr'],
        ];
    }

    /** @return array{items:array,page:int,perPage:int,total:int,hasMore:bool} */
    public function list(?string $type, int $page, int $perPage, ?string $lang, string $status): array
    {
        $types = $type ? array_intersect([$type], self::TYPES) : self::TYPES;

        $refs = [];
        foreach ($types as $t) {
            foreach ($this->idsFor($t, $lang, $status) as $id) {
                $refs[] = [$t, $id];
            }
        }

        $total = count($refs);
        $slice = array_slice($refs, ($page - 1) * $perPage, $perPage);
        $items = [];
        foreach ($slice as [$t, $id]) {
            $row = $this->hydrate($t, $id, $lang);
            if ($row) $items[] = $row;
        }

        return [
            'items' => $items,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'hasMore' => $page * $perPage < $total,
        ];
    }

    public function find(string $compositeId, ?string $lang): ?array
    {
        [$type, $id] = $this->parseId($compositeId);
        if (! $type) return null;
        return $this->hydrate($type, $id, $lang);
    }

    /** @return array{0:?string,1:?array} [resource|null, errors|null] */
    public function update(string $compositeId, array $data): array
    {
        [$type, $id] = $this->parseId($compositeId);
        if (! $type) return [null, ['code' => 'NOT_FOUND', 'error' => 'Geçersiz kaynak id']];

        $lang = $data['lang'] ?? null;

        return match ($type) {
            'article' => $this->updatePost($id, $data, $lang),
            'event'   => $this->updateEvent($id, $data),
            'page'    => $this->updateLegal($id, $data, $lang),
            default   => [null, ['code' => 'NOT_FOUND', 'error' => 'Bilinmeyen tip']],
        };
    }

    // ── id listeleri ────────────────────────────────────────────────

    private function idsFor(string $type, ?string $lang, string $status): array
    {
        return match ($type) {
            'article' => $this->postIds($lang, $status),
            'event'   => $this->eventIds($status),
            'page'    => $this->legalIds($status),
            default   => [],
        };
    }

    private function postIds(?string $lang, string $status): array
    {
        $q = Post::query();
        if ($lang) $q->where('locale', $lang);
        $this->applyStatus($q, $status, 'is_published', 'published_at');
        return $q->orderByDesc('published_at')->orderByDesc('id')->pluck('id')->all();
    }

    private function eventIds(string $status): array
    {
        $q = Event::query()->whereNull('parent_event_id');
        $this->applyStatus($q, $status, 'is_active');
        return $q->orderByDesc('starts_at')->pluck('id')->all();
    }

    private function legalIds(string $status): array
    {
        $q = LegalPage::query();
        if ($status === 'published') $q->where('is_published', true);
        elseif ($status === 'draft') $q->where('is_published', false);
        return $q->orderBy('sort_order')->pluck('key')->all();
    }

    private function applyStatus($q, string $status, string $col, ?string $dateCol = null): void
    {
        if ($status === 'all') return;
        if ($status === 'draft') { $q->where($col, false); return; }
        // published (default)
        $q->where($col, true);
        if ($dateCol) $q->whereNotNull($dateCol)->where($dateCol, '<=', now());
    }

    // ── normalize (hydrate) ─────────────────────────────────────────

    private function hydrate(string $type, $id, ?string $lang): ?array
    {
        return match ($type) {
            'article' => $this->fromPost($id),
            'event'   => $this->fromEvent($id, $lang ?: 'en'),
            'page'    => $this->fromLegal($id, $lang ?: 'en'),
            default   => null,
        };
    }

    private function fromPost($id): ?array
    {
        $p = Post::find($id);
        if (! $p) return null;
        return [
            'id' => 'article-' . $p->id,
            'type' => 'article',
            'title' => $p->title,
            'slug' => $p->slug,
            'url' => url('/' . $p->locale . '/blog/' . $p->slug),
            'seoTitle' => $p->meta_title ?: null,
            'seoDescription' => $p->meta_description ?: null,
            'focusKeyword' => null,
            'status' => $p->is_published ? 'published' : 'draft',
            'lang' => $p->locale,
            'updatedAt' => optional($p->updated_at)->toIso8601String(),
            'bodyPreview' => $this->plain($p->content_html ?: $p->content_md, 500),
            'bodyForAi' => $this->plain($p->content_html ?: $p->content_md, 1600),
            'imageUrl' => $p->featured_image ? url($p->featured_image) : null,
            'groupLabel' => 'Blog',
        ];
    }

    private function fromEvent($id, string $lang): ?array
    {
        $e = Event::find($id);
        if (! $e) return null;
        $title = $e->{'title_' . $lang} ?: $e->title_en ?: $e->title_tr;
        $body = $e->{'description_md_' . $lang} ?: $e->description_md_en ?: $e->description_md_tr;
        return [
            'id' => 'event-' . $e->id,
            'type' => 'event',
            'title' => $title,
            'slug' => $e->slug,
            'url' => url('/events/' . $e->slug),
            'seoTitle' => $e->meta_title ?: null,
            'seoDescription' => $e->meta_description ?: null,
            'focusKeyword' => null,
            'status' => $e->is_active ? 'published' : 'draft',
            'lang' => $lang,
            'updatedAt' => optional($e->updated_at)->toIso8601String(),
            'bodyPreview' => $this->plain($body, 500),
            'bodyForAi' => $this->plain($body, 1600),
            'imageUrl' => $e->banner_url ?: null,
            'groupLabel' => 'Etkinlikler',
        ];
    }

    private function fromLegal($key, string $lang): ?array
    {
        $l = LegalPage::where('key', $key)->first();
        if (! $l) return null;
        $title = $this->jsonLocale($l->titles, $lang);
        $desc = $this->jsonLocale($l->descriptions, $lang);
        $body = $this->jsonLocale($l->bodies, $lang);
        return [
            'id' => 'page-' . $l->key,
            'type' => 'page',
            'title' => $title ?: $l->key,
            'slug' => $l->key,
            'url' => $this->legalUrl($l->key),
            'seoTitle' => $title ?: null,
            'seoDescription' => $desc ?: null,
            'focusKeyword' => null,
            'status' => $l->is_published ? 'published' : 'draft',
            'lang' => $lang,
            'updatedAt' => optional($l->updated_at)->toIso8601String(),
            'bodyPreview' => $this->plain($body, 500),
            'bodyForAi' => $this->plain($body, 1600),
            'imageUrl' => null,
            'groupLabel' => 'Yasal',
        ];
    }

    // ── güncelleme (Faz 2) ──────────────────────────────────────────

    private function updatePost($id, array $data, ?string $lang): array
    {
        $p = Post::find($id);
        if (! $p) return [null, ['code' => 'NOT_FOUND', 'error' => 'Yazı bulunamadı']];
        if (array_key_exists('seoTitle', $data)) $p->meta_title = $data['seoTitle'];
        if (array_key_exists('seoDescription', $data)) $p->meta_description = $data['seoDescription'];
        $p->save();
        return [$this->fromPost($p->id), null];
    }

    private function updateEvent($id, array $data): array
    {
        $e = Event::find($id);
        if (! $e) return [null, ['code' => 'NOT_FOUND', 'error' => 'Etkinlik bulunamadı']];
        if (array_key_exists('seoTitle', $data)) $e->meta_title = $data['seoTitle'];
        if (array_key_exists('seoDescription', $data)) $e->meta_description = $data['seoDescription'];
        $e->save();
        return [$this->fromEvent($e->id, $data['lang'] ?? 'en'), null];
    }

    private function updateLegal($key, array $data, ?string $lang): array
    {
        $l = LegalPage::where('key', $key)->first();
        if (! $l) return [null, ['code' => 'NOT_FOUND', 'error' => 'Sayfa bulunamadı']];
        $lang = $lang ?: 'en';
        if (array_key_exists('seoTitle', $data)) {
            $titles = $l->titles ?? [];
            $titles[$lang] = $data['seoTitle'];
            $l->titles = $titles;
        }
        if (array_key_exists('seoDescription', $data)) {
            $descriptions = $l->descriptions ?? [];
            $descriptions[$lang] = $data['seoDescription'];
            $l->descriptions = $descriptions;
        }
        $l->save();
        return [$this->fromLegal($l->key, $lang), null];
    }

    // ── yardımcılar ─────────────────────────────────────────────────

    /** @return array{0:?string,1:mixed} */
    private function parseId(string $compositeId): array
    {
        $pos = strpos($compositeId, '-');
        if ($pos === false) return [null, null];
        $type = substr($compositeId, 0, $pos);
        $rest = substr($compositeId, $pos + 1);
        if (! in_array($type, self::TYPES, true)) return [null, null];
        return [$type, $rest];
    }

    private function jsonLocale($json, string $lang): ?string
    {
        if (! is_array($json)) return null;
        return $json[$lang] ?? $json['en'] ?? $json['tr'] ?? (reset($json) ?: null);
    }

    private function legalUrl(string $key): string
    {
        $name = 'legal.' . $key;
        try {
            return \Illuminate\Support\Facades\Route::has($name) ? route($name) : url('/' . $key);
        } catch (\Throwable $e) {
            return url('/' . $key);
        }
    }

    private function plain(?string $text, int $max): string
    {
        if (! $text) return '';
        $t = strip_tags($text);
        // basit markdown temizliği
        $t = preg_replace('/\[([^\]]*)\]\([^)]*\)/u', '$1', $t); // [text](url) → text
        $t = preg_replace('/[#>*_`~]+/u', ' ', $t);
        $t = preg_replace('/\s+/u', ' ', trim((string) $t));
        return Str::limit((string) $t, $max, '');
    }
}
