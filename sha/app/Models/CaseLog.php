<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseLog extends Model
{
    public function preauth_register() {
        return $this->belongsTo('App\Models\PreauthRegister', 'preauth_register_id');
    }
    public function role() {
        return $this->belongsTo('App\Models\Role', 'role_id');
    }
    public function user() {
        return $this->belongsTo('App\Models\User', 'added_by');
    }
}
