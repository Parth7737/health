<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnualDeclaration extends Model
{
    protected $fillable = ['hospital_id', 'year', 'submitted_date', 'status'];

    public function hospital() {
        return $this->belongsTo('App\Models\Hospitals', 'hospital_id');
    }
}
