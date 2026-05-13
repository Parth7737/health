<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HumanResource extends Model
{
    protected $fillable = ['type', 'type_slug', 'name'];
}
