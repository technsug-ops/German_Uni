<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $with = explode(',', (string) $request->query('with', ''));

        return [
            'id' => $this->id,
            'wikidata_id' => $this->wikidata_id,
            'slug' => $this->slug,
            'name' => [
                'tr' => $this->name_tr,
                'de' => $this->name_de,
                'en' => $this->name_en,
            ],
            'population' => $this->population,
            'image_url' => $this->image_url,
            'location' => [
                'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
                'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
            ],
            'state' => $this->whenLoaded('state', fn () => [
                'id' => $this->state?->id,
                'slug' => $this->state?->slug,
                'name' => $this->state?->name_de,
            ]),
            'universities_count' => $this->when(
                isset($this->universities_count),
                fn () => (int) $this->universities_count,
            ),
            'has_content' => !empty($this->content_blocks),
            'last_enriched_at' => $this->last_enriched_at?->toIso8601String(),

            // İçerik blokları — sadece ?with=content_blocks query'sinde döner (sayfa yapıcı uygulamalar için)
            'content_blocks' => $this->when(
                in_array('content_blocks', $with, true) && !empty($this->content_blocks),
                fn () => $this->content_blocks,
            ),
            'urls' => [
                'web' => url('/cities/' . $this->slug),
                'api' => url('/api/v1/cities/' . $this->slug),
                'content' => url('/api/v1/cities/' . $this->slug . '/content'),
            ],
        ];
    }
}
