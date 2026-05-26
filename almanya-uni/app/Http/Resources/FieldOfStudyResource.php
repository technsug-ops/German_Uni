<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FieldOfStudyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name_tr' => $this->name_tr,
            'name_de' => $this->name_de,
            'name_en' => $this->name_en,
            'icon' => $this->icon,
            'color' => $this->color,
            'programs_count' => $this->when(isset($this->programs_count), $this->programs_count),
        ];
    }
}
