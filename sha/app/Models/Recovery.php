<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recovery extends Model
{
    public const STATUS_REQUESTED = 0;
    public const STATUS_APPROVED = 1;
    public const STATUS_REJECTED = 2;
    public const STATUS_COMPLETED = 3;
    public function getStatusLabelAttribute()
    {
        $statuses = [
            self::STATUS_REQUESTED => 'Requested',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_COMPLETED => 'Completed',
        ];
        return $statuses[$this->status] ?? 'Unknown';
    }
    public function hospital() {
        return $this->belongsTo(Hospitals::class, 'hospital_id');
    }
}
