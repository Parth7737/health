<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreauthReferenceOption extends Model
{
    protected $table = 'preauth_reference_options';

    protected $guarded = [];

    public function getNameAttribute(): string
    {
        return (string) $this->label;
    }
}
