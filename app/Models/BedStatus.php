<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BedStatus extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    // Status Constants
    const AVAILABLE = 1;
    const OCCUPIED = 2;
    const MAINTENANCE = 3;
    const RESERVED = 4;
    const RESERVED_FOR_DISCHARGE = 5;

    /**
     * Get the beds with this status.
     */
    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }

    /**
     * Get status label with color.
     */
    public static function getStatusLabel($statusId)
    {
        $statuses = [
            self::AVAILABLE => ['label' => 'उपलब्ध (Available)', 'color' => '#28a745'],
            self::OCCUPIED => ['label' => 'व्यस्त (Occupied)', 'color' => '#dc3545'],
            self::MAINTENANCE => ['label' => 'रखरखाव (Maintenance)', 'color' => '#ffc107'],
            self::RESERVED => ['label' => 'आरक्षित (Reserved)', 'color' => '#17a2b8'],
            self::RESERVED_FOR_DISCHARGE => ['label' => 'डिस्चार्ज के लिए (Discharge)', 'color' => '#6c757d'],
        ];

        return $statuses[$statusId] ?? ['label' => 'अज्ञात', 'color' => '#999'];
    }
}
