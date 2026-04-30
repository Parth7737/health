<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\MedicineInstruction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class MedicineInstructionsController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-medicine-instructions', ['only' => ['store']]);
        $this->middleware('permission:edit-medicine-instructions', ['only' => ['update']]);
        $this->middleware('permission:delete-medicine-instructions', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.pharmacy.medicine-instructions.destroy', ['medicine_instruction' => '__MEDICINE_INSTRUCTION__']),
            'store'     => route('hospital.settings.pharmacy.medicine-instructions.store'),
            'loadtable' => route('hospital.settings.pharmacy.medicine-instructions-load'),
            'showform'  => route('hospital.settings.pharmacy.medicine-instructions.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.pharmacy.medicine-instructions.index', [
            'pathurl' => 'medicine-instructions',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = MedicineInstruction::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.pharmacy.medicine-instructions.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = MedicineInstruction::where('id', $id)->first();
        }
        return view('hospital.settings.pharmacy.medicine-instructions.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'instruction' => 'required|string|max:1000|unique:medicine_instructions,instruction,' . $request->id . ',id,hospital_id,' . $this->hospital_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        MedicineInstruction::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'instruction' => $request->instruction,
            ]
        );

        $msg = $request->id ? 'Medicine Instruction updated successfully.' : 'Medicine Instruction created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(MedicineInstruction $medicine_instruction)
    {
        if ($medicine_instruction->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $medicine_instruction->delete();
        return response()->json(['status' => true, 'message' => 'Medicine Instruction deleted successfully.']);
    }
}
