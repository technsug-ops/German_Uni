<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScholarshipSubject extends Model
{
    protected $table = 'scholarship_subject_groups';
    protected $primaryKey = 'code';

    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['code', 'name_de', 'name_en', 'name_es'];
}
