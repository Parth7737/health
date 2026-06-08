<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\MedicineInteraction;
use App\Models\Medicine;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class MedicineInteractionController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-medicine-interaction', ['only' => ['store']]);
        $this->middleware('permission:delete-medicine-interaction', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.pharmacy.medicine-interaction.destroy', ['medicine_interaction' => '__MEDICINE_INTERACTION__']),
            'store'     => route('hospital.settings.pharmacy.medicine-interaction.store'),
            'loadtable' => route('hospital.settings.pharmacy.medicine-interaction-load'),
            'showform'  => route('hospital.settings.pharmacy.medicine-interaction.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.pharmacy.medicine-interaction.index', [
            'pathurl' => 'medicine-interaction',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = MedicineInteraction::with(['medicine', 'interactMedicine']);
        return DataTables::of($data)
            ->addColumn('medicine_name', function ($row) {
                return $row->medicine ? $row->medicine->name : 'N/A';
            })
            ->addColumn('interact_medicine_name', function ($row) {
                return $row->interactMedicine ? $row->interactMedicine->name : 'N/A';
            })
            ->addColumn('severity_badge', function ($row) {
                $badges = [
                    'minor' => 'badge bg-info',
                    'moderate' => 'badge bg-warning text-dark',
                    'major' => 'badge bg-danger',
                    'critical' => 'badge bg-dark text-white',
                ];
                $class = $badges[$row->severity] ?? 'badge bg-secondary';
                return '<span class="' . $class . '">' . ucfirst($row->severity) . '</span>';
            })
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.pharmacy.medicine-interaction.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['severity_badge', 'actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = MedicineInteraction::where('id', $id)->first();
        }
        $medicines = Medicine::orderBy('name')->get();
        return view('hospital.settings.pharmacy.medicine-interaction.form', compact('data', 'id', 'medicines'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'medicine_id' => 'required|exists:medicines,id',
            'interact_medicine_id' => 'required|exists:medicines,id|different:medicine_id',
            'severity' => 'required|in:minor,moderate,major,critical',
            'clinical_effect' => 'nullable|string',
            'recommendation' => 'nullable|string',
        ], [
            'interact_medicine_id.different' => 'Interacting medicine must be different from the primary medicine.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        // Check if interaction already exists (bidirectionally or unidirectionally)
        $exists = MedicineInteraction::where(function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $q->where('medicine_id', $request->medicine_id)
                  ->where('interact_medicine_id', $request->interact_medicine_id);
            })->orWhere(function ($q) use ($request) {
                $q->where('medicine_id', $request->interact_medicine_id)
                  ->where('interact_medicine_id', $request->medicine_id);
            });
        })
        ->when($request->id, function ($query) use ($request) {
            $query->where('id', '!=', $request->id);
        })
        ->exists();

        if ($exists) {
            return response()->json(['errors' => [
                ['code' => 'interact_medicine_id', 'message' => 'An interaction between these two medicines already exists.']
            ]], 422);
        }

        MedicineInteraction::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'medicine_id' => $request->medicine_id,
                'interact_medicine_id' => $request->interact_medicine_id,
                'severity' => $request->severity,
                'clinical_effect' => $request->clinical_effect,
                'recommendation' => $request->recommendation,
            ]
        );

        $msg = $request->id ? 'Drug Interaction updated successfully.' : 'Drug Interaction created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(MedicineInteraction $medicineInteraction)
    {
        if ($medicineInteraction->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $medicineInteraction->delete();
        return response()->json(['status' => true, 'message' => 'Drug Interaction deleted successfully.']);
    }
}
