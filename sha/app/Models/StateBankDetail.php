<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateBankDetail extends Model
{
    protected $fillable = ['bank_name', 'state_id', 'ifsc_code', 'account_name', 'account_number'];

    public function state() {
        return $this->belongsTo('App\Models\HospitalState', 'state_id');
    }
}
