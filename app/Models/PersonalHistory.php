<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalHistory extends Model
{
    protected $guarded = [];

    public function appetite(): BelongsTo
    {
        return $this->belongsTo(PreauthReferenceOption::class, 'appetite_id');
    }

    public function bowels(): BelongsTo
    {
        return $this->belongsTo(PreauthReferenceOption::class, 'bowels_id');
    }

    public function nutrition(): BelongsTo
    {
        return $this->belongsTo(PreauthReferenceOption::class, 'nutrition_id');
    }

    public function diet(): BelongsTo
    {
        return $this->belongsTo(PreauthReferenceOption::class, 'diet_id');
    }
}
