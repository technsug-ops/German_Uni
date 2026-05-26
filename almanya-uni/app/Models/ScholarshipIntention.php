<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScholarshipIntention extends Model
{
    protected $table = 'scholarship_intentions';

    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = ['id', 'name_de', 'name_en'];
}
