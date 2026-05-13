@extends('layouts.cex.app')
@section('title','Pre-Authorization')
@section('content')
@php
    use \App\Models\PreauthRegister;
    use \App\CentralLogics\Helpers;
    $helpers = new Helpers();
    $medicalinfo = $helpers->checkStepSeen($preauth_register->id, 'Medical_Information', 'CEX');
    $admissioninfo = $helpers->checkStepSeen($preauth_register->id, 'Admission_Informations', 'CEX');
    $treatmentinfo = $helpers->checkStepSeen($preauth_register->id, 'Treatment', 'CEX');
    $documentsinfo = $helpers->checkDocStepSeen($preauth_register->id, 'cex_status');
    $financeinfo = $helpers->checkStepSeen($preauth_register->id, 'Finance', 'CEX');
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
                            <div
                                class="d-flex text-center justify-content-center flex-column border-end border-secondary">
                                <div class="position-relative image-overlay">
                                    <img src="{{ $preauth_register->benificiary->image_url }}" width="80" alt="avatar"
                                        class="mb-3 rounded-circle" />
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
                                <label>Claim Amount</label>
                                <p><strong>₹{{ number_format($preauth_register->preauth_amount_without_deduction,2) }}</strong></p>
                                <label>Claim Initiate Amount</label>
                                <p><strong>₹{{ number_format($preauth_register->claim_amount,2) }}</strong></p>
                                @if($preauth_register->claim_approved_amount)
                                    <label>Claim Approved Amount</label>
                                    <p><strong>₹{{ number_format($preauth_register->claim_approved_amount,2) }}</strong></p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bs-stepper wizard-numbered mt-2">
                @include('cex._partials.preauth-step')
                <div class="bs-stepper-content">
                    <!-- Account Details -->
                    <div id="account-details" class="content">
                        <h6 class="mb-0">First Step</h6>
                    </div>
                    <!-- Personal Info -->
                    <div id="personal-info" class="content active">
                        <div class="accordion accordion-popout mt-4" id="accordionPopout">
                            <div class="accordion-item ">
                                <h2 class="accordion-header" id="headingPopoutOnepast">
                                    <button type="button" class="accordion-button collapsed theme-color"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#accordionPopoutOnepast"
                                        aria-expanded="true" aria-controls="accordionPopoutOnepast" >
                                        Past History
                                    </button>
                                </h2>
                                <div id="accordionPopoutOnepast"
                                    class="accordion-collapse collapse"
                                    aria-labelledby="headingPopoutOnepast"
                                    data-bs-parent="#accordionPopout">
                                    <div class="accordion-body">
                                        <div class="row g-5">
                                            <div class="col-12">
                                                <div class="table-responsive mt-5 text-nowrap">
                                                    <table class="table">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Name</th>
                                                                <th>Case Id</th>
                                                                <th>Package Code</th>
                                                                <th>Hospital Name</th>
                                                                <th>Preauth Submission Date</th>
                                                                <th>Claim Submitted Amount</th>
                                                                <th>Status</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @include('cex._partials.past-preauth')
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item ">
                                <h2 class="accordion-header" id="headingPopoutOne">
                                    <button type="button" class="accordion-button collapsed Medical_Information {{ @$medicalinfo ? 'theme-color' : 'pending-color' }}"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#accordionPopoutOne"
                                        aria-expanded="true" aria-controls="accordionPopoutOne" onclick="opentab('Medical_Information', '{{$preauth_register->id}}')">
                                        Medical Information
                                    </button>
                                </h2>
                                <div id="accordionPopoutOne"
                                    class="accordion-collapse collapse"
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
                                <h2 class="accordion-header" id="headingPopoutTwo">
                                    <button type="button"
                                        class="accordion-button Admission_Informations {{ @$admissioninfo ?'theme-color':'pending-color' }} collapsed"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#accordionPopoutTwo"
                                        aria-expanded="false"
                                        aria-controls="accordionPopoutTwo"
                                        onclick="opentab('Admission_Informations', '{{$preauth_register->id}}')">
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
                                <h2 class="accordion-header" id="headingPopoutThree">
                                    <button type="button"
                                        class="accordion-button Treatment {{ (@$treatmentinfo) ?'theme-color':'pending-color' }} collapsed"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#accordionPopoutThree"
                                        aria-expanded="false"
                                        aria-controls="accordionPopoutThree"
                                        onclick="opentab('Treatment', '{{$preauth_register->id}}')">
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
                                                                @include('cex._partials.diagnosis')
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
                                                                @include('cex._partials.procedures')
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
                                                            @include('cex._partials.teams')
                                                        </tbody>
                                                    </table>
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
                                        <div class="inside-left-info-box mt-3">
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
                                                @include('cex._partials.investigations',['preauth_register_id'=>$preauth_register->id])
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
                                        onclick="opentab('Finance', '{{$preauth_register->id}}')">
                                        Finance
                                    </button>
                                </h2>
                                <div id="accordionPopoutFour"
                                    class="accordion-collapse collapse"
                                    aria-labelledby="headingPopoutFour"
                                    data-bs-parent="#accordionPopout">
                                    <div class="accordion-body">
                                        <h4 class="theme-color">Amount and incentive Details
                                        </h4>
                                        <div class="row g-5">
                                            <div class="col-12">
                                                <div class="table-responsive mt-5 text-nowrap">
                                                    <table class="table">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Package Code</th>
                                                                <th>Package Type</th>
                                                                <th>Stratification Cost</th>
                                                                <th>Quality</th>
                                                                <th>Package Cost</th>
                                                                <th>Adj Factor</th>
                                                                <th>Incentive</th>
                                                                <th>Amount Requested</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="table-border-bottom-0 finance-body">
                                                            @include('cex._partials.finance')
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="documentdetails">
                                            @include('cex._partials.documentdetails', ['preauth_register' => $preauth_register])
                                        </div>
                                        @if($preauth_register->status == PreauthRegister::STATUS_CLAIM_PENDING)
                                            <div class="forward-form d-none mt-3">
                                                <form onSubmit="return false" id="approveRejectPreauthForwardForm">
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
                                                                            <th>Amount Approved</th>
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
                                                                            <td>{{ @$procedure->icd_code??'Not Available' }}</td>
                                                                            <td>₹{{ number_format(@$sub_total, 2) }}</td>
                                                                        </tr>
                                                                        @if($procedure->implant_id)
                                                                        <tr>
                                                                            <td>{{ $i++ }}</td>
                                                                            <td>{{ @$procedure->implant->code }}</td>
                                                                            <td>₹{{ number_format(@$procedure->implant_price, 2) }}</td>
                                                                            <td>{{ @$procedure->icd_code??'Not Available' }}</td>
                                                                            <td>₹{{ number_format(@$sub_total, 2) }}</td>
                                                                        </tr>
                                                                        @endif
                                                                        @endforeach
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <ul class="d-flex listing-right finance-total-body">
                                                                @include('cex._partials.finance-total')
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-6 mt-2">
                                                        <select name="preauth_status" id="preauth_status" class="select2 form-select form-select-lg ">
                                                            <option value="">Select</option>
                                                            <option value="{{PreauthRegister::STATUS_CPD_CLAIM_PENDING}}" selected>Forward</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-12 mt-2">
                                                        <div class="form-floating form-floating-outline mb-6 cexerror">
                                                            <textarea class="form-control h-px-100" id="cex_remark" name="cex_remark" placeholder="Write remarks here..."></textarea>
                                                            <label for="remarks">Remarks<span class="text-danger">*</span></label>
                                                        </div>
                                                        <input type="hidden" name="preauth_register_id" value="{{ $preauth_register->id }}">
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12">
                                                            <div class="d-flex justify-content-end">
                                                                <button id="forward-claim-btn"
                                                                    class="btn btn-primary">Forward Claim</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            @if($preauth_register->status == PreauthRegister::STATUS_CLAIM_PENDING)
                                <button type="button" id="approve-preauth-btn" class="btn btn-primary ms-2 action-btn" onclick="forwardClaim()">Forward Claim</button>
                            @endif
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
    // $(document).ready(function () {
    //     $('.document0').click();
    // });
    $(document).ready(function() {
        const firstPendingIndex = findFirstPendingIndex();
        if (firstPendingIndex !== -1) {
            loadDocumentByIndex(firstPendingIndex);
        }
    });
    
    function forwardClaim(){
        if($("#accordionPopoutFour.show").length == 0){
            $('button[data-bs-target="#accordionPopoutFour"]').trigger("click");
        }
        $(".forward-form").removeClass('d-none');
        $(".action-btn").addClass('d-none');
        $("#approveRejectPreauthForwardForm")[0].reset();
    }
    $("#forward-claim-btn").on("click",function(){
        
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
        swal({
            title: "Are you sure?",
            text: "Forward this claim request.",
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
                var formData = new FormData($('#approveRejectPreauthForwardForm')[0]);
        
                $(".loader-overlay").show();
                $('.error').remove();
                $.ajax({
                    url: '{{route("cex.approve-preauth")}}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $(".loader-overlay").hide();
                        $("#approveModal").modal("hide");
                        if(response.success){
                            successMessage(response.message);
                            setTimeout(() => {
                                window.location.href="{{ route('cex.dashboard') }}";
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
            url: '{{route("cex.open-tabs")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: {
                'tab': tab,
                'id': id,
                'type': 'CEX',
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
</script>
@endpush