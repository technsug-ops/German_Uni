<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScholarshipDeadline extends Model
{
    protected $table = 'scholarship_deadlines';
    protected $primaryKey = 'sap_objid';

    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'sap_objid',
        'general_de',
        'general_en',
        'countries_json',
        'last_seen_at',
    ];

    protected $casts = [
        'countries_json' => 'array',
        'last_seen_at'   => 'datetime',
    ];
}
