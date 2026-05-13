<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name'];

    public function subServices() {
        return $this->hasMany('App\Models\SubService', 'service_id');
    }
}
