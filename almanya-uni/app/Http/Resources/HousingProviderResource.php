<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HousingProviderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $lang = in_array($request->query('lang'), ['tr', 'de', 'en'], true) ? $request->query('lang') : 'en';
        $desc = $this->{'description_' . $lang} ?: $this->description_en ?: $this->description_tr;

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'type' => $this->type,
            'description' => $desc,
            'website' => $this->website,
            'email' => $this->email,
            'phone' => $this->phone,
            'logo_url' => $this->logo_url,
            'price_min' => $this->price_min,
            'price_max' => $this->price_max,
            'cities' => $this->cities,
            'features' => $this->features,
            'total_capacity' => $this->total_capacity,
            'waiting_period' => $this->waiting_period,
            'is_featured' => (bool) $this->is_featured,
        ];
    }
}
