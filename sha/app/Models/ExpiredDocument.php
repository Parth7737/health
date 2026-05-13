<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpiredDocument extends Model
{
    protected $fillable = ['user_id', 'hospital_id', 'document_id', 'document_name', 'document_ref_table', 'notification_id', 'expiry_date', 'is_updated'];

    public function notifications() {
        return $this->belongsTo('App\Models\Notifications', 'notification_id');
    }

    public function hospital() {
        return $this->belongsTo('App\Models\Hospitals', 'hospital_id');
    }
}
