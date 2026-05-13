<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stratification extends Model
{
    protected $fillable = ['name','stratification_category_id','code','code2','rule','price'];
}
