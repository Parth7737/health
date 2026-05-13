<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model
{
    public const STATUS_PENDING = 0;
    public const STATUS_PAID = 1;
    public function getStatusLabelAttribute()
    {
        $statuses = [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PAID => 'Paid',
        ];
        return $statuses[$this->status] ?? 'Unknown';
    }
    public function hospital() {
        return $this->belongsTo(Hospitals::class, 'hospital_id');
    }
}
