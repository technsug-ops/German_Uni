<?php

namespace App\Services;

use App\Models\City;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Ticketmaster Discovery API → /events (Kültür & Konser).
 *
 * Almanya'daki konser/tiyatro etkinliklerini çeker ve mevcut Event modeline
 * idempotent şekilde (source=ticketmaster + external_id unique) yazar.
 * Venue şehri → City eşlenir (bildirim/filtre için), eşleşmezse location_city string kalır.
 *
 * Ücretsiz tier: 5 req/sn, 5000/gün. Attribution + satın-alma linki (event.url) zorunlu (ToS).
 */
class TicketmasterImporter
{
    private const API = 'https://app.ticketmaster.com/discovery/v2/events.json';

    /**
     * Ticketmaster bazı Alman şehirlerini İngilizce/tam adla indeksliyor — sorguda bunu
     * kullanmazsak 0 sonuç döner (München→Munich: 0 vs 94). Dönen venue city.name de bu
     * ada gelir → matchCity name_en'den (Munich/Cologne) bizim City'ye eşler.
     */
    private const TM_QUERY_NAME = [
        'München' => 'Munich',
        'Köln'    => 'Cologne',
    ];

    public function __construct(private ?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?: config('services.ticketmaster.key');
    }

    public function isConfigured(): bool
    {
        return ! empty($this->apiKey);
    }

    /**
     * Tek şehir için import. @return array{imported:int,updated:int,skipped:int}
     */
    public function importCity(string $city, array $opts = []): array
    {
        $size     = (int) ($opts['size'] ?? 100);
        $segments = $opts['segments'] ?? ['Music', 'Arts & Theatre'];
        $cultureCatId = EventCategory::where('slug', 'culture')->value('id');

        $stats = ['imported' => 0, 'updated' => 0, 'skipped' => 0];

        $queryCity = self::TM_QUERY_NAME[$city] ?? $city;

        foreach ($segments as $segment) {
            $resp = Http::timeout(20)->retry(2, 600)->get(self::API, [
                'apikey'        => $this->apiKey,
                'countryCode'   => 'DE',
                'city'          => $queryCity,
                'segmentName'   => $segment,
                'size'          => min($size, 200),
                'sort'          => 'date,asc',
                'startDateTime' => now()->utc()->format('Y-m-d\TH:i:s\Z'),
            ]);

            if (! $resp->ok()) {
                continue;
            }

            foreach (data_get($resp->json(), '_embedded.events', []) as $tm) {
                $stats[$this->upsert($tm, $cultureCatId)]++;
            }
        }

        return $stats;
    }

    /** @return 'imported'|'updated'|'skipped' */
    private function upsert(array $tm, ?int $cultureCatId): string
    {
        $externalId = $tm['id'] ?? null;
        $name       = trim((string) ($tm['name'] ?? ''));
        if (! $externalId || $name === '') {
            return 'skipped';
        }

        $startsAt = $this->resolveStart($tm);
        if (! $startsAt) {
            return 'skipped';
        }

        $venue    = data_get($tm, '_embedded.venues.0', []);
        $cityName = data_get($venue, 'city.name');
        $city     = $this->matchCity($cityName);

        $segment = data_get($tm, 'classifications.0.segment.name');
        $genre   = data_get($tm, 'classifications.0.genre.name');

        $existing = Event::where('source', 'ticketmaster')->where('external_id', $externalId)->first();

        $values = [
            'type'             => $this->mapType($segment, $genre, $name),
            'category_id'      => $cultureCatId,
            'title_tr'         => Str::limit($name, 250, ''),
            'title_en'         => Str::limit($name, 250, ''),
            'title_de'         => Str::limit($name, 250, ''),
            'starts_at'        => $startsAt,
            'timezone'         => data_get($tm, 'dates.timezone', 'Europe/Berlin'),
            'mode'             => 'offline',
            // Konser/tiyatronun "sunum dili" yok → null (kolon default 'tr' yanlış rozet veriyordu).
            'presentation_language' => null,
            'location_name'    => Str::limit((string) data_get($venue, 'name'), 250, '') ?: null,
            'location_city'    => $cityName,
            'city_id'          => $city?->id,
            'registration_url' => $tm['url'] ?? null,
            'price_eur'        => data_get($tm, 'priceRanges.0.min'),
            'banner_url'       => $this->bestImage($tm['images'] ?? []),
            'is_active'        => true,
        ];

        // Slug yalnızca ilk oluşturmada — sonraki importlarda değişmez (SEO/link kararlılığı).
        if (! $existing) {
            $values['slug'] = $this->uniqueSlug($name, $externalId);
        }

        Event::updateOrCreate(
            ['source' => 'ticketmaster', 'external_id' => $externalId],
            $values
        );

        return $existing ? 'updated' : 'imported';
    }

    /**
     * Etkinliğin yerel başlangıç zamanı. TM localDate+localTime venue saatidir → naif
     * yerel olarak sakla (app tz'den bağımsız doğru görünür). Yoksa UTC dateTime fallback.
     */
    private function resolveStart(array $tm): ?Carbon
    {
        $localDate = data_get($tm, 'dates.start.localDate');
        if ($localDate) {
            $localTime = data_get($tm, 'dates.start.localTime', '20:00:00');
            try {
                return Carbon::parse("$localDate $localTime");
            } catch (\Throwable) {
                return null;
            }
        }

        $dateTime = data_get($tm, 'dates.start.dateTime');
        if ($dateTime) {
            try {
                return Carbon::parse($dateTime)->setTimezone('Europe/Berlin')->setTimezone(config('app.timezone'));
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }

    private function matchCity(?string $cityName): ?City
    {
        if (! $cityName) {
            return null;
        }

        return City::where('name_de', $cityName)
            ->orWhere('name_en', $cityName)
            ->orWhere('name_tr', $cityName)
            ->first();
    }

    private function mapType(?string $segment, ?string $genre, string $name): string
    {
        $g = Str::lower((string) $genre);
        $n = Str::lower($name);

        if ($segment === 'Arts & Theatre') {
            return str_contains($g, 'comedy') ? 'comedy' : 'theater';
        }

        // Music (varsayılan)
        if (str_contains($g, 'classical') || str_contains($g, 'opera')) {
            return 'opera_classical';
        }
        if (str_contains($n, 'festival')) {
            return 'music_festival';
        }

        return 'concert';
    }

    private function bestImage(array $images): ?string
    {
        $best = null;
        $bestW = 0;
        foreach ($images as $img) {
            $w = (int) ($img['width'] ?? 0);
            if ($w > $bestW && ! empty($img['url'])) {
                $bestW = $w;
                $best = $img['url'];
            }
        }

        return $best;
    }

    private function uniqueSlug(string $name, string $externalId): string
    {
        $base = Str::slug(Str::limit($name, 60, '')) ?: 'event';
        $slug = $base . '-' . Str::lower(Str::substr($externalId, -6));

        if (Event::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(4));
        }

        return $slug;
    }
}
