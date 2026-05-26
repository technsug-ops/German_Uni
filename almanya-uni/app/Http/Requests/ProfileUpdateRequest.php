<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],

            // AlmanyaUni profil alanları (opsiyonel)
            'high_school_type'  => ['nullable', 'in:anadolu,fen,duz,meslek,imam_hatip,other'],
            'status'            => ['nullable', 'in:lise_ogrencisi,lise_mezunu,uni_ogrencisi,uni_mezunu,calisan,other'],
            'german_level'      => ['nullable', 'string', 'max:20'],
            'english_level'     => ['nullable', 'string', 'max:20'],
            'target_field_id'   => ['nullable', 'integer', 'exists:fields_of_study,id'],
            'target_degree'     => ['nullable', 'in:bachelor,master,phd,studienkolleg,other'],
            'target_semester'   => ['nullable', 'string', 'max:30'],
            'monthly_budget_eur'=> ['nullable', 'integer', 'min:0', 'max:99999'],
            'preferred_state_id'=> ['nullable', 'integer', 'exists:states,id'],
            'bio'               => ['nullable', 'string', 'max:1000'],
        ];
    }
}
