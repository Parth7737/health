<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Hospital, User, HospitalSpeciality, HospitalService, HospitalLicense, HospitalState};
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminHospitalEmpanelmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->hasRole('Master Admin')) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    public function createForm()
    {
        $empanelment_step_status = json_decode(Helpers::get_settings('empanelment_step_status'));
        $states = HospitalState::where('country_id', 101)->get();
        return view('admin-views.hospitals.create-form', compact('empanelment_step_status', 'states'));
    }

    public function storeCreateProfile(Request $request)
    {
        $user_id = $request->user_id??0;
        $hospital_id = $request->hospital_id??0;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'hospital_type' => 'required|in:Single,Multi-Branch',
            'hospital' => 'nullable',
            'state' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user_id,
            'mobile_no' => 'required|digits:10',
            'password' => 'required|string|min:6',
            'confirmation_password' => 'required|string|same:password',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $parentId = ($request->hospital_type === 'Multi-Branch' && $request->hospital) ? (int) $request->hospital : 0;
        
        if($hospital_id){
            $hospital = Hospital::find($hospital_id);
        }else{
            $hospital = new Hospital;
        }
        $hospital->parent_id = $parentId;
        $hospital->hospital_type = $request->hospital_type;
        $hospital->save();

        if($user_id){
            $user = User::find($user_id);
        }else{
            $user = new User;
            $user->uuid = Helpers::generateUUID();
            $enable_step = Helpers::get_settings('empanelment_step_status');
            $user->enable_step = $enable_step;
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->gender = $request->gender;
        $user->state = $request->state;
        $user->mobile_no = $request->mobile_no;
        $user->hospital_id = $hospital->id;
        if ($request->hasFile('avatar')) {
            $user->avatar = $request->file('avatar')->store('profiles', 'public');
        }
        $user->save();
        $user->assignRole('Admin');

        $hospital->user_id = $user->id;
        $hospital->uuid = $user->uuid;
        $hospital->save();

        return response()->json(['success' => true, 'message' => 'Profile created. Continue with hospital details.', 'hospital_id' => $hospital->id, 'user_id' => $user->id]);
    }

    public function edit($hospital)
    {
        if (!$hospital instanceof Hospital) {
            $hospital = Hospital::where('id', $hospital)->first();
        }
        if (!$hospital) {
            abort(404, 'Hospital not found');
        }
        $hospital->load(['user', 'documents', 'specialities', 'services', 'licenses']);
        $empanelment_step_status = $hospital->user && $hospital->user->enable_step
            ? json_decode($hospital->user->enable_step)
            : json_decode(Helpers::get_settings('empanelment_step_status'));
        $routes = [];
        return view('admin-views.hospitals.edit-form', compact('hospital', 'empanelment_step_status', 'routes'));
    }

    public function stepLoad(Request $request, $hospital)
    {
        $id = $hospital instanceof Hospital ? $hospital->id : $hospital;
        $hospital = Hospital::where('id', $id)->first();
        if (!$hospital) {
            return '<h3 class="text-danger">Hospital not found</h3>';
        }

        $step = (int) $request->step;
        
        switch ($step) {
            case 2:
                return view('admin-views.hospitals.edit-partials.hospital-info', compact('hospital'))->render();
            case 3:
                $specialities = Helpers::getCommanData('Speciality');
                return view('admin-views.hospitals.edit-partials.speciality', compact('hospital', 'specialities'))->render();
            case 4:
                $services = Helpers::getCommanData('Service');
                return view('admin-views.hospitals.edit-partials.services', compact('hospital', 'services'))->render();
            case 5:
                $licenses = Helpers::getCommanData('License');
                return view('admin-views.hospitals.edit-partials.licenses', compact('hospital', 'licenses'))->render();
            case 6:
                $allStepCompleted = Helpers::checkAllStepIsCompleteOrNot(@$hospital->user->uuid);
                return view('admin-views.hospitals.edit-partials.documents', compact('hospital','allStepCompleted'))->render();
            default:
                return '<h3 class="text-danger">Invalid step</h3>';
        }
    }

    public function updateHospitalInfo(Request $request, $hospital)
    {
        $id = $hospital instanceof Hospital ? $hospital->id : $hospital;
        $hospital = Hospital::where('id', $id)->first();
        if (!$hospital) {
            return response()->json(['success' => false, 'message' => 'Hospital not found']);
        }

        $user = User::where('hospital_id', $id)->whereHas('roles', fn($q) => $q->where('name', 'Chairman'))->first();

        $rules = [
            'name' => 'required',
            'code' => 'required',
            'type_id' => 'required',
            'hospital_phone' => 'required',
            'hospital_email' => 'required|email',
            'pincode' => 'required',
            'city' => 'required',
            'address' => 'required',
        ];

        if ($hospital->hospital_type == 'Multi-Branch' && $hospital->parent_id == 0) {
            $rules['chairman_name'] = 'required';
            $rules['chairman_email'] = ['required', 'email', Rule::unique('users', 'email')->ignore($user?->id)];
            $rules['password'] = 'nullable|string';
            $rules['confirmation_password'] = 'same:password';
        }

        $request->validate($rules);

        $hospital->name = $request->name;
        $hospital->email = $request->hospital_email;
        $hospital->code = $request->code;
        $hospital->type_id = $request->type_id;
        $hospital->phone = $request->hospital_phone;
        $hospital->address = $request->address;
        $hospital->city = $request->city;
        $hospital->landmark = $request->landmark;
        $hospital->pincode = $request->pincode;
        $hospital->save();

        if ($hospital->hospital_type == 'Multi-Branch' && $hospital->parent_id == 0) {
            if (!$user) {
                $user = new User;
                $user->assignRole('Chairman');
            }
            $user->name = $request->chairman_name;
            $user->email = $request->chairman_email;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->hospital_id = $hospital->id;
            $user->save();
        }
        $enable_step = Helpers::get_settings('empanelment_step_status');
        $enable_step_decoded = json_decode($enable_step);
        if($enable_step_decoded->speciality_status == 1){
            $step = 3;
        }elseif($enable_step_decoded->service_status == 1){
            $step = 4;
        }elseif($enable_step_decoded->licenses_status == 1){
            $step = 5;
        }else{
            $step = 6;
        }

        return response()->json(['success' => true, 'message' => 'Hospital information updated successfully','step' => $step]);
    }

    public function updateSpecialities(Request $request, $hospital)
    {
        $id = $hospital instanceof Hospital ? $hospital->id : $hospital;
        $hospital = Hospital::where('id', $id)->first();
        if (!$hospital) {
            return response()->json(['success' => false, 'message' => 'Hospital not found']);
        }

        $hospital->specialities()->delete();
        if ($request->speciality_id ?? []) {
            foreach ($request->speciality_id as $value) {
                if ($request->input('available_' . $value)) {
                    $hospital->specialities()->create([
                        'uuid' => Helpers::generateUUID(),
                        'speciality_id' => $value,
                        'available' => $request->input('available_' . $value),
                        'remark' => $request->input('remark_' . $value),
                    ]);
                }
            }
        }

        $enable_step = @$hospital->user->enable_step;
        $enable_step_decoded = json_decode($enable_step);
        if($enable_step_decoded->service_status == 1){
            $step = 4;
        }elseif($enable_step_decoded->licenses_status == 1){
            $step = 5;
        }else{
            $step = 6;
        }
        return response()->json(['success' => true, 'message' => 'Specialities updated successfully','step' => $step]);
    }

    public function updateServices(Request $request, $hospital)
    {
        $id = $hospital instanceof Hospital ? $hospital->id : $hospital;
        $hospital = Hospital::where('id', $id)->first();
        if (!$hospital) {
            return response()->json(['success' => false, 'message' => 'Hospital not found']);
        }

        $services = Helpers::getCommanData('Service');
        $rules = [];
        $messages = [];
        foreach ($services as $value) {
            foreach ($value->subServices()->orderBy('sort_order', 'ASC')->get() as $v) {
                if ($v->is_required) {
                    $name = str_replace(' ', '-', strtolower($v->name));
                    $checklicences = $hospital->services()->where(['service_id' => $value->id, 'sub_service_id' => $v->id])->first();
                    $rules[$value->id . '_' . $v->id . '_' . $name] = 'required';
                    if (($request->{$value->id . '_' . $v->id . '_' . $name} ?? '') == 1) {
                        $rules[$value->id . '_' . $v->id . '_' . $name . '_text'] = 'sometimes|required';
                        $rules[$value->id . '_' . $v->id . '_' . $name . '_image'] = $checklicences ? 'nullable' : 'sometimes|required|mimes:jpg,png,jpeg';
                    }
                    $messages[$value->id . '_' . $v->id . '_' . $name] = 'This field is Required';
                }
            }
        }

        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'errors' => $validator->errors()->messages()], 422);
        }

        foreach ($services as $value) {
            foreach ($value->subServices()->orderBy('sort_order', 'ASC')->get() as $v) {
                $name = str_replace(' ', '-', strtolower($v->name));
                if (in_array($request->{$value->id . '_' . $v->id . '_' . $name} ?? '', [0, 1], true) || ($request->{$value->id . '_' . $v->id . '_' . $name} ?? '') !== '') {
                    $array = [
                        'uuid' => Helpers::generateUUID(),
                        'service_id' => $value->id,
                        'sub_service_id' => $v->id,
                        'action_id' => $request->{$value->id . '_' . $v->id . '_' . $name . '_action'},
                        'service_value' => $request->{$value->id . '_' . $v->id . '_' . $name},
                        'text_value' => $request->{$value->id . '_' . $v->id . '_' . $name . '_text'},
                        'remark' => $request->{$value->id . '_' . $v->id . '_remark'},
                    ];
                    if ($request->hasFile($value->id . '_' . $v->id . '_' . $name . '_image')) {
                        $array['image'] = $request->file($value->id . '_' . $v->id . '_' . $name . '_image')->store('serviceimage', 'public');
                    }
                    $hospital->services()->updateOrCreate(['service_id' => $value->id, 'sub_service_id' => $v->id], $array);
                }
            }
        }

        $enable_step = @$hospital->user->enable_step;
        $enable_step_decoded = json_decode($enable_step);
        if($enable_step_decoded->licenses_status == 1){
            $step = 5;
        }else{
            $step = 6;
        }
        return response()->json(['success' => true, 'message' => 'Services updated successfully','step' => $step]);
    }

    public function updateLicenses(Request $request, $hospital)
    {
        $id = $hospital instanceof Hospital ? $hospital->id : $hospital;
        $hospital = Hospital::where('id', $id)->first();
        if (!$hospital) {
            return response()->json(['success' => false, 'message' => 'Hospital not found']);
        }

        $licenses = Helpers::getCommanData('License');
        $rules = [];
        $messages = [];
        foreach ($licenses as $value) {
            foreach ($value->licenseType as $v) {
                if ($v->is_required) {
                    $checklicences = $hospital->licenses()->where(['license_id' => $value->id, 'license_type_id' => $v->id])->first();
                    $rules[$value->id . '_' . $v->id . '_dateissue'] = 'required|date';
                    $rules[$value->id . '_' . $v->id . '_dateexpiry'] = 'required|date';
                    $rules['document_' . $value->id . '_' . $v->id] = $checklicences ? 'nullable|mimes:pdf' : 'required|mimes:pdf';
                    $messages[$value->id . '_' . $v->id . '_dateissue.required'] = 'The Date of Issue for ' . $v->name . ' is required.';
                    $messages[$value->id . '_' . $v->id . '_dateexpiry.required'] = 'The Date of Expiry for ' . $v->name . ' is required.';
                }
            }
        }

        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'errors' => $validator->errors()->messages()], 422);
        }

        foreach ($licenses as $value) {
            foreach ($value->licenseType as $v) {
                if ($request->{$value->id . '_' . $v->id . '_dateissue'} && $request->{$value->id . '_' . $v->id . '_dateexpiry'}) {
                    $issueDate = date('Y-m-d', strtotime($request->{$value->id . '_' . $v->id . '_dateissue'}));
                    $expiryDate = date('Y-m-d', strtotime($request->{$value->id . '_' . $v->id . '_dateexpiry'}));
                    $array = [
                        'uuid' => Helpers::generateUUID(),
                        'license_id' => $value->id,
                        'license_type_id' => $v->id,
                        'issue_date' => $issueDate,
                        'expiry_date' => $expiryDate,
                        'remark' => $request->{$value->id . '_' . $v->id . '_remark'},
                    ];
                    if ($request->hasFile('document_' . $value->id . '_' . $v->id)) {
                        $array['document'] = $request->file('document_' . $value->id . '_' . $v->id)->store('certificate', 'public');
                    }
                    $hospital->licenses()->updateOrCreate(['license_id' => $value->id, 'license_type_id' => $v->id], $array);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Licenses updated successfully','step' => 6]);
    }

    public function updateDocuments(Request $request, $hospital)
    {
        $id = $hospital instanceof Hospital ? $hospital->id : $hospital;
        $hospital = Hospital::where('id', $id)->first();
        if (!$hospital) {
            return response()->json(['success' => false, 'message' => 'Hospital not found']);
        }

        $documents = Helpers::getCommanData('EmpanelmentDocument');
        $rules = [];
        $messages = [];
        foreach ($documents as $value) {
            if ($value->is_required) {
                $checkdocument = $hospital->documents()->where('document_id', $value->id)->first();
                $rules['document_' . $value->id . '_doc'] = $checkdocument ? 'nullable' : 'required|mimes:pdf|max:10240';
                $messages['document_' . $value->id . '_doc'] = 'The Document for ' . $value->name . ' is required.';
            }
        }

        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'errors' => $validator->errors()->messages()], 422);
        }

        foreach ($documents as $value) {
            $array = [
                'uuid' => Helpers::generateUUID(),
                'document_id' => $value->id,
                'remarks' => $request->{$value->id . '_remarkdoc'},
            ];
            if ($request->hasFile('document_' . $value->id . '_doc')) {
                $array['document'] = $request->file('document_' . $value->id . '_doc')->store('certificate', 'public');
            }
            $hospital->documents()->updateOrCreate(['document_id' => $value->id], $array);
        }

        $allStepCompleted = Helpers::checkAllStepIsCompleteOrNot(@$hospital->user->uuid);
        return response()->json(['success' => true, 'message' => 'Documents updated successfully', 'is_complete' => $allStepCompleted]);
    }
    public function hospitalSubmit(Request $request, $hospital) {

        $id = $hospital instanceof Hospital ? $hospital->id : $hospital;
        $hospital = Hospital::where('id', $id)->first();
        if (!$hospital) {
            return response()->json(['success' => false, 'message' => 'Hospital not found']);
        }
        $check = Helpers::checkAllStepIsCompleteOrNot(@$hospital->user->uuid);
        if($check) {
            $hospital->reject_reason = null;
            $hospital->is_approve = 1;
            $hospital->status = 'Approved';
            $hospital->status_update_date = date('Y-m-d H:i:s');
            $hospital->save();
            return response()->json(['success' => true, 'message' => $hospital->code.' Hospital Payment Is Initiated!!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Please fill all details first of hospital']);
        }
    }
}
