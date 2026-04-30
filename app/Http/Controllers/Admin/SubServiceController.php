<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ Service, SubService, SubServiceAction };
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\CentralLogics\Helpers;

class SubServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->hasRole('Master Admin')) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });

        $this->routes = [
            'destroy' => route('admin.sub-services.destroy', ['sub_service' => '__SUB_SERVICE__']),            
            'store'   => route('admin.sub-services.store'),   
            'loadtable'   => route('admin.load-sub-services'),
            'showform'   => route('admin.sub-services.showform'),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin-views.sub-service.index', ['pathurl' => 'sub-services', 'routes' => $this->routes]);
    }

    public function loaddata(Request $request)
    {
        $data = SubService::with('service');
        return DataTables::of($data)
            ->addColumn('actions', function ($row) {
                return view('admin-views.sub-service.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'service_id' => 'required',
            'type.*' => 'required',
            'label.*' => 'required',
        ])->sometimes('value.*', 'required', function ($input) {
            return in_array('radio', $input->type);
        });
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }
        if($request->id){
            $SubService = SubService::where('id', $request->id)->first();
        }else{
            $SubService = new SubService;
        }
        $SubService->name = $request->name;
        $SubService->service_id = $request->service_id;
        $SubService->is_required = $request->is_required ? 1 : 0;
        $SubService->save();
        
        $id = $SubService->id;

        if(sizeof($request->type) > 0) {
            if($request->id){
                $SubService->actions()->delete();
            }
            foreach ($request->type as $key => $value) {
                $action = new SubServiceAction;
                $action->type = $value;
                $action->value = $request->value[$key];
                $action->label = $request->label[$key];
                $action->is_text_input = isset($request->is_text_input[$key]) && $request->is_text_input[$key] == 'on' ? 1 : 0;
                $action->is_image = isset($request->is_image[$key]) && $request->is_image[$key] == 'on' ? 1 : 0;
                $action->sublabel = $request->sublabel[$key];
                $action->bed_count = isset($request->bed_count[$key]) && $request->bed_count[$key] == 'on' ? 1 : 0;
                $SubService->actions()->save($action);
            }
        }

        $msg = $request->id ?'Sub Service updated successfully.' : 'Sub Service created successfully.';
        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function showform(Request $request)
    {
        $id = $request->id;
        $data = '';
        if($id) {
            $data = SubService::where('id', $id)->first();
        }
        return view('admin-views.sub-service.form', compact('data', 'id'));
    }

    public function destroy(SubService $SubService)
    {
        $SubService->actions()->delete();
        $SubService->delete();
        return response()->json(['status' => true, 'message' => 'Sub Service Deleted Successfully.']);
    }
}
