<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    //
    protected $fillable = [
        'site_title',
        'header_logo',
        'footer_logo',
    ];
}
