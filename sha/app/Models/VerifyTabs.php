<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifyTabs extends Model
{
    protected $fillable = ['preauth_register_id', 'tab', 'type', 'is_open'];

    public function preauthRegister() {
        return $this->belongsTo(PreauthRegister::class, 'preauth_register_id');
    }
}
