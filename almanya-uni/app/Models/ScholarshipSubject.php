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

    protected $fillable = ['code', 'name_de', 'name_en', 'name_tr', 'name_es'];

    public function getNameAttribute(): string
    {
        $loc = app()->getLocale();
        $value = $this->attributes['name_' . $loc] ?? null;
        if (! empty($value)) return $value;
        foreach (['name_en', 'name_de', 'name_tr'] as $fb) {
            if (! empty($this->attributes[$fb] ?? null)) return $this->attributes[$fb];
        }
        return '';
    }
}
