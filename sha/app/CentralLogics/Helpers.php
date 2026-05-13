<?php

namespace App\CentralLogics;
use App\Models\{
    BusinessSetting,
    Hospitals,
    PreauthRegister,
    PreauthProcedure,
    Procedure,
    Investigation,
    PreauthInvestigation,
    WorkFlowHistory,
    StateBankDetail,
    CaseLog,
    GeneralInfo,
    FamilyHistory,
    PersonalHistory,
    AuthenticationConsent,
    AdmissionDetails,
    PreauthDiagnosis,
    PreauthCareTeam,
    TabStatus,
    ExpiredDocument,
    UHospitals, 
    UHospitalAddress, 
    UHospitalSpeciality, 
    UHospitalLicense, 
    UHospitalServices, 
    UHospitalCeo, 
    UHospitalHumanResource, 
    UHospitalTeam, 
    UHospitalAccreditation, 
    UFinancialInformation, 
    UTaxDetails,
    UHospitalImages,
    UHospitalDocument,
    HospitalSpeciality,
    HospitalServices,
    HospitalLicense,
    HospitalTeam,
    HospitalHumanResource,
    AuditCategory,
    HospitalQualityAudit
};
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use DB;

class Helpers
{
    
    public static function get_settings($name)
    {
        $config = null;

        $paymentmethod = BusinessSetting::where('key', $name)->first();

        if ($paymentmethod) {
            $config = json_decode($paymentmethod->value, true);
        }

        return $config;
    }
    
    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            array_push($err_keeper, ['code' => $index, 'message' => $error[0]]);
        }
        return $err_keeper;
    }

    public static function getCommanData($modelName) {
       // Resolve the fully qualified model class name
       $modelClass = "App\\Models\\" . $modelName;

       if (!class_exists($modelClass)) {
           throw new \Exception("Model {$modelName} does not exist.");
       }

       // Fetch all records
       return App::make($modelClass)->all();

        return [];
    }

    public static function generateUUID(){
       return Str::uuid()->toString();
    }
    public static function getRegisterID(){
       $last = PreauthRegister::latest()->first();
       if(!$last){
        return 1000000001;
       }else{
        return $last->register_id+1;
       }
    }

    public static function getCount($userid, $status) {
        if($status == "Queried") {
            return Hospitals::whereIn('status', [$status, 'Response Required From Facility', 'Query On Upgradation Request From Facility'])->where('user_id', $userid)->count();
        } else if($status == "Rejected") {
            return Hospitals::whereIn('status', [$status, 'Empanelment Not Recommended by DEC'])->where('user_id', $userid)->count();
        } else if($status == "Submitted" || $status == "Re-Submitted") {
            return Hospitals::whereIn('status', [$status, 'Re-Submitted'])->where('user_id', $userid)->count();
        } else {
            return Hospitals::status($status)->where('user_id', $userid)->count();
        }       
    }

    public static function getSingleSpecialities($hospital_id, $speciality_id) {
        $hospitals = Hospitals::where('id' , $hospital_id)->first();
        
        return $hospitals->specialities()->where('speciality_id', $speciality_id)->first(); 
    }

    public static function getUSingleSpecialities($hospital_id, $speciality_id) {
        $hospitals = UHospitals::where('main_hospitalid' , $hospital_id)->first();
        
        return $hospitals->specialities()->where('speciality_id', $speciality_id)->first(); 
    }

    public static function getSingleServices($hospital_id, $service_id, $sub_service_id) {
        $hospitals = Hospitals::where('id' , $hospital_id)->first();
        
        return $hospitals->services()->where('service_id', $service_id)->where('sub_service_id', $sub_service_id)->first(); 
    }

    public static function getUSingleServices($hospital_id, $service_id, $sub_service_id) {
        $hospitals = UHospitals::where('main_hospitalid' , $hospital_id)->first();
        
        return $hospitals->services()->where('service_id', $service_id)->where('sub_service_id', $sub_service_id)->first(); 
    }

    public static function getSingleLicense($hospital_id, $license_id, $license_type_id) {
        $hospitals = Hospitals::where('id' , $hospital_id)->first();
        
        return $hospitals->licenses()->where('license_id', $license_id)->where('license_type_id', $license_type_id)->first(); 
    }

    public static function getUSingleLicense($hospital_id, $license_id, $license_type_id) {
        $hospitals = UHospitals::where('main_hospitalid' , $hospital_id)->first();
        
        return $hospitals->licenses()->where('license_id', $license_id)->where('license_type_id', $license_type_id)->first(); 
    }

    public static function getSingleDocument($hospital_id, $document_id) {
        $hospitals = Hospitals::where('id' , $hospital_id)->first();
        
        return $hospitals->documents()->where('document_id', $document_id)->first(); 
    }

    public static function encryptCC($plainText, $key)
    {
        $key = self::hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        $encryptedText = bin2hex($openMode);
        return $encryptedText;
    }

    public static function decryptCC($encryptedText, $key)
    {
        $key = self::hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = self::hextobin($encryptedText);
        $decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        return $decryptedText;
    }

    public static function pkcs5_padCC($plainText, $blockSize)
    {
        $pad = $blockSize - (strlen($plainText) % $blockSize);
        return $plainText . str_repeat(chr($pad), $pad);
    }

    public static function hextobin($hexString)
    {
        $length = strlen($hexString);
        $binString = "";
        $count = 0;
        while ($count < $length) {
            $subString = substr($hexString, $count, 2);
            $packedString = pack("H*", $subString);
            if ($count == 0) {
                $binString = $packedString;
            } else {
                $binString .= $packedString;
            }

            $count += 2;
        }
        return $binString;
    }
    public static function isMadicalPackage($preauth_register_id)
    {
        return PreauthProcedure::where('preauth_register_id', $preauth_register_id)
            ->whereHas('procedure', function ($query) {
                $query->where('medical_or_surgical', 'Medical');
            })
            ->exists();
    }
    public static function getInvestigations($preauth_register_id,$is_resubmission=0,$is_required=0)
    {
        if($is_resubmission){
            $procedures = PreauthProcedure::where('preauth_register_id', $preauth_register_id)->where('is_resubmission_delete',0)->withoutGlobalScopes()->get();
        }else{
            $procedures = PreauthProcedure::where('preauth_register_id', $preauth_register_id)->get();
        }
        $investigations = [];

        if ($procedures->isNotEmpty()) {
            $procedure_ids = $procedures->pluck('procedure_id');
            $pre_docs_ids = Procedure::whereIn('id', $procedure_ids)
                ->pluck('mandatory_documents_pre_auth')
                ->filter()
                ->flatMap(function ($item) {
                    return explode(',', $item);
                })
                ->unique()
                ->toArray();

            if (!empty($pre_docs_ids)) {
                $investigations = Investigation::whereIn('id', $pre_docs_ids)->when($is_required === 1, function ($query) {
                    return $query->where('is_required', 1);
                })->get();
            }
        }

        return $investigations;
    }
    public static function getPostInvestigations($preauth_register_id)
    {
        $procedures = PreauthProcedure::where('preauth_register_id', $preauth_register_id)->get();
        $investigations = [];

        if ($procedures->isNotEmpty()) {
            $procedure_ids = $procedures->pluck('procedure_id');
            $pre_docs_ids = Procedure::whereIn('id', $procedure_ids)
                ->pluck('mandatory_documents_claim_processing')
                ->filter()
                ->flatMap(function ($item) {
                    return explode(',', $item);
                })
                ->unique()
                ->toArray();

            if (!empty($pre_docs_ids)) {
                $investigations = Investigation::whereIn('id', $pre_docs_ids)->get();
            }
        }

        return $investigations;
    }

    public static function getPreauthInvestigationsStatus($preauth_register_id,$is_resubmission=0)
    {
        $investigations = self::getInvestigations($preauth_register_id,$is_resubmission,1);
        $preauth_register = PreauthRegister::where('id', $preauth_register_id)->first();

        if (!$preauth_register) {
            return false;
        }
        if($is_resubmission){
            $preauth_investigations_count = PreauthInvestigation::where('preauth_register_id',$preauth_register_id)->where('is_resubmission_delete',0)->whereHas('investigation',function($query){
                $query->where('is_required',1);
            })->withoutGlobalScopes()->count() ?? 0;
        }else{
            $preauth_investigations_count = PreauthInvestigation::where('preauth_register_id',$preauth_register_id)->whereHas('investigation',function($query){
                $query->where('is_required',1);
            })->count() ?? 0;
        }
        $retrieved_investigations_count = count($investigations);
        return $preauth_investigations_count === $retrieved_investigations_count;
    }
    
    public static function getPreauthPackageStatus($preauth_register_id,$is_resubmission=0)
    {
        if($is_resubmission){
            $procedures = PreauthProcedure::where('preauth_register_id', $preauth_register_id)
                        ->whereHas('procedure', fn($query) => $query->whereRaw("LOWER(TRIM(procedure_label)) NOT LIKE ?", ['%add-onprocedure%']))
                        ->where('is_resubmission_delete', 0)
                        ->withoutGlobalScopes()
                        ->get();
        }else{
            $procedures = PreauthProcedure::where('preauth_register_id', $preauth_register_id)
                        ->whereHas('procedure', fn($query) => $query->whereRaw("LOWER(TRIM(procedure_label)) NOT LIKE ?", ['%add-onprocedure%']))->get();
        }
        $package_type = '';
        $mismatch=0;
        foreach($procedures as $procedure){
            if($package_type == '' && @$procedure->procedure->medical_or_surgical != ''){
                $package_type = $procedure->procedure->medical_or_surgical;
            }
            if($package_type != '' && $package_type != @$procedure->procedure->medical_or_surgical){
                $mismatch = 1;
            }
        }
        return $mismatch;
    }

    public static function getU100PackageStatus($preauth_register_id,$is_resubmission=0)
    {
        if($is_resubmission){
            $procedure_count = PreauthProcedure::where('preauth_register_id', $preauth_register_id)
                        ->whereHas('procedure', fn($query) => $query->whereRaw("LOWER(TRIM(procedure_label)) NOT LIKE ?", ['%add-onprocedure%']))
                        ->where('is_resubmission_delete', 0)
                        ->withoutGlobalScopes()
                        ->get()->count();
            $u100 = PreauthProcedure::where('preauth_register_id', $preauth_register_id)
                        ->whereHas('procedure', fn($query) => $query->whereRaw("LOWER(TRIM(procedure_label)) NOT LIKE ?", ['%add-onprocedure%'])->where('procedure_code_1','U100'))
                        ->where('is_resubmission_delete', 0)
                        ->withoutGlobalScopes()
                        ->get()->count();
        }else{
            $procedure_count = PreauthProcedure::where('preauth_register_id', $preauth_register_id)
                        ->whereHas('procedure', fn($query) => $query->whereRaw("LOWER(TRIM(procedure_label)) NOT LIKE ?", ['%add-onprocedure%']))->get()->count();

            $u100 = PreauthProcedure::where('preauth_register_id', $preauth_register_id)
                        ->whereHas('procedure', fn($query) => $query->whereRaw("LOWER(TRIM(procedure_label)) NOT LIKE ?", ['%add-onprocedure%'])->where('procedure_code_1','U100'))->get()->count();
        }
        $mismatch=0;
        if($u100 != 0  && $procedure_count != $u100){
            $mismatch=1;
        }
        return $mismatch;
    }
    public static function addWorkflowForHospital($hospital, $array) {
        $array['uuid'] = self::generateUUID();

        $workflow = $hospital->workFlowHistories()->create($array);
        
        return $workflow->id;
    }
    
    public static function addCaseLog($preauth_register_id, $data,$amount_condition_type=0,$custom_amount=0) {
        // $amount_condition_type => 0 preauth_status == 'Approved'|| $procedure->preauth_implant_status == 'Approved'
        // $amount_condition_type => 1 preauth_claim_status == 'Approved'|| $procedure->preauth_claim_implant_status == 'Approved'
        // $amount_condition_type => 2,4 Erroneous Amount
        // $amount_condition_type => 3 not any condition
        // $amount_condition_type => 5 custom amount (mean medical committee approved, CEO approved , ACS Approved)
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->first();
        $procedures = PreauthProcedure::where('preauth_register_id',$preauth_register_id)->get();
        $case_log = new CaseLog;
        $case_log->preauth_register_id = $preauth_register_id;
        $case_log->status = $preauth_register->status_label;
        $case_log->stage = @$data['stage']?$data['stage']:'';
        $case_log->type = @$data['type']?$data['type']:'';
        $case_log->role_id = @auth()->user()->role_id;
        if($amount_condition_type == 2 || $amount_condition_type == 4){
            $case_log->amount = ($amount_condition_type == 2) 
                                ? $preauth_register->erroneous_raise_amount 
                                : (!empty($preauth_register->erroneous_appoved_amount) 
                                    ? $preauth_register->erroneous_appoved_amount 
                                    : $preauth_register->erroneous_raise_amount);

            $case_log->procedures = json_encode([]);
        }elseif($amount_condition_type == 5){
            $case_log->amount = number_format($custom_amount,2);
            $case_log->procedures = json_encode($procedures);
        }else{
            $case_log->amount = self::preauthAmount($procedures,$amount_condition_type);
            $case_log->procedures = json_encode($procedures);
        }
        $case_log->remarks = @$data['remarks']?$data['remarks']:'N/A';
        $case_log->added_by = @auth()->user()->id;
        $case_log->save();
    }
    public static function preauthAmount($procedures,$amount_condition_type) {
        $total=0;$total_incentive=0;$deduction_discharge_amount=0;
        foreach(@$procedures as $procedure){
            $i=0;
            if($i==0){
                $deduction_discharge_amount= @$procedure->preauth_register->deduction_discharge_amount;
            }
            $i++; 
            $is_apply=0;
            if($amount_condition_type == 3){
                $is_apply=1;
            }elseif($amount_condition_type == 2){
                $is_apply=0;
            }elseif($amount_condition_type == 1 && ($procedure->preauth_claim_status == 'Approved'|| $procedure->preauth_claim_implant_status == 'Approved')){
                $is_apply=1;
            }elseif($amount_condition_type == 0 && $procedure->preauth_status == 'Approved' || $procedure->preauth_status == 'Forwarded To Medical Committee' || $procedure->preauth_implant_status == 'Approved'){
                $is_apply=1;
            }
            if($is_apply){
                $total +=@$procedure->procedure_price;
                $total +=@$procedure->stratification_price;
                if(@$procedure->procedure_price == 0 && $procedure->stratification_price != 0 && $procedure->no_of_days > 1){
                    $total +=$procedure->stratification_price*intval($procedure->no_of_days ?$procedure->no_of_days-1: 0);;
                }
                if($amount_condition_type == 3 || ($amount_condition_type == 1 && $procedure->preauth_claim_implant_status == 'Approved') || ($amount_condition_type == 0 && $procedure->preauth_implant_status == 'Approved')){
                    $total +=@$procedure->implant_price*$procedure->implant_qty;
                }
                $total -=@$procedure->deducted_amount;
                $total_incentive +=@$procedure->incentive;
            }
        }
        return number_format($total+$total_incentive-$deduction_discharge_amount,2);
    }
    public static function getTotalAmount($status=null, $where = [],$is_extra_con=0,$column='claim_approved_amount')
    {
        $query = PreauthRegister::selectRaw("
            SUM(
                ".$column."
            ) as total_amount
        ");
        if($status == PreauthRegister::STATUS_PREAUTH_PENDING && $is_extra_con == 1){
            $query->whereIn('status',[PreauthRegister::STATUS_PREAUTH_PENDING,PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING,PreauthRegister::STATUS_MEDICAL_COMMITTEE_APPROVED,PreauthRegister::STATUS_ACS_PENDING]);
        }elseif($status == PreauthRegister::STATUS_PREAUTH_QUERIED && $is_extra_con == 1){
            $query->whereIn('status',[PreauthRegister::STATUS_PREAUTH_QUERIED,PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED,PreauthRegister::STATUS_CEO_QUERIED,PreauthRegister::STATUS_ACS_QUERIED]);
        }elseif($status == PreauthRegister::STATUS_PREAUTH_REJECTED && $is_extra_con == 1){
            $query->whereIn('status',[PreauthRegister::STATUS_PREAUTH_REJECTED,PreauthRegister::STATUS_MEDICAL_COMMITTEE_REJECTED,PreauthRegister::STATUS_CEO_REJECTED,PreauthRegister::STATUS_ACS_REJECTED]);
        }elseif($status == PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING && $is_extra_con == 1){
            $query->whereIn('status',[PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING,PreauthRegister::STATUS_MEDICAL_COMMITTEE_APPROVED,PreauthRegister::STATUS_ACS_PENDING]);
        }elseif($status == PreauthRegister::STATUS_MEDICAL_COMMITTEE_APPROVED && $is_extra_con == 1){
            $query->whereNotNull('ceo_approved_date')->orWhereNotNull('acs_approved_date');
        }elseif($status == PreauthRegister::STATUS_MEDICAL_COMMITTEE_REJECTED && $is_extra_con == 1){
            $query->whereIn('status',[PreauthRegister::STATUS_PREAUTH_REJECTED,PreauthRegister::STATUS_MEDICAL_COMMITTEE_REJECTED,PreauthRegister::STATUS_CEO_REJECTED,PreauthRegister::STATUS_ACS_REJECTED]);
        }elseif($status == PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED && $is_extra_con == 1){
            $query->whereIn('status',[PreauthRegister::STATUS_PREAUTH_QUERIED,PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED,PreauthRegister::STATUS_CEO_QUERIED,PreauthRegister::STATUS_ACS_QUERIED]);
        }elseif($status == PreauthRegister::STATUS_PREAUTH_APPROVED && $is_extra_con == 1){
            $query->whereNotNull('preauth_approved_date')->whereNot('status',PreauthRegister::STATUS_PREAUTH_PENDING);
        }elseif($status == PreauthRegister::STATUS_CLAIM_APPROVED && $is_extra_con == 1){
            $query->whereNotNull('claim_approved_date');
        }elseif($status == PreauthRegister::STATUS_ACO_CLAIM_APPROVED && $is_extra_con == 1){
            $query->whereNotNull('claim_aco_approved_date');
        }elseif ($status == PreauthRegister::STATUS_CPD_CLAIM_PENDING && $is_extra_con == 2) {

        }elseif ($status == PreauthRegister::STATUS_CPD_CLAIM_PENDING && $is_extra_con == 1) {
            $query->where(function ($query1) {
                $query1->where('status', PreauthRegister::STATUS_CPD_CLAIM_PENDING)
                       ->orWhere(function ($query2) {
                           $query2->whereIn('status', [
                               PreauthRegister::STATUS_ACO_CLAIM_QUERIED,
                               PreauthRegister::STATUS_SHA_CLAIM_QUERIED
                           ])->where('claim_approve_reject_query_by', auth()->user()->id);
                       });
            });
        }elseif ($status == PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING && $is_extra_con == 1) {
            $query->where(function ($query1) {
                $query1->where('status', PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING)
                       ->orWhere(function ($query2) {
                           $query2->whereIn('status', [
                               PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_QUERIED,
                               PreauthRegister::STATUS_ERRONEOUS_SHA_CLAIM_QUERIED
                           ])->where('erroneous_claim_approve_reject_query_by', auth()->user()->id);
                       });
            });
        }else{
            if($status != null){
                $query->where('status', $status);
            }
        }

        if(is_array($where)) {
            foreach ($where as $key => $value) {
                $query->where($key, $value);
            }
        }

        $result = $query->first();

        $finalAmount = ($result->total_amount ?? 0);

        return "₹".number_format($finalAmount, 2);
    }

    public static function downloadReportData($status=null, $where = [],$is_extra_con=0)
    {
        $query = PreauthRegister::with([
            'benificiary' => function ($q) {
                $q->select('id', 'card_id', 'name', 'age', 'gender', 'care_plan', 'mobile_no');
            }
        ])->select('benificiary_id', 'status', 'preauth_initiated_amount', 'preauth_approved_amount', 'preauth_amount_without_deduction', 'erroneous_raise_amount', 'erroneous_appoved_amount', 'claim_approved_amount', 'register_id', 'created_at');

        if($status == PreauthRegister::STATUS_PREAUTH_PENDING && $is_extra_con == 1){
            $query->whereIn('status',[PreauthRegister::STATUS_PREAUTH_PENDING,PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING,PreauthRegister::STATUS_MEDICAL_COMMITTEE_APPROVED,PreauthRegister::STATUS_ACS_PENDING]);
        }elseif($status == PreauthRegister::STATUS_PREAUTH_QUERIED && $is_extra_con == 1){
            $query->whereIn('status',[PreauthRegister::STATUS_PREAUTH_QUERIED,PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED,PreauthRegister::STATUS_CEO_QUERIED,PreauthRegister::STATUS_ACS_QUERIED]);
        }elseif($status == PreauthRegister::STATUS_PREAUTH_REJECTED && $is_extra_con == 1){
            $query->whereIn('status',[PreauthRegister::STATUS_PREAUTH_REJECTED,PreauthRegister::STATUS_MEDICAL_COMMITTEE_REJECTED,PreauthRegister::STATUS_CEO_REJECTED,PreauthRegister::STATUS_ACS_REJECTED]);
        }elseif($status == PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING && $is_extra_con == 1){
            $query->whereIn('status',[PreauthRegister::STATUS_MEDICAL_COMMITTEE_PENDING,PreauthRegister::STATUS_MEDICAL_COMMITTEE_APPROVED,PreauthRegister::STATUS_ACS_PENDING]);
        }elseif($status == PreauthRegister::STATUS_MEDICAL_COMMITTEE_APPROVED && $is_extra_con == 1){
            $query->whereNotNull('ceo_approved_date')->orWhereNotNull('acs_approved_date');
        }elseif($status == PreauthRegister::STATUS_MEDICAL_COMMITTEE_REJECTED && $is_extra_con == 1){
            $query->whereIn('status',[PreauthRegister::STATUS_PREAUTH_REJECTED,PreauthRegister::STATUS_MEDICAL_COMMITTEE_REJECTED,PreauthRegister::STATUS_CEO_REJECTED,PreauthRegister::STATUS_ACS_REJECTED]);
        }elseif($status == PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED && $is_extra_con == 1){
            $query->whereIn('status',[PreauthRegister::STATUS_PREAUTH_QUERIED,PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED,PreauthRegister::STATUS_CEO_QUERIED,PreauthRegister::STATUS_ACS_QUERIED]);
        }elseif($status == PreauthRegister::STATUS_PREAUTH_APPROVED && $is_extra_con == 1){
            $query->whereNotNull('preauth_approved_date')->whereNot('status',PreauthRegister::STATUS_PREAUTH_PENDING);
        }elseif($status == PreauthRegister::STATUS_CLAIM_APPROVED && $is_extra_con == 1){
            $query->whereNotNull('claim_approved_date');
        }elseif($status == PreauthRegister::STATUS_ACO_CLAIM_APPROVED && $is_extra_con == 1){
            $query->whereNotNull('claim_aco_approved_date');
        }elseif ($status == PreauthRegister::STATUS_CPD_CLAIM_PENDING && $is_extra_con == 2) {

        }elseif ($status == PreauthRegister::STATUS_CPD_CLAIM_PENDING && $is_extra_con == 1) {
            $query->where(function ($query1) {
                $query1->where('status', PreauthRegister::STATUS_CPD_CLAIM_PENDING)
                       ->orWhere(function ($query2) {
                           $query2->whereIn('status', [
                               PreauthRegister::STATUS_ACO_CLAIM_QUERIED,
                               PreauthRegister::STATUS_SHA_CLAIM_QUERIED
                           ])->where('claim_approve_reject_query_by', auth()->user()->id);
                       });
            });
        }elseif ($status == PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING && $is_extra_con == 1) {

            $query->where(function ($query1) {
                $query1->where('status', PreauthRegister::STATUS_ERRONEOUS_CLAIM_PENDING)
                       ->orWhere(function ($query2) {
                           $query2->whereIn('status', [
                               PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_QUERIED,
                               PreauthRegister::STATUS_ERRONEOUS_SHA_CLAIM_QUERIED
                           ])->where('erroneous_claim_approve_reject_query_by', auth()->user()->id);
                       });
            });

        }elseif($status == PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED && $is_extra_con == 1){
            $query->whereNotNull('erroneous_claim_approved_date');
        }elseif($status == PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_APPROVED && $is_extra_con == 1){
            $query->whereNotNull('erroneous_claim_aco_approved_date');
        }else{

            if($status != null){
                $query->where('status', $status);
            }

        }

        foreach ($where as $key => $value) {
            $query->where("$key", $value);
        }

        $result = $query->get();
        return $result;
        // $finalAmount = ($result->total_amount ?? 0) - ($result->deduction_discharge_amount ?? 0);

        // return "₹".number_format($finalAmount, 2);
    }

    public static function getTotalErroneousAmount($status=null, $where = [],$column='erroneous_raise_amount',$is_extra_con=0){
        $query = PreauthRegister::selectRaw("
            SUM(
                COALESCE(".$column.", 0)
            ) as total_amount
        ");
        if($status == PreauthRegister::STATUS_ERRONEOUS_CLAIM_APPROVED && $is_extra_con == 1){
            $query->whereNotNull('erroneous_claim_approved_date');
        }elseif($status == PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_APPROVED && $is_extra_con == 1){
            $query->whereNotNull('erroneous_claim_aco_approved_date');
        }else{
            if($status != null){
                $query->where('status', $status);
            }
        }

        $result = $query->first();
        $finalAmount = ($result->total_amount ?? 0);
        return "₹".number_format($finalAmount, 2);
    }
    public static function getDeductionAmount($preauth_register_id){
        return PreauthProcedure::where('preauth_register_id', $preauth_register_id)->where('preauth_claim_status','Approved')->sum('deducted_amount') ?? 0;
    }
    public static function getPreauthIntiateAmount($preauth_register_id, $is_applicable_discharge = 1)
    {
        
        $result = PreauthProcedure::with('preauth_register')
            ->selectRaw('
                SUM(
                    COALESCE(procedure_price, 0) +
                    COALESCE(stratification_price, 0) +
                    (CASE 
                        WHEN COALESCE(procedure_price, 0) = 0 
                            AND COALESCE(stratification_price, 0) != 0 
                            AND COALESCE(no_of_days, 0) > 1 
                        THEN COALESCE(stratification_price, 0) * (COALESCE(no_of_days, 0) - 1) 
                        ELSE 0 
                    END) +
                    (COALESCE(implant_price, 0) * COALESCE(implant_qty, 0)) +
                    COALESCE(incentive, 0)
                ) as total
            ')
            ->where('preauth_register_id', $preauth_register_id)
            ->first();

        $total = $result->total ?? 0;

        if ($is_applicable_discharge == 1) {
            // Fetch deduction_discharge_amount from first related register
            $deduction = PreauthProcedure::where('preauth_register_id', $preauth_register_id)
                ->with('preauth_register:id,deduction_discharge_amount')
                ->first()
                ->preauth_register
                ->deduction_discharge_amount ?? 0;

            $total -= $deduction;
        }

        return $total;
    }
    public static function getPreauthAmountWithoutDeduction($preauth_register_id, $is_applicable_discharge = 1,$amount_action_type=0)
    {
        
        $result = PreauthProcedure::with('preauth_register')
                ->where(function($query) use($amount_action_type){
                    if($amount_action_type == 0){
                        $query->whereIn('preauth_status',['Approved','Forwarded To Medical Committee'])
                            ->orWhere('preauth_implant_status','Approved');
                    }else{
                        $query->where('preauth_claim_status','Approved')
                            ->orWhere('preauth_claim_implant_status','Approved');
                    }
                })
                ->selectRaw('
                    SUM(
                        COALESCE(procedure_price, 0) +
                        COALESCE(stratification_price, 0) +
                        (CASE 
                            WHEN COALESCE(procedure_price, 0) = 0 
                                AND COALESCE(stratification_price, 0) != 0 
                                AND COALESCE(no_of_days, 0) > 1 
                            THEN COALESCE(stratification_price, 0) * (COALESCE(no_of_days, 0) - 1) 
                            ELSE 0 
                        END) +
                        ' . ($amount_action_type == 0 ? '
                        (CASE 
                            WHEN preauth_implant_status = "Approved" 
                            THEN COALESCE(implant_price, 0) * COALESCE(implant_qty, 0)
                            ELSE 0 
                        END)' : '
                        (CASE 
                            WHEN preauth_claim_implant_status = "Approved" 
                            THEN COALESCE(implant_price, 0) * COALESCE(implant_qty, 0)
                            ELSE 0 
                        END)') . ' +
                        COALESCE(incentive, 0)
                    ) as total
                ')
                ->where('preauth_register_id', $preauth_register_id)
                ->first();


        $total = $result->total ?? 0;

        if ($is_applicable_discharge == 1) {
            // Fetch deduction_discharge_amount from first related register
            $deduction = PreauthProcedure::where('preauth_register_id', $preauth_register_id)
                ->with('preauth_register:id,deduction_discharge_amount')
                ->first()
                ->preauth_register
                ->deduction_discharge_amount ?? 0;

            $total -= $deduction;
        }

        return $total;
    }


    public static function checkU100Package($preauth_register_id)
    {
        return PreauthProcedure::where('preauth_register_id', $preauth_register_id)
            ->whereHas('procedure', function ($query) {
                $query->where('procedure_code_1', 'U100');
            })
            ->exists();
    }

    public static function generateUserId($email, $name) {
        $baseString = $name . $email;
        $hash = md5($baseString);
        $userId = substr($hash, 0, 8);
        while (DB::table('users')->where('userid', $userId)->exists()) {
            $userId = substr(md5($baseString . Str::random(8)), 0, 8);
        }

        return $userId;
    }

    public static function generateHospitalId($code) {
        if($code){
            $code = strtoupper(substr($code, 0, 1));
        }
        $stateCode = '05';
        $lastHospital = \DB::table('hospitals')->orderBy('id', 'desc')->first();
        if($lastHospital) {
            $nextIncrementalId = (int) substr($lastHospital->hospital_id, -5) + 1;
        } else {
            $nextIncrementalId = 00001;  
        }
                
        $formattedIncrementalId = str_pad($nextIncrementalId, 5, '0', STR_PAD_LEFT);    
        $hospitalId = 'HOSP' . $stateCode . $code . $formattedIncrementalId;
        return $hospitalId;
    }
    
    public static function checkStatus($id, $type) {
        $preauth_register = PreauthRegister::where('id',$id)->first();
        if($preauth_register) {
            $status = json_decode($preauth_register->{$type.'_status'}, true);
            $result = false;
            $born_baby_birth_certificate=$preauth_query_supporting_doc=$preauth_query_add_doc=$committee_query_supporting_doc=$ceo_query_supporting_doc=$acs_query_supporting_doc=$claim_query_supporting_doc=$erroneous_raise_supporting_doc=$erroneous_query_supporting_doc=$claim_query_add_doc=$death_certificate=$death_summary=$mortality_audit_report=$in_treatment_photo=$post_surgery_photo=$discharge_summary=$claim_other_doc=$hospital_bill=$feedback_form=$beneficiary_verification_form=$hospital_certificate=true;
            if($preauth_register->born_baby_birth_certificate) {
                if(@$status['born_baby_birth_certificate'] == 'Correct') {
                    $born_baby_birth_certificate = true;
                } else {
                    $born_baby_birth_certificate = false;
                }
            }
            if($preauth_register->preauth_query_supporting_doc) {
                if(@$status['preauth_query_supporting_doc'] == 'Correct') {
                    $preauth_query_supporting_doc = true;
                } else {
                    $preauth_query_supporting_doc = false;
                }
            }
            if($preauth_register->preauth_query_add_doc) {
                if(@$status['preauth_query_add_doc'] == 'Correct') {
                    $preauth_query_add_doc = true;
                } else {
                    $preauth_query_add_doc = false;
                }
            }
            if($preauth_register->committee_query_supporting_doc) {
                if(@$status['committee_query_supporting_doc'] == 'Correct') {
                    $committee_query_supporting_doc = true;
                } else {
                    $committee_query_supporting_doc = false;
                }
            }
            if($preauth_register->ceo_query_supporting_doc) {
                if(@$status['ceo_query_supporting_doc'] == 'Correct') {
                    $ceo_query_supporting_doc = true;
                } else {
                    $ceo_query_supporting_doc = false;
                }
            }
            if($preauth_register->acs_query_supporting_doc) {
                if(@$status['acs_query_supporting_doc'] == 'Correct') {
                    $acs_query_supporting_doc = true;
                } else {
                    $acs_query_supporting_doc = false;
                }
            }
            if($preauth_register->claim_query_supporting_doc) {
                if(@$status['claim_query_supporting_doc'] == 'Correct') {
                    $claim_query_supporting_doc = true;
                } else {
                    $claim_query_supporting_doc = false;
                }
            }
            if($preauth_register->claim_query_add_doc) {
                if(@$status['claim_query_add_doc'] == 'Correct') {
                    $claim_query_add_doc = true;
                } else {
                    $claim_query_add_doc = false;
                }
            }
            if($preauth_register->death_certificate) {
                if(@$status['death_certificate'] == 'Correct') {
                    $death_certificate = true;
                } else {
                    $death_certificate = false;
                }
            }
            if($preauth_register->death_summary) {
                if(@$status['death_summary'] == 'Correct') {
                    $death_summary = true;
                } else {
                    $death_summary = false;
                }
            }
            if($preauth_register->mortality_audit_report) {
                if(@$status['mortality_audit_report'] == 'Correct') {
                    $mortality_audit_report = true;
                } else {
                    $mortality_audit_report = false;
                }
            }
            if($preauth_register->in_treatment_photo) {
                if(@$status['in_treatment_photo'] == 'Correct') {
                    $in_treatment_photo = true;
                } else {
                    $in_treatment_photo = false;
                }
            }
            if($preauth_register->post_surgery_photo) {
                if(@$status['post_surgery_photo'] == 'Correct') {
                    $post_surgery_photo = true;
                } else {
                    $post_surgery_photo = false;
                }
            }
            if($preauth_register->discharge_summary) {
                if(@$status['discharge_summary'] == 'Correct') {
                    $discharge_summary = true;
                } else {
                    $discharge_summary = false;
                }
            }
            if($preauth_register->hospital_bill) {
                if(@$status['hospital_bill'] == 'Correct') {
                    $hospital_bill = true;
                } else {
                    $hospital_bill = false;
                }
            }
            if($preauth_register->feedback_form) {
                if(@$status['feedback_form'] == 'Correct') {
                    $feedback_form = true;
                } else {
                    $feedback_form = false;
                }
            }
            if($preauth_register->beneficiary_verification_form) {
                if(@$status['beneficiary_verification_form'] == 'Correct') {
                    $beneficiary_verification_form = true;
                } else {
                    $beneficiary_verification_form = false;
                }
            }
            if($preauth_register->hospital_certificate) {
                if(@$status['hospital_certificate'] == 'Correct') {
                    $hospital_certificate = true;
                } else {
                    $hospital_certificate = false;
                }
            }
            if($preauth_register->claim_other_doc) {
                if(@$status['claim_other_doc'] == 'Correct') {
                    $claim_other_doc = true;
                } else {
                    $claim_other_doc = false;
                }
            }
            if($preauth_register->erroneous_raise_supporting_doc) {
                if(@$status['erroneous_raise_supporting_doc'] == 'Correct') {
                    $erroneous_raise_supporting_doc = true;
                } else {
                    $erroneous_raise_supporting_doc = false;
                }
            }
            if($preauth_register->erroneous_query_supporting_doc) {
                if(@$status['erroneous_query_supporting_doc'] == 'Correct') {
                    $erroneous_query_supporting_doc = true;
                } else {
                    $erroneous_query_supporting_doc = false;
                }
            }
            $totalCount = $preauth_register->investigations()->count();
            $correctCount = $preauth_register->investigations()->where($type.'_status', 'Correct')->count();
           
            if($totalCount === $correctCount) {
                $investigations = true;
            } else {
                $investigations = false;
            }
            $enhancement_docstotalCount = $preauth_register->enhancement_docs()->count();
            $enhancement_docscorrectCount = $preauth_register->enhancement_docs()->where($type.'_status', 'Correct')->count();
           
            if($enhancement_docstotalCount === $enhancement_docscorrectCount) {
                $enhancement_docs = true;
            } else {
                $enhancement_docs = false;
            }

            $claim_investigationstotalCount = $preauth_register->claim_investigations()->count();
            $claim_investigationscorrectCount = $preauth_register->claim_investigations()->where($type.'_status', 'Correct')->count();

            if($claim_investigationstotalCount === $claim_investigationscorrectCount) {
                $claim_investigations = true;
            } else {
                $claim_investigations = false;
            }

            if($investigations && $claim_investigations && $enhancement_docs && $claim_other_doc && $beneficiary_verification_form && $hospital_certificate && $feedback_form && $hospital_bill && $discharge_summary && $post_surgery_photo && $in_treatment_photo && $mortality_audit_report && $death_summary && $death_certificate && $claim_query_add_doc && $claim_query_supporting_doc && $preauth_query_add_doc && $committee_query_supporting_doc && $ceo_query_supporting_doc && $acs_query_supporting_doc && $preauth_query_supporting_doc && $born_baby_birth_certificate && $erroneous_raise_supporting_doc && $erroneous_query_supporting_doc) {
                $result = true;
            }

            return $result;
        } else {
            return false;
        }
    }

    public function checkStepSeen($id, $tab, $type) {
        $preauth_register = PreauthRegister::where('id',$id)->first();
        if($preauth_register) {
            $check = $preauth_register->tabs()->where('tab', $tab)->where('type', $type)->where('is_open', 1)->first();
            if($check) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public static function checkDocStepSeen($id, $type) {
        $preauth_register = PreauthRegister::where('id',$id)->first();
        if($preauth_register) {
            $count = $preauth_register->investigations()->whereNull($type)->get()->count();
            $status = json_decode($preauth_register->{$type}, true);
            if($preauth_register->born_baby_birth_certificate){
                if(!@$status['born_baby_birth_certificate']){
                    $count=1;
                }
            }

            if($preauth_register->hospital_declaration_form){
                if(!@$status['hospital_declaration_form']){
                    $count=1;
                }
            }

            if($preauth_register->preauth_query_supporting_doc){
                if(!@$status['preauth_query_supporting_doc']){
                    $count=1;
                }
            }
            if($preauth_register->preauth_query_add_doc){
                if(!@$status['preauth_query_add_doc']){
                    $count=1;
                }
            }
            
            $enhancement_count = $preauth_register->enhancement_docs()->whereNull($type)->get()->count();
            if($enhancement_count){
                $count=1;
            }
            if($preauth_register->committee_query_supporting_doc){
                if(!@$status['committee_query_supporting_doc']){
                    $count=1;
                }
            }
            if($preauth_register->ceo_query_supporting_doc){
                if(!@$status['ceo_query_supporting_doc']){
                    $count=1;
                }
            }
            if($preauth_register->acs_query_supporting_doc){
                if(!@$status['acs_query_supporting_doc']){
                    $count=1;
                }
            }
            if($type != 'ppd_status'){

                if($preauth_register->claim_query_supporting_doc){
                    if(!@$status['claim_query_supporting_doc']){
                        $count=1;
                    }
                }
                if($preauth_register->claim_query_add_doc){
                    if(!@$status['claim_query_add_doc']){
                        $count=1;
                    }
                }
                if($preauth_register->death_certificate){
                    if(!@$status['death_certificate']){
                        $count=1;
                    }
                }
                if($preauth_register->death_summary){
                    if(!@$status['death_summary']){
                        $count=1;
                    }
                }
                if($preauth_register->mortality_audit_report){
                    if(!@$status['mortality_audit_report']){
                        $count=1;
                    }
                }
                if($preauth_register->in_treatment_photo){
                    if(!@$status['in_treatment_photo']){
                        $count=1;
                    }
                }
                if($preauth_register->post_surgery_photo){
                    if(!@$status['post_surgery_photo']){
                        $count=1;
                    }
                }
                if($preauth_register->discharge_summary){
                    if(!@$status['discharge_summary']){
                        $count=1;
                    }
                }
                if($preauth_register->feedback_form){
                    if(!@$status['feedback_form']){
                        $count=1;
                    }
                }
                if($preauth_register->beneficiary_verification_form){
                    if(!@$status['beneficiary_verification_form']){
                        $count=1;
                    }
                }
                if($preauth_register->hospital_certificate){
                    if(!@$status['hospital_certificate']){
                        $count=1;
                    }
                }
                if($preauth_register->hospital_bill){
                    if(!@$status['hospital_bill']){
                        $count=1;
                    }
                }
                if($preauth_register->claim_other_doc){
                    if(!@$status['claim_other_doc']){
                        $count=1;
                    }
                }
                if($preauth_register->erroneous_raise_supporting_doc){
                    if(!@$status['erroneous_raise_supporting_doc']){
                        $count=1;
                    }
                }
                if($preauth_register->erroneous_query_supporting_doc){
                    if(!@$status['erroneous_query_supporting_doc']){
                        $count=1;
                    }
                }
                $claim_count = $preauth_register->claim_investigations()->whereNull($type)->get()->count();
                if($claim_count){
                    $count=1;
                }
            }
            if($count == 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function getStaticBankDetails($state_id) {
        $bank = StateBankDetail::where('state_id', $state_id)->first();
        return $bank;
    }
    public static function getPastHistory($preauth_register_id){
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->first();
        $past_hostories = [];
        if($preauth_register) {
            $past_hostories = PreauthRegister::whereNot('id',$preauth_register_id)->where('benificiary_id',$preauth_register->benificiary_id)->get();
        }
        return $past_hostories;
    }
    public static function checkPermission($preauth_register_id){
        $check = '';
        if(auth()->user()->role_id == 4){
            $query = PreauthRegister::query();
            $hospital_id = auth()->user()->hospital_id;
            // $statuses = [
            //     PreauthRegister::STATUS_REGISTER,
            //     PreauthRegister::STATUS_PREAUTH_PENDING,
            //     PreauthRegister::STATUS_CANCELLED,
            //     PreauthRegister::STATUS_PREAUTH_CANCELLED,
            //     PreauthRegister::STATUS_PREAUTH_APPROVED,
            //     PreauthRegister::STATUS_PREAUTH_REJECTED,
            //     PreauthRegister::STATUS_PREAUTH_QUERIED,
            //     PreauthRegister::STATUS_CLAIM_SUBMITTED,
            //     PreauthRegister::STATUS_CLAIM_QUERIED,
            //     PreauthRegister::STATUS_CLAIM_PENDING,
            //     PreauthRegister::STATUS_CLAIM_REJECTED,
            //     PreauthRegister::STATUS_SHA_CLAIM_REJECTED,
            //     PreauthRegister::STATUS_CLAIM_APPROVED,
            // ];
            // $query->whereIn('status', $statuses);
            $query->where('id', $preauth_register_id);
            $query->where('hospital_id',$hospital_id);
            $check = $query->first();
        }elseif(auth()->user()->role_id == 13){
            $query = PreauthRegister::query();
            
            // $statuses = [
            //     PreauthRegister::STATUS_PREAUTH_PENDING,
            //     PreauthRegister::STATUS_PREAUTH_APPROVED,
            //     PreauthRegister::STATUS_PREAUTH_REJECTED,
            //     PreauthRegister::STATUS_PREAUTH_QUERIED,
            // ];
            // $query->whereIn('status', $statuses);
            $query->where('id', $preauth_register_id);
            $check = $query->first();
        }elseif(auth()->user()->role_id == 15){
            $query = PreauthRegister::query();
            
            // $statuses = [
            //     PreauthRegister::STATUS_CLAIM_PENDING,
            //     PreauthRegister::STATUS_CPD_CLAIM_PENDING,
            // ];
            // $query->whereIn('status', $statuses);
            // $query->where('id', $preauth_register_id);
            $check = $query->first();
        }elseif(auth()->user()->role_id == 14){
            $query = PreauthRegister::query();
            
            // $statuses = [
            //     PreauthRegister::STATUS_CPD_CLAIM_PENDING,
            //     PreauthRegister::STATUS_CLAIM_APPROVED,
            //     PreauthRegister::STATUS_CLAIM_REJECTED,
            //     PreauthRegister::STATUS_CLAIM_QUERIED,
            //     PreauthRegister::STATUS_SHA_CLAIM_QUERIED,
            // ];
            // $query->whereIn('status', $statuses);
            $query->where('id', $preauth_register_id);
            $check = $query->first();
        }elseif(auth()->user()->role_id == 16){
            $query = PreauthRegister::query();
            
            // $statuses = [
            //     PreauthRegister::STATUS_CLAIM_APPROVED,
            //     PreauthRegister::STATUS_ACO_CLAIM_APPROVED,
            //     PreauthRegister::STATUS_ACO_CLAIM_REJECTED,
            //     PreauthRegister::STATUS_ACO_CLAIM_QUERIED,
            // ];
            // $query->whereIn('status', $statuses);
            $query->where('id', $preauth_register_id);
            $check = $query->first();
        }elseif(auth()->user()->role_id == 17){
            $query = PreauthRegister::query();
            
            // $statuses = [
            //     PreauthRegister::STATUS_ACO_CLAIM_APPROVED,
            //     PreauthRegister::STATUS_SHA_CLAIM_APPROVED,
            //     PreauthRegister::STATUS_SHA_CLAIM_REJECTED,
            //     PreauthRegister::STATUS_SHA_CLAIM_QUERIED,
            //     PreauthRegister::STATUS_CLAIM_QUERIED,
            //     PreauthRegister::STATUS_CLAIM_APPROVED,
            //     PreauthRegister::STATUS_CLAIM_REJECTED,
            // ];
            // $query->whereIn('status', $statuses);
            $query->where('id', $preauth_register_id);
            $check = $query->first();
        }else{
            $query = PreauthRegister::query();
            $query->where('id', $preauth_register_id);
            $check = $query->first();
        }
        if(!$check){
            abort(403, 'Unauthorized');
        }
    }
    public static function checkandUpdateSurgicalPackage($preauth_register_id){
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->first();
        if($preauth_register->scheme_id != 1){
            $surgical_procedures = PreauthProcedure::where('preauth_register_id', $preauth_register_id)
                    ->whereHas('procedure', function ($query) {
                        $query->where('medical_or_surgical', 'Surgical')
                            ->whereRaw("LOWER(TRIM(procedure_label)) NOT LIKE ?", ['%add-onprocedure%']);
                    })
                    ->with(['procedure'])
                    ->get()
                    ->sortByDesc(function ($item) {
                        return $item->procedure->price ?? 0;
                    })
            ->values();

            
            foreach($surgical_procedures as $key => $surgical_procedure){
                $procedure_price = @$surgical_procedure->procedure->price;
                if($key == 0){
                    $surgical_procedure->adj_per = 100;
                    $surgical_procedure->procedure_price = $procedure_price;
                }else if($key == 1){
                    if($procedure_price){
                        $surgical_procedure->adj_per = 50;
                        $surgical_procedure->procedure_price = $procedure_price/2;
                    }
                }else{
                    if($procedure_price){
                        $surgical_procedure->adj_per = 25;
                        $surgical_procedure->procedure_price = $procedure_price * 0.25;
                    }
                }
                if($surgical_procedure->incentive_per != 0){
                    $surgical_procedure->incentive = ($surgical_procedure->incentive_per*$surgical_procedure->procedure_price)/100;
                }
                $surgical_procedure->save();
            }
        }
    }
    public static function updatePackageCalculation($preauth_register_id,$rejected_ids,$rejected_implant_ids){
        $preauth_register = PreauthRegister::where('id',$preauth_register_id)->first();
        $procedures=[];
        if($preauth_register->scheme_id != 1){
            $ids=[];
            $surgical_procedures = PreauthProcedure::where('preauth_register_id', $preauth_register_id)
                    ->whereHas('procedure', function ($query) {
                        $query->where('medical_or_surgical', 'Surgical')
                            ->whereRaw("LOWER(TRIM(procedure_label)) NOT LIKE ?", ['%add-onprocedure%']);
                    })
                    ->whereNotIn('id', collect($rejected_ids)->flatten()->toArray())
                    ->with(['procedure'])
                    ->get()
                    ->sortByDesc(function ($item) {
                        return $item->procedure->price ?? 0;
                    })
                    ->values();
            // echo "<pre>";print_r($surgical_procedures);exit;
            foreach($surgical_procedures as $key => $surgical_procedure){
                $procedure_price = @$surgical_procedure->procedure->price;
                if($key == 0){
                    $surgical_procedure->adj_per = 100;
                    $surgical_procedure->procedure_price = $procedure_price;
                }else if($key == 1){
                    if($procedure_price){
                        $surgical_procedure->adj_per = 50;
                        $surgical_procedure->procedure_price = $procedure_price/2;
                    }
                }else{
                    if($procedure_price){
                        $surgical_procedure->adj_per = 25;
                        $surgical_procedure->procedure_price = $procedure_price * 0.25;
                    }
                }

                // if($surgical_procedure->deducted_amount > 0) {
                //     $surgical_procedure->procedure_price = $surgical_procedure->procedure_price - $surgical_procedure->deducted_amount;
                // }

                if($surgical_procedure->incentive_per != 0){
                    $surgical_procedure->incentive = ($surgical_procedure->incentive_per*$surgical_procedure->procedure_price)/100;
                }
                if(in_array($surgical_procedure->id,collect($rejected_implant_ids)->flatten()->toArray())){
                    $surgical_procedure->implant_qty = 0;
                    $surgical_procedure->implant_price = 0;
                }
                $ids[]= $surgical_procedure->id;
                $procedures[]= $surgical_procedure;
            }
            $except_procedures = PreauthProcedure::where('preauth_register_id', $preauth_register_id)
                            ->whereNotIn('id', collect($ids)->flatten()->toArray())
                            ->whereNotIn('id', collect($rejected_ids)->flatten()->toArray())
                            ->get();
            $procedures = array_merge($procedures, $except_procedures->all());

        }else{
            $rejected_implant_ids = collect($rejected_implant_ids)->flatten()->toArray();
            $procedures = PreauthProcedure::where('preauth_register_id', $preauth_register_id)
            ->whereNotIn('id', collect($rejected_ids)->flatten()->toArray())
            ->get()
            ->map(function ($procedure) use ($rejected_implant_ids) {
                if (in_array($procedure->procedure_id, $rejected_implant_ids)) {
                    $procedure->implant_qty = 0;
                    $procedure->implant_price = 0;
                }
                return $procedure;
            });
        }
        return $procedures;
    }
    public static function checkHospitalActive($preauth_register_id){
        $preauth_register = PreauthRegister::find($preauth_register_id);
        if(@$preauth_register->hospital->status == 'Empanelled' || @$preauth_register->hospital->status == 'Re-Empanelled' || @$preauth_register->hospital->is_empanelled == 1 || (@$preauth_register->hospital->is_empanelled == 5 && @$preauth_register->hospital->is_preauth_stop == 0 )){
            return true;
        }else{
            return false;
        }
    }
    public static function expiredDocs($preauth_register_id){
        $preauth_register = PreauthRegister::find($preauth_register_id);
        $docs = ExpiredDocument::where('hospital_id', $preauth_register->hospital_id)
            ->where('is_updated', 0)
            ->whereDate('expiry_date', '<=', date('Y-m-d'))
            ->get()
            ->pluck('expiry_date', 'document_name');

        if ($docs->isNotEmpty()) {
            $doc_html = collect($docs)->map(fn($expiry_date, $document_name) => "$document_name ($expiry_date)")->implode(', ');
            return 'Expired Documents: ' . $doc_html;
        }else{
            return '';
        }
    }
    public static function fillCompleteStep($preauth_register_id){

        $preauth_register = PreauthRegister::find($preauth_register_id);
        $general_info = GeneralInfo::where('preauth_register_id',$preauth_register->id)->first();
        $family_history = FamilyHistory::where('preauth_register_id',$preauth_register->id)->first();
        $personal_history = PersonalHistory::where('preauth_register_id',$preauth_register->id)->first();
        $authentication_consent = AuthenticationConsent::where('preauth_register_id',$preauth_register->id)->first();
        $admission_details = AdmissionDetails::where('preauth_register_id',$preauth_register->id)->first();
        $preauth_diagnosis = PreauthDiagnosis::where('preauth_register_id',$preauth_register->id)->get();
        $procedures = PreauthProcedure::where('preauth_register_id', $preauth_register->id)->get();
        $preauth_investigations = PreauthInvestigation::where('preauth_register_id', $preauth_register->id)->get();
        $preauth_investigation_status = Helpers::getPreauthInvestigationsStatus($preauth_register->id);
        $preauth_teams = PreauthCareTeam::where('preauth_register_id',$preauth_register->id)->get();

        $response['medical'] = false;
        $response['admission'] = false;
        $response['treatment'] = false;
        if(@$general_info && @$family_history && @$personal_history){
            $response['medical'] = true;
        }
        if(@$authentication_consent && @$admission_details){
            $response['admission'] = true;
        }
        if((@$preauth_diagnosis->count() > 0) && (@$procedures->count() > 0) && $preauth_investigation_status && (@$preauth_teams->count() > 0)){
            $response['treatment'] = true;
        }
        return $response;
    }

    public static function stepCheck($step, $hospital_id, $type, $who) {
        $check = TabStatus::where('tab', $step)->where('type', $type)->where('hospital_id', $hospital_id);
        if($who == "verifier") {
            $check = $check->where('is_verifier', 1)->first();
        }

        if($who == "dec") {
            $check = $check->where('is_dec', 1)->first();
        }  

        if($who == "sec") {
            $check = $check->where('is_sec', 1)->first();
        }    
        
        if($check) {
            return true;
        } else {
            return false;
        }
    }

    public static function saveTabStatus($step, $type, $hospital_id, $who) {
        
        $requestarr = [
            'tab' => $step,
            'type' => $type,
            'hospital_id' => $hospital_id,
        ];

        $checkerror = [
            'tab' => $step,
            'type' => $type,
            'hospital_id' => $hospital_id,
        ];
        
        if($who == 'verifier') {
            $checkerror['is_verifier'] = 1;
        }
        if($who == 'dec') {
            $checkerror['is_dec'] = 1;
        }
        if($who == 'sec') {
            $checkerror['is_sec'] = 1;
        }

        $tab = TabStatus::updateOrCreate($requestarr,$checkerror);

        return $tab;
    }

    public static function TransferOldData($hospitalId) {
        $hospital = UHospitals::where('main_hospitalid', $hospitalId)->first();
        $hospital->images()->delete();
        $hospital->documents()->delete();
        $hospital->hospitalAddress()->delete();
        $hospital->specialities()->delete();
        $hospital->licenses()->delete();
        $hospital->services()->delete();
        $hospital->humanResources()->delete();
        $hospital->ceo()->delete();
        $hospital->hospitalTeam()->delete();
        $hospital->hospitalAccreditation()->delete();
        $hospital->financialInformation()->delete();
        $hospital->taxDetails()->delete();
        $hospital->delete();

        self::MakeCopyData($hospitalId);
    }

    public static function MakeCopyData($hospitalId) {
        $hospital = Hospitals::where('id', $hospitalId)->first();
        if($hospital) {
            // HospitalData
            $hospitalData = $hospital->toArray();
            if(!empty($hospitalData)) {
                unset($hospitalData['is_upgrade_application'], $hospitalData['is_payment_stop'], $hospitalData['is_preauth_stop'], $hospitalData['exists_hospital_id']);
                $hospitalData['main_hospitalid'] = $hospital->id;
                $hospitalData['created_at'] = $hospital->created_at;
                $hospitalData['updated_at'] = $hospital->updated_at;
                UHospitals::insert($hospitalData);
            }

            $images = $hospital->images;
            if(sizeof($images) > 0) {
                foreach ($images as $key => $value) {
                    $imagesData = $value->toArray();
                    if(!empty($imagesData)) {
                        unset($imagesData['id']);
                        $imagesData['hospital_id'] = $value->hospital_id;
                        $imagesData['created_at'] = $value->created_at;
                        $imagesData['updated_at'] = $value->updated_at;
                        UHospitalImages::create($imagesData);
                    }
                }
            }

            $documents = $hospital->documents;
            if(sizeof($documents) > 0) {
                foreach ($documents as $key => $value) {
                    $documentsData = $value->toArray();
                    if(!empty($documentsData)) {
                        unset($documentsData['id']);
                        $documentsData['hospital_id'] = $value->hospital_id;
                        $documentsData['main_hospitalid'] = $value->hospital_id;
                        $documentsData['old_id'] = $value->id;
                        $documentsData['created_at'] = $value->created_at;
                        $documentsData['updated_at'] = $value->updated_at;
                        UHospitalDocument::create($documentsData);
                    }
                }
            }
            
            // Hospital Address
            $hospitalAddress = $hospital->hospitalAddress->toArray();
            if(!empty($hospitalAddress)) {
                $hospitalAddress['main_hospitalid'] = $hospitalAddress['hospital_id'];
                $hospitalAddress['created_at'] = $hospital->hospitalAddress->created_at;
                $hospitalAddress['updated_at'] = $hospital->hospitalAddress->updated_at;
                UHospitalAddress::insert($hospitalAddress);
            }

            // Specialities
            $hospitalspeciality = $hospital->specialities;
            if(sizeof($hospitalspeciality) > 0) {
                foreach ($hospitalspeciality as $key => $value) {
                    $specData = $value->toArray();
                    if(!empty($specData)) {
                        unset($specData['id']);
                        $specData['main_hospitalid'] = $value->hospital_id;
                        $specData['old_id'] = $value->id;
                        $specData['created_at'] = $value->created_at;
                        $specData['updated_at'] = $value->updated_at;
                        UHospitalSpeciality::create($specData);
                    }
                }
            }

            // License
            $hospitallicenses = $hospital->licenses;
            if(sizeof($hospitallicenses) > 0) {
                foreach ($hospitallicenses as $key => $value) {
                    $licensesData = $value->toArray();
                    if(!empty($licensesData)) {
                        unset($licensesData['id']);
                        
                        $licensesData['main_hospitalid'] = $value->hospital_id;
                        $licensesData['old_id'] = $value->id;
                        $licensesData['created_at'] = $value->created_at;
                        $licensesData['updated_at'] = $value->updated_at;
                        UHospitalLicense::create($licensesData);
                    }
                }
            }

            // Service
            $hospitalservices = $hospital->services;
            if(sizeof($hospitalservices) > 0) {
                foreach ($hospitalservices as $key => $value) {
                    $servicesData = $value->toArray();
                    if(!empty($servicesData)) {
                        unset($servicesData['id']);
                        $servicesData['main_hospitalid'] = $value->hospital_id;
                        $servicesData['old_id'] = $value->id;
                        $servicesData['created_at'] = $value->created_at;
                        $servicesData['updated_at'] = $value->updated_at;
                        UHospitalServices::create($servicesData);
                    }
                }
            }

            // CEO
            $hospitalceo = $hospital->ceo;
            if(!empty($hospitalceo)) {
                $ceoData = $hospitalceo->toArray();
                if(!empty($ceoData)) {
                    unset($ceoData['id']);
                    $ceoData['main_hospitalid'] = $hospitalceo->hospital_id;
                    $ceoData['old_id'] = $hospitalceo->id;
                    $ceoData['created_at'] = $hospitalceo->created_at;
                    $ceoData['updated_at'] = $hospitalceo->updated_at;
                    UHospitalCeo::create($ceoData);
                }
            }

            // humanresource
            $hospitalhumanResources = $hospital->humanResources;
            if(sizeof($hospitalhumanResources) > 0) {
                foreach ($hospitalhumanResources as $key => $value) {
                    $humanResourcesData = $value->toArray();
                    if(!empty($humanResourcesData)) {
                        unset($humanResourcesData['id']);
                        $humanResourcesData['main_hospitalid'] = $value->hospital_id;
                        $humanResourcesData['old_id'] = $value->id;
                        $humanResourcesData['created_at'] = $value->created_at;
                        $humanResourcesData['updated_at'] = $value->updated_at;
                        UHospitalHumanResource::create($humanResourcesData);
                    }
                }
            }

            // HospitalTeam
            $hospitalhospitalTeam = $hospital->hospitalTeam;
            if(sizeof($hospitalhospitalTeam) > 0) {
                foreach ($hospitalhospitalTeam as $key => $value) {
                    $hospitalTeamData = $value->toArray();
                    if(!empty($hospitalTeamData)) {
                        unset($hospitalTeamData['id']);
                        $hospitalTeamData['main_hospitalid'] = $value->hospital_id;
                        $hospitalTeamData['old_id'] = $value->id;
                        $hospitalTeamData['created_at'] = $value->created_at;
                        $hospitalTeamData['updated_at'] = $value->updated_at;
                        UHospitalTeam::create($hospitalTeamData);
                    }
                }
            }

            // Accreditation
            $hospitalAccreditation = $hospital->hospitalAccreditation;
            if(!empty($hospitalAccreditation)) {
                $hospitalAccreditationData = $hospitalAccreditation->toArray();
                if(!empty($hospitalAccreditation)) {
                    unset($hospitalAccreditationData['id']);
                    $hospitalAccreditationData['main_hospitalid'] = $hospitalAccreditation->hospital_id;
                    $hospitalAccreditationData['old_id'] = $hospitalAccreditation->id;
                    $hospitalAccreditationData['created_at'] = $hospitalAccreditation->created_at;
                    $hospitalAccreditationData['updated_at'] = $hospitalAccreditation->updated_at;
                    UHospitalAccreditation::create($hospitalAccreditationData);
                }
            }

            // FinancialInformation
            $UFinancialInformation = $hospital->financialInformation;
            if(!empty($UFinancialInformation)) {
                $UFinancialInformationData = $UFinancialInformation->toArray();
                if(!empty($UFinancialInformationData)) {
                    unset($UFinancialInformationData['id']);
                    $UFinancialInformationData['main_hospitalid'] = $UFinancialInformation->hospital_id;
                    $UFinancialInformationData['old_id'] = $UFinancialInformation->id;
                    $UFinancialInformationData['created_at'] = $UFinancialInformation->created_at;
                    $UFinancialInformationData['updated_at'] = $UFinancialInformation->updated_at;
                    UFinancialInformation::create($UFinancialInformationData);
                }
            }

            //  TaxDetails
            $taxDetails = $hospital->taxDetails;
            if(!empty($taxDetails)) {
                $taxDetailsData = $taxDetails->toArray();
                if(!empty($taxDetailsData)) {
                    unset($taxDetailsData['id']);
                    $taxDetailsData['main_hospitalid'] = $taxDetails->hospital_id;
                    $taxDetailsData['old_id'] = $taxDetails->id;
                    $taxDetailsData['created_at'] = $taxDetails->created_at;
                    $taxDetailsData['updated_at'] = $taxDetails->updated_at;
                    UTaxDetails::create($taxDetailsData);
                }
            }

            $hospital->is_upgrade_application = 0; 
            $hospital->save();
        }
    }

    public static function TransferDataCopytoLive($hospitalId) {
        $hospital = UHospitals::where('main_hospitalid', $hospitalId)->first();
        $checkhospital = Hospitals::where('id', $hospital->main_hospitalid)->first();
        if(!empty($hospital)) {
            if($checkhospital) {
                $hospitalData = $hospital->toArray();
                unset($hospitalData['main_hospitalid']);
                $hospitalData['created_at'] = $hospital->created_at;
                $hospitalData['updated_at'] = $hospital->updated_at;
                $checkhospital->update($hospitalData);
            }            
        }

        $images = $hospital->images;
        if(sizeof($images) > 0) {
            $checkhospital->images()->delete();
            foreach ($images as $key => $value) {
                $imagesData = $value->toArray();
                if(!empty($imagesData)) {
                    unset($imagesData['id']);
                    $imagesData['hospital_id'] = $value->hospital_id;
                    $imagesData['created_at'] = $value->created_at;
                    $imagesData['updated_at'] = $value->updated_at;
                    $checkhospital->images()->insert($imagesData);
                }
            }
        }

        $hospitalAddress = $hospital->hospitalAddress;
        if(!empty($hospitalAddress)) {
            $existsAddress = $checkhospital->hospitalAddress;
            if($existsAddress) {
                $haddress = $hospitalAddress->toArray();
                unset($haddress['main_hospitalid']);
                $haddress['created_at'] = $hospitalAddress->created_at;
                $haddress['updated_at'] = $hospitalAddress->updated_at;
                $existsAddress->update($haddress);
            }
        }

        $hospitalspeciality = $hospital->specialities;
        if(sizeof($hospitalspeciality) > 0) {
            foreach ($hospitalspeciality as $key => $value) {
                $hspec = $value->toArray();
                unset($hspec['main_hospitalid'], $hspec['old_id'], $hspec['id']);
                $existaspecialities = $checkhospital->specialities()->where('id', $value->old_id)->first();
                if($existaspecialities) {
                    $existaspecialities->update($hspec);                   
                } else {
                     $specdata = HospitalSpeciality::create($hspec);
                     $value->old_id = $specdata->id;
                     $value->main_hospitalid = $specdata->hospital_id;
                     $value->save();
                }
            }
        }

        $hospitalservices = $hospital->services;
        if(sizeof($hospitalservices) > 0) {
            foreach ($hospitalservices as $key => $value) {
                $hservices = $value->toArray();
                unset($hservices['main_hospitalid'], $hservices['old_id'], $hservices['id']);
                $existsServices = $checkhospital->services()->where('id', $value->old_id)->first();
                if($existsServices) {
                    $existsServices->update($hservices);                   
                } else {
                     $servicdata = HospitalServices::create($hservices);
                     $value->old_id = $servicdata->id;
                     $value->main_hospitalid = $servicdata->hospital_id;
                     $value->save();
                }
            }
        }

        $hospitallicenses = $hospital->licenses;
        if(sizeof($hospitallicenses) > 0) {
            foreach ($hospitallicenses as $key => $value) {
                $hlicenses = $value->toArray();
                unset($hlicenses['main_hospitalid'], $hlicenses['old_id'], $hlicenses['id']);
                $existslicenses = $checkhospital->licenses()->where('id', $value->old_id)->first();
                if($existslicenses) {
                    $existslicenses->update($hlicenses);                   
                } else {
                    $ldata = HospitalLicense::create($hlicenses);
                    $value->old_id = $ldata->id;
                    $value->main_hospitalid = $ldata->hospital_id;
                    $value->save();
                }
            }
        }

        $hospitalceo = $hospital->ceo;
        if(!empty($hospitalceo)) {
            $existsceo = $checkhospital->ceo;
            if($existsceo) {
                $hceo = $hospitalceo->toArray();
                unset($hceo['main_hospitalid'], $hceo['old_id'], $hceo['id']);
                $hceo['created_at'] = $hospitalceo->created_at;
                $hceo['updated_at'] = $hospitalceo->updated_at;
                $existsceo->update($hceo);
            }
        }
        
        // FinancialInformation
        $UFinancialInformation = $hospital->financialInformation;
        if(!empty($UFinancialInformation)) {
            $existsfinfo = $checkhospital->financialInformation;
            if($existsfinfo) {
                $hinfodata = $UFinancialInformation->toArray();
                unset($hinfodata['main_hospitalid'], $hinfodata['old_id'], $hinfodata['id']);
                $hinfodata['created_at'] = $UFinancialInformation->created_at;
                $hinfodata['updated_at'] = $UFinancialInformation->updated_at;
                $existsfinfo->update($hinfodata);   
            }
        }

        //  TaxDetails
        $taxDetails = $hospital->taxDetails;
        if(!empty($taxDetails)) {
            $existstax = $checkhospital->taxDetails;
            if($existstax) {
                $taxdata = $taxDetails->toArray();
                unset($taxdata['main_hospitalid'], $taxdata['old_id'], $taxdata['id']);
                $taxdata['created_at'] = $taxDetails->created_at;
                $taxdata['updated_at'] = $taxDetails->updated_at;
                $existstax->update($taxdata);
            }
        }

        $hospitalAccreditation = $hospital->hospitalAccreditation;
        if(!empty($hospitalAccreditation)) {
            $existsaccr = $checkhospital->hospitalAccreditation;
            if(!empty($existsaccr)) {
                $haccr = $hospitalAccreditation->toArray();
                unset($haccr['main_hospitalid'], $haccr['old_id'], $haccr['id']);
                $haccr['created_at'] = $hospitalAccreditation->created_at;
                $haccr['updated_at'] = $hospitalAccreditation->updated_at;

                // $selected_ids = $hospitalAccreditation->speciality_ids ? json_decode($hospitalAccreditation->speciality_ids, true) : [];
                // $getoldids = $hospital->specialities()->whereIn('')

                $existsaccr->update($haccr);
            }
        }

        $hospitalhospitalTeam = $hospital->hospitalTeam;
        if(sizeof($hospitalhospitalTeam) > 0) {
            HospitalTeam::where('hospital_id', $hospitalId)->whereNotIn('id', $hospitalhospitalTeam->pluck('old_id')->toArray())->delete();
            foreach ($hospitalhospitalTeam as $key => $value) {
                $hTeamData = $value->toArray();
                $existsteam = $checkhospital->hospitalTeam()->where('id', $value->old_id)->first();
                unset($hTeamData['main_hospitalid'], $hTeamData['old_id'], $hTeamData['id']);
                if($existsteam) {
                    $existsteam->update($hTeamData);
                } else {
                    $teamdata = HospitalTeam::create($hTeamData);
                    $value->old_id = $teamdata->id;
                    $value->main_hospitalid = $teamdata->hospital_id;
                    $value->save();
                }
            }
        }

        $hospitalhumanResources = $hospital->humanResources;
        if(sizeof($hospitalhumanResources) > 0) {
            HospitalHumanResource::where('hospital_id', $hospitalId)->whereNotIn('id', $hospitalhumanResources->pluck('old_id')->toArray())->delete();
            foreach ($hospitalhumanResources as $key => $value) {
                $hhrdata = $value->toArray();
                $existshr = $checkhospital->humanResources()->where('id', $value->old_id)->first();
                unset($hhrdata['main_hospitalid'], $hhrdata['old_id'], $hhrdata['id']);
                if($existshr) {
                    $existshr->update($hhrdata);
                } else {
                    $hrdata = HospitalHumanResource::create($hhrdata);
                    $value->old_id = $hrdata->id;
                    $value->main_hospitalid = $hrdata->hospital_id;
                    $value->save();
                }
            }
        }

        $checkhospital->is_upgrade_application = 0;
        $checkhospital->save();
    }

    public static function checkdataupdateornot($hospital_id) {
        $hospital = Hospitals::where('id', $hospital_id)->first();
        $upgradehospitals = $hospital->upgradeHospital;
        if($upgradehospitals) {
            if($upgradehospitals->establishment_details || $upgradehospitals->address || $upgradehospitals->scheme || $upgradehospitals->speciality || $upgradehospitals->services || $upgradehospitals->statutory_licences || $upgradehospitals->human_resources || $upgradehospitals->quality_accreditation || $upgradehospitals->financial_information || $upgradehospitals->tax_details) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function isbtnenabled($status) {
        if($status == "Draft" || $status == "Queried" || $status == "Response Required From Facility" || $status == "Empanelled") {
            return true;
        } else {
            return false;
        }
    }

    public static function isbtnenablednyId($id) {
        $hospital = UHospitals::where('main_hospitalid', $id)->first();
        if($hospital->status == "Draft" || $hospital->status == "Queried" || $hospital->status == "Response Required From Facility" || $hospital->status == "Empanelled") {
            return true;
        } else {
            return false;
        }
    }

    public static function complianceCount($hospitalid, $categoryid) {
        $hospital = Hospitals::where('id', $hospitalid)->first();

        $count = $hospital->qualityAudit()->where('category_id', $categoryid)->where('action', "Compliance")->where('month', date('m'))->where('year', date('Y'))->count();

        return $count;
    }
    public static function noncomplianceCount($hospitalid, $categoryid) {
        $hospital = Hospitals::where('id', $hospitalid)->first();

        $count = $hospital->qualityAudit()->where('category_id', $categoryid)->where('action', "Non-Compliance")->where('month', date('m'))->where('year', date('Y'))->count();

        return $count;
    }
    
    public static function checkmonthaudit($hospitalid) {
        $hospital = Hospitals::where('id', $hospitalid)->first();
        $category = AuditCategory::get();
        foreach ($category as $key => $value) {
            $check = $hospital->qualityAudit()->where('category_id', $value->id)->where('month', date('m'))->where('year', date('Y'))->first();
            if(!$check) {
                return "Quality Audit is Not Submitted For " . date('F Y');
            }
        }

        return "All audits are submitted for " . date('F Y');
    }

    public static function existaudit($hospital_id, $categoryid, $subcategoryid, $auditid) {
        return HospitalQualityAudit::where([
            "hospital_id" => $hospital_id,
            "category_id" => $categoryid,
            "sub_category_id" => $subcategoryid,
            "audit_id" => $auditid,
            "year" => date('Y'),
            "month" => date('m')
        ])->first();
    }

    public static function mainstatus() {
        return [
            [
                "name" => "Initiate General Communication",
                "block" => "dateblock"
            ],
            [
                "name" =>  "Initiate Show Cause Notice",
                "block" => "dateblock"
            ],
            [
                "name" => "Initiate Immediate Suspension",
                "block" => "dateblock"
            ],
            [
                "name" =>  "Initiate Penalty",
                "block" => "penalty"
            ],
            [
                "name" =>  "FIR",
                "block" => "fir",
            ],
            [
                "name" => "De-Empanelled",
                "block" => "none"
            ],
            [
                "name" => "Watch List", 
                "block" => "none"
            ]
        ];
    }

    public static function edcstatus() {
        return [
            "Initiate General Communication" => [
                "main_status" => "Initiate General Communication",
                "sub_statuses" => [
                    "Initiate General Communication" => [
                        "sec" => [],
                        "hospital" => ["Responded on General Communication"]
                    ],
                    "Responded on General Communication" => [
                        "sec" => ["SEC Responded On General Communication"],
                        "hospital" => []
                    ],
                    "SEC Responded On General Communication" => [
                        "sec" => [],
                        "hospital" => ["Responded on General Communication"]
                    ]
                ]
            ],
            "Initiate Immediate Suspension" => [
                "main_status" => "Initiate Immediate Suspension",
                "sub_statuses" => [
                    "Initiate Immediate Suspension" => [
                        "sec" => [],
                        "hospital" => ["Responded on Immediate Suspension"] 
                    ],
                    "Responded on Immediate Suspension" => [
                        "sec" => ["Initiate Blacklist", "De-Empanelled", "Revoke Suspension"],
                        "hospital" => []
                    ],
                    "Initiate Blacklist" => [
                        "sec" => ["Revoke Blacklist"],
                        "hospital" => []
                    ]
                ]
            ],
            "Initiate Show Cause Notice" => [
                "main_status" => "Initiate Show Cause Notice",
                "sub_statuses" => [
                    "Initiate Show Cause Notice" => [ 
                        "sec" => [],
                        "hospital" => ["Responded on Show Cause Notice"]
                    ],
                    "Responded on Show Cause Notice" => [
                        "sec" => ["Initiate Blacklist", "Close The Matter" ],
                        "hospital" => []
                    ],
                    "Initiate Blacklist" => [
                        "sec" => ["Revoke Blacklist"],
                        "hospital" => []
                    ]
                ]
            ],
            "Initiate Penalty" => [
                "main_status" => "Initiate Penalty",
                "sub_statuses" => [
                    "Initiate Penalty" => [
                        "sec" => [],
                        "hospital" => []
                    ],
                ]
            ],
            "FIR" => [
                "main_status" => "FIR",
                "sub_statuses" => [
                    "FIR" => [
                        "sec" => [],
                        "hospital" => []
                    ]
                ]
            ],
            "De-Empanelled" => [
                "main_status" => "De-Empanelled",
                "sub_statuses" => [
                    "De-Empanelled" => [
                        "sec" => [],
                        "hospital" => []
                    ]
                ]
            ],
            "Watch List" => [
                "main_status" => "Watch List",
                "sub_statuses" => [
                    "Watch List" => [
                        "sec" => [],
                        "hospital" => []
                    ]
                ]
            ]
        ];
    }

    public static function getNextStatuses($mainStatus, $currentStatus, $panel) {

        $statusFlow = self::edcstatus();
        
        if (isset($statusFlow[$mainStatus]['sub_statuses'][$currentStatus][$panel])) {
            return $statusFlow[$mainStatus]['sub_statuses'][$currentStatus][$panel];
        }
        return [];
    }

    public static function getDashboardRedirect($user) {
       
        switch ($user->role_id) {
            case 4: return route('preauth.dashboard');
            case 6: return route('dec.dashboard');
            case 7: return route('decverifier.dashboard');
            case 8: return route('sec.dashboard');
            case 13: return route('ppd.dashboard');
            case 14: return route('cpd.dashboard');
            case 15: return route('cex.dashboard');
            case 16: return route('aco.dashboard');
            case 17: return route('sha.dashboard');
            case 18: return route('shaadmin.dashboard');
            case 19: return route('isaadmin.dashboard');
            case 20: return route('medical-committee.dashboard');
            case 21: return route('ceo.dashboard');
            case 22: return route('acschairman.dashboard');
            default: return route('hospital.dashboard');
        }
    }
}