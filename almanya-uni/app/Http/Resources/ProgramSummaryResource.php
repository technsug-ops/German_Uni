<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name_de' => $this->name_de,
            'name_en' => $this->name_en,
            'name_tr' => $this->name_tr,
            'degree' => $this->degree,
            'language' => $this->language,
            'duration_semesters' => $this->duration_semesters,
            'study_form' => $this->study_form,
            'admission_mode' => $this->admission_mode,
            'nc_value' => $this->nc_value,
            'tuition_fee_eur' => $this->tuition_fee_eur,
            'university' => $this->whenLoaded('university', fn () => [
                'id' => $this->university->id,
                'slug' => $this->university->slug,
                'name' => $this->university->name_de,
                'type' => $this->university->type,
            ]),
            'field' => $this->whenLoaded('field', fn () => $this->field ? [
                'id' => $this->field->id,
                'slug' => $this->field->slug,
                'name' => $this->field->name_de,
            ] : null),
        ];
    }
}
