<?php

namespace App\Services\Scraping\Adapters;

use App\Models\ScrapeSource;

interface ScraperAdapter
{
    /**
     * Source'tan program listesi çıkar.
     *
     * @return array<int, array{
     *   external_key: string|null,
     *   source_url: string|null,
     *   name_de: string|null,
     *   name_en: string|null,
     *   degree: string|null,
     *   language: string|null,
     *   duration_semesters: int|null,
     *   description_de: string|null,
     *   raw: array<string,mixed>
     * }>
     */
    public function scrape(ScrapeSource $source): array;
}
