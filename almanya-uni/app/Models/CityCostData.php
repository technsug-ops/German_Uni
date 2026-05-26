<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CityCostData extends Model
{
    protected $table = 'city_cost_data';

    protected $fillable = [
        'city_id',
        'tier',
        'rent_wg',
        'rent_studio',
        'rent_apartment',
        'food',
        'transport',
        'utilities',
        'health_insurance',
        'entertainment',
        'misc',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function totalForHousing(string $housing, float $multiplier = 1.0): int
    {
        $rent = match ($housing) {
            'studio' => $this->rent_studio,
            'apartment' => $this->rent_apartment,
            default => $this->rent_wg,
        };

        $flexible = (int) round(($this->food + $this->entertainment + $this->misc) * $multiplier);
        $fixed = $rent + $this->transport + $this->utilities + $this->health_insurance;

        return $rent + $this->transport + $this->utilities + $this->health_insurance + $flexible;
    }
}
