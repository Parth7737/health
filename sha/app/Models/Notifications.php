<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $fillable = ['user_id', 'hospital_id', 'type', 'date', 'message', 'is_read', 'ref_id'];

    public function hospital() {
        return $this->belongsTo('App\Models\Hospitals', 'hospital_id');
    }

    public function expiredDocument() {
        return $this->belongsTo('App\Models\ExpiredDocument', 'ref_id');
    }
}
