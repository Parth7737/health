<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\AgeGroup;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Yajra\DataTables\Facades\DataTables;

class AgeGroupController extends BaseHospitalController
{
    public $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-pathology-age-group', ['only' => ['store']]);
        $this->middleware('permission:edit-pathology-age-group', ['only' => ['update']]);
        $this->middleware('permission:delete-pathology-age-group', ['only' => ['destroy']]);
        $this->routes = [
            'destroy'   => route('hospital.settings.pathology.age-group.destroy', ['age_group' => '__AGE_GROUP__']),
            'store'     => route('hospital.settings.pathology.age-group.store'),
            'loadtable' => route('hospital.settings.pathology.age-group-load'),
            'showform'  => route('hospital.settings.pathology.age-group.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.pathology.age-group.index', [
            'pathurl' => 'pathology-age-group',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = AgeGroup::select('*');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.pathology.age-group.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if ($id) {
            $data = AgeGroup::where('id', $id)->first();
        }
        return view('hospital.settings.pathology.age-group.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'from_age' => 'required|integer|min:0',
            'to_age' => 'required|integer|min:0',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->from_age > $request->to_age) {
                $validator->errors()->add('to_age', 'To age must be greater than or equal to from age.');
            }
            $exists = AgeGroup::where('hospital_id', $this->hospital_id)
                ->where('from_age', $request->from_age)
                ->where('to_age', $request->to_age)
                ->when($request->id, function ($q) use ($request) {
                    return $q->where('id', '!=', $request->id);
                })->exists();
            if ($exists) {
                $validator->errors()->add('from_age', 'Age group range already exists.');
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        AgeGroup::updateOrCreate(
            ['id' => $request->id],
            [
                'hospital_id' => $this->hospital_id,
                'title' => $request->title,
                'from_age' => $request->from_age,
                'to_age' => $request->to_age,
            ]
        );

        $msg = $request->id ? 'Age Group updated successfully.' : 'Age Group created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function destroy(AgeGroup $age_group)
    {
        if ($age_group->hospital_id != $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }
        $age_group->delete();
        return response()->json(['status' => true, 'message' => 'Age Group deleted successfully.']);
    }
}
