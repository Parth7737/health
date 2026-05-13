<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserHfr extends Model
{
    protected $fillable = ['user_id', 'hfr_id', 'hospital_uuid_id', 'mobile_no'];
}
