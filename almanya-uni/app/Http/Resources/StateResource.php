<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'wikidata_id' => $this->wikidata_id,
            'slug' => $this->slug,
            'name' => [
                'tr' => $this->name_tr,
                'de' => $this->name_de,
                'en' => $this->name_en,
            ],
            'location' => [
                'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
                'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
            ],
            'cities_count' => $this->when(
                isset($this->cities_count),
                fn () => (int) $this->cities_count,
            ),
        ];
    }
}
