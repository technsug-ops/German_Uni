<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScholarshipStatus extends Model
{
    protected $table = 'scholarship_statuses';

    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = ['id', 'name_de', 'name_en', 'name_es', 'sortierung'];
}
