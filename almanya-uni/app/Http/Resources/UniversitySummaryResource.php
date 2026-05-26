<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UniversitySummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'wikidata_id' => $this->wikidata_id,
            'slug' => $this->slug,
            'name' => $this->name_de,
            'name_tr' => $this->name_tr,
            'name_de' => $this->name_de,
            'name_en' => $this->name_en,
            'short_name' => $this->short_name,
            'type' => $this->type,
            'founded_year' => $this->founded_year,
            'student_count' => $this->student_count,
            'website_url' => $this->website_url,
            'logo_url' => $this->logo_url,
            'city' => $this->whenLoaded('city', fn () => [
                'id' => $this->city->id,
                'slug' => $this->city->slug,
                'name' => $this->city->name_de,
            ]),
        ];
    }
}
