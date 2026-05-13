<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabStatus extends Model
{
    protected $fillable = ['hospital_id', 'tab', 'type', 'is_verifier', 'is_dec', 'is_sec'];
}
