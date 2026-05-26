<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'berufenet_id' => $this->berufenet_id,
            'kldb_code' => $this->kldb_code,
            'slug' => $this->slug,
            'name_de' => $this->name_de,
            'name_tr' => $this->name_tr,
            'short_name' => $this->short_name,
            'cluster' => $this->cluster,
            'cluster_label' => $this->cluster_label,
            'type' => $this->type,
            'description_de' => $this->when($request->boolean('full'), $this->description_de),
            'description_tr' => $this->when($request->boolean('full'), $this->description_tr),
            'steckbrief' => $this->when($request->boolean('full'), $this->steckbrief),
            'image_url' => $this->image_url,
            'field' => $this->whenLoaded('field', fn () => $this->field ? [
                'id' => $this->field->id,
                'slug' => $this->field->slug,
                'name' => $this->field->name_de,
            ] : null),
        ];
    }
}
