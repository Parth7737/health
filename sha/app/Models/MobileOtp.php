<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileOtp extends Model
{
    //
    protected $fillable = ['mobile_no', 'otp', 'status'];
}
