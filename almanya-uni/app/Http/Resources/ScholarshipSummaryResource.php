<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScholarshipSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'name_de' => $this->name_de,
            'name_en' => $this->name_en,
            'is_daad' => (bool) $this->is_daad,
            'detail_url' => $this->detail_url,
            'links' => [
                'web' => url('/scholarships/' . $this->slug),
                'api' => url('/api/v1/scholarships/' . $this->slug),
            ],
        ];
    }
}
