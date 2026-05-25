<?php

namespace App\CentralLogics;
use App\Models\{
    BusinessSetting,
    Hospital,
    HospitalSpeciality,
    HospitalTeam,
    PreauthRegister,
    StaffStrength,
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
    public static function getRegisterID()
    {
        $last = PreauthRegister::query()->orderByDesc('id')->value('register_id');
        if ($last === null || $last === '') {
            return '1000000001';
        }
        if (is_numeric($last)) {
            return (string) (((int) $last) + 1);
        }

        return (string) (time());
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
        return auth()->user() ? auth()->user()->hospital_id : null;
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
    
    /**
     * 8-step hospital empanelment wizard (see form.blade.php $wizardSteps).
     */
    public static function empanelmentWizardStepConfig(): array
    {
        return [
            1 => ['key' => 'facility_type', 'label' => 'Select facility type'],
            2 => ['key' => 'basic_info', 'label' => 'Basic information'],
            3 => ['key' => 'infrastructure', 'label' => 'Infrastructure details'],
            4 => ['key' => 'staff_services', 'label' => 'Staff strength & services'],
            5 => ['key' => 'documents', 'label' => 'Documents'],
            6 => ['key' => 'ab_empanelment', 'label' => 'Ayushman Bharat empanelment'],
            7 => ['key' => 'hmis_setup', 'label' => 'HMIS & IT setup'],
            8 => ['key' => 'review_submit', 'label' => 'Review & submit'],
        ];
    }

    public static function checkAllStepIsCompleteOrNot($uuid): bool
    {
        $completed = self::checkstepComplete($uuid);
        $required = ['facility_type', 'basic_info', 'infrastructure', 'staff_services', 'documents', 'ab_empanelment', 'hmis_setup'];

        foreach ($required as $key) {
            if (empty($completed[$key])) {
                return false;
            }
        }

        return true;
    }

    public static function checkstepComplete($uuid): array
    {
        $user = User::where('uuid', $uuid)->first();
        $enableStep = json_decode($user->enable_step ?? self::get_settings('empanelment_step_status') ?: '{}');
        if (!is_object($enableStep)) {
            $enableStep = (object) ['speciality_status' => 0, 'service_status' => 0, 'licenses_status' => 0];
        }

        $facilityType = false;
        $basicInfo = false;
        $infrastructure = false;
        $staffServices = false;
        $documents = false;
        $abEmpanelment = false;
        $hmisSetup = false;

        if ($user) {
            $wizard = (array) ($user->wizard_onboarding ?? []);
            $facilityType = !empty($wizard['type_id']);
        }

        $hospital = null;
        if ($user && $user->hospital_id) {
            $hospital = Hospital::where('id', $user->hospital_id)->first();
            if ($hospital) {
                $om = is_array($hospital->onboarding_meta) ? $hospital->onboarding_meta : [];

                $basicInfo = trim((string) $hospital->name) !== '' && trim((string) $hospital->code) !== '';

                $infrastructure = array_key_exists('infra_sanctioned_beds', $om)
                    && $om['infra_sanctioned_beds'] !== ''
                    && $om['infra_sanctioned_beds'] !== null
                    && array_key_exists('infra_functional_beds', $om)
                    && $om['infra_functional_beds'] !== ''
                    && $om['infra_functional_beds'] !== null;

                $staffServices = self::isStaffServicesStepComplete($hospital, $om, $enableStep);

                $docsOk = self::areRequiredEmpanelmentDocumentsComplete($hospital);

                $licenses = self::getCommanData('License');
                $licensesOk = ($hospital->licenses()->count() > 0) || (count($licenses) == 0);
                $documents = $docsOk && $licensesOk;

                $ab = (array) ($om['ab_empanelment'] ?? []);
                $abEmpanelment = !empty($ab) && (
                    (is_array($ab['eligibility'] ?? null) && count($ab['eligibility']) > 0)
                    || !empty($ab['sha_code'])
                    || !empty($ab['rohini_id'])
                    || !empty($ab['bank_account'])
                );

                $hm = (array) ($om['hmis_setup'] ?? []);
                $hmisSetup = !empty($hm) && (
                    !empty($hm['admin_username'])
                    || (is_array($hm['modules'] ?? null) && count($hm['modules']) > 0)
                );
            }
        }

        return [
            'facility_type' => $facilityType,
            'basic_info' => $basicInfo,
            'infrastructure' => $infrastructure,
            'staff_services' => $staffServices,
            'documents' => $documents,
            'ab_empanelment' => $abEmpanelment,
            'hmis_setup' => $hmisSetup,
            // Legacy keys (admin / older partials)
            'hospitalinfostep' => $basicInfo,
            'specialiststep' => empty($enableStep->speciality_status) || ($hospital && $hospital->specialities()->count() > 0),
            'servicestep' => empty($enableStep->service_status) || ($hospital && $hospital->services()->count() > 0),
            'licensesstep' => empty($enableStep->licenses_status) || ($hospital && $hospital->licenses()->count() > 0),
            'documentstep' => $documents,
        ];
    }

    protected static function isStaffServicesStepComplete(Hospital $hospital, array $om, object $enableStep): bool
    {
        $staffStrength = (array) ($om['staff_strength'] ?? []);
        if (StaffStrength::count() > 0) {
            $hasStaffData = false;
            foreach ($staffStrength as $row) {
                if (!is_array($row)) {
                    continue;
                }
                if (isset($row['sanctioned']) && $row['sanctioned'] !== '' && $row['sanctioned'] !== null) {
                    $hasStaffData = true;
                    break;
                }
                if (isset($row['in_position']) && $row['in_position'] !== '' && $row['in_position'] !== null) {
                    $hasStaffData = true;
                    break;
                }
            }
            if (!$hasStaffData) {
                return false;
            }
        }

        if (!empty($enableStep->speciality_status) && $hospital->specialities()->count() <= 0) {
            return false;
        }

        if (!empty($enableStep->service_status) && $hospital->services()->count() <= 0) {
            return false;
        }

        return true;
    }

    protected static function areRequiredEmpanelmentDocumentsComplete(Hospital $hospital): bool
    {
        $documents = self::getCommanData('EmpanelmentDocument');
        if (count($documents) === 0) {
            return true;
        }

        foreach ($documents as $doc) {
            if (!$doc->is_required) {
                continue;
            }
            $uploaded = $hospital->documents()
                ->where('document_id', $doc->id)
                ->whereNotNull('document')
                ->where('document', '!=', '')
                ->exists();
            if (!$uploaded) {
                return false;
            }
        }

        return true;
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
            case 'State Super Admin': return route('state-admin.dashboard.index');
            case 'Doctor': return route('hospital.doctor-dashboard');
            case 'doctor': return route('hospital.doctor-dashboard');
            default: return route('hospital.dashboard');
        }
    }

    public static function getBeforeTime() {
        return 20; //Minutes
    }
}