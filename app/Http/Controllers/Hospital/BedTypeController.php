<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\BedType;
use App\Models\ChargeMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class BedTypeController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-bed-type', ['only' => ['store']]);
        $this->middleware('permission:edit-bed-type', ['only' => ['store']]);
        $this->middleware('permission:delete-bed-type', ['only' => ['destroy']]);

        $this->routes = [
            'destroy' => route('hospital.settings.beds.bed-type.destroy', ['bed_type' => '__BED_TYPE__']),
            'store' => route('hospital.settings.beds.bed-type.store'),
            'loadtable' => route('hospital.settings.beds.bed-type-load'),
            'showform' => route('hospital.settings.beds.bed-type.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.beds.bed-type.index', [
            'pathurl' => 'bed-type',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = BedType::query()
            ->with(['chargeMaster:id,code,name'])
            ->withCount('beds');

        return DataTables::of($data)
            ->addColumn('charge_master_label', function ($row) {
                if (!$row->chargeMaster) {
                    return '-';
                }

                return $row->chargeMaster->name . ' (' . $row->chargeMaster->code . ')';
            })
            ->addColumn('status_badge', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.beds.bed-type.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = null;

        if ($id) {
            $data = BedType::where('id', $id)->first();
        }

        $selectedChargeMasterId = $data?->charge_master_id;
        $chargeMasters = ChargeMaster::query()
            ->where(function ($query) use ($selectedChargeMasterId) {
                $query->where('is_active', true);
                if ($selectedChargeMasterId) {
                    $query->orWhere('id', $selectedChargeMasterId);
                }
            })
            ->whereIn('category', ['bed_charge', 'general'])
            ->withCount('tpaRates')
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'standard_rate', 'category']);

        return view('hospital.settings.beds.bed-type.form', compact('id', 'data', 'chargeMasters'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'charge_master_id' => 'required|integer|exists:charge_masters,id',
            'is_active' => 'nullable|boolean',
        ]);

        $validator->after(function ($validator) use ($request) {
            $exists = BedType::where('type_name', trim((string) $request->type_name))
                ->when($request->id, function ($q) use ($request) {
                    return $q->where('id', '!=', $request->id);
                })
                ->exists();

            if ($exists) {
                $validator->errors()->add('type_name', 'Bed type with this name already exists.');
            }

            if ($request->id) {
                $ownedBedType = BedType::where('id', $request->id)->exists();
                if (!$ownedBedType) {
                    $validator->errors()->add('id', 'Invalid bed type selected.');
                }
            }

            $chargeMaster = ChargeMaster::where('id', $request->charge_master_id)->first();
            if (!$chargeMaster) {
                $validator->errors()->add('charge_master_id', 'Selected charge master is invalid.');
            } elseif (!in_array($chargeMaster->category, ['bed_charge', 'general'], true)) {
                $validator->errors()->add('charge_master_id', 'Selected charge master category is not valid for bed types.');
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $chargeMaster = ChargeMaster::where('id', $request->charge_master_id)->first();
        $baseCharge = (float) ($chargeMaster?->standard_rate ?? 0);

        BedType::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'type_name' => trim((string) $request->type_name),
                'description' => $request->description,
                'charge_master_id' => $chargeMaster?->id,
                'base_charge' => $baseCharge,
                'is_active' => $request->boolean('is_active', true),
            ]
        );

        $msg = $request->id ? 'Bed Type updated successfully.' : 'Bed Type created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(BedType $bed_type)
    {
        if ($bed_type->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        if ($bed_type->beds()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'This bed type is already assigned to beds and cannot be deleted.',
            ], 422);
        }

        $bed_type->delete();

        return response()->json(['status' => true, 'message' => 'Bed Type deleted successfully.']);
    }
}