<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScholarshipOrigin extends Model
{
    protected $table = 'scholarship_origins_lookup';

    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = ['id', 'name_de', 'name_en', 'name_es', 'sortname'];
}
