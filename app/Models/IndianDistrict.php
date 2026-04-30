<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndianDistrict extends Model
{
    public $timestamps = false;

    protected $fillable = ['state_id', 'name', 'is_active'];

    public function state(): BelongsTo
    {
        return $this->belongsTo(IndianState::class, 'state_id');
    }
}
