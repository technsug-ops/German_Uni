<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramResource extends JsonResource
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
            'degree_specification' => $this->degree_specification,
            'language' => $this->language,
            'languages' => $this->languages_array,
            'duration_semesters' => $this->duration_semesters,
            'study_form' => $this->study_form,
            'location' => $this->location,
            'admission_mode' => $this->admission_mode,
            'admission_summary' => $this->admission_summary,
            'nc_value' => $this->nc_value,
            'subjects' => $this->subjects,
            'study_fields_raw' => $this->study_fields_raw,
            'tuition_fee_eur' => $this->tuition_fee_eur,
            'application_fee_eur' => $this->application_fee_eur,
            'cost_per_semester_eur' => $this->cost_per_semester_eur,
            'application_deadline_summer' => $this->application_deadline_summer?->toDateString(),
            'application_deadline_winter' => $this->application_deadline_winter?->toDateString(),
            'description_tr' => $this->description_tr,
            'description_en' => $this->description_en,
            'qualification_requirements_tr' => $this->qualification_requirements_tr,
            'language_requirements_tr' => $this->language_requirements_tr,
            'required_documents_tr' => $this->required_documents_tr,
            'source_url' => $this->source_url,
            'source' => $this->source,
            'last_synced_at' => $this->last_synced_at?->toIso8601String(),
            'university' => $this->whenLoaded('university', fn () => [
                'id' => $this->university->id,
                'slug' => $this->university->slug,
                'name' => $this->university->name_de,
                'name_tr' => $this->university->name_tr,
                'type' => $this->university->type,
                'website_url' => $this->university->website_url,
                'logo_url' => $this->university->logo_url,
                'city' => $this->university->relationLoaded('city') && $this->university->city ? [
                    'id' => $this->university->city->id,
                    'slug' => $this->university->city->slug,
                    'name' => $this->university->city->name_de,
                ] : null,
            ]),
            'field' => $this->whenLoaded('field', fn () => $this->field ? [
                'id' => $this->field->id,
                'slug' => $this->field->slug,
                'name' => $this->field->name_de,
            ] : null),
        ];
    }
}
