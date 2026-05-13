<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Service, SubService, SubServiceAction, FacilitySpecialityType };
use Illuminate\Support\Facades\Validator;
use App\CentralLogics\Helpers;

class SubServiceController extends Controller
{
      /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subservice = SubService::with('service')->latest()->get();
        return view('admin-views.subservice.index', compact('subservice'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $service = Service::latest()->get();
        $types = FacilitySpecialityType::get();
        return view('admin-views.subservice.create', compact('service', 'types'));
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
            // 'value.*' => 'required',
            // // 'is_required' => 'required',
        ])->sometimes('value.*', 'required', function ($input) {
            return in_array('radio', $input->type);
        });
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $speciality = new SubService;
        $speciality->name = $request->name;
        $speciality->service_id = $request->service_id;
        $speciality->is_required = $request->is_required ? 1 : 0;
        $speciality->required_when = $request->required_when ? implode(',', $request->required_when) : '';
        $speciality->save();
        
        $id = $speciality->id;

        if(sizeof($request->type) > 0) {
            foreach ($request->type as $key => $value) {
                $action = new SubServiceAction;
                $action->type = $value;
                $action->value = $request->value[$key];
                $action->label = $request->label[$key];
                $action->is_text_input = isset($request->is_text_input[$key]) && $request->is_text_input[$key] == 'on' ? 1 : 0;
                $action->is_image = isset($request->is_image[$key]) && $request->is_image[$key] == 'on' ? 1 : 0;
                $action->bed_count = isset($request->bed_count[$key]) && $request->bed_count[$key] == 'on' ? 1 : 0;
                $action->sublabel = $request->sublabel[$key];
                $speciality->actions()->save($action);
            }
        }

        return response()->json(['success' => true, 'message'=>'Sub Service Added Successfully.'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(SubService $Service)
    {
        return response()->json(['data'=>$SubService], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {   
        $subservice = SubService::where('id', $id)->first();
        $service = Service::latest()->get();
        $types = FacilitySpecialityType::get();

        return view('admin-views.subservice.edit', compact('service', 'subservice', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'service_id' => 'required',
            'type.*' => 'required',
            'label.*' => 'required',
            // 'value.*' => 'required',
            // 'is_required' => 'required',
        ])->sometimes('value.*', 'required', function ($input) {
            return in_array('radio', $input->type);
        });
        
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $SubService = SubService::where('id', $id)->first();
        $SubService->name = $request->name;
        $SubService->service_id = $request->service_id;
        $SubService->is_required = $request->is_required ? 1 : 0;
        $SubService->required_when = ($SubService->is_required && $request->required_when) ? implode(',', $request->required_when) : '';
        $SubService->save();
        
        $id = $SubService->id;

        if(sizeof($request->type) > 0) {
            $SubService->actions()->delete();
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

        
        return response()->json(['success' => true, 'message'=>'Service Updated Successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubService $SubService)
    {
        $SubService->actions()->delete();
        $SubService->delete();
        return redirect()->back()->with('success','Service Deleted.');
    }
}
