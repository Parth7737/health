<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubService extends Model
{
    protected $fillable = ['service_id', 'name', 'is_required', 'sort_order'];

    public function service() {
        return $this->belongsTo('App\Models\Service', 'service_id');
    }

    public function actions() {
        return $this->hasMany('App\Models\SubServiceAction', 'sub_service_id');
    }
}
