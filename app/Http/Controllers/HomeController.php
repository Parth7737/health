<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Validator;
use App\CentralLogics\Helpers;


class HomeController extends Controller
{
    protected $bankService;

    public function __construct()
    {

    }

    public function getDistrict(Request $request) {
        $states = new HospitalDistrict();
        if($request->state_id) {
            $states = $states->where('state_id', $request->state_id)->get();
        } else {
            $states = $states->get();
        }
        return response()->json($states);
    }

    public function getHospitalSubtype(Request $request) {
        $HospitalSubType = new HospitalSubType();
        if($request->hospital_type_id) {
            $HospitalSubType = $HospitalSubType->where('hospital_type_id', $request->hospital_type_id)->get();
        } else {
            $HospitalSubType = $HospitalSubType->get();
        }
        return response()->json($HospitalSubType);
    }

    public function store(Request $request) {
        $hospital = new Hospitals();
        $hospital->hospital_id = 1;
        $hospital->scheme = $request->scheme;
        $hospital->scheme_type = $request->scheme_type;
        $hospital->state = HospitalState::where('id', $request->state)->first()->name;
        $hospital->state_id = $request->state;
        $hospital->district = HospitalDistrict::where('id', $request->district)->first()->name;
        $hospital->district_id = $request->district;
        $hospital->name = $request->name;
        $hospital->parent_type_id = $request->parentType;
        $hospital->contact_no = $request->hospContactNo;
        $hospital->password = Hash::make(rand(00000000,999999999));
        $hospital->email = $request->hospEmailId;
        $hospital->hospital_type_id = $request->hospType;
        $hospital->hospital_type = HospitalType::where('name', $request->scheme)->first()->display_name;
        $hospital->hospital_sub_type_id = $request->hospPublicSubType;
        $hospital->empanelment_type = HospitalSubType::where('code', $request->hospPublicSubType)->first()->name;
        $hospital->empanelment_type_id = $request->empanelmentType;
        $hospital->empanelment_type = EmpanelmentType::where('id', $request->empanelmentType)->first()->name;
        $hospital->is_rohini_id = $request->rohiniYN;
        $hospital->rohini_id = $request->scheme;
        $hospital->is_nin_id = $request->ninYN;
        $hospital->nin_id = $request->scheme;
        $hospital->pan_no = $request->scheme;
        $hospital->save();
    }

    public function sendOTPOnMobile(Request $request) {
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'digits:10']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 200);
        }

        $otp = rand(000000, 999999);
        $data = MobileOtp::where(['mobile_no' => $request->mobile])->first();
        if($data) {
            $data->otp = $otp;
            $data->status = 0;
            $data->save();
        } else {
            $data = MobileOtp::create([ 'mobile_no' => $request->mobile, 'otp' => $otp, 'status' => 0]);
        }
        return response()->json(['success' => true, 'message' => 'Otp sent in your Mobile No','otp'=>$otp]);
    }
    public function reSendOTPOnMobile(Request $request) {
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'digits:10']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 200);
        }

        $otp = rand(000000, 999999);
        $data = MobileOtp::where(['mobile_no' => $request->mobile])->first();
        if($data) {
            $data->otp = $otp;
            $data->status = 0;
            $data->save();
        }

        return response()->json(['success' => true, 'message' => 'OTP Re-sent Successfully!','otp'=>$otp]);

    }
    public function verifiyMobileOtp(Request $request){

        $mobile_otp = MobileOtp::where(['mobile_no' => $request->mobile_no, 'status' => 0])->latest()->first();
        if($mobile_otp) {
            if($mobile_otp->otp == $request->otp){
                $mobile_otp->status=1;
                $mobile_otp->save();
                return response()->json(['success' => true, 'message' => 'OTP Verified Successfully!']);
            }else{
                return response()->json(['success' => false, 'message' => 'Wrong OTP!']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Something Went Wrong!!']);
        }
    }

    public function sendOTPOnEmail(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 200);
        }

        $otp = rand(000000, 999999);
        $data = EmailOtp::where(['email' => $request->email])->first();
        if($data) {
            $data->otp = $otp;
            $data->status = 0;
            $data->save();
        } else {
            $data = EmailOtp::create([ 'email' => $request->email, 'otp' => $otp, 'status' => 0]);
        }
        return response()->json(['success' => true, 'message' => 'Otp sent in your Email','otp'=>$otp]);
    }
    public function ReSendOTPOnEmail(Request $request) {
        
        $otp = rand(000000, 999999);
        $data = EmailOtp::where(['email' => $request->mobile])->first();
        if($data) {
            $data->otp = $otp;
            $data->status = 0;
            $data->save();
        }

        return response()->json(['success' => true, 'message' => 'OTP Re-sent Successfully!','otp'=>$otp]);

    }
    public function verifiyEmailOtp(Request $request){

        $emails = EmailOtp::where(['email' => $request->email, 'status' => 0])->latest()->first();
        if($emails) {
            if($emails->otp == $request->otp){
                $emails->status=1;
                $emails->save();
                $emails->delete();
                return response()->json(['success' => true, 'message' => 'OTP Verified Successfully!']);
            }else{
                return response()->json(['success' => false, 'message' => 'Wrong OTP!']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Something Went Wrong!!']);
        }
    }

    public function getNotifications(Request $request) {
        $userid = auth()->user()->id;
        $notifications = auth()->user()->notifications()->where('is_read', 0)->orderBy('id', 'DESC')->get();
        $unreadCount = auth()->user()->notifications()->where('is_read', 0)->count();
        $content = view('layouts.hospital.notificationdata',['notifications'=>$notifications, 'unreadCount' => $unreadCount])->render();
        $isNew = auth()->user()->notifications()
        ->where('is_read', 0)->where('created_at', '>=', now()->subHour())
        ->get();
        return response()->json(['success' => true,'message' => 'Data Get Successfully!!', 'content' => $content, 'isNew' => sizeof($isNew) > 0 ? 1 : 0]);
    }

    public function markasRead(Request $request) {
        $notification = auth()->user()->notifications()->find($request->id);
        if ($notification) {
            $notification->is_read = 1;
            $notification->save();
        }
        return response()->json(['success' => true]);
    }

    public function getBankData(Request $request) {
        $validatedData = $request->validate([
            'ifsc_code' => 'required',
        ]);

        $response = $this->bankService->getBankDetails($request->ifsc_code);

        return response()->json($response);
    }

    public function getaccountdetails(Request $request) {
        $validatedData = $request->validate([
            'ifsc_code' => 'required',
            'account_no_confirmation' => 'required',
        ]);

        $response = $this->bankService->getaccountdetails($request->ifsc_code, $request->account_no_confirmation);

        return response()->json($response);        
    }

    public function getGstDetails(Request $request) {
        $validatedData = $request->validate([
            'gst_no' => 'required',
        ]);

        $response = $this->bankService->getGstDetails($request->gst_no);

        return response()->json($response);        
    }

    public function downloadCaseReport(Request $request, $status, $extracondition, $conditions = null) {
        $conditionArray = [];

        if ($conditions) {
            $conditions = base64_decode($conditions);
            $pairs = explode('|', $conditions);

            foreach ($pairs as $pair) {
                if (str_contains($pair, ':')) {
                    [$key, $value] = explode(':', $pair, 2);
                    $conditionArray[$key] = $value;
                }
            }
        }

        $data = Helpers::downloadReportData($status, $conditionArray, $extracondition);
        $export = new CaseReportData();
        $filePath = $export->generate($data, PreauthRegister::getStatusLabelByValue($status));
    
        return response()->download($filePath, PreauthRegister::getStatusLabelByValue($status).'.xlsx')->deleteFileAfterSend(true);
    }
    public function appointments(){
        return view('front.create-appointment');
    }
    public function newDashboard(){
        return view('front.dashboard');
    }
    public function visitorBook(){
        return view('front.visitor-book');
    }
    public function opdPatients() {
        return view('front.opd-patients');
    }
}
