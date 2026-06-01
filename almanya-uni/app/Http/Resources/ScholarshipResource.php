<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScholarshipResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $lang = in_array($request->query('lang'), ['de', 'en'], true) ? $request->query('lang') : 'en';

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'name_de' => $this->name_de,
            'name_en' => $this->name_en,
            'program_name' => $lang === 'de' ? $this->programmname_de : $this->programmname_en,
            'is_daad' => (bool) $this->is_daad,
            'detail_url' => $this->detail_url,
            'introduction' => $this->introduction_json,
            'questions' => $lang === 'de' ? $this->q_de_json : $this->q_en_json,
            'last_seen_at' => $this->last_seen_at?->toIso8601String(),
            'links' => [
                'web' => url('/scholarships/' . $this->slug),
                'web_daad' => $this->detail_url,
            ],
        ];
    }
}
