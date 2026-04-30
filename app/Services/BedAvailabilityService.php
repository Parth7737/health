<?php

namespace App\Services;

use App\Models\Bed;
use App\Models\BedAllocation;
use App\Models\BedStatus;
use App\Models\Room;
use App\Models\Ward;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class BedAvailabilityService
{
    /**
     * Get all available beds with optional filters.
     */
    public function getAvailableBeds($hospitalId, $filters = [])
    {
        $query = Bed::query()
            ->where('hospital_id', $hospitalId)
            ->where('bed_status_id', BedStatus::AVAILABLE)
            ->with(['room', 'room.ward', 'room.ward.floor', 'bedType']);

        // Filter by ward type
        if (!empty($filters['ward_type'])) {
            $query->whereHas('room.ward', function ($q) {
                $q->where('ward_type', $filters['ward_type']);
            });
        }

        // Filter by bed type
        if (!empty($filters['bed_type_id'])) {
            $query->where('bed_type_id', $filters['bed_type_id']);
        }

        // Filter by floor
        if (!empty($filters['floor_id'])) {
            $query->whereHas('room.ward', function ($q) use ($filters) {
                $q->where('floor_id', $filters['floor_id']);
            });
        }

        return $query->get();
    }

    /**
     * Get available beds count by ward.
     */
    public function getAvailableByWard($hospitalId, $wardId = null)
    {
        $query = Bed::query()
            ->where('hospital_id', $hospitalId)
            ->where('bed_status_id', BedStatus::AVAILABLE);

        if ($wardId) {
            $query->whereHas('room', function ($q) use ($wardId) {
                $q->where('ward_id', $wardId);
            });
        }

        return $query->count();
    }

    /**
     * Get available beds count by bed type.
     */
    public function getAvailableByBedType($hospitalId, $bedTypeId = null)
    {
        $query = Bed::query()
            ->where('hospital_id', $hospitalId)
            ->where('bed_status_id', BedStatus::AVAILABLE);

        if ($bedTypeId) {
            $query->where('bed_type_id', $bedTypeId);
        }

        return $query->count();
    }

    /**
     * Get available beds count by floor.
     */
    public function getAvailableByFloor($hospitalId, $floorId = null)
    {
        $query = Bed::query()
            ->where('hospital_id', $hospitalId)
            ->where('bed_status_id', BedStatus::AVAILABLE);

        if ($floorId) {
            $query->whereHas('room', function ($q) use ($floorId) {
                $q->where('floor_id', $floorId);
            });
        }

        return $query->count();
    }

    /**
     * Get total beds statistics for a hospital.
     */
    public function getBedStatistics($hospitalId)
    {
        $totalBeds = Bed::where('hospital_id', $hospitalId)->count();
        $availableBeds = Bed::where('hospital_id', $hospitalId)
            ->where('bed_status_id', BedStatus::AVAILABLE)
            ->count();
        $occupiedBeds = Bed::where('hospital_id', $hospitalId)
            ->where('bed_status_id', BedStatus::OCCUPIED)
            ->count();
        $maintenanceBeds = Bed::where('hospital_id', $hospitalId)
            ->where('bed_status_id', BedStatus::MAINTENANCE)
            ->count();
        $reservedBeds = Bed::where('hospital_id', $hospitalId)
            ->where('bed_status_id', BedStatus::RESERVED)
            ->count();

        return [
            'total_beds' => $totalBeds,
            'available_beds' => $availableBeds,
            'occupied_beds' => $occupiedBeds,
            'maintenance_beds' => $maintenanceBeds,
            'reserved_beds' => $reservedBeds,
            'occupancy_rate' => $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 2) : 0,
            'available_rate' => $totalBeds > 0 ? round(($availableBeds / $totalBeds) * 100, 2) : 0,
        ];
    }

    /**
     * Get beds by ward with count.
     */
    public function getWardBedSummary($hospitalId)
    {
        return Ward::where('hospital_id', $hospitalId)
            ->with([
                'rooms' => function ($query) {
                    $query->with('beds.bedStatus');
                }
            ])
            ->get()
            ->map(function ($ward) {
                $allBeds = $ward->rooms->flatMap->beds;
                $available = $allBeds->filter(fn($bed) => $bed->bed_status_id == BedStatus::AVAILABLE)->count();
                $occupied = $allBeds->filter(fn($bed) => $bed->bed_status_id == BedStatus::OCCUPIED)->count();

                return [
                    'ward_id' => $ward->id,
                    'ward_name' => $ward->ward_name,
                    'ward_type' => $ward->ward_type,
                    'total_beds' => $allBeds->count(),
                    'available_beds' => $available,
                    'occupied_beds' => $occupied,
                    'occupancy_rate' => $allBeds->count() > 0 ? round(($occupied / $allBeds->count()) * 100, 2) : 0,
                ];
            });
    }

    /**
     * Get best available bed based on requirements.
     */
    public function findBestAvailableBed($hospitalId, $wardId = null, $bedTypeId = null)
    {
        $query = Bed::query()
            ->where('hospital_id', $hospitalId)
            ->where('bed_status_id', BedStatus::AVAILABLE)
            ->with(['room', 'room.ward', 'bedType']);

        if ($wardId) {
            $query->whereHas('room', function ($q) use ($wardId) {
                $q->where('ward_id', $wardId);
            });
        }

        if ($bedTypeId) {
            $query->where('bed_type_id', $bedTypeId);
        }

        return $query->first();
    }

    /**
     * Check if a specific bed is available.
     */
    public function isBedAvailable($bedId): bool
    {
        $bed = Bed::find($bedId);
        return $bed && $bed->bed_status_id == BedStatus::AVAILABLE;
    }

    /**
     * Get bed occupancy history for analytics.
     */
    public function getBedOccupancyHistory($hospitalId, $days = 30)
    {
        $startDate = Carbon::now()->subDays($days);

        return BedAllocation::where('hospital_id', $hospitalId)
            ->where('admission_date', '>=', $startDate)
            ->with(['patient', 'bed'])
            ->get();
    }
}
