<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\BedAllocation;
use App\Models\Bed;
use App\Models\Patient;
use App\Services\BedAllocationService;
use App\Services\BedAvailabilityService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BedAllocationController extends BaseHospitalController
{
    protected $allocationService;
    protected $availabilityService;
    public $routes = [];

    public function __construct(BedAllocationService $allocationService, BedAvailabilityService $availabilityService)
    {
        parent::__construct();
        $this->allocationService = $allocationService;
        $this->availabilityService = $availabilityService;
    }

    /**
     * Display all bed allocations.
     */
    public function index()
    {
        return view('hospital.bed-allocation.index', ['pathurl' => 'bed-allocation', 'routes' => $this->routes]);
    }

    /**
     * Load active allocations for DataTable.
     */
    public function loaddata(Request $request)
    {
        $allocations = BedAllocation::where('hospital_id', auth()->user()->hospital_id)
            ->whereNull('discharge_date')
            ->with(['patient', 'bed', 'bed.room', 'bed.room.ward', 'admittedBy'])
            ->orderBy('admission_date', 'desc')
            ->get();

        return DataTables::of($allocations)
            ->addIndexColumn()
            ->addColumn('patient_id', function ($allocation) {
                return $allocation->patient->patient_id ?? 'N/A';
            })
            ->addColumn('patient_name', function ($allocation) {
                return $allocation->patient->name ?? 'N/A';
            })
            ->addColumn('bed_identifier', function ($allocation) {
                return $allocation->bed->getFullBedIdentifier();
            })
            ->addColumn('ward', function ($allocation) {
                return $allocation->getWardIdentifier();
            })
            ->addColumn('los', function ($allocation) {
                return $allocation->getLengthOfStay() . ' दिन';
            })
            ->addColumn('admission_type', function ($allocation) {
                return ucfirst($allocation->admission_type);
            })
            ->addColumn('action', function ($allocation) {
                $actions = '<button class="btn btn-sm btn-info view-btn" data-id="' . $allocation->id . '">विवरण</button> ';
                $actions .= '<button class="btn btn-sm btn-warning transfer-btn" data-id="' . $allocation->id . '">स्थानांतरण</button> ';
                $actions .= '<button class="btn btn-sm btn-danger discharge-btn" data-id="' . $allocation->id . '">डिस्चार्ज</button>';
                return $actions;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show active admission form.
     */
    public function createAllocation()
    {
        return view('hospital.bed-allocation.create-allocation');
    }

    /**
     * Allocate a bed to a patient.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'bed_id' => 'required|exists:beds,id',
            'admission_type' => 'required|string',
            'admission_notes' => 'nullable|string',
        ]);

        try {
            $allocation = $this->allocationService->allocateBed(
                auth()->user()->hospital_id,
                $request->patient_id,
                $request->bed_id,
                auth()->id(),
                $request->admission_type,
                $request->admission_notes
            );

            return response()->json([
                'message' => 'रोगी को बेड आवंटित किया गया / Patient allocated to bed successfully',
                'allocation' => $allocation
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Display allocation details.
     */
    public function show(BedAllocation $allocation)
    {
        return response()->json($allocation->load([
            'patient', 'bed', 'bed.room', 'bed.room.ward',
            'admittedBy', 'dischargedBy'
        ]));
    }

    /**
     * Transfer patient to another bed.
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'allocation_id' => 'required|exists:bed_allocations,id',
            'new_bed_id' => 'required|exists:beds,id',
            'transfer_reason' => 'nullable|string',
        ]);

        try {
            $allocation = $this->allocationService->transferBed(
                auth()->user()->hospital_id,
                $request->allocation_id,
                $request->new_bed_id,
                auth()->id(),
                $request->transfer_reason
            );

            return response()->json([
                'message' => 'रोगी को नए बेड पर स्थानांतरित किया गया / Patient transferred successfully',
                'allocation' => $allocation
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Discharge a patient.
     */
    public function discharge(Request $request)
    {
        $request->validate([
            'allocation_id' => 'required|exists:bed_allocations,id',
            'discharge_status' => 'required|string',
            'discharge_notes' => 'nullable|string',
        ]);

        try {
            $allocation = $this->allocationService->dischargeBed(
                $request->allocation_id,
                auth()->id(),
                $request->discharge_status,
                $request->discharge_notes
            );

            return response()->json([
                'message' => 'रोगी को डिस्चार्ज किया गया / Patient discharged successfully',
                'allocation' => $allocation
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Get patient's current allocation.
     */
    public function getCurrentAllocation(Request $request)
    {
        $request->validate(['patient_id' => 'required|exists:patients,id']);

        $allocation = $this->allocationService->getPatientCurrentAllocation(
            $request->patient_id,
            auth()->user()->hospital_id
        );

        if (!$allocation) {
            return response()->json(['message' => 'रोगी का कोई सक्रिय आवंटन नहीं है / No active allocation found'], 404);
        }

        return response()->json($allocation->load(['bed', 'bed.room', 'bed.room.ward']));
    }

    /**
     * Get patient's allocation history.
     */
    public function getHistory(Request $request)
    {
        $request->validate(['patient_id' => 'required|exists:patients,id']);

        $allocations = $this->allocationService->getPatientAllocationHistory(
            $request->patient_id,
            auth()->user()->hospital_id
        );

        return response()->json($allocations);
    }

    /**
     * Get available beds for allocation.
     */
    public function getAvailableBeds(Request $request)
    {
        $filters = [
            'ward_type' => $request->ward_type,
            'bed_type_id' => $request->bed_type_id,
        ];

        $beds = $this->availabilityService->getAvailableBeds(auth()->user()->hospital_id, $filters);

        return response()->json($beds->map(function ($bed) {
            return [
                'id' => $bed->id,
                'identifier' => $bed->getFullBedIdentifier(),
                'bed_type' => $bed->bedType->type_name,
                'ward' => $bed->room->ward->ward_name,
                'room' => $bed->room->room_number,
            ];
        }));
    }

    /**
     * Load discharge requests (beds marked for discharge).
     */
    public function loadDischargeRequests(Request $request)
    {
        $allocations = BedAllocation::where('hospital_id', auth()->user()->hospital_id)
            ->where('discharge_status', 'reserved_for_discharge')
            ->whereNull('discharge_date')
            ->with(['patient', 'bed', 'bed.room.ward'])
            ->get();

        return DataTables::of($allocations)
            ->addIndexColumn()
            ->addColumn('patient_name', function ($allocation) {
                return $allocation->patient->name;
            })
            ->addColumn('bed', function ($allocation) {
                return $allocation->bed->getFullBedIdentifier();
            })
            ->addColumn('action', function ($allocation) {
                return '<button class="btn btn-sm btn-success confirm-btn" data-id="' . $allocation->id . '">पुष्टि & डिस्चार्ज</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
