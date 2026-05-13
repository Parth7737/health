@extends('layouts.sha.app')
@section('title','Pre-Authorization')
@section('content')
@php
    use \App\Models\PreauthRegister;
    use \App\CentralLogics\Helpers;
    $helpers = new Helpers();
    $status = \App\CentralLogics\Helpers::checkStatus($preauth_register->id, 'cex');
    $cpdstatus = \App\CentralLogics\Helpers::checkStatus($preauth_register->id, 'cpd');
    $acoDetails = json_decode($preauth_register->aco_observation_details, true);
    $bankdetails = $helpers->getStaticBankDetails($preauth_register->state_id);

    $helpers = new Helpers();
    $medicalinfo = $helpers->checkStepSeen($preauth_register->id, 'Medical_Information', 'SHA');
    $admissioninfo = $helpers->checkStepSeen($preauth_register->id, 'Admission_Informations', 'SHA');
    $treatmentinfo = $helpers->checkStepSeen($preauth_register->id, 'Treatment', 'SHA');
    $documentsinfo = $helpers->checkDocStepSeen($preauth_register->id, 'sha_status');
    $financeinfo = $helpers->checkStepSeen($preauth_register->id, 'Finance', 'SHA');
@endphp
<div class="container-xxl flex-grow-1 container-p-y mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Home</a>
            </li>
            <li class="breadcrumb-item active">Pre Authorization ({{ $preauth_register->register_id }})</li>
        </ol>
    </nav>
    <div class="row">
        <div class="bs-stepper-content">
            <div class="card mb-6 ps-0 border border-primary">
                <div class="card-body">
                    <div class="row row-cols-5">
                        <div class="col">
                            <div class="d-flex text-center justify-content-center flex-column border-end border-secondary">
                                <div class="position-relative image-overlay">
                                    <img src="{{ $preauth_register->benificiary->image_url }}" width="80" alt="avatar" class="mb-3 rounded-circle" />
                                </div>
                                <span class="number-3 mb-2">{{ @$preauth_register->benificiary->name }}</span>
                                <span class="number-2 mb-2">{{ @$preauth_register->benificiary->age }} Yr / {{ @$preauth_register->benificiary->gender }}</span>
                                @if(@$preauth_register->is_new_born_baby == 1)
                                    <strong><span class="number-3 mt-2">New Born Baby</span></strong>
                                    <span>{{ $preauth_register->born_baby_name }}</span>
                                    <span class="number-2 mb-2">DOB : {{ @$preauth_register->born_baby_dob }}  / {{ @$preauth_register->born_baby_gender }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col">
                            <div class="infodata">
                                <label>Care Plan</label>
                                <p><strong>{{ @$preauth_register->benificiary->care_plan }}</strong></p>
                                <label>SGHS ID</label>
                                <p><strong>{{ @$preauth_register->benificiary->card_id }}</strong></p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="infodata">
                                <label>Mobile Number</label>
                                <p><strong>{{ @$preauth_register->benificiary->mobile_no }}</strong></p>
                                <label>District</label>
                                <p><strong>{{ @$preauth_register->district->name }}</strong></p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="infodata">
                                <label>Registration ID</label>
                                <p><strong>{{ $preauth_register->register_id }}</strong></p>
                                <label>Claim Submission Date</label>
                                <p><strong>{{ date("d/m/Y h:i A",strtotime($preauth_register->claim_submited_date)) }}</strong></p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="infodata">
                                <label>Claim Approved Amount</label>
                                <p><strong>₹{{ number_format($preauth_register->claim_approved_amount,2) }}</strong></p>
                                <label>Claim Initiate Amount</label>
                                <p><strong>₹{{ number_format($preauth_register->claim_amount,2) }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bs-stepper wizard-numbered mt-2">
                @include('sha._partials.preauth-step')
                <div class="bs-stepper-content">
                    <!-- Account Details -->
                    <div id="account-details" class="content">
                        <h6 class="mb-0">First Step</h6>
                    </div>
                    <!-- Personal Info -->
                    <div id="personal-info" class="content active">
                        <div class="accordion accordion-popout mt-4" id="accordionPopout">
                           
                            <div class="accordion-item ">
                                <h2 class="accordion-header" id="headingPopoutOne">
                                    <button type="button" class="accordion-button Medical_Information {{ @$medicalinfo ? 'theme-color' : 'pending-color' }} "
                                        data-bs-toggle="collapse"
                                        data-bs-target="#accordionPopoutOne"
                                        aria-expanded="true" aria-controls="accordionPopoutOne" 
                                        onclick="opentab('Medical_Information', '{{$preauth_register->id}}');">
                                        Medical Information
                                    </button>
                                </h2>
                                <div id="accordionPopoutOne"
                                    class="accordion-collapse collapse "
                                    aria-labelledby="headingPopoutOne"
                                    data-bs-parent="#accordionPopout">
                                    <div class="accordion-body">
                                        <div class="inside-left-info-box {{ @$general_info?'success':'pending' }}">
                                            <div class="card p-0 shadow-none rounded-0  border-bottom">
                                                <h5 class="theme-color mt-3">General Information</h5>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Temperature(°F)</strong></label>
                                                            <p>{{ @$general_info->temprature??'' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Pulse Rate Per Minute(BPM)</strong></label>
                                                            <p>{{ @$general_info->pulserate??'' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Height (In CM)</strong></label>
                                                            <p>{{ @$general_info->height??'' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Weight (In KG)</strong></label>
                                                            <p>{{ @$general_info->weight??'' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>BMI</strong></label>
                                                            <p>{{ @$general_info->bmi??'' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Cyanosis</strong></label>
                                                            <p>{{ @$general_info->cyanosis }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Pallor</strong></label>
                                                            <p>{{ @$general_info->pallor }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Malnutration</strong></label>
                                                            <p>{{ @$general_info->malnutration }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Oedema in
                                                            Feet</strong></label>
                                                            <p>{{ @$general_info->oedema }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="inside-left-info-box {{ @$family_history?'success':'pending' }} mt-3">
                                            <div class="card p-0 shadow-none rounded-0  border-bottom">
                                                <h5 class="theme-color mt-3">Family History</h5>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Diabetes</strong></label>
                                                            <p>{{ @$family_history->diabetes->name }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Hypertension</strong></label>
                                                            <p>{{ @$family_history->hypertension->name }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Heart Disease</strong></label>
                                                            <p>{{ @$family_history->heartdisease->name }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Stroke</strong></label>
                                                            <p>{{ @$family_history->stroke->name }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Cancer</strong></label>
                                                            <p>{{ @$family_history->cancer->name }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Tuberculosis</strong></label>
                                                            <p>{{ @$family_history->tuberculosis->name }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Tuberculosis</strong></label>
                                                            <p>{{ @$family_history->tuberculosis->name }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="inside-left-info-box {{ @$personal_history?'success':'pending' }} mt-3">
                                            <div class="card p-0 shadow-none rounded-0  border-bottom">
                                                <h5 class="theme-color mt-3">Personal History</h5>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Appetite</strong></label>
                                                            <p>{{ @$personal_history->appetite->name }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Bowels</strong></label>
                                                            <p>{{ @$personal_history->bowel->name }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Nutrition</strong></label>
                                                            <p>{{ @$personal_history->nutrition->name }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Diet</strong></label>
                                                            <p>{{ @$personal_history->diet->name }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Known Allergies</strong></label>
                                                            <p>{{ @$personal_history->known_allergies }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Allergy Details</strong></label>
                                                            <p>{{ @$personal_history->allergy_detail }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Habits/Addictions</strong></label>
                                                            <p>{{ @$personal_history->habits }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Habits/Addiction Details</strong></label>
                                                            <p>{{ @$personal_history->habits_detail }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                           
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingPopoutThree">
                                    <button type="button"
                                        class="accordion-button Treatment {{ (@$treatmentinfo) ?'theme-color':'pending-color' }} collapsed"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#accordionPopoutThree"
                                        aria-expanded="false"
                                        aria-controls="accordionPopoutThree"
                                        onclick="opentab('Treatment', '{{$preauth_register->id}}');"
                                       >
                                        Treatment
                                    </button>
                                </h2>
                                <div id="accordionPopoutThree"
                                    class="accordion-collapse collapse"
                                    aria-labelledby="headingPopoutThree"
                                    data-bs-parent="#accordionPopout">
                                    <div class="accordion-body">
                                        <div class="inside-left-info-box {{ (@$preauth_diagnosis->count() > 0) ?'success':'pending' }}">
                                            <h4 class="colored-verticle-title">Diagnosis <span
                                                    class="status-dot">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        height="24px" viewBox="0 -960 960 960"
                                                        width="24px" fill="undefined">
                                                        <path
                                                            d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
                                                    </svg>
                                                </span>
                                            </h4>
                                            <div class="row g-5">
                                                <div class="col-12">
                                                    <div
                                                        class="table-responsive mt-5 text-nowrap">
                                                        <table class="table">
                                                            <thead class="table-dark">
                                                                <tr>
                                                                    <th>No.</th>
                                                                    <th>Diagnosis Code</th>
                                                                    <th>Diagnosis Description</th>
                                                                    <th>Diagnosis Type</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody
                                                                class="table-border-bottom-0 diagnosis-body">
                                                                @include('sha._partials.diagnosis')
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="inside-left-info-box {{ (@$procedures->count() > 0)?'success':'pending' }} mt-3">
                                            <h4 class="colored-verticle-title">Treatment Plan
                                                <span class="status-dot">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        height="24px" viewBox="0 -960 960 960"
                                                        width="24px" fill="undefined">
                                                        <path
                                                            d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
                                                    </svg>
                                                </span>
                                            </h4>
                                            <div class="row justify-content-center">
                                                <div class="col-12">
                                                    <div
                                                        class="table-responsive mt-5 text-nowrap">
                                                        <table class="table">
                                                            <thead class="table-dark">
                                                                <tr>
                                                                    <th>No.</th>
                                                                    <th>Speciality</th>
                                                                    <th>Procedure</th>
                                                                    <th>Stratification</th>
                                                                    
                                                                    <th>Day/Units</th>
                                                                    <th>Amount</th>
                                                                    <th>ICHI Code</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody
                                                                class="table-border-bottom-0 procedure-body">
                                                                @include('sha._partials.procedures')
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                       
                                        <div class="inside-left-info-box {{ @$preauth_teams->count() > 0?'success':'pending' }} mt-3">
                                            <h4 class="colored-verticle-title">Care Team Details
                                                <span class="status-dot">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        height="24px" viewBox="0 -960 960 960"
                                                        width="24px" fill="undefined">
                                                        <path
                                                            d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
                                                    </svg>
                                                </span>
                                            </h4>
                                            <div class="col-12">
                                                <div class="table-responsive mt-5 text-nowrap">
                                                    <table class="table">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Doctor Name</th>
                                                                <th>Registration ID / HPR ID
                                                                </th>
                                                                <th>Speciality</th>
                                                                <th>Contact Number</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="table-border-bottom-0 care-team-body">
                                                            @include('sha._partials.teams')
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingPopoutTwo">
                                    <button type="button"
                                        class="accordion-button Admission_Informations {{ @$admissioninfo ?'theme-color':'pending-color' }} "
                                        data-bs-toggle="collapse"
                                        data-bs-target="#accordionPopoutTwo"
                                        aria-expanded="false"
                                        aria-controls="accordionPopoutTwo"
                                        onclick="opentab('Admission_Informations', '{{$preauth_register->id}}');"
                                        >
                                        Admission Information
                                    </button>
                                </h2>
                                <div id="accordionPopoutTwo" class="accordion-collapse collapse"
                                    aria-labelledby="headingPopoutTwo"
                                    data-bs-parent="#accordionPopout">
                                    <div class="accordion-body">
                                        <div class="inside-left-info-box {{ @$authentication_consent?'success':'pending' }}">
                                            <div class="card p-0 shadow-none rounded-0  border-bottom">
                                                <h5 class="theme-color mt-3">Authentication Consent</h5>
                                                <div class="row">
                                                    @if(@$authentication_consent->hospital_declaration_form)
                                                        <div class="col-md-2 mb-2">
                                                            <div class="infodata">
                                                                <label><strong>Hospital Declaration Form (During Admission)</strong>&nbsp; <a href="{{ asset('public/storage/'.@$authentication_consent->hospital_declaration_form) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-md-6">
                                                        <div class="infodata">
                                                            <label><strong>Remarks</strong></label>
                                                            <p>{{ @$authentication_consent->remarks }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="inside-left-info-box {{ @$admission_details?'success':'pending' }} mt-3">
                                            <div class="card p-0 shadow-none rounded-0  border-bottom">
                                                <h5 class="theme-color mt-3">Admission Details</h5>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Admission Date</strong></label>
                                                            <p>{{ @$admission_details->admission_date }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Proposed Surgery Date</strong></label>
                                                            <p>{{ @$admission_details->surgery_date }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>AdmissionType</strong></label>
                                                            <p>{{ @$admission_details->admission_type->name }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="infodata">
                                                            <label><strong>Medico Legal Case</strong></label>
                                                            <p>{{ @$admission_details->legal_case }}</p>
                                                        </div>
                                                    </div>
                                                    @if(@$admission_details->fir_doc)
                                                        <div class="col-md-2 mb-2">
                                                            <div class="infodata">
                                                                <label><strong>FIR </strong>&nbsp; <a href="{{ asset('public/storage/'.@$admission_details->fir_doc) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if(@$preauth_register->discharge_type)
                                                        <div class="col-md-2">
                                                            <div class="infodata">
                                                                <label><strong>Discharge Type</strong></label>
                                                                <p>{{ @$preauth_register->discharge_type }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if(@$preauth_register->discharge_stage)
                                                        <div class="col-md-2">
                                                            <div class="infodata">
                                                                <label><strong>Discharge Stage</strong></label>
                                                                <p>{{ @$preauth_register->discharge_stage }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if(@$preauth_register->lama_date)
                                                        <div class="col-md-2">
                                                            <div class="infodata">
                                                                <label><strong>LAMA/DAMA/DOPR Date</strong></label>
                                                                <p>{{ @$preauth_register->lama_date }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if(@$preauth_register->surgery_date)
                                                        <div class="col-md-2">
                                                            <div class="infodata">
                                                                <label><strong>Surgery Date</strong></label>
                                                                <p>{{ @$preauth_register->surgery_date }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if(@$preauth_register->discharge_date)
                                                        <div class="col-md-2">
                                                            <div class="infodata">
                                                                <label><strong>Discharge Date</strong></label>
                                                                <p>{{ @$preauth_register->discharge_date }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if(@$preauth_register->provide_medicine)
                                                        <div class="col-md-2">
                                                            <div class="infodata">
                                                                <label><strong>Hospital Provided Medicine During Treatment</strong></label>
                                                                <p>{{ @$preauth_register->provide_medicine }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingPopoutFive">
                                    <button type="button"
                                        class="accordion-button Documents {{ @$documentsinfo ? 'theme-color' : 'pending-color' }} collapsed"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#accordionPopoutFive"
                                        aria-expanded="false"
                                        aria-controls="accordionPopoutFive">
                                        Documents
                                    </button>
                                </h2>
                                <div id="accordionPopoutFive"
                                    class="accordion-collapse collapse"
                                    aria-labelledby="headingPopoutFive"
                                    data-bs-parent="#accordionPopout">
                                    <div class="accordion-body">
                                        <div class="inside-left-info-box {{ $preauth_investigation_status?'success':'pending' }} mt-3">
                                            <h4 class="colored-verticle-title">Investigation
                                                <!-- <span class="status-dot">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        height="24px" viewBox="0 -960 960 960"
                                                        width="24px" fill="undefined">
                                                        <path
                                                            d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
                                                    </svg>
                                                </span> -->
                                            </h4>
                                            <div class="row justify-content-center">                                               
                                                @include('sha._partials.investigations',['preauth_register_id'=>$preauth_register->id])
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                         
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingPopoutFour">
                                    <button type="button"
                                        class="accordion-button finance-color Finance {{ (@$financeinfo)?'theme-color':'pending-color' }} collapsed"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#accordionPopoutFour"
                                        aria-expanded="false"
                                        aria-controls="accordionPopoutFour"
                                        onclick="opentab('Finance', '{{$preauth_register->id}}');">
                                        Finance
                                    </button>
                                </h2>
                                <div id="accordionPopoutFour"
                                    class="accordion-collapse  collapse"
                                    aria-labelledby="headingPopoutFour"
                                    data-bs-parent="#accordionPopout">
                                    <div class="accordion-body">
                                        <h5 class="theme-color">Static Details About Procedure(s)</h5>
                                        <div class="row g-5">
                                            <div class="col-12">
                                                <div class="table-responsive text-nowrap">
                                                    <table class="table">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Package Code</th>
                                                                <th>Package Cost</th>
                                                                <th>Quantity</th>
                                                                <th>Adj Factor</th>
                                                                <th>Incentive</th>
                                                                <th>Amount Requested</th>
                                                                <th>CPD-Trust(Observation)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="table-border-bottom-0 finance-body">
                                                            @if(@$procedures)
                                                            @php $i=1; @endphp
                                                            @foreach(@$procedures as $procedure)
                                                            
                                                            @if(@$procedure->procedure_price == 0 && $procedure->stratification_price != 0 && $procedure->no_of_days > 1)
                                                                @php $procedure->procedure_price = $procedure->stratification_price*intval($procedure->no_of_days ?$procedure->no_of_days-1: 0); @endphp
                                                            @endif
                                                            @php $sub_total = @$procedure->procedure_price+@$procedure->incentive+@$procedure->stratification_price-@$procedure->deducted_amount @endphp
                                                            <tr>
                                                                <td>{{ $i++ }}</td>
                                                                <td>{{ @$procedure->procedure->procedure_code_2 }}</td>
                                                                <td>₹{{ number_format(@$procedure->original_price, 2) }}</td>
                                                                <td>{{ @$procedure->no_of_days }}</td>
                                                                <td>{{ @$procedure->adj_per?@$procedure->adj_per."%":'100%' }}</td>
                                                                <td>{{ @$procedure->incentive?@$procedure->incentive_per."%":'N/A' }}</td>
                                                                <td>₹{{ number_format(@$sub_total, 2) }}</td>
                                                                @php
                                                                    $pstatus = @$procedure->preauth_claim_status??'Rejected';
                                                                @endphp
                                                                <td> <span class="badge @if($pstatus == 'Rejected') text-danger @elseif($pstatus == 'Query') text-warning @else text-primary @endif">{{$pstatus}}</span></td>
                                                            </tr>
                                                            @if($procedure->implant_id)
                                                            @php $sub_total = (@$procedure->implant_price*@$procedure->implant_qty) @endphp
                                                            <tr>
                                                                <td>{{ $i++ }}</td>
                                                                <td>{{ @$procedure->implant->code }}</td>
                                                                <td>₹{{ number_format(@$procedure->implant_price, 2) }}</td>
                                                                <td>{{ @$procedure->implant_qty }}</td>
                                                                <td>{{ 'N/A' }}</td>
                                                                <td>{{ 'N/A' }}</td>
                                                                <td>₹{{ number_format(@$sub_total, 2) }}</td>
                                                                @php
                                                                    $pstatus = @$procedure->preauth_claim_implant_status??'Rejected';
                                                                @endphp
                                                                <td> <span class="badge @if($pstatus == 'Rejected') text-danger @elseif($pstatus == 'Query') text-warning @else text-primary @endif">{{$pstatus}}</span></td>
                                                            </tr>
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>                                                
                                            </div>
                                        </div>

                                        <!-- -- -->
                                        <div class="row g-5 mt-2">
                                            <div class="col-12">
                                                <table class="table">
                                                    <thead class="table-white">
                                                        <tr class="border-1">
                                                            <th class=" p-2">Overall observation on the documents by CEX @if($status) <span class="text-primary"><strong>Correct</strong></span> @else <span class="text-danger"><strong>Incorrect</strong></span> @endif</th>
                                                            <th class=" p-2">Overall finding on the document by CPD @if($cpdstatus) <span class="text-primary"><strong>Correct</strong></span> @else <span class="text-danger"><strong>Incorrect</strong></span> @endif</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- observation  -->
                                        
                                        <div class="row g-5 mt-2">
                                            <h5 class="theme-color">Aco (Observation)</h5>
                                            <div class="col-12">
                                                @if($acoDetails)
                                                <table class="table">
                                                    <thead class="table-white">
                                                        @foreach($acoDetails as $key => $detail)
                                                        <tr class="border-1">
                                                            <th class=" p-2">{{ $detail['name'] }}</th>
                                                            <th class=" p-2"><span class="text-primary">{{$detail['value']}}</span></th>
                                                        </tr>
                                                        @endforeach
                                                    </thead>
                                                </table>
                                                @endif
                                            </div>
                                        </div>
                                        
                                     
                                        @if($preauth_register->status == PreauthRegister::STATUS_ACO_CLAIM_APPROVED)
                                            <form onSubmit="return false" id="approveRejectPreauthForm">
                                                <h4 class="theme-color mt-3">Actionable Details
                                                </h4>
                                                <div class="row g-5">
                                                    <div class="col-12">
                                                        <div class="table-responsive text-nowrap">
                                                            <table class="table">
                                                                <thead class="table-dark">
                                                                    <tr>
                                                                        <th>No.</th>
                                                                        <th>Package Code</th>
                                                                        <th>Package Cost</th>
                                                                        <th>ICHI Code</th>
                                                                        <th>Approved Qty</th>
                                                                        <th>Deductions</th>
                                                                        <th>Deducted Amount</th>
                                                                        <th>Amount Approved</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="table-border-bottom-0 finance-body">
                                                                    @if(@$procedures)
                                                                    @php $i=1; @endphp
                                                                    @foreach(@$procedures as $procedure)
                                                                    
                                                                        @if($procedure->preauth_claim_status == 'Approved' || $procedure->preauth_claim_implant_status == 'Approved')
                                                                            @if(@$procedure->procedure_price == 0 && $procedure->stratification_price != 0 && $procedure->no_of_days > 1)
                                                                                @php $procedure->procedure_price = $procedure->stratification_price*intval($procedure->no_of_days ?$procedure->no_of_days-1: 0); @endphp
                                                                            @endif
                                                                            @php $sub_total = @$procedure->procedure_price+@$procedure->incentive+@$procedure->stratification_price-@$procedure->deducted_amount @endphp
                                                                            <tr>
                                                                                <td>{{ $i++ }}</td>
                                                                                <td>{{ @$procedure->procedure->procedure_code_2 }}</td>
                                                                                <td>₹{{ number_format(@$procedure->original_price, 2) }}</td>
                                                                                <td>{{ @$procedure->icd_code??'Not Available' }}</td>
                                                                                <td>{{ @$procedure->no_of_days }}</td>
                                                                                <td>
                                                                                    <div>
                                                                                        @if(@$procedure->deducted_amount != 0)
                                                                                            <span class="text-success">Yes</span>
                                                                                        @else
                                                                                            <span class="text-danger">No</span>
                                                                                        @endif
                                                                                    </div>
                                                                                </td>
                                                                                <td class="deduction-total">₹{{ number_format(@$procedure->deducted_amount,2) }}</td>
                                                                                <td>₹{{ number_format(@$sub_total, 2) }}</td>
                                                                            </tr>
                                                                            @if($procedure->implant_id)
                                                                            @php $sub_total = (@$procedure->implant_price*@$procedure->implant_qty) @endphp
                                                                            <tr>
                                                                                <td>{{ $i++ }}</td>
                                                                                <td>{{ @$procedure->implant->code }}</td>
                                                                                <td>₹{{ number_format(@$procedure->implant_price, 2) }}</td>
                                                                                <td>{{ 'Not Available' }}</td>
                                                                                <td>{{ @$procedure->implant_qty }}</td>
                                                                                <td>{{ 'Not Applicable' }}</td>
                                                                                <td>{{ 'Not Applicable' }}</td>
                                                                                <td>₹{{ number_format(@$sub_total, 2) }}</td>
                                                                            </tr>
                                                                            @endif
                                                                        @endif
                                                                    @endforeach
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <ul class="d-flex listing-right finance-total-body">
                                                            @include('sha._partials.finance-total')
                                                        </ul>
                                                    </div>
                                                </div>

                                                <!-- <div class="row g-5">
                                                    <h5 class="theme-color">Primary Account Details</h5>
                                                    <div class="col-12">
                                                        <div class="table-responsive mt-5 text-nowrap">
                                                            <table class="table">
                                                                <thead class="table-dark">
                                                                    <tr>
                                                                        <td><strong>Bank Name:</strong> {{@$bankdetails->bank_name}}</td>
                                                                        <td><strong>Bank Id:</strong> {{@$bankdetails->id}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>State:</strong> {{@$bankdetails->state->name}}</td>
                                                                        <td><strong>Account Name:</strong> {{@$bankdetails->account_name}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>IFSC Code:</strong> {{@$bankdetails->ifsc_code}}</td>
                                                                        <td><strong>Account Number:</strong> {{@$bankdetails->account_number}}</td>
                                                                    </tr>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div> -->
                                                
                                                <div class="col-md-3 mt-2">
                                                    <select name="preauth_status" id="preauth_status" class="select2 form-select form-select-lg ">
                                                        <option value="">Select</option>
                                                        <option value="Approve">Approve</option>
                                                        <option value="Reject">Reject</option>
                                                        <option value="Revoked to CPD">Revoked to CPD</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-12 mt-2">
                                                    <div class="form-floating form-floating-outline mb-6 cexerror">
                                                        <textarea class="form-control h-px-100" id="sha_remark" name="sha_remark" placeholder="Write remarks here..."></textarea>
                                                        <label for="remarks">Remarks</label>
                                                    </div>
                                                    <input type="hidden" name="preauth_register_id" value="{{ $preauth_register->id }}">
                                                </div>

                                                <button type="button" id="approve-preauth-btn" class="btn btn-primary ms-2">Submit</button>
                                            </form>
                                        @endif

                                        @if($preauth_register->status == PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_APPROVED)
                                            <div class="erroneous-form">
                                                <form onSubmit="return false" id="approveRejectErroneousClaimForm">
                                                    <h4 class="theme-color mt-3">Actionable Details
                                                    </h4>
                                                    <div class="row">
                                                        <div class="col-md-3 pt-1">
                                                            <select name="erroneous_status" id="erroneous_status" class="select2 form-select form-select-lg">
                                                                <option value="">Select</option>
                                                                <option value="Approve">Approve</option>
                                                                <option value="Reject">Reject</option>
                                                                <option value="Revoked to CPD">Revoked to CPD</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3 pt-1">
                                                            <div
                                                                class="form-floating form-floating-outline">
                                                                <input type="number" name="erroneous_appoved_amount" id="erroneous_appoved_amount" readonly value="{{ $preauth_register->erroneous_raise_amount }}" max="{{ $preauth_register->erroneous_raise_amount }}" class="form-control" />
                                                                <label for="erroneous_appoved_amount">Approved Amount</label>
                                                            </div>
                                                            <div id="erroneous-approve-amount-error"></div>
                                                        </div>
                                                        <div class="col-md-3 pt-1">
                                                            <div
                                                                class="form-floating form-floating-outline">
                                                                <input type="number" readonly value="{{ $preauth_register->erroneous_raise_amount }}"
                                                                    class="form-control" />
                                                                <label for="erroneous_raise_amount">Erroneous Raised Amount</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 mt-2">
                                                        <div class="form-floating form-floating-outline mb-6">
                                                            <textarea class="form-control h-px-100" id="remarks" name="remarks" placeholder="Write remarks here..."></textarea>
                                                            <label for="remarks">Remarks<span class="text-danger">*</span></label>
                                                        </div>
                                                        <div id="preauth-remark-error" class="text-danger"></div>
                                                        <input type="hidden" name="preauth_register_id" value="{{ $preauth_register->id }}">
                                                    </div>
                                                    @if($preauth_register->erroneous_aco_remarks && $preauth_register->status == PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_APPROVED)
                                                        <div class="col-md-12 mt-2">
                                                            <div class="form-floating form-floating-outline mb-6">
                                                                <textarea class="form-control h-px-50 text-success" disabled >{{$preauth_register->erroneous_aco_remarks}}</textarea>
                                                                <label for="remarks">Erroneous Remarks by ACO</label>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="row ">
                                                        <div class="col-md-12">
                                                            <div class="d-flex justify-content-end">
                                                                <button id="erroneous-claim-btn"
                                                                    class="btn btn-primary">Submit</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                        @if($preauth_register->preauth_approve_remarks && $preauth_register->status == PreauthRegister::STATUS_PREAUTH_REJECTED)
                                            <div class="col-md-12 mt-2">
                                                <div class="form-floating form-floating-outline mb-6">
                                                    <textarea class="form-control h-px-50 text-danger" id="ppd_preauth_remarks" readonly  >{{$preauth_register->preauth_approve_remarks}}</textarea>
                                                    <label for="remarks">Rejected By PPD</label>
                                                </div>
                                            </div>
                                        @endif
                                        @if($preauth_register->committee_remarks && $preauth_register->status == PreauthRegister::STATUS_MEDICAL_COMMITTEE_REJECTED)
                                            <div class="col-md-12 mt-2">
                                                <div class="form-floating form-floating-outline mb-6">
                                                    <textarea class="form-control h-px-50 text-danger" id="committee_remarks" readonly  >{{$preauth_register->committee_remarks}}</textarea>
                                                    <label for="remarks">Rejected By Medical Committee</label>
                                                </div>
                                            </div>
                                        @endif
                                        @if($preauth_register->ceo_remarks && $preauth_register->status == PreauthRegister::STATUS_CEO_REJECTED)
                                            <div class="col-md-12 mt-2">
                                                <div class="form-floating form-floating-outline mb-6">
                                                    <textarea class="form-control h-px-50 text-danger" id="ceo_remarks" readonly  >{{$preauth_register->ceo_remarks}}</textarea>
                                                    <label for="remarks">Rejected By CEO</label>
                                                </div>
                                            </div>
                                        @endif
                                        @if($preauth_register->acs_remarks && $preauth_register->status == PreauthRegister::STATUS_ACS_REJECTED)
                                            <div class="col-md-12 mt-2">
                                                <div class="form-floating form-floating-outline mb-6">
                                                    <textarea class="form-control h-px-50 text-danger" id="acs_remarks" readonly  >{{$preauth_register->acs_remarks}}</textarea>
                                                    <label for="remarks">Rejected By ACS/Chairman</label>
                                                </div>
                                            </div>
                                        @endif
                                        @if($preauth_register->aco_remark && $preauth_register->status == PreauthRegister::STATUS_ACO_CLAIM_REJECTED)
                                            <div class="col-md-12 mt-2">
                                                <div class="form-floating form-floating-outline mb-6">
                                                    <textarea class="form-control h-px-50 text-danger" id="aco_claim_remarks" readonly  >{{$preauth_register->aco_remark}}</textarea>
                                                    <label for="remarks">Rejected By ACO</label>
                                                </div>
                                            </div>
                                        @endif
                                        @if($preauth_register->claim_approve_remarks && $preauth_register->status == PreauthRegister::STATUS_CLAIM_REJECTED)
                                            <div class="col-md-12 mt-2">
                                                <div class="form-floating form-floating-outline mb-6">
                                                    <textarea class="form-control h-px-50 text-danger" id="claim_remarks" readonly  >{{$preauth_register->claim_approve_remarks}}</textarea>
                                                    <label for="remarks">Rejected By CPD</label>
                                                </div>
                                            </div>
                                        @endif
                                        @if($preauth_register->erroneous_remarks && $preauth_register->status == PreauthRegister::STATUS_ERRONEOUS_CLAIM_REJECTED)
                                            <div class="col-md-12 mt-2">
                                                <div class="form-floating form-floating-outline mb-6">
                                                    <textarea class="form-control h-px-50 text-danger" readonly  >{{$preauth_register->erroneous_remarks}}</textarea>
                                                    <label for="remarks">Rejected By CPD</label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                    <!-- Social Links -->
                    <div id="social-links" class="content">
                        <div class="row g-5">
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-outline-secondary btn-prev">
                                    <i class="ri-arrow-left-line me-sm-1 me-0"></i>
                                    <span
                                        class="align-middle d-sm-inline-block d-none">Previous</span>
                                </button>
                                <button class="btn btn-primary btn-submit">Submit</button>
                            </div>
                        </div>
                    </div>
                    <!-- Clamin Pending -->
                    <div id="claim-pending" class="content">
                        <div class="row g-5">
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-outline-secondary btn-prev">
                                    <i class="ri-arrow-left-line me-sm-1 me-0"></i>
                                    <span
                                        class="align-middle d-sm-inline-block d-none">Previous</span>
                                </button>
                                <button class="btn btn-primary btn-submit">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        // $('.document0').click();
        $('.Finance').click();
    });
    $(document).ready(function() {
        const firstPendingIndex = findFirstPendingIndex();
        if (firstPendingIndex !== -1) {
            loadDocumentByIndex(firstPendingIndex);
        }
    });
    $("#approve-preauth-btn").on("click",function(){
        var required_flag=0;
        $(".accordion-button").each(function(){
            if (!$(this).hasClass("theme-color")) {
                required_flag=1;
            }
        });
        if(required_flag){
            errorMessage('Please see all tabs and fill a required details.');
            return false;
        }
        var status = $("#preauth_status").val();
        swal({
            title: "Are you sure?",
            text: status +" this claim.",
            type: "warning",
            buttons: {
            cancel: {
                visible: true,
                text: "No, cancel!",
                className: "btn btn-danger",
            },
            confirm: {
                text: "Yes!",
                className: "btn btn-success",
            },
            },
        }).then((willDelete) => {
            if (willDelete) {
                var formData = new FormData($('#approveRejectPreauthForm')[0]);
        
                $(".loader-overlay").show();
                $('.error').remove();
                $.ajax({
                    url: '{{route("sha.approve-preauth")}}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $(".loader-overlay").hide();
                        if(response.success){
                            successMessage(response.message);
                            setTimeout(() => {
                                window.location.href="{{ route('sha.dashboard') }}";
                            }, 1000);
                        }else{
                            errorMessage(response.message);
                        }
                    },
                    error: function (xhr) {
                        $(".loader-overlay").hide();
                        $('.error').remove();
                        
                        if (xhr.status === 422) { 
                            let errors = xhr.responseJSON.errors;
                            for (let field in errors) {
                                if($(`select[name="${field}"]`).length > 0){
                                    $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                                }else{
                                    $(`[name="${field}"]`).closest('.cexerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                                }
                            }
                        } else {
                            errorMessage('Something went wrong. Please try again later.');
                        }
                    }
                });
            }
        });
    });
    
    function opentab(tab, id) {
        $('.'+tab).removeClass('pending-color').addClass('theme-color');
        $.ajax({
            url: '{{route("sha.open-tabs")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: {
                'tab': tab,
                'id': id,
                'type': 'SHA',
                'is_open': 1
                
            },
            success: function (response) {
                $(".loader-overlay").hide();
                if(response.success){
                    // successMessage(response.message);
                    $('.'+tab).removeClass('pending-color').addClass('theme-color');
                }else{
                    // errorMessage(response.message);
                }
            },
            error: function (xhr) {
                $('.'+tab).addClass('pending-color').removeClass('theme-color');
                $(".loader-overlay").hide();
                $('.error').remove();
                errorMessage('Something went wrong. Please try again later.');
            }
        });
    }
    $("#erroneous-claim-btn").on("click",function(){
        var required_flag=0;
        $(".accordion-button").each(function(){
            if (!$(this).hasClass("theme-color")) {
                required_flag=1;
            }
        });
        if(required_flag){
            errorMessage('Please see all tabs and fill a required details.');
            return false;
        }
        var status = $("#erroneous_status").val();
        swal({
            title: "Are you sure?",
            text: status +" this claim request.",
            type: "warning",
            buttons: {
            cancel: {
                visible: true,
                text: "No, cancel!",
                className: "btn btn-danger",
            },
            confirm: {
                text: "Yes!",
                className: "btn btn-success",
            },
            },
        }).then((willDelete) => {
            if (willDelete) {
                var formData = new FormData($('#approveRejectErroneousClaimForm')[0]);
                
                $(".loader-overlay").show();
                $('.error').remove();
                $.ajax({
                    url: '{{route("sha.erroneous-claim-action")}}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $(".loader-overlay").hide();
                        if(response.success){
                            successMessage(response.message);
                            setTimeout(() => {
                                window.location.href="{{ route('sha.dashboard') }}";
                            }, 1000);
                        }else{
                            errorMessage(response.message);
                        }
                    },
                    error: function (xhr) {
                        $(".loader-overlay").hide();
                        $('.error').remove();
                        
                        if (xhr.status === 422) { 
                            let errors = xhr.responseJSON.errors;
                            for (let field in errors) {
                                if($(`select[name="${field}"]`).length > 0){
                                    $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                                } else{
                                    $(`.${field}`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                                    $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                                }
                            }
                        } else {
                            errorMessage('Something went wrong. Please try again later.');
                        }
                    }
                });
            }
        });
    });
</script>
@endpush