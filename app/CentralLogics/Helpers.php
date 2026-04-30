<?php

namespace App\CentralLogics;
use App\Models\{
    BusinessSetting,
    Hospital,
    HospitalSpeciality,
    HospitalTeam,
    User,
};
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use DB;

class Helpers
{
    
    public static function getCountAll($status) {
        if($status == 'all') {
            $sts = [0,1,2];
        } else {
            $sts = [$status];
        }
        return Hospital::whereIn('is_approve', $sts)->count();  
    }

    public static function get_settings($name)
    {
        $config = null;

        $paymentmethod = BusinessSetting::where('key', $name)->first();
        if ($paymentmethod) {
            $config = $paymentmethod->value;
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

    public static function getSingleSpecialities($hospital_id, $speciality_id) {
        $hospitals = Hospital::where('id' , $hospital_id)->first();
        
        return $hospitals->specialities()->where('speciality_id', $speciality_id)->first(); 
    }
    public static function getSingleServices($hospital_id, $service_id, $sub_service_id) {
        $hospitals = Hospital::where('id' , $hospital_id)->first();
        
        return $hospitals->services()->where('service_id', $service_id)->where('sub_service_id', $sub_service_id)->first(); 
    }

    public static function getSingleLicense($hospital_id, $license_id, $license_type_id) {
        $hospitals = Hospital::where('id' , $hospital_id)->first();
        
        return $hospitals->licenses()->where('license_id', $license_id)->where('license_type_id', $license_type_id)->first(); 
    }

    public static function getSingleDocument($hospital_id, $document_id) {
        $hospitals = Hospital::where('id' , $hospital_id)->first();
        
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
    public static function getHospitalId(){
        return auth()->user()->hospital_id;
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
    
    public static function checkAllStepIsCompleteOrNot($uuid) {
        $completedStep = self::checkstepComplete($uuid);
        if($completedStep['hospitalinfostep'] && $completedStep['specialiststep'] && $completedStep['servicestep'] && $completedStep['licensesstep'] && $completedStep['documentstep']) {
            return true;
        } else {
            return false;
        }
    }

    public static function checkstepComplete($uuid) {
        $user = User::where('uuid',$uuid)->first();
        $hospitalinfostep = false;
        $multibranchstep = false;
        $documentstep = false;
        $servicestep = false;
        $specialiststep = false;
        $licensesstep = false;
        
        $enable_step = $user->enable_step;
        $enable_step_decoded = json_decode($enable_step);
        if($user->hospital_id){
            $hospital = Hospital::where('id', $user->hospital_id)->first();
            $documentsdata = Helpers::getCommanData('EmpanelmentDocument');
            $hospitalinfostep = true;
            if(sizeof($documentsdata) > 0 && $hospital && $hospital->documents()->count() > 0) {
                $documentstep = true;
            } else if(sizeof($documentsdata) <= 0) {
                $documentstep = true;
            }
            if( $hospital && $hospital->services->count() > 0 || @$enable_step_decoded->service_status == 0){
                $servicestep = true;
            }
            if( $hospital && $hospital->specialities->count() > 0 || @$enable_step_decoded->speciality_status == 0){
                $specialiststep = true;
            }
            if( $hospital && $hospital->licenses->count() > 0 || @$enable_step_decoded->licenses_status == 0){
                $licensesstep = true;
            }
        }


        return ['hospitalinfostep' => $hospitalinfostep, 'specialiststep' => $specialiststep, 'servicestep' => $servicestep, 'licensesstep' => $licensesstep, 'documentstep' => $documentstep];
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

    public static function getDashboardRedirect($user) {
       
        switch ($user->getRoleNames()->first()) {
            case 'Master Admin': return route('admin.dashboard.index');
            case 'Doctor': return route('hospital.doctor-dashboard');
            case 'doctor': return route('hospital.doctor-dashboard');
            default: return route('hospital.dashboard');
        }
    }

    public static function getBeforeTime() {
        return 20; //Minutes
    }
}