<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['hospital_id', 'user_id', 'amount', 'hospital_uuid', 'uuid', 'order_id', 'currency', 'language', 'billing_name', 'billing_email', 'billing_tel', 'transaction_id', 'status'];

    public function hospital() {
        return $this->belongsTo('App\Models\Hospitals', 'hospital_id');
    }
}
