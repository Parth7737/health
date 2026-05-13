<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalHistory extends Model
{
    public function appetite() {
        return $this->belongsTo('App\Models\Appetite', 'appetite_id');
    }
    public function bowel() {
        return $this->belongsTo('App\Models\Bowels', 'bowels_id');
    }
    public function nutrition() {
        return $this->belongsTo('App\Models\Nutrition', 'nutrition_id');
    }
    public function diet() {
        return $this->belongsTo('App\Models\Diet', 'diet_id');
    }
}
