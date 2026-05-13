<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyHistory extends Model
{
    public function diabetes() {
        return $this->belongsTo('App\Models\Diabetes', 'diabetes_id');
    }
    public function hypertension() {
        return $this->belongsTo('App\Models\Hypertension', 'hypertension_id');
    }
    public function heartdisease() {
        return $this->belongsTo('App\Models\HeartDisease', 'heartdisease_id');
    }
    public function Stroke() {
        return $this->belongsTo('App\Models\Stroke', 'stroke_id');
    }
    public function cancer() {
        return $this->belongsTo('App\Models\Cancer', 'cancer_id');
    }
    public function tuberculosis() {
        return $this->belongsTo('App\Models\Tuberculosis', 'tuberculosis_id');
    }
    public function asthma() {
        return $this->belongsTo('App\Models\Asthma', 'asthma_id');
    }
}
