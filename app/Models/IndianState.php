<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IndianState extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'code', 'is_active'];

    public function districts(): HasMany
    {
        return $this->hasMany(IndianDistrict::class, 'state_id');
    }
}
