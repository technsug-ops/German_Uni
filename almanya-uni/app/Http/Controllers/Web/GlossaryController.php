<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Almanya eğitim sistemi entity glossary (semantic SEO).
 * URL: /{locale}/sozluk veya /{locale}/glossary
 * config/glossary.php'den okur.
 */
class GlossaryController extends Controller
{
    public function index(): View
    {
        $entries = collect(config('glossary', []))
            ->map(fn ($e) => $this->localizeEntry($e))
            ->sortBy('title')
            ->values()
            ->all();

        return view('glossary.index', [
            'entries' => $entries,
        ]);
    }

    public function show(string $slug): View|Response
    {
        $raw = config('glossary.' . $slug);
        if (!$raw) {
            abort(404);
        }

        $entry = $this->localizeEntry($raw);

        // Related entries (full data for cards)
        $related = collect($raw['related'] ?? [])
            ->map(fn ($s) => config('glossary.' . $s))
            ->filter()
            ->map(fn ($e) => $this->localizeEntry($e))
            ->values()
            ->all();

        return view('glossary.show', [
            'entry' => $entry,
            'related' => $related,
        ]);
    }

    /**
     * Locale-aware field extraction (TR > EN > DE fallback).
     */
    private function localizeEntry(array $raw): array
    {
        $locale = app()->getLocale();
        $pick = fn ($field) => $raw[$field][$locale]
            ?? $raw[$field]['en']
            ?? $raw[$field]['tr']
            ?? $raw[$field]['de']
            ?? (is_string($raw[$field] ?? null) ? $raw[$field] : '');

        return [
            'slug' => $raw['slug'],
            'icon' => $raw['icon'] ?? '📘',
            'title' => $pick('title'),
            'short' => $pick('short'),
            'body' => $pick('body'),
            'related_slugs' => $raw['related'] ?? [],
            'official_url' => $raw['official_url'] ?? null,
            'faq' => collect($raw['faq'] ?? [])->map(function ($item) use ($locale) {
                return [
                    'q' => $item['q'][$locale] ?? $item['q']['en'] ?? $item['q']['tr'] ?? '',
                    'a' => $item['a'][$locale] ?? $item['a']['en'] ?? $item['a']['tr'] ?? '',
                ];
            })->all(),
        ];
    }
}
