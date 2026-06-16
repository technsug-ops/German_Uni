<?php
use App\Models\University;
$unis = University::where('is_active',1)->whereNull('city_id')->orderBy('name_de')->get(['id','name_de','postal_code','wikidata_id']);
echo "COUNT=".$unis->count().PHP_EOL;
foreach($unis as $u){
    echo "#{$u->id}|{$u->name_de}|".($u->postal_code?:'-')."|".($u->wikidata_id?:'YOK').PHP_EOL;
}
