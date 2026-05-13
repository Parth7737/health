<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExistsHospital extends Model
{
    protected $fillable = ['hospital_id', 'hospital_name', 'district', 'city_town', 'is_added'];
}
