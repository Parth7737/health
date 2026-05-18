<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreauthCareTeam extends Model
{
    protected $table = 'preauth_care_teams';

    protected $guarded = [];

    public function hospital_team(): BelongsTo
    {
        return $this->belongsTo(HospitalTeam::class, 'hospital_team_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
