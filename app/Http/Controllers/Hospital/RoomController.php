<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\Floor;
use App\Models\Bed;
use App\Models\BedType;
use App\Models\Room;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RoomController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-room', ['only' => ['store']]);
        $this->middleware('permission:edit-room', ['only' => ['store']]);
        $this->middleware('permission:delete-room', ['only' => ['destroy']]);

        $this->routes = [
            'destroy' => route('hospital.settings.beds.room.destroy', ['room' => '__ROOM__']),
            'store' => route('hospital.settings.beds.room.store'),
            'loadtable' => route('hospital.settings.beds.room-load'),
            'showform' => route('hospital.settings.beds.room.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.beds.room.index', [
            'pathurl' => 'room',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = Room::query()
            ->where('hospital_id', $this->hospital_id)
            ->with(['ward:id,ward_name,floor_id', 'ward.floor:id,name,building_id', 'ward.floor.building:id,building_name']);

        return DataTables::of($data)
            ->addColumn('ward_name', function ($row) {
                return $row->ward?->ward_name ?? '-';
            })
            ->addColumn('floor_name', function ($row) {
                return $row->ward?->floor?->building?->building_name . ' - ' . $row->ward?->floor?->name ?? '-';
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.beds.room.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['is_active', 'actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $isBulk = !$id && $request->boolean('bulk');
        $data = null;

        if ($id) {
            $data = Room::where('hospital_id', $this->hospital_id)
                ->where('id', $id)
                ->first();
        }

        $wards = Ward::where('hospital_id', $this->hospital_id)
            ->with(['floor.building:id,building_name'])
            ->orderBy('ward_name')
            ->get(['id', 'ward_name', 'floor_id']);

        $bedTypes = BedType::query()
            ->where('is_active', true)
            ->orderBy('type_name')
            ->get(['id', 'type_name']);

        return view('hospital.settings.beds.room.form', compact('id', 'data', 'wards', 'isBulk', 'bedTypes'));
    }

    public function store(Request $request)
    {
        $isBulk = !$request->id && $request->boolean('is_bulk');
        $generateBeds = !$request->id && $request->boolean('generate_beds');

        $validator = Validator::make($request->all(), [
            'ward_id' => 'required|integer|exists:wards,id',
            'room_number' => $isBulk ? 'nullable' : 'required|string|max:50',
            'room_code' => $isBulk ? 'nullable' : 'required|string|max:100',
            'room_code_prefix' => $isBulk ? 'required|string|max:20' : 'nullable',
            'bulk_from' => $isBulk ? 'required|integer|min:1' : 'nullable',
            'bulk_to' => $isBulk ? 'required|integer|gte:bulk_from|max:10000' : 'nullable',
            'bed_capacity' => 'required|integer|min:1|max:50',
            'bed_type_id' => $generateBeds ? 'required|integer|exists:bed_types,id' : 'nullable',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'generate_beds' => 'nullable|boolean',
        ]);

        $validator->after(function ($validator) use ($request, $isBulk) {
            $validWard = Ward::where('hospital_id', $this->hospital_id)
                ->where('id', $request->ward_id)
                ->exists();
            if (!$validWard) {
                $validator->errors()->add('ward_id', 'Selected ward is invalid.');
            }

            if ($isBulk) {
                $rangeCount = ((int) $request->bulk_to - (int) $request->bulk_from) + 1;
                if ($rangeCount > 200) {
                    $validator->errors()->add('bulk_to', 'You can create maximum 200 rooms in one bulk operation.');
                }

                $prefix = strtoupper(trim((string) $request->room_code_prefix));
                for ($number = (int) $request->bulk_from; $number <= (int) $request->bulk_to; $number++) {
                    $candidateCode = $prefix . '-' . str_pad((string) $number, 3, '0', STR_PAD_LEFT);
                    $existsCode = Room::where('hospital_id', $this->hospital_id)
                        ->where('room_code', $candidateCode)
                        ->exists();
                    if ($existsCode) {
                        $validator->errors()->add('bulk_from', 'Room code already exists in selected range: ' . $candidateCode);
                        break;
                    }
                }
            } else {
                $exists = Room::where('hospital_id', $this->hospital_id)
                    ->where('room_code', trim((string) $request->room_code))
                    ->when($request->id, function ($q) use ($request) {
                        return $q->where('id', '!=', $request->id);
                    })
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('room_code', 'Room code already exists.');
                }
            }

            if ($request->id) {
                $existingBeds = Bed::where('hospital_id', $this->hospital_id)
                    ->where('room_id', $request->id)
                    ->count();

                if ((int) $request->bed_capacity < $existingBeds) {
                    $validator->errors()->add(
                        'bed_capacity',
                        'Bed capacity cannot be less than current beds (' . $existingBeds . ').'
                    );
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        if ($isBulk && !$request->id) {
            $createdRooms = 0;
            $createdBeds = 0;

            DB::transaction(function () use ($request, &$createdRooms, &$createdBeds, $generateBeds) {
                $prefix = strtoupper(trim((string) $request->room_code_prefix));

                for ($number = (int) $request->bulk_from; $number <= (int) $request->bulk_to; $number++) {
                    $room = Room::create([
                        'hospital_id' => $this->hospital_id,
                        'ward_id' => (int) $request->ward_id,
                        'room_number' => (string) $number,
                        'room_code' => $prefix . '-' . str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                        'bed_capacity' => (int) $request->bed_capacity,
                        'notes' => $request->notes,
                        'is_active' => $request->boolean('is_active', true),
                    ]);
                    $createdRooms++;

                    if ($generateBeds) {
                        for ($index = 1; $index <= (int) $request->bed_capacity; $index++) {
                            Bed::create([
                                'hospital_id' => $this->hospital_id,
                                'room_id' => $room->id,
                                'bed_type_id' => (int) $request->bed_type_id,
                                'bed_status_id' => 1,
                                'bed_number' => (string) $index,
                                'bed_code' => strtoupper($room->room_code) . '-B' . $index,
                                'notes' => 'Auto-generated bed',
                            ]);
                            $createdBeds++;
                        }
                    }
                }
            });

            $message = $createdRooms . ' rooms created successfully.';
            if ($createdBeds > 0) {
                $message .= ' ' . $createdBeds . ' beds auto-generated.';
            }

            return response()->json(['status' => true, 'message' => $message]);
        }

        $room = Room::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'ward_id' => (int) $request->ward_id,
                'room_number' => trim((string) $request->room_number),
                'room_code' => strtoupper(trim((string) $request->room_code)),
                'bed_capacity' => (int) $request->bed_capacity,
                'notes' => $request->notes,
                'is_active' => $request->boolean('is_active', true),
            ]
        );

        if (!$request->id && $generateBeds) {
            for ($index = 1; $index <= (int) $request->bed_capacity; $index++) {
                Bed::create([
                    'hospital_id' => $this->hospital_id,
                    'room_id' => $room->id,
                    'bed_type_id' => (int) $request->bed_type_id,
                    'bed_status_id' => 1,
                    'bed_number' => (string) $index,
                    'bed_code' => strtoupper($room->room_code) . '-B' . $index,
                    'notes' => 'Auto-generated bed',
                ]);
            }
        }

        $msg = $request->id ? 'Room updated successfully.' : 'Room created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(Room $room)
    {
        if ($room->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $room->delete();

        return response()->json(['status' => true, 'message' => 'Room deleted successfully.']);
    }
}
