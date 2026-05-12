<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\BaseHospitalController;
use App\Models\HrTrainingCategory;
use App\CentralLogics\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class HrTrainingCategoryController extends BaseHospitalController
{
    public array $routes = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:create-hr-training-category', ['only' => ['store']]);
        $this->middleware('permission:edit-hr-training-category', ['only' => ['update']]);
        $this->middleware('permission:delete-hr-training-category', ['only' => ['destroy']]);
        $this->routes = [
            'destroy' => route('hospital.settings.hr.training-category.destroy', ['training_category' => '__TYPE__']),
            'store' => route('hospital.settings.hr.training-category.store'),
            'loadtable' => route('hospital.settings.hr.training-category-load'),
            'showform' => route('hospital.settings.hr.training-category.showform'),
        ];
    }

    public function index()
    {
        return view('hospital.settings.hr.training-category.index', [
            'pathurl' => 'hr-training-category',
            'routes' => $this->routes,
        ]);
    }

    public function loaddata(Request $request)
    {
        $data = HrTrainingCategory::query()->select('*')->orderBy('sort_order')->orderBy('name');
        return DataTables::of($data)
            ->editColumn('description', fn ($row) => \Illuminate\Support\Str::limit((string) ($row->description ?? ''), 100))
            ->editColumn('is_active', fn ($row) => $row->is_active ? 'Yes' : 'No')
            ->addColumn('actions', function ($row) {
                return view('hospital.settings.hr.training-category.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showform(Request $request)
    {
        $id = $request->input('id');
        $data = '';
        if ($id) {
            $data = HrTrainingCategory::query()->where('id', $id)->first();
        }

        return view('hospital.settings.hr.training-category.form', compact('data', 'id'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150|unique:hr_training_categories,name,' . $request->input('id') . ',id,hospital_id,' . $this->hospital_id,
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        HrTrainingCategory::query()->updateOrCreate(
            ['id' => $request->input('id')],
            [
                'hospital_id' => $this->hospital_id,
                'name' => $request->string('name')->toString(),
                'description' => $request->filled('description') ? $request->string('description')->toString() : null,
                'sort_order' => (int) $request->input('sort_order', 0),
                'is_active' => $request->boolean('is_active', true),
            ]
        );

        $msg = $request->input('id') ? 'Training category updated successfully.' : 'Training category created successfully.';

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function update(Request $request)
    {
        return $this->store($request);
    }

    public function destroy(HrTrainingCategory $training_category)
    {
        if ((int) $training_category->hospital_id !== (int) $this->hospital_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        if (Schema::hasTable('hr_training_programs')) {
            $inUse = $training_category->programs()->exists();
            if ($inUse) {
                return response()->json([
                    'status' => false,
                    'message' => 'This category is assigned to one or more training programmes and cannot be deleted.',
                ], 422);
            }
        }

        $training_category->delete();

        return response()->json(['status' => true, 'message' => 'Training category deleted successfully.']);
    }
}
