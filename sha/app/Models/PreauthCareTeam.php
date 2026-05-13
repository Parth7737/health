<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreauthCareTeam extends Model
{
    public function hospital_team() {
        return $this->belongsTo('App\Models\HospitalTeam', 'hospital_team_id');
    }
}
