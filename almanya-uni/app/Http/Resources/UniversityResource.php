<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UniversityResource extends JsonResource
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
            'short_name' => $this->short_name,

            'description' => [
                'tr' => $this->description_tr,
                'de' => $this->description_de,
                'en' => $this->description_en,
            ],

            'type' => $this->type,
            'founded_year' => $this->founded_year,
            'student_count' => $this->student_count,

            'website_url' => $this->website_url,
            'logo_url' => $this->logo_url,
            'image_url' => $this->image_url,

            'location' => [
                'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
                'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
                'city' => $this->whenLoaded('city', fn () => new CityResource($this->city)),
            ],

            'wikipedia' => [
                'tr' => $this->wikipedia_url_tr,
                'de' => $this->wikipedia_url_de,
                'en' => $this->wikipedia_url_en,
            ],

            'data_source' => $this->data_source,
            'last_synced_at' => $this->last_synced_at?->toIso8601String(),

            'has_content' => !empty($this->content_blocks),
            'last_enriched_at' => $this->last_enriched_at?->toIso8601String(),

            // İçerik blokları — ?with=content_blocks query'sinde döner
            'content_blocks' => $this->when(
                in_array('content_blocks', $with, true) && !empty($this->content_blocks),
                fn () => $this->content_blocks,
            ),
            'urls' => [
                'web' => url('/universities/' . $this->slug),
                'api' => url('/api/v1/universities/' . $this->slug),
                'content' => url('/api/v1/universities/' . $this->slug . '/content'),
            ],
        ];
    }
}
