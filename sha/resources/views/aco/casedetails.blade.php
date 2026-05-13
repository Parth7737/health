@extends('layouts.aco.app')
@section('title','Case Details')
@section('content')
@php
    use \App\Models\PreauthRegister;
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
                                    <img src="{{  $preauth_register->benificiary->image_url  }}" width="80" alt="avatar"
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
                                <label>Registration Date</label>
                                <p><strong>{{ date("d/m/Y h:i A",strtotime($preauth_register->created_at)) }}</strong></p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="infodata">
                                <label>Preauth Amount</label>
                                <p><strong>₹{{ number_format($preauth_register->preauth_amount_without_deduction,2) }}</strong></p>
                            </div>
                            @if($preauth_register->claim_approved_amount)
                                <label>Claim Approved Amount</label>
                                <p><strong>₹{{ number_format($preauth_register->claim_approved_amount,2) }}</strong></p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="bs-stepper wizard-numbered mt-2">
                @include('ppd._partials.preauth-step')
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
                                    <button type="button" class="accordion-button collapsed {{ @$general_info && @$family_history && @$personal_history?'theme-color':'pending-color' }}"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#accordionPopoutOne"
                                        aria-expanded="true" aria-controls="accordionPopoutOne">
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
                                        class="accordion-button {{ @$authentication_consent && @$admission_details?'theme-color':'pending-color' }} collapsed"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#accordionPopoutTwo"
                                        aria-expanded="false"
                                        aria-controls="accordionPopoutTwo">
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
                                        class="accordion-button {{ ((@$preauth_diagnosis->count() > 0) && (@$procedures->count() > 0) && (@$preauth_teams->count() > 0))?'theme-color':'pending-color' }} collapsed"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#accordionPopoutThree"
                                        aria-expanded="false"
                                        aria-controls="accordionPopoutThree">
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
                                                                @include('ppd._partials.diagnosis')
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
                                                                @include('ppd._partials.procedures')
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
                                                            @include('ppd._partials.teams')
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
                                        class="accordion-button {{ (@$preauth_investigation_status) ? 'theme-color':'pending-color' }} collapsed"
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
                                                @include('aco._partials.investigations',['preauth_register_id'=>$preauth_register->id])
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingPopoutFour">
                                    <button type="button"
                                        class="accordion-button finance-color {{ (@$procedures->count() > 0)?'theme-color':'pending-color' }} collapsed"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#accordionPopoutFour"
                                        aria-expanded="false"
                                        aria-controls="accordionPopoutFour">
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
                                                            @include('aco._partials.finance')
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
        $('.document0').click();
    });
</script>
@endpush