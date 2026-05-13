<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
    protected $fillable = ['name','code'];
    public function schemeType()
    {
        return $this->belongsTo(SchemeType::class, 'scheme_type_id');
    }
}
