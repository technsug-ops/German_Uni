<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockedAccountProviderResource extends JsonResource
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
            'backend_bank' => $this->backend_bank,
            'description' => $desc,
            'logo_url' => $this->logo_url,
            'website_url' => $this->website_url,
            'fees' => [
                'setup_eur' => $this->setup_fee_eur,
                'monthly_eur' => $this->monthly_fee_eur,
                'yearly_eur' => $this->yearly_fee_eur,
            ],
            'activation_days' => [
                'min' => $this->activation_days_min,
                'max' => $this->activation_days_max,
            ],
            'combo_insurance' => (bool) $this->combo_insurance,
            'insurance_provider_name' => $this->insurance_provider_name,
            'insurance_monthly_eur' => $this->insurance_monthly_eur,
            'monthly_withdrawal_limit_eur' => $this->monthly_withdrawal_limit_eur,
            'required_yearly_deposit_eur' => $this->required_yearly_deposit_eur,
            'has_mobile_app' => (bool) $this->has_mobile_app,
            'bafin_licensed' => (bool) $this->bafin_licensed,
            'supported_languages' => $this->supported_languages,
            'pros' => $this->pros,
            'cons' => $this->cons,
            'features' => $this->features,
            'visa_recognition_note' => $this->visa_recognition_note,
            'is_featured' => (bool) $this->is_featured,
            'last_verified_at' => $this->last_verified_at?->toIso8601String(),
        ];
    }
}
