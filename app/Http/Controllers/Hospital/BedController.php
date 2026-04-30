<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\Bed;
use App\Models\BedStatus;
use App\Models\BedType;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Milon\Barcode\Facades\DNS1DFacade;
use Yajra\DataTables\Facades\DataTables;

class BedController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-bed', ['only' => ['store']]);
        $this->middleware('permission:edit-bed', ['only' => ['store']]);
        $this->middleware('permission:edit-bed', ['only' => ['updateStatus']]);
        $this->middleware('permission:delete-bed', ['only' => ['destroy']]);

        $this->routes = [
            'destroy' => route('hospital.settings.beds.bed.destroy', ['bed' => '__BED__']),
            'store' => route('hospital.settings.beds.bed.store'),
            'dashboard' => route('hospital.settings.beds.bed-dashboard'),
            'loadtable' => route('hospital.settings.beds.bed-load'),
            'showform' => route('hospital.settings.beds.bed.showform'),
            'status' => route('hospital.settings.beds.bed.status', ['bed' => '__BED__']),
            'scan' => route('hospital.settings.beds.bed.scan'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.beds.bed.index', [
            'pathurl' => 'bed',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = Bed::query()
            ->where('hospital_id', $this->hospital_id)
            ->with([
                'room:id,room_number,ward_id',
                'room.ward:id,ward_name,floor_id',
                'room.ward.floor:id,name',
                'bedType:id,type_name',
                'bedStatus:id,status_name,color_code',
            ]);

        return DataTables::of($data)
            ->addColumn('ward_name', function ($row) {
                return $row->room?->ward?->ward_name ?? '-';
            })
            ->addColumn('room_number', function ($row) {
                return $row->room?->room_number ?? '-';
            })
            ->addColumn('bed_type', function ($row) {
                return $row->bedType?->type_name ?? '-';
            })
            ->addColumn('barcode', function ($row) {
                $barcodePng = DNS1DFacade::getBarcodePNG((string) $row->bed_code, 'C128', 1.6, 40);
                return '<div class="text-center">'
                    . '<img src="data:image/png;base64,' . $barcodePng . '" alt="barcode" style="max-width:140px;">'
                    . '<div class="small text-muted">' . e($row->bed_code) . '</div>'
                    . '</div>';
            })
            ->addColumn('status_badge', function ($row) {
                $status = $row->bedStatus;
                if (!$status) {
                    return '<span class="badge bg-secondary">Unknown</span>';
                }

                return '<span class="badge" style="background:' . e($status->color_code) . ';">' . e($status->status_name) . '</span>';
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.beds.bed.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['barcode', 'status_badge', 'actions'])
            ->make(true);
    }

    public function dashboard()
    {
        $beds = Bed::query()
            ->where('hospital_id', $this->hospital_id)
            ->with([
                'room:id,room_number,ward_id',
                'room.ward:id,ward_name,floor_id',
                'room.ward.floor:id,name',
                'bedType:id,type_name',
                'bedStatus:id,status_name,color_code',
            ])
            ->orderBy('room_id')
            ->orderBy('bed_number')
            ->get();

        $statusCounts = Bed::query()
            ->where('hospital_id', $this->hospital_id)
            ->select('bed_status_id', DB::raw('COUNT(*) as total'))
            ->groupBy('bed_status_id')
            ->pluck('total', 'bed_status_id');

        $statuses = BedStatus::query()->orderBy('id')->get(['id', 'status_name', 'color_code']);

        return view('hospital.settings.beds.bed.dashboard', [
            'beds' => $beds,
            'statuses' => $statuses,
            'statusCounts' => $statusCounts,
            'routes' => $this->routes,
        ]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $isBulk = !$id && $request->boolean('bulk');
        $data = null;

        if ($id) {
            $data = Bed::where('hospital_id', $this->hospital_id)
                ->where('id', $id)
                ->with('bedStatus:id,status_name,color_code')
                ->first();
        }

        $rooms = Room::where('hospital_id', $this->hospital_id)
            ->withCount('beds')
            ->orderBy('room_number')
            ->get(['id', 'room_number', 'room_code', 'bed_capacity']);

        $selectedBedTypeId = $data?->bed_type_id;
        $bedTypes = BedType::query()
            ->where(function ($query) use ($selectedBedTypeId) {
                $query->where('is_active', true);
                if ($selectedBedTypeId) {
                    $query->orWhere('id', $selectedBedTypeId);
                }
            })
            ->orderBy('type_name')
            ->get(['id', 'type_name']);
        return view('hospital.settings.beds.bed.form', compact('id', 'data', 'rooms', 'bedTypes', 'isBulk'));
    }

    public function store(Request $request)
    {
        $isBulk = !$request->id && $request->boolean('is_bulk');

        $validator = Validator::make($request->all(), [
            'room_id' => 'required|integer|exists:rooms,id',
            'bed_type_id' => 'required|integer|exists:bed_types,id',
            'bed_number' => $isBulk ? 'nullable' : 'required|string|max:50',
            'bulk_from' => $isBulk ? 'required|integer|min:1' : 'nullable',
            'bulk_to' => $isBulk ? 'required|integer|gte:bulk_from|max:10000' : 'nullable',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validator->after(function ($validator) use ($request, $isBulk) {
            $room = Room::where('hospital_id', $this->hospital_id)
                ->where('id', (int) $request->room_id)
                ->first();

            if (!$room) {
                $validator->errors()->add('room_id', 'Selected room is invalid.');
                return;
            }

            $validBedType = BedType::where('id', $request->bed_type_id)->exists();
            if (!$validBedType) {
                $validator->errors()->add('bed_type_id', 'Selected bed type is invalid.');
            }

            $currentBedCount = Bed::where('hospital_id', $this->hospital_id)
                ->where('room_id', $room->id)
                ->when($request->id, function ($q) use ($request) {
                    return $q->where('id', '!=', $request->id);
                })
                ->count();

            if ($isBulk) {
                $rangeCount = ((int) $request->bulk_to - (int) $request->bulk_from) + 1;
                if ($rangeCount > 300) {
                    $validator->errors()->add('bulk_to', 'You can create maximum 300 beds in one bulk operation.');
                }

                if (($currentBedCount + $rangeCount) > (int) $room->bed_capacity) {
                    $validator->errors()->add('bulk_to', 'Room capacity exceeded. Please adjust room capacity or reduce range.');
                }

                $duplicates = Bed::where('hospital_id', $this->hospital_id)
                    ->where('room_id', $room->id)
                    ->whereBetween('bed_number', [(string) $request->bulk_from, (string) $request->bulk_to])
                    ->count();
                if ($duplicates > 0) {
                    $validator->errors()->add('bulk_from', 'Some bed numbers in this range already exist for selected room.');
                }
            } else {
                if (($currentBedCount + 1) > (int) $room->bed_capacity) {
                    $validator->errors()->add('bed_number', 'Room capacity exceeded for selected room.');
                }

                $duplicateNumber = Bed::where('hospital_id', $this->hospital_id)
                    ->where('room_id', $room->id)
                    ->where('bed_number', trim((string) $request->bed_number))
                    ->when($request->id, function ($q) use ($request) {
                        return $q->where('id', '!=', $request->id);
                    })
                    ->exists();

                if ($duplicateNumber) {
                    $validator->errors()->add('bed_number', 'Bed number already exists in selected room.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $room = Room::where('hospital_id', $this->hospital_id)
            ->where('id', (int) $request->room_id)
            ->first();

        if ($isBulk && !$request->id) {
            $created = 0;
            DB::transaction(function () use ($request, $room, &$created) {
                for ($number = (int) $request->bulk_from; $number <= (int) $request->bulk_to; $number++) {
                    $bedNumber = (string) $number;
                    $bedCode = $this->generateUniqueBedCode($room, $bedNumber);

                    Bed::create([
                        'hospital_id' => $this->hospital_id,
                        'room_id' => (int) $request->room_id,
                        'bed_type_id' => (int) $request->bed_type_id,
                        'bed_status_id' => BedStatus::AVAILABLE,
                        'bed_number' => $bedNumber,
                        'bed_code' => $bedCode,
                        'notes' => $request->notes,
                    ]);

                    $created++;
                }
            });

            return response()->json([
                'status' => true,
                'message' => $created . ' beds created successfully.',
            ]);
        }

        $existingBed = $request->id
            ? Bed::where('hospital_id', $this->hospital_id)->where('id', $request->id)->first()
            : null;

        $bedStatusId = $existingBed?->bed_status_id ?? BedStatus::AVAILABLE;
        $bedNumber = trim((string) $request->bed_number);
        $bedCode = $existingBed?->bed_code ?: $this->generateUniqueBedCode($room, $bedNumber, $existingBed?->id);

        Bed::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'room_id' => (int) $request->room_id,
                'bed_type_id' => (int) $request->bed_type_id,
                'bed_status_id' => (int) $bedStatusId,
                'bed_number' => $bedNumber,
                'bed_code' => $bedCode,
                'notes' => $request->notes,
            ]
        );

        $msg = $request->id ? 'Bed updated successfully.' : 'Bed created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function updateStatus(Request $request, Bed $bed)
    {
        if ((int) $bed->hospital_id !== (int) $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'bed_status_id' => 'required|integer|exists:bed_statuses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        // Prevent status change for Occupied and Reserved for Discharge beds
        if (in_array((int) $bed->bed_status_id, [BedStatus::OCCUPIED, BedStatus::RESERVED_FOR_DISCHARGE], true)) {
            return response()->json([
                'status' => false,
                'message' => 'Status cannot be changed for ' . strtolower($bed->bedStatus?->status_name ?? 'this') . ' beds.',
            ], 422);
        }

        if ((int) $request->bed_status_id === BedStatus::AVAILABLE && $bed->currentAllocation()) {
            return response()->json([
                'status' => false,
                'message' => 'This bed has an active allocation and cannot be set to Available manually.',
            ], 422);
        }

        $bed->update(['bed_status_id' => (int) $request->bed_status_id]);

        return response()->json(['status' => true, 'message' => 'Bed status updated successfully.']);
    }

    public function scanByBarcode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barcode' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $bed = Bed::query()
            ->where('hospital_id', $this->hospital_id)
            ->where('bed_code', strtoupper(trim((string) $request->barcode)))
            ->with([
                'room:id,room_number,ward_id',
                'room.ward:id,ward_name,floor_id',
                'room.ward.floor:id,name',
                'bedType:id,type_name',
                'bedStatus:id,status_name,color_code',
            ])
            ->first();

        if (!$bed) {
            return response()->json(['status' => false, 'message' => 'Bed not found for scanned barcode.'], 404);
        }

        $bedResponse = [
            'id' => $bed->id,
            'bed_code' => $bed->bed_code,
            'bed_number' => $bed->bed_number,
            'room' => $bed->room?->room_number,
            'ward' => $bed->room?->ward?->ward_name,
            'floor' => $bed->room?->ward?->floor?->name,
            'type' => $bed->bedType?->type_name,
            'status' => $bed->bedStatus?->status_name,
            'status_color' => $bed->bedStatus?->color_code,
            'patient' => null,
        ];

        // Add patient details if bed is occupied
        $currentAllocation = $bed->currentAllocation();
        if ($currentAllocation && $currentAllocation->patient) {
            $bedResponse['patient'] = [
                'name' => $currentAllocation->patient?->name ?? '-',
                'uhid' => $currentAllocation->patient?->patient_id ?? '-',
                'admission_no' => $currentAllocation->admission_no ?? '-',
                'admission_date' => optional($currentAllocation->admission_date)->format('d-m-Y H:i') ?? '-',
                'consultant' => $currentAllocation->consultantDoctor?->full_name ?? '-',
                'department' => $currentAllocation->department?->name ?? '-',
            ];
        }

        return response()->json([
            'status' => true,
            'bed' => $bedResponse,
        ]);
    }

    public function barcode(Bed $bed)
    {
        if ((int) $bed->hospital_id !== (int) $this->hospital_id) {
            abort(403, 'Unauthorized bed record.');
        }

        $barcodePng = DNS1DFacade::getBarcodePNG((string) $bed->bed_code, 'C128', 2, 60);

        return response(base64_decode($barcodePng), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    public function destroy(Bed $bed)
    {
        if ($bed->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $hasActiveAllocation = $bed->bedAllocations()->whereNull('discharge_date')->exists();
        if ($hasActiveAllocation || (int) $bed->bed_status_id === (int) BedStatus::OCCUPIED) {
            return response()->json([
                'status' => false,
                'message' => 'Occupied/allocated bed cannot be deleted. Please discharge/transfer first.',
            ], 200);
        }

        $bed->delete();

        return response()->json(['status' => true, 'message' => 'Bed deleted successfully.']);
    }

    protected function generateUniqueBedCode(Room $room, string $bedNumber, ?int $excludeBedId = null): string
    {
        $sanitized = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $bedNumber));
        $baseCode = strtoupper(trim((string) $room->room_code)) . '-B' . ($sanitized !== '' ? $sanitized : 'X');

        $code = $baseCode;
        $counter = 1;

        while (
            Bed::where('hospital_id', $this->hospital_id)
                ->where('bed_code', $code)
                ->when($excludeBedId, function ($q) use ($excludeBedId) {
                    return $q->where('id', '!=', $excludeBedId);
                })
                ->exists()
        ) {
            $counter++;
            $code = $baseCode . '-' . $counter;
        }

        return $code;
    }
}
