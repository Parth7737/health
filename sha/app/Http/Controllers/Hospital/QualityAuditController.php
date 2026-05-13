<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Hospitals, AuditList, AuditSubCategory, AuditCategory, HospitalQualityAudit};

class QualityAuditController extends Controller
{
    public function index($uuid) {
        $hospital = Hospitals::where('uuid', base64_decode($uuid))->first();
        $auditcategory = AuditCategory::get();
        return view('hospital.qualityaudit.index', compact('hospital', 'auditcategory'));
    }

    public function loadstep(Request $request, $uuid, $hospital_id) {
        $hospital = Hospitals::where('uuid', base64_decode($uuid))->first();
        $validatedData = $request->validate([
            'step' => 'required',
            'id' => 'required'
        ]);

        if($request->step == 1) {
            $auditcategory = AuditCategory::get();
            return view('hospital.qualityaudit._partials.dashboard', compact('auditcategory','uuid', 'hospital_id', 'hospital'));
        } else if($request->step == 2 || $request->step == 3 || $request->step == 4 || $request->step == 5 || $request->step == 6) {
            $auditcategory = AuditCategory::where('id', $request->id)->first();
            return view('hospital.qualityaudit._partials.auditlist', compact('auditcategory','uuid', 'hospital_id', 'hospital'));
        } 
    }

    public function saveQualityAudit(Request $request, $uuid, $hospital_id) {
        $hospital = Hospitals::where('uuid', base64_decode($uuid))->first();
        $validatedData = $request->validate([
            'categoryid' => 'required',
        ]);
        $auditcategory = AuditCategory::where('id', $request->categoryid)->first();
        $rules = [];
        $messages = [];
        foreach(@$auditcategory->auditSubCategories as $key => $value) {
            foreach($value->auditlist as $k => $v) {
                if($v->is_required) {
                    $rules['audit_'.$v->category_id.'_'.$v->sub_category_id.'_'.$v->id] = 'required';
                    $messages['audit_'.$v->category_id.'_'.$v->sub_category_id.'_'.$v->id] = 'This field is Required';
                }
            }
        }

        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // Format the errors for AJAX response
            $errors = $validator->errors();

            return response()->json([
                'message' => $errors->first(), // Get the first error message
                'errors' => $errors->messages() // Get all errors keyed by input field
            ], 422);
        }

        foreach(@$auditcategory->auditSubCategories as $key => $value) {
            foreach($value->auditlist as $k => $v) {
                if($request->{'audit_'.$v->category_id.'_'.$v->sub_category_id.'_'.$v->id}) {
                    $hospital->qualityAudit()->updateOrCreate([
                        'hospital_id' => $hospital->id,
                        'category_id' => $v->category_id,
                        'sub_category_id' => $v->sub_category_id,
                        'audit_id' => $v->id,
                        'year' => date('Y'),
                        'month' => date('m'),
                    ],[
                        'hospital_id' => $hospital->id,
                        'category_id' => $v->category_id,
                        'sub_category_id' => $v->sub_category_id,
                        'audit_id' => $v->id,
                        'year' => date('Y'),
                        'month' => date('m'),
                        'action' => $request->{'audit_'.$v->category_id.'_'.$v->sub_category_id.'_'.$v->id},
                        'submitted_date' => date('Y-m-d'),
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => $auditcategory->name.' Submitted Successfully!!']);
    }
}
