<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrRecruitmentApplicationStatusLog extends Model
{
    public $timestamps = false;

    protected $table = 'hr_recruitment_application_status_logs';

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(HrRecruitmentApplication::class, 'hr_recruitment_application_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
