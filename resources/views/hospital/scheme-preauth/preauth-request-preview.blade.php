@extends('layouts.preauth.app')
@section('title','Pre-Authorization')
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
                                <label>ABHA Number</label>
                                <p><strong>{{ @$preauth_register->benificiary->aabha_id }}</strong></p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="infodata">
                                <label>Mobile Number</label>
                                <p>{{ @$preauth_register->benificiary->mobile_no }}</p>
                                <label>Address</label>
                                <p>{{ @$preauth_register->benificiary->address }} | {{ @$preauth_register->city }}, {{ @$preauth_register->district->name }}, {{ @$preauth_register->state->name }} - {{ @$preauth_register->pincode }}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="infodata">
                                <label>Registration ID</label>
                                <p>{{ $preauth_register->register_id }}</p>
                                <label>Registration Date</label>
                                <p><strong>{{ date("d/m/Y h:i A",strtotime($preauth_register->created_at)) }}</strong></p>
                            </div>
                        </div>
                        <div class="col">
                            @if(@$preauth_register->preauth_initiated_amount)
                            <div class="infodata">
                                <label>Preauth Initiate Amount</label>
                                <p><strong>₹{{ number_format($preauth_register->preauth_initiated_amount,2) }}</strong></p>
                            </div>
                            @endif
                            @if(@$preauth_register->preauth_approved_amount)
                                <div class="infodata">
                                    <label>Preauth Approved Amount</label>
                                    <p><strong>₹{{ number_format($preauth_register->preauth_approved_amount,2) }}</strong></p>
                                </div>
                            @endif
                            @if(@$preauth_register->claim_approved_amount)
                                <div class="infodata">
                                    <label>Claim Approved Amount</label>
                                    <p><strong>₹{{ number_format($preauth_register->claim_approved_amount,2) }}</strong></p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="bs-stepper wizard-numbered mt-2">
                @include('preauth._partials.preauth-step')
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
                                        class="accordion-button {{ ((@$preauth_diagnosis->count() > 0) && (@$procedures->count() > 0) && $preauth_investigation_status && (@$preauth_teams->count() > 0))?'theme-color':'pending-color' }} collapsed"
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
                                                                @include('preauth._partials.diagnosis',['is_action_hide'=>1])
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="inside-left-info-box rsf-content {{ (@$procedures->count() > 0)?'success':'pending' }} mt-3">
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
                                                                class="table-border-bottom-0">
                                                                @include('preauth._partials.procedures',['is_action_hide'=>1])
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="inside-left-info-box rsf-content {{ $preauth_investigation_status?'success':'pending' }} mt-3">
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
                                                <div class="col-12">
                                                    <div
                                                        class="table-responsive mt-5 text-nowrap">
                                                        <table class="table">
                                                            <thead class="table-dark">
                                                                <tr>
                                                                    <th>No.</th>
                                                                    <th>Name</th>
                                                                    <th>Attachment</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody
                                                                class="table-border-bottom-0 investigation-body">
                                                                @include('preauth._partials.investigations',['preauth_register_id'=>$preauth_register->id,'is_preview'=>1])
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if($preauth_register->status == PreauthRegister::STATUS_PREAUTH_APPROVED && !empty($preauth_register->preauth_approved_date))
                                            <div class="resubmission-form d-none">
                                                <div class="inside-left-info-box {{ (@$procedures->count() > 0)?'success':'pending' }} mt-3">
                                                    <form onSubmit="return false" id="resubmissionForm">
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
                                                            <div class="col-md-8 mb-4">
                                                                <div
                                                                    class="form-floating form-floating-outline">
                                                                    <input type="hidden" name="type" id="submission_type">
                                                                    <select class="form-select select2" id="specialitys"
                                                                        name="speciality_id">
                                                                        <option value=""></option>
                                                                        @foreach($hospital_speciality as $hospital_spe)
                                                                            <option value="{{ $hospital_spe->speciality_id }}">{{ @$hospital_spe->speciality->name }}</option>
                                                                        @endforeach
                                                                        @if(@$us)
                                                                            <option value="{{ $us->id }}">{{ @$us->name }}</option>
                                                                        @endif
                                                                    </select>
                                                                    <label
                                                                        for="speciality_id">Speciality</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-8 mb-4">
                                                                <div
                                                                    class="form-floating form-floating-outline">
                                                                    <select class="form-select select2" id="procedure_id"
                                                                        name="procedure_id">
                                                                        <option value=""></option>
                                                                    </select>
                                                                    <label for="procedure_id">Procedure</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-8 mb-4 implant-field d-none">
                                                                <div
                                                                    class="form-floating form-floating-outline">
                                                                    <select class="form-select select2" id="implant_id"
                                                                        name="implant_id">
                                                                        <option value=""></option>
                                                                    </select>
                                                                    <label for="implant_id">Implant</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-8 mb-4 implant-field d-none">
                                                                <div
                                                                    class="form-floating form-floating-outline">
                                                                    <input type="text" id="implant_qty" name="implant_qty"
                                                                        class="form-control"
                                                                        placeholder="" readonly />
                                                                    <label for="implant_qty">Quantity</label>
                                                                </div>
                                                                <div id="implant-qty-error"></div>
                                                            </div>
                                                            <div class="col-md-8 mb-4 stratification-field d-none">
                                                                <div
                                                                    class="form-floating form-floating-outline">
                                                                    <select class="form-select select2" id="stratification_id"
                                                                        name="stratification_id">
                                                                        <option value=""></option>
                                                                    </select>
                                                                    <label for="stratification_id">Stratification</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-8 mb-4">
                                                                <div
                                                                    class="form-floating form-floating-outline">
                                                                    <input type="text" id="no_of_days" name="no_of_days"
                                                                        class="form-control"
                                                                        placeholder="" />
                                                                    <label for="no_of_days">No of
                                                                        Days/Units</label>
                                                                </div>
                                                                <div id="no-of-days-error"></div>
                                                            </div>
                                                            <div class="col-md-8 mb-4 u100-field d-none">
                                                                <div
                                                                    class="form-floating form-floating-outline">
                                                                    <input type="text" id="u100_amount" name="u100_amount" oninput="sanitize(this, 'n','10');"
                                                                        class="form-control"
                                                                        placeholder="" />
                                                                    <label for="u100_amount">Unverfied Amount</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-8 mb-4">
                                                                <div
                                                                    class="form-floating form-floating-outline">
                                                                    <input type="text" id="icd_code" name="ichi"
                                                                        class="form-control" readonly
                                                                        placeholder="" />
                                                                    <label for="icd_code">ICHI Code:</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 ">
                                                                <button id="resubmission-btn"
                                                                    class="btn btn-outline-info" disabled>Add</button>
                                                            </div>
                                                        </div>
                                                    </form>
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
                                                                            <th>Actions</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody
                                                                        class="table-border-bottom-0 resubmission-procedure-body">
                                                                        @include('preauth._partials.procedures',['is_enhancement'=>1])
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="inside-left-info-box {{ $preauth_investigation_status?'success':'pending' }} mt-3">
                                                    <form onSubmit="return false" id="investigationForm">
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
                                                            <div class="col-12">
                                                                <div
                                                                    class="table-responsive mt-5 text-nowrap">
                                                                    <table class="table">
                                                                        <thead class="table-dark">
                                                                            <tr>
                                                                                <th>No.</th>
                                                                                <th>Name</th>
                                                                                <th>Attachment</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody
                                                                            class="table-border-bottom-0 resubmission-investigation-body">
                                                                            @include('preauth._partials.investigations',['preauth_register_id'=>$preauth_register->id,'is_preview'=>1])
                                                                        </tbody>
                                                                        <tbody
                                                                            class="table-border-bottom-0 inhancement-doc d-none">
                                                                            @include('preauth._partials.inhancement-docs',['preauth_register_id'=>$preauth_register->id])
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="d-flex justify-content-end">
                                                                    <button id="investigation-btn"
                                                                        class="btn btn-primary">SAVE</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
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
                                                            @include('preauth._partials.teams',['is_action_hide'=>1])
                                                        </tbody>
                                                    </table>
                                                </div>
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
                                                                <th>LOS/Quantity</th>
                                                                <th>Package Cost</th>
                                                                <th>Adj Factor</th>
                                                                <th>Incentive</th>
                                                                <th>Total Amount</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="table-border-bottom-0 finance-body">
                                                            @include('preauth._partials.finance')
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @if(($preauth_register->status != PreauthRegister::STATUS_CLAIM_QUERIED) && ($preauth_register->status != PreauthRegister::STATUS_PREAUTH_QUERIED))
                                                    <ul class="d-flex listing-right finance-total-body">
                                                        @include('preauth._partials.finance-total')
                                                    </ul>
                                                @endif
                                            </div>
                                        </div>
                                        @if($preauth_register->status == PreauthRegister::STATUS_PREAUTH_APPROVED)
                                            <div class="discharge-form d-none mt-3">
                                                <div class="inside-left-info-box pending">
                                                    <h4 class="colored-verticle-title">Discharge <span class="status-dot">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            height="24px" viewBox="0 -960 960 960"
                                                            width="24px" fill="undefined">
                                                            <path
                                                                d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
                                                        </svg>
                                                    </span></h4>
                                                    <form onSubmit="return false" id="dischargeForm">
                                                        <div class="row">
                                                            <div class="col-md-3 mb-6 mt-2">
                                                                <div class="form-floating form-floating-outline">
                                                                    <select id="discharge_type" name="discharge_type" class="select2 form-select form-select-lg" data-allow-clear="true">
                                                                    <option value="">Select Type</option>
                                                                    <option value="Normal">Normal Discharge</option>
                                                                    <option value="LAMA/DAMA/DOPR">LAMA/DAMA/DOPR</option>
                                                                    <!-- <option value="DAMA">DAMA</option> -->
                                                                    <option value="RHC">Refer to Higher Center</option>
                                                                    <option value="Death">Death</option>
                                                                    </select>
                                                                    <label for="discharge_type">Discharge Type<span class="text-danger">*</span></label>
                                                                </div>
                                                                <input type="hidden" name="discharge_patient_id" id="discharge_patient_id">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3 mb-6 discharge-stage-field">
                                                                <div class="form-floating form-floating-outline">
                                                                    <select id="discharge_stage" name="discharge_stage" class="select2 form-select form-select-lg" data-allow-clear="true">
                                                                        <option value="">Select</option>
                                                                        <option value="Before Surgery">Before Surgery</option>
                                                                        <option value="During Surgery">During Surgery</option>
                                                                        <option value="After Surgery">After Surgery</option>
                                                                        <option value="Refer before PAC and surgery">Refer before PAC and Surgery</option>
                                                                        <option value="Refer after PAC but before surgery">Refer after PAC but before Surgery</option>
                                                                        <option value="Refer after surgery">Refer After Surgery</option>
                                                                    </select>
                                                                    <label for="discharge_stage" class="discharge-stage-title">LAMA Stage<span class="text-danger">*</span></label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-lg-3 lama-field d-none">
                                                                <div
                                                                    class="form-floating form-floating-outline">
                                                                    <input type="text"
                                                                        id="bs-rangepicker-single-2" name="lama_date" value="" oninput="sanitize(this, 'd');"
                                                                        class="form-control" />
                                                                    <label for="bs-rangepicker-single-2">LAMA/DAMA/DOPR Date <span class="text-danger">*</span></label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-lg-3 lama-field dama-field normal-field d-none">
                                                                <div
                                                                    class="form-floating form-floating-outline">
                                                                    <input type="text"
                                                                        id="bs-rangepicker-third" name="surgery_date" value="" oninput="sanitize(this, 'd');"
                                                                        class="form-control" />
                                                                    <label for="bs-rangepicker-third">Surgery Date <span class="text-danger">*</span></label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-lg-3 death-field d-none">
                                                                <div
                                                                    class="form-floating form-floating-outline">
                                                                    <input type="text"
                                                                        id="bs-rangepicker-forth" name="death_date" value="" oninput="sanitize(this, 'd');"
                                                                        class="form-control" />
                                                                    <label for="bs-rangepicker-forth">Death Date <span class="text-danger">*</span></label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-lg-3 dama-field normal-field d-none">
                                                                <div
                                                                    class="form-floating form-floating-outline">
                                                                    <input type="text"
                                                                        id="discharge-date" name="discharge_date" value="" oninput="sanitize(this, 'd');"
                                                                        class="form-control" />
                                                                    <label for="discharge-date" class="lama-dama-title">Discharge Date <span class="text-danger">*</span></label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 mb-6 normal-field lama-field d-none">
                                                                <div class="form-floating form-floating-outline">
                                                                    <select id="provide_medicine" name="provide_medicine" class="select2 form-select form-select-lg" data-allow-clear="true">
                                                                        <option value="">Select</option>
                                                                        <option value="Yes">Yes</option>
                                                                        <option value="No">No</option>
                                                                    </select>
                                                                    <label for="provide_medicine">Hospital Provided Medicine During Treatment<span class="text-danger">*</span></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 col-lg-3 death-field d-none">
                                                                <label for="formFile" class="form-label">Death Certificate<span class="text-danger">*</span></label>
                                                                <div class="file-upload-section">
                                                                    <div class="file-upload-wrapper">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px" fill="#6200ea">
                                                                            <path
                                                                                d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                        </svg>
                                                                        <p>
                                                                            <strong>Browse</strong></p>
                                                                    </div>
                                                                    <input type="file" name="death_certificate"
                                                                        class="file-input d-none" />
                                                                    <div
                                                                        class="uploaded-file file-upload-display d-none">
                                                                        <span
                                                                            class="file-name">Sample.pdf</span>
                                                                        <i class="fas fa-trash "></i>
                                                                        <button
                                                                            class="remove-file-btn bg-transparent border-0 p-0">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                height="24px"
                                                                                viewBox="0 -960 960 960"
                                                                                width="24px"
                                                                                fill="undefined">
                                                                                <path
                                                                                    d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-lg-3 death-field d-none">
                                                                <label for="formFile" class="form-label">Clinical Note/Death Summary<span class="text-danger">*</span></label>
                                                                <div class="file-upload-section">
                                                                    <div class="file-upload-wrapper">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px" fill="#6200ea">
                                                                            <path
                                                                                d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                        </svg>
                                                                        <p>
                                                                            <strong>Browse</strong></p>
                                                                    </div>
                                                                    <input type="file" name="death_summary"
                                                                        class="file-input d-none" />
                                                                    <div
                                                                        class="uploaded-file file-upload-display d-none">
                                                                        <span
                                                                            class="file-name">Sample.pdf</span>
                                                                        <i class="fas fa-trash "></i>
                                                                        <button
                                                                            class="remove-file-btn bg-transparent border-0 p-0">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                height="24px"
                                                                                viewBox="0 -960 960 960"
                                                                                width="24px"
                                                                                fill="undefined">
                                                                                <path
                                                                                    d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-lg-3 death-field d-none">
                                                                <label for="formFile" class="form-label">Mortality Audit Report<span class="text-danger">*</span></label>
                                                                <div class="file-upload-section">
                                                                    <div class="file-upload-wrapper">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px" fill="#6200ea">
                                                                            <path
                                                                                d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                        </svg>
                                                                        <p>
                                                                            <strong>Browse</strong></p>
                                                                    </div>
                                                                    <input type="file" name="mortality_audit_report"
                                                                        class="file-input d-none" />
                                                                    <div
                                                                        class="uploaded-file file-upload-display d-none">
                                                                        <span
                                                                            class="file-name">Sample.pdf</span>
                                                                        <i class="fas fa-trash "></i>
                                                                        <button
                                                                            class="remove-file-btn bg-transparent border-0 p-0">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                height="24px"
                                                                                viewBox="0 -960 960 960"
                                                                                width="24px"
                                                                                fill="undefined">
                                                                                <path
                                                                                    d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-lg-3 lama-field dama-field d-none">
                                                                <label for="formFile" class="form-label">In-Treatment Photo with doctor/PMAM<span class="text-danger">*</span></label>
                                                                <div class="file-upload-section">
                                                                    <div class="file-upload-wrapper">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px" fill="#6200ea">
                                                                            <path
                                                                                d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                        </svg>
                                                                        <p>
                                                                            <strong>Browse</strong></p>
                                                                    </div>
                                                                    <input type="file" name="in_treatment_photo"
                                                                        class="file-input d-none" />
                                                                    <div
                                                                        class="uploaded-file file-upload-display d-none">
                                                                        <span
                                                                            class="file-name">Sample.pdf</span>
                                                                        <i class="fas fa-trash "></i>
                                                                        <button
                                                                            class="remove-file-btn bg-transparent border-0 p-0">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                height="24px"
                                                                                viewBox="0 -960 960 960"
                                                                                width="24px"
                                                                                fill="undefined">
                                                                                <path
                                                                                    d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-lg-3 normal-field d-none">
                                                                <label for="formFile" class="form-label">Post-Surgery Photo with doctor/PMAM<span class="text-danger">*</span></label>
                                                                <div class="file-upload-section">
                                                                    <div class="file-upload-wrapper">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px" fill="#6200ea">
                                                                            <path
                                                                                d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                        </svg>
                                                                        <p>
                                                                            <strong>Browse</strong></p>
                                                                    </div>
                                                                    <input type="file" name="post_surgery_photo"
                                                                        class="file-input d-none" />
                                                                    <div
                                                                        class="uploaded-file file-upload-display d-none">
                                                                        <span
                                                                            class="file-name">Sample.pdf</span>
                                                                        <i class="fas fa-trash "></i>
                                                                        <button
                                                                            class="remove-file-btn bg-transparent border-0 p-0">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                height="24px"
                                                                                viewBox="0 -960 960 960"
                                                                                width="24px"
                                                                                fill="undefined">
                                                                                <path
                                                                                    d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-lg-3 normal-field d-none">
                                                                <label for="formFile" class="form-label">Discharge Summary<span class="text-danger">*</span></label>
                                                                <div class="file-upload-section">
                                                                    <div class="file-upload-wrapper">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px" fill="#6200ea">
                                                                            <path
                                                                                d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                        </svg>
                                                                        <p>
                                                                            <strong>Browse</strong></p>
                                                                    </div>
                                                                    <input type="file" name="discharge_summary"
                                                                        class="file-input d-none" />
                                                                    <div
                                                                        class="uploaded-file file-upload-display d-none">
                                                                        <span
                                                                            class="file-name">Sample.pdf</span>
                                                                        <i class="fas fa-trash "></i>
                                                                        <button
                                                                            class="remove-file-btn bg-transparent border-0 p-0">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                height="24px"
                                                                                viewBox="0 -960 960 960"
                                                                                width="24px"
                                                                                fill="undefined">
                                                                                <path
                                                                                    d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-lg-3 normal-field lama-field dama-field d-none">
                                                                <label for="formFile" class="form-label">Feedback Form<span class="text-danger">*</span></label>
                                                                <div class="file-upload-section">
                                                                    <div class="file-upload-wrapper">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px" fill="#6200ea">
                                                                            <path
                                                                                d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                        </svg>
                                                                        <p>
                                                                            <strong>Browse</strong></p>
                                                                    </div>
                                                                    <input type="file" name="feedback_form"
                                                                        class="file-input d-none" />
                                                                    <div
                                                                        class="uploaded-file file-upload-display d-none">
                                                                        <span
                                                                            class="file-name">Sample.pdf</span>
                                                                        <i class="fas fa-trash "></i>
                                                                        <button
                                                                            class="remove-file-btn bg-transparent border-0 p-0">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                height="24px"
                                                                                viewBox="0 -960 960 960"
                                                                                width="24px"
                                                                                fill="undefined">
                                                                                <path
                                                                                    d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-lg-3 normal-field lama-field dama-field d-none">
                                                                <label for="formFile" class="form-label">Beneficiary Verification Form<span class="text-danger">*</span></label>
                                                                <div class="file-upload-section">
                                                                    <div class="file-upload-wrapper">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px" fill="#6200ea">
                                                                            <path
                                                                                d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                        </svg>
                                                                        <p>
                                                                            <strong>Browse</strong></p>
                                                                    </div>
                                                                    <input type="file" name="beneficiary_verification_form"
                                                                        class="file-input d-none" />
                                                                    <div
                                                                        class="uploaded-file file-upload-display d-none">
                                                                        <span
                                                                            class="file-name">Sample.pdf</span>
                                                                        <i class="fas fa-trash "></i>
                                                                        <button
                                                                            class="remove-file-btn bg-transparent border-0 p-0">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                height="24px"
                                                                                viewBox="0 -960 960 960"
                                                                                width="24px"
                                                                                fill="undefined">
                                                                                <path
                                                                                    d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-lg-3 normal-field lama-field dama-field d-none">
                                                                <label for="formFile" class="form-label">Hospital Certificate<span class="text-danger">*</span></label>
                                                                <div class="file-upload-section">
                                                                    <div class="file-upload-wrapper">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px" fill="#6200ea">
                                                                            <path
                                                                                d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                        </svg>
                                                                        <p>
                                                                            <strong>Browse</strong></p>
                                                                    </div>
                                                                    <input type="file" name="hospital_certificate"
                                                                        class="file-input d-none" />
                                                                    <div
                                                                        class="uploaded-file file-upload-display d-none">
                                                                        <span
                                                                            class="file-name">Sample.pdf</span>
                                                                        <i class="fas fa-trash "></i>
                                                                        <button
                                                                            class="remove-file-btn bg-transparent border-0 p-0">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                height="24px"
                                                                                viewBox="0 -960 960 960"
                                                                                width="24px"
                                                                                fill="undefined">
                                                                                <path
                                                                                    d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-md-12">
                                                                <div class="d-flex justify-content-end">
                                                                    <button id="discharge-btn"
                                                                        class="btn btn-primary">Discharge</button>
                                                                    <button id="back-btn"
                                                                        class="btn btn-primary ms-2">Back</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                        @if($preauth_register->status == PreauthRegister::STATUS_CLAIM_SUBMITTED)
                                            <div class="claim-form d-none mt-3">
                                                <div class="inside-left-info-box pending">
                                                <h4 class="colored-verticle-title">Claim Initiate <span class="status-dot">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            height="24px" viewBox="0 -960 960 960"
                                                            width="24px" fill="undefined">
                                                            <path
                                                                d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
                                                        </svg>
                                                    </span></h4>
                                                    <form onSubmit="return false" id="claimForm">
                                                        <div class="row">
                                                            <div class="col-md-3 mb-6 mt-2">
                                                                <div
                                                                    class="form-floating form-floating-outline">
                                                                    <input type="number"
                                                                        id="bill_no" name="bill_no" value=""
                                                                        class="form-control" />
                                                                    <label for="bill_no">Hospital Bill Number <span class="text-danger">*</span></label>
                                                                </div>
                                                                <input type="hidden" name="claim_patient_id" id="claim_patient_id" value="{{ $preauth_register->id }}">
                                                            </div>
                                                            <div class="col-md-3 mb-6 mt-2">
                                                                <div
                                                                    class="form-floating form-floating-outline">
                                                                    <input type="text"
                                                                        id="claim-date" name="bill_date" value="{{ date('Y-m-d') }}" oninput="sanitize(this, 'd');"
                                                                        class="form-control" />
                                                                    <label for="claim-date">Date <span class="text-danger">*</span></label>
                                                                </div>
                                                            </div>
                                                            @php $total_preauth_amount = App\CentralLogics\Helpers::getPreauthAmountWithoutDeduction($preauth_register->id); @endphp
                                                            
                                                            <div class="col-md-3 mb-6 mt-2">
                                                                <div
                                                                    class="form-floating form-floating-outline">
                                                                    <input type="number"
                                                                        id="claim_amount" name="claim_amount" value="{{ $total_preauth_amount }}" max="{{ $total_preauth_amount }}"
                                                                        class="form-control" />
                                                                    <label for="claim_amount">Hospital Bill Amount<span class="text-danger">*</span></label>
                                                                </div>
                                                                <input type="hidden" name="claim_patient_id" id="claim_patient_id" value="{{ $preauth_register->id }}">
                                                                <div id="claim-amount-error"></div>
                                                            </div>
                                                            <div class="col-md-3 mb-6 mt-2">
                                                                <div
                                                                    class="form-floating form-floating-outline">
                                                                    <input type="number" value="{{ $total_preauth_amount }}" id="claim-initiate-amount" disabled class="form-control" />
                                                                    <label for="claim-initiate-amount">Claim Initiate Amount<span class="text-danger">*</span></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 col-lg-3">
                                                                <label for="formFile" class="form-label">Hospital Bill</label>
                                                                <div class="file-upload-section">
                                                                    <div class="file-upload-wrapper">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px" fill="#6200ea">
                                                                            <path
                                                                                d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                        </svg>
                                                                        <p>
                                                                            <strong>Browse</strong></p>
                                                                    </div>
                                                                    <input type="file" name="hospital_bill"
                                                                        class="file-input d-none" />
                                                                    <div
                                                                        class="uploaded-file file-upload-display d-none">
                                                                        <span
                                                                            class="file-name">Sample.pdf</span>
                                                                        <i class="fas fa-trash "></i>
                                                                        <button
                                                                            class="remove-file-btn bg-transparent border-0 p-0">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                height="24px"
                                                                                viewBox="0 -960 960 960"
                                                                                width="24px"
                                                                                fill="undefined">
                                                                                <path
                                                                                    d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <br/><small class="text-danger fs-11">Upload a only pdf format file and max size should be 5MB</small><br/>
                                                            </div>
                                                            <div class="col-md-6 col-lg-3">
                                                                <label for="formFile" class="form-label">Any Other Document</label>
                                                                <div class="file-upload-section">
                                                                    <div class="file-upload-wrapper">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px" fill="#6200ea">
                                                                            <path
                                                                                d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                        </svg>
                                                                        <p>
                                                                            <strong>Browse</strong></p>
                                                                    </div>
                                                                    <input type="file" name="claim_other_doc"
                                                                        class="file-input d-none" />
                                                                    <div
                                                                        class="uploaded-file file-upload-display d-none">
                                                                        <span
                                                                            class="file-name">Sample.pdf</span>
                                                                        <i class="fas fa-trash "></i>
                                                                        <button
                                                                            class="remove-file-btn bg-transparent border-0 p-0">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                height="24px"
                                                                                viewBox="0 -960 960 960"
                                                                                width="24px"
                                                                                fill="undefined">
                                                                                <path
                                                                                    d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <br/><small class="text-danger fs-11">Upload a only pdf format file and max size should be 5MB</small><br/>
                                                            </div>
                                                        </div>
                                                        <h6 class="colored-verticle-title mt-3">Post Investigations<span class="status-dot">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    height="24px" viewBox="0 -960 960 960"
                                                                    width="24px" fill="undefined">
                                                                    <path
                                                                        d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
                                                                </svg>
                                                            </span></h6>
                                                        <div class="row justify-content-center">
                                                            <div class="col-12">
                                                                <div
                                                                    class="table-responsive mt-2 text-nowrap">
                                                                    <table class="table">
                                                                        <thead class="table-dark">
                                                                            <tr>
                                                                                <th>No.</th>
                                                                                <th>Name</th>
                                                                                <th>Attachment</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody
                                                                            class="table-border-bottom-0 investigation-body">
                                                                            @include('preauth._partials.post-investigations',['preauth_register_id'=>$preauth_register->id])
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="d-flex justify-content-end">
                                                                    <button id="claim-btn"
                                                                        class="btn btn-primary">Submit Claim</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($preauth_register->status == PreauthRegister::STATUS_PREAUTH_QUERIED)
                                            <div class="query-form d-none mt-3">
                                                <form onSubmit="return false" id="queryPreauthForm">
                                                    <h4 class="theme-color mt-3">Query Response
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
                                                                            <th>Action</th>
                                                                            <th>Reason</th>
                                                                            <th>Remarks</th>
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
                                                                            <td>₹{{ number_format(@$sub_total-@$procedure->incentive, 2) }}</td>
                                                                            <td>{{ @$procedure->icd_code??'Not Available' }}</td>
                                                                            <td>₹{{ number_format(@$sub_total, 2) }}</td>
                                                                            <td>
                                                                                {{ $procedure->preauth_status??'Not Applicable' }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $procedure->preauth_reason??'Not Applicable' }}
                                                                            </td>
                                                                            <td>
                                                                                <div class="remarks-section">
                                                                                    @if($procedure->preauth_reason == 'Other')
                                                                                        <a href="javascript:;" onclick="openRemarkModal('{{ $procedure->id }}','procedure');"><i class="ri-message-2-line"></i></a>
                                                                                    @else
                                                                                        Not Applicable
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        @if($procedure->implant_id)
                                                                        @php $sub_total = (@$procedure->implant_price*@$procedure->implant_qty) @endphp
                                                                        <tr>
                                                                            <td>{{ $i++ }}</td>
                                                                            <td>{{ @$procedure->implant->code }}</td>
                                                                            <td>₹{{ number_format(@$procedure->implant_price, 2) }}</td>
                                                                            <td>{{ 'Not Available' }}</td>
                                                                            <td>₹{{ number_format(@$sub_total, 2) }}</td>
                                                                            <td>
                                                                                {{ $procedure->preauth_implant_status??'Not Applicable' }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $procedure->preauth_implant_reason??'Not Applicable' }}
                                                                            </td>
                                                                            <td>
                                                                                <div class="remarks-section">
                                                                                    @if($procedure->preauth_implant_reason == 'Other')
                                                                                        <a href="javascript:;" onclick="openRemarkModal('{{ $procedure->id }}','implant');"><i class="ri-message-2-line"></i></a>
                                                                                    @else
                                                                                        Not Applicable
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        @endif
                                                                        @endforeach
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <ul class="d-flex listing-right finance-total-body">
                                                                @include('preauth._partials.finance-total')
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-6 col-lg-3">
                                                            <label for="formFile" class="form-label">Additional Document</label>
                                                            <div class="file-upload-section">
                                                                <div class="file-upload-wrapper">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        height="24px"
                                                                        viewBox="0 -960 960 960"
                                                                        width="24px" fill="#6200ea">
                                                                        <path
                                                                            d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                    </svg>
                                                                    <p>
                                                                        <strong>Browse</strong></p>
                                                                </div>
                                                                <input type="file" name="preauth_query_add_doc"
                                                                    class="file-input d-none" />
                                                                <div
                                                                    class="uploaded-file file-upload-display d-none">
                                                                    <span
                                                                        class="file-name">Sample.pdf</span>
                                                                    <i class="fas fa-trash "></i>
                                                                    <button
                                                                        class="remove-file-btn bg-transparent border-0 p-0">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px"
                                                                            fill="undefined">
                                                                            <path
                                                                                d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-3">
                                                            <label for="formFile" class="form-label">Supporting Document<span class="text-danger">*</span></label>
                                                            <div class="file-upload-section">
                                                                <div class="file-upload-wrapper">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        height="24px"
                                                                        viewBox="0 -960 960 960"
                                                                        width="24px" fill="#6200ea">
                                                                        <path
                                                                            d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                    </svg>
                                                                    <p>
                                                                        <strong>Browse</strong></p>
                                                                </div>
                                                                <input type="file" name="preauth_query_supporting_doc"
                                                                    class="file-input d-none" />
                                                                <div
                                                                    class="uploaded-file file-upload-display d-none">
                                                                    <span
                                                                        class="file-name">Sample.pdf</span>
                                                                    <i class="fas fa-trash "></i>
                                                                    <button
                                                                        class="remove-file-btn bg-transparent border-0 p-0">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px"
                                                                            fill="undefined">
                                                                            <path
                                                                                d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <div class="form-floating form-floating-outline mb-6">
                                                            <textarea class="form-control h-px-100" id="query_remarks" name="query_remarks" placeholder="Write remarks here..."></textarea>
                                                            <label for="query_remarks">Remarks<span class="text-danger">*</span></label>
                                                        </div>
                                                        <div id="preauth-remark-error" class="text-danger"></div>
                                                        <input type="hidden" name="preauth_register_id" value="{{ $preauth_register->id }}">
                                                    </div>
                                                    @if($preauth_register->preauth_approve_remarks && $preauth_register->status == PreauthRegister::STATUS_PREAUTH_QUERIED)
                                                        <div class="col-md-12 mt-2">
                                                            <div class="form-floating form-floating-outline mb-6">
                                                                <textarea class="form-control h-px-50 text-danger" id="remarks" readonly >{{$preauth_register->preauth_approve_remarks}}</textarea>
                                                                <label for="remarks">Queried By PPD</label>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="d-flex justify-content-end">
                                                                <button id="query-preauth-btn"
                                                                    class="btn btn-primary">Submit Preauth</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                        @if($preauth_register->status == PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED || $preauth_register->status == PreauthRegister::STATUS_CEO_QUERIED || $preauth_register->status == PreauthRegister::STATUS_ACS_QUERIED)
                                            <div class="u100-query-form d-none mt-3">
                                                <form onSubmit="return false" id="u100QueryPreauthForm">
                                                    <h4 class="theme-color mt-3">Query Response
                                                    </h4>
                                                    <div class="row">
                                                        <div class="col-md-6 col-lg-3">
                                                            <label for="formFile" class="form-label">Supporting Document<span class="text-danger">*</span></label>
                                                            <div class="file-upload-section">
                                                                <div class="file-upload-wrapper">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        height="24px"
                                                                        viewBox="0 -960 960 960"
                                                                        width="24px" fill="#6200ea">
                                                                        <path
                                                                            d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                    </svg>
                                                                    <p>
                                                                        <strong>Browse</strong></p>
                                                                </div>
                                                                <input type="file" name="query_supporting_doc"
                                                                    class="file-input d-none" />
                                                                <div
                                                                    class="uploaded-file file-upload-display d-none">
                                                                    <span
                                                                        class="file-name">Sample.pdf</span>
                                                                    <i class="fas fa-trash "></i>
                                                                    <button
                                                                        class="remove-file-btn bg-transparent border-0 p-0">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px"
                                                                            fill="undefined">
                                                                            <path
                                                                                d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <div class="form-floating form-floating-outline mb-6">
                                                            <textarea class="form-control h-px-100" id="u100_query_remarks" name="u100_query_remarks" placeholder="Write remarks here..."></textarea>
                                                            <label for="u100_query_remarks">Remarks<span class="text-danger">*</span></label>
                                                        </div>
                                                        <div id="u100-remark-error" class="text-danger"></div>
                                                        <input type="hidden" name="preauth_register_id" value="{{ $preauth_register->id }}">
                                                    </div>
                                                    @if($preauth_register->committee_remarks && $preauth_register->status == PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED)
                                                        <div class="col-md-12 mt-2">
                                                            <div class="form-floating form-floating-outline mb-6">
                                                                <textarea class="form-control h-px-50 text-danger" id="remarks" readonly >{{$preauth_register->committee_remarks}}</textarea>
                                                                <label for="remarks">Queried By Medical Committee</label>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($preauth_register->ceo_remarks && $preauth_register->status == PreauthRegister::STATUS_CEO_QUERIED)
                                                        <div class="col-md-12 mt-2">
                                                            <div class="form-floating form-floating-outline mb-6">
                                                                <textarea class="form-control h-px-50 text-danger" id="remarks" readonly >{{$preauth_register->ceo_remarks}}</textarea>
                                                                <label for="remarks">Queried By CEO</label>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($preauth_register->acs_remarks && $preauth_register->status == PreauthRegister::STATUS_ACS_QUERIED)
                                                        <div class="col-md-12 mt-2">
                                                            <div class="form-floating form-floating-outline mb-6">
                                                                <textarea class="form-control h-px-50 text-danger" id="remarks" readonly >{{$preauth_register->acs_remarks}}</textarea>
                                                                <label for="remarks">Queried By ACS/Chairman</label>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="d-flex justify-content-end">
                                                                <button id="u100-query-preauth-btn"
                                                                    class="btn btn-primary">Submit Preauth</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                        @if($preauth_register->status == PreauthRegister::STATUS_CLAIM_QUERIED)
                                            <div class="claim-query-form d-none mt-3">
                                                <form onSubmit="return false" id="queryClaimForm">
                                                    <h4 class="theme-color mt-3">Claim Query Response
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
                                                                            <th>Action</th>
                                                                            <th>Reason</th>
                                                                            <th>Remarks</th>
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
                                                                            <td>₹{{ number_format(@$sub_total-@$procedure->incentive, 2) }}</td>
                                                                            <td>{{ @$procedure->icd_code??'Not Available' }}</td>
                                                                            <td>₹{{ number_format(@$sub_total, 2) }}</td>
                                                                            <td>
                                                                                {{ $procedure->preauth_claim_status??'Not Applicable' }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $procedure->preauth_claim_reason??'Not Applicable' }}
                                                                            </td>
                                                                            <td>
                                                                                <div class="remarks-section">
                                                                                    @if($procedure->preauth_claim_reason == 'Other')
                                                                                        <a href="javascript:;" onclick="openRemarkModal('{{ $procedure->id }}','procedure');"><i class="ri-message-2-line"></i></a>
                                                                                    @else
                                                                                        Not Applicable
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        @if($procedure->implant_id)
                                                                        @php $sub_total = (@$procedure->implant_price*@$procedure->implant_qty) @endphp
                                                                        <tr>
                                                                            <td>{{ $i++ }}</td>
                                                                            <td>{{ @$procedure->implant->code }}</td>
                                                                            <td>₹{{ number_format(@$procedure->implant_price, 2) }}</td>
                                                                            <td>{{ 'Not Available' }}</td>
                                                                            <td>₹{{ number_format(@$sub_total, 2) }}</td>
                                                                            <td>
                                                                                {{ $procedure->preauth_claim_implant_status??'Not Applicable' }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $procedure->preauth_claim_implant_reason??'Not Applicable' }}
                                                                            </td>
                                                                            <td>
                                                                                <div class="remarks-section">
                                                                                    @if($procedure->preauth_claim_implant_reason == 'Other')
                                                                                        <a href="javascript:;" onclick="openRemarkModal('{{ $procedure->id }}','implant');"><i class="ri-message-2-line"></i></a>
                                                                                    @else
                                                                                        Not Applicable
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        @endif
                                                                        @endforeach
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <ul class="d-flex listing-right finance-total-body">
                                                                @include('preauth._partials.finance-total')
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-6 col-lg-3">
                                                            <label for="formFile" class="form-label">Additional Document</label>
                                                            <div class="file-upload-section">
                                                                <div class="file-upload-wrapper">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        height="24px"
                                                                        viewBox="0 -960 960 960"
                                                                        width="24px" fill="#6200ea">
                                                                        <path
                                                                            d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                    </svg>
                                                                    <p>
                                                                        <strong>Browse</strong></p>
                                                                </div>
                                                                <input type="file" name="claim_query_add_doc"
                                                                    class="file-input d-none" />
                                                                <div
                                                                    class="uploaded-file file-upload-display d-none">
                                                                    <span
                                                                        class="file-name">Sample.pdf</span>
                                                                    <i class="fas fa-trash "></i>
                                                                    <button
                                                                        class="remove-file-btn bg-transparent border-0 p-0">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px"
                                                                            fill="undefined">
                                                                            <path
                                                                                d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-3">
                                                            <label for="formFile" class="form-label">Supporting Document<span class="text-danger">*</span></label>
                                                            <div class="file-upload-section">
                                                                <div class="file-upload-wrapper">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        height="24px"
                                                                        viewBox="0 -960 960 960"
                                                                        width="24px" fill="#6200ea">
                                                                        <path
                                                                            d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                    </svg>
                                                                    <p>
                                                                        <strong>Browse</strong></p>
                                                                </div>
                                                                <input type="file" name="claim_query_supporting_doc"
                                                                    class="file-input d-none" />
                                                                <div
                                                                    class="uploaded-file file-upload-display d-none">
                                                                    <span
                                                                        class="file-name">Sample.pdf</span>
                                                                    <i class="fas fa-trash "></i>
                                                                    <button
                                                                        class="remove-file-btn bg-transparent border-0 p-0">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px"
                                                                            fill="undefined">
                                                                            <path
                                                                                d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <div class="form-floating form-floating-outline mb-6">
                                                            <textarea class="form-control h-px-100" id="claim_query_remarks" name="claim_query_remarks" placeholder="Write remarks here..."></textarea>
                                                            <label for="claim_query_remarks">Remarks<span class="text-danger">*</span></label>
                                                        </div>
                                                        <div id="claim-remark-error" class="text-danger"></div>
                                                        <input type="hidden" name="claim_register_id" value="{{ $preauth_register->id }}">
                                                    </div>
                                                    @if($preauth_register->claim_approve_remarks && $preauth_register->status == PreauthRegister::STATUS_CLAIM_QUERIED)
                                                        <div class="col-md-12 mt-2">
                                                            <div class="form-floating form-floating-outline mb-6">
                                                                <textarea class="form-control h-px-50 text-danger" id="claim_remarks" readonly  >{{$preauth_register->claim_approve_remarks}}</textarea>
                                                                <label for="remarks">Queried By CPD</label>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="d-flex justify-content-end">
                                                                <button id="query-claim-btn"
                                                                    class="btn btn-primary">Submit Claim</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                        @if($preauth_register->status == PreauthRegister::STATUS_ACO_CLAIM_QUERIED)
                                            <div class="claim-query-form d-none mt-3">
                                                <form onSubmit="return false" id="queryClaimForm">
                                                    <h4 class="theme-color mt-3">Claim Query Response
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
                                                                            <th>Action</th>
                                                                            <th>Reason</th>
                                                                            <th>Remarks</th>
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
                                                                            <td>₹{{ number_format(@$sub_total-@$procedure->incentive, 2) }}</td>
                                                                            <td>{{ @$procedure->icd_code??'Not Available' }}</td>
                                                                            <td>₹{{ number_format(@$sub_total, 2) }}</td>
                                                                            <td>
                                                                                {{ $procedure->preauth_claim_status??'Not Applicable' }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $procedure->preauth_claim_reason??'Not Applicable' }}
                                                                            </td>
                                                                            <td>
                                                                                <div class="remarks-section">
                                                                                    @if($procedure->preauth_claim_reason == 'Other')
                                                                                        <a href="javascript:;" onclick="openRemarkModal('{{ $procedure->id }}','procedure');"><i class="ri-message-2-line"></i></a>
                                                                                    @else
                                                                                        Not Applicable
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        @if($procedure->implant_id)
                                                                        @php $sub_total = (@$procedure->implant_price*@$procedure->implant_qty) @endphp
                                                                        <tr>
                                                                            <td>{{ $i++ }}</td>
                                                                            <td>{{ @$procedure->implant->code }}</td>
                                                                            <td>₹{{ number_format(@$procedure->implant_price, 2) }}</td>
                                                                            <td>{{ 'Not Available' }}</td>
                                                                            <td>₹{{ number_format(@$sub_total, 2) }}</td>
                                                                            <td>
                                                                                {{ $procedure->preauth_claim_implant_status??'Not Applicable' }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $procedure->preauth_claim_implant_reason??'Not Applicable' }}
                                                                            </td>
                                                                            <td>
                                                                                <div class="remarks-section">
                                                                                    @if($procedure->preauth_claim_implant_reason == 'Other')
                                                                                        <a href="javascript:;" onclick="openRemarkModal('{{ $procedure->id }}','implant');"><i class="ri-message-2-line"></i></a>
                                                                                    @else
                                                                                        Not Applicable
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        @endif
                                                                        @endforeach
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <ul class="d-flex listing-right finance-total-body">
                                                                @include('preauth._partials.finance-total')
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-6 col-lg-3">
                                                            <label for="formFile" class="form-label">Additional Document</label>
                                                            <div class="file-upload-section">
                                                                <div class="file-upload-wrapper">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        height="24px"
                                                                        viewBox="0 -960 960 960"
                                                                        width="24px" fill="#6200ea">
                                                                        <path
                                                                            d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                    </svg>
                                                                    <p>
                                                                        <strong>Browse</strong></p>
                                                                </div>
                                                                <input type="file" name="claim_query_add_doc"
                                                                    class="file-input d-none" />
                                                                <div
                                                                    class="uploaded-file file-upload-display d-none">
                                                                    <span
                                                                        class="file-name">Sample.pdf</span>
                                                                    <i class="fas fa-trash "></i>
                                                                    <button
                                                                        class="remove-file-btn bg-transparent border-0 p-0">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px"
                                                                            fill="undefined">
                                                                            <path
                                                                                d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-3">
                                                            <label for="formFile" class="form-label">Supporting Document<span class="text-danger">*</span></label>
                                                            <div class="file-upload-section">
                                                                <div class="file-upload-wrapper">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        height="24px"
                                                                        viewBox="0 -960 960 960"
                                                                        width="24px" fill="#6200ea">
                                                                        <path
                                                                            d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                    </svg>
                                                                    <p>
                                                                        <strong>Browse</strong></p>
                                                                </div>
                                                                <input type="file" name="claim_query_supporting_doc"
                                                                    class="file-input d-none" />
                                                                <div
                                                                    class="uploaded-file file-upload-display d-none">
                                                                    <span
                                                                        class="file-name">Sample.pdf</span>
                                                                    <i class="fas fa-trash "></i>
                                                                    <button
                                                                        class="remove-file-btn bg-transparent border-0 p-0">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px"
                                                                            fill="undefined">
                                                                            <path
                                                                                d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <div class="form-floating form-floating-outline mb-6">
                                                            <textarea class="form-control h-px-100" id="claim_query_remarks" name="claim_query_remarks" placeholder="Write remarks here..."></textarea>
                                                            <label for="claim_query_remarks">Remarks<span class="text-danger">*</span></label>
                                                        </div>
                                                        <div id="claim-remark-error" class="text-danger"></div>
                                                        <input type="hidden" name="claim_register_id" value="{{ $preauth_register->id }}">
                                                    </div>
                                                    @if($preauth_register->claim_approve_remarks && $preauth_register->status == PreauthRegister::STATUS_CLAIM_QUERIED)
                                                        <div class="col-md-12 mt-2">
                                                            <div class="form-floating form-floating-outline mb-6">
                                                                <textarea class="form-control h-px-50 text-danger" id="claim_remarks" readonly  >{{$preauth_register->claim_approve_remarks}}</textarea>
                                                                <label for="remarks">Queried By CPD</label>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="d-flex justify-content-end">
                                                                <button id="query-claim-btn"
                                                                    class="btn btn-primary">Submit Claim</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                        @php
                                         $deducted_amount = App\CentralLogics\Helpers::getDeductionAmount($preauth_register->id);
                                        @endphp
                                        @if($preauth_register->status == PreauthRegister::STATUS_CLAIM_PAID_BY_BANK && $deducted_amount != 0)
                                            <div class="erroneous-claim-raise-form d-none mt-3">
                                                <form onSubmit="return false" id="erroneousClaimForm">
                                                    <h4 class="theme-color mt-3">Raise a Erroneous Claim
                                                    </h4>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-6 col-lg-3">
                                                            <label for="formFile" class="form-label">Supporting Document<span class="text-danger">*</span></label>
                                                            <div class="file-upload-section">
                                                                <div class="file-upload-wrapper">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        height="24px"
                                                                        viewBox="0 -960 960 960"
                                                                        width="24px" fill="#6200ea">
                                                                        <path
                                                                            d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                    </svg>
                                                                    <p>
                                                                        <strong>Browse</strong></p>
                                                                </div>
                                                                <input type="file" name="erroneous_raise_supporting_doc"
                                                                    class="file-input d-none" />
                                                                <div
                                                                    class="uploaded-file file-upload-display d-none">
                                                                    <span
                                                                        class="file-name">Sample.pdf</span>
                                                                    <i class="fas fa-trash "></i>
                                                                    <button
                                                                        class="remove-file-btn bg-transparent border-0 p-0">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px"
                                                                            fill="undefined">
                                                                            <path
                                                                                d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 pt-7">
                                                            <div
                                                                class="form-floating form-floating-outline">
                                                                <input type="number"
                                                                    id="erroneous_raise_amount" name="erroneous_raise_amount" value="" max="{{ $deducted_amount }}"
                                                                    class="form-control" />
                                                                <label for="erroneous_raise_amount">Erroneous Amount<span class="text-danger">*</span></label>
                                                            </div>
                                                            <div id="erroneous-raise-amount-error"></div>
                                                        </div>
                                                        <div class="col-md-12 mt-3">
                                                            <div class="form-floating form-floating-outline mb-6">
                                                                <textarea class="form-control h-px-100" id="erroneous_raise_remarks" name="erroneous_raise_remarks" placeholder="Write remarks here..."></textarea>
                                                                <label for="erroneous_raise_remarks">Remarks<span class="text-danger">*</span></label>
                                                            </div>
                                                            <input type="hidden" name="erroneous_register_id" value="{{ $preauth_register->id }}">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="d-flex justify-content-end">
                                                                <button id="erroneous-raise-btn"
                                                                    class="btn btn-primary">Submit Erroneous</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                        
                                        @if($preauth_register->status == PreauthRegister::STATUS_ERRONEOUS_CLAIM_QUERIED)
                                            <div class="erroneous-claim-query-form d-none mt-3">
                                                <form onSubmit="return false" id="erroneousClaimQueryForm">
                                                    <h4 class="theme-color mt-3">Erroneous Query Response
                                                    </h4>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-6 col-lg-3">
                                                            <label for="formFile" class="form-label">Supporting Document<span class="text-danger">*</span></label>
                                                            <div class="file-upload-section">
                                                                <div class="file-upload-wrapper">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        height="24px"
                                                                        viewBox="0 -960 960 960"
                                                                        width="24px" fill="#6200ea">
                                                                        <path
                                                                            d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                    </svg>
                                                                    <p>
                                                                        <strong>Browse</strong></p>
                                                                </div>
                                                                <input type="file" name="erroneous_query_supporting_doc"
                                                                    class="file-input d-none" />
                                                                <div
                                                                    class="uploaded-file file-upload-display d-none">
                                                                    <span
                                                                        class="file-name">Sample.pdf</span>
                                                                    <i class="fas fa-trash "></i>
                                                                    <button
                                                                        class="remove-file-btn bg-transparent border-0 p-0">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            height="24px"
                                                                            viewBox="0 -960 960 960"
                                                                            width="24px"
                                                                            fill="undefined">
                                                                            <path
                                                                                d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 pt-7">
                                                            <div
                                                                class="form-floating form-floating-outline">
                                                                <input type="number"
                                                                    id="erroneous_raise_amount" name="erroneous_raise_amount" value="{{ $preauth_register->erroneous_raise_amount }}" max="{{ $deducted_amount }}"
                                                                    class="form-control" />
                                                                <label for="erroneous_raise_amount">Erroneous Amount<span class="text-danger">*</span></label>
                                                            </div>
                                                            <div id="erroneous-raise-amount-error"></div>
                                                        </div>
                                                        <div class="col-md-12 mt-3">
                                                            <div class="form-floating form-floating-outline mb-6">
                                                                <textarea class="form-control h-px-100" id="erroneous_raise_remarks" name="erroneous_raise_remarks" placeholder="Write remarks here..."></textarea>
                                                                <label for="erroneous_raise_remarks">Remarks<span class="text-danger">*</span></label>
                                                            </div>
                                                            <input type="hidden" name="erroneous_register_id" value="{{ $preauth_register->id }}">
                                                        </div>
                                                    </div>
                                                    @if($preauth_register->erroneous_remarks && $preauth_register->status == PreauthRegister::STATUS_ERRONEOUS_CLAIM_QUERIED)
                                                        <div class="col-md-12 mt-2">
                                                            <div class="form-floating form-floating-outline mb-6">
                                                                <textarea class="form-control h-px-50 text-danger" readonly >{{$preauth_register->erroneous_remarks}}</textarea>
                                                                <label for="remarks">Queried By CPD</label>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="d-flex justify-content-end">
                                                                <button id="erroneous-query-btn"
                                                                    class="btn btn-primary">Submit Erroneous</button>
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
                                        @if($preauth_register->sha_remark && $preauth_register->status == PreauthRegister::STATUS_SHA_CLAIM_REJECTED)
                                            <div class="col-md-12 mt-2">
                                                <div class="form-floating form-floating-outline mb-6">
                                                    <textarea class="form-control h-px-50 text-danger" id="claim_remarks" readonly  >{{$preauth_register->sha_remark}}</textarea>
                                                    <label for="remarks">Rejected By SHA</label>
                                                </div>
                                            </div>
                                        @endif
                                        @if($preauth_register->erroneous_aco_remarks && $preauth_register->status == PreauthRegister::STATUS_ERRONEOUS_ACO_CLAIM_REJECTED)
                                            <div class="col-md-12 mt-2">
                                                <div class="form-floating form-floating-outline mb-6">
                                                    <textarea class="form-control h-px-50 text-danger" readonly  >{{$preauth_register->erroneous_aco_remarks}}</textarea>
                                                    <label for="remarks">Rejected By SHA</label>
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
                                        @if($preauth_register->erroneous_sha_remarks && $preauth_register->status == PreauthRegister::STATUS_ERRONEOUS_SHA_CLAIM_REJECTED)
                                            <div class="col-md-12 mt-2">
                                                <div class="form-floating form-floating-outline mb-6">
                                                    <textarea class="form-control h-px-50 text-danger" readonly  >{{$preauth_register->erroneous_sha_remarks}}</textarea>
                                                    <label for="remarks">Rejected By SHA</label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            @if($preauth_register->status == PreauthRegister::STATUS_PREAUTH_APPROVED)
                                <button type="button" class="btn btn-primary ms-2 action-btn" onclick="dischagePatient('{{ $preauth_register->id }}')">Ready for Discharge</button>
                                @if(!empty($preauth_register->preauth_approved_date))
                                    @if($preauth_register->is_resubmit_done == 0)
                                        <button type="button" class="btn btn-primary ms-2 action-btn" onclick="initiateResubmission('{{ $preauth_register->id }}','resubmission')">Initiate Resubmission</button>
                                    @endif
                                    @if(App\CentralLogics\Helpers::isMadicalPackage($preauth_register->id))
                                        <button type="button" class="btn btn-primary ms-2 action-btn" onclick="initiateResubmission('{{ $preauth_register->id }}','enhancement')">Initiate Enhancement</button>
                                        <button type="button" class="btn btn-primary ms-2 action-btn" onclick="initiateResubmission('{{ $preauth_register->id }}','addon')">Initiate Add-on</button>
                                    @endif
                                @endif
                            @endif
                            @if($preauth_register->status == PreauthRegister::STATUS_CLAIM_SUBMITTED)
                            <button type="button" class="btn btn-primary ms-2 action-btn" onclick="claimInitiate('{{ $preauth_register->id }}')">Claim Initiate</button>
                            @endif
                            @if($preauth_register->status == PreauthRegister::STATUS_PREAUTH_QUERIED)
                            <button type="button" class="btn btn-primary ms-2 action-btn" onclick="queryResponse()">Show Query Response</button>
                            @endif
                            @if($preauth_register->status == PreauthRegister::STATUS_MEDICAL_COMMITTEE_QUERIED || $preauth_register->status == PreauthRegister::STATUS_CEO_QUERIED || $preauth_register->status == PreauthRegister::STATUS_ACS_QUERIED)
                            <button type="button" class="btn btn-primary ms-2 action-btn" onclick="u100QueryResponse()">Show Query Response</button>
                            @endif
                            @if($preauth_register->status == PreauthRegister::STATUS_CLAIM_QUERIED)
                            <button type="button" class="btn btn-primary ms-2 action-btn" onclick="queryClaimResponse()">Show Claim Query Response</button>
                            @endif
                            @if($preauth_register->status == PreauthRegister::STATUS_CLAIM_PAID_BY_BANK && $deducted_amount != 0)
                            <button type="button" class="btn btn-primary ms-2 action-btn" onclick="erroneousClaimRaise()">Raise a Erroneous Claim</button>
                            @endif
                            @if($preauth_register->status == PreauthRegister::STATUS_ERRONEOUS_CLAIM_QUERIED)
                            <button type="button" class="btn btn-primary ms-2 action-btn" onclick="queryErroneousClaimResponse()">Show Erroneous Response</button>
                            @endif
                        </div>  

                        @if($preauth_register->status == PreauthRegister::STATUS_PREAUTH_APPROVED)
                            <div class="d-flex justify-content-end mt-3 previewbutton d-none">
                                <button class="btn btn-primary btn-lg" id="preview">Validate & Preview</button>
                            </div>
                        @endif                     
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

<div class="modal fade" id="remarkmodal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title mb-4 text-white" id="previewModalLabel34">Add Remark</h4>
                <button type="button" class="btn-close mb-4 text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-0">
                <div class="card chat-container">
                    <div class="card-body chat-body chathistory">
                        
                    </div>
                    <!-- Chat Input -->
                    <!-- chat-input-container -->
                    <div class=" mt-3 px-3 row mb-2">
                        <form id="remarkform" class="d-flex ">
                            <input type="hidden" id="procedureid" name="procedureid">
                            <input type="hidden" id="type" name="type">
                            <div class="col-md-11">
                                <textarea class="form-control" id="remarkcontent" name="content" rows="2" placeholder="Type your remark here..."></textarea>
                            </div>
                            <div class="col-md-1 ms-2" style="display: flex;justify-content: center;flex-wrap: wrap;">
                                <button class="btn btn-primary sendRemark" type="button"><i class="ri-send-plane-2-fill text-white"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <!-- <div class="modal-footer mt-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div> -->
        </div>
    </div>
</div>


<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <h4 class="modal-title" id="previewModalLabel3">Preview</h4>
                <button type="button" class="btn-primary btn ms-4" id="print-form">Print Form</button>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-6 p-0">
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
                                    <span class="number-2">{{ @$preauth_register->benificiary->age }} Yr / {{ @$preauth_register->benificiary->gender }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="infodata">
                                    <label>Care Plan</label>
                                    <p><strong>{{ @$preauth_register->benificiary->care_plan }}</strong></p>
                                    <label>SGHS ID</label>
                                    <p><strong>{{ @$preauth_register->benificiary->card_id }}</strong></p>
                                    <label>ABHA Number</label>
                                    <p><strong>{{ @$preauth_register->benificiary->aabha_id }}</strong></p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="infodata">
                                    <label>Mobile Number</label>
                                    <p>{{ @$preauth_register->benificiary->mobile_no }}</p>
                                    <label>Address</label>
                                    <p>{{ @$preauth_register->benificiary->address }} | {{ @$preauth_register->city }}, {{ @$preauth_register->district->name }}, {{ @$preauth_register->state->name }} - {{ @$preauth_register->pincode }}</p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="infodata">
                                    <label>Registration ID</label>
                                    <p>{{ $preauth_register->register_id }}</p>
                                    <label>Registration Date</label>
                                    <p><strong>{{ date("d/m/Y h:i A",strtotime($preauth_register->created_at)) }}</strong></p>
                                </div>
                            </div>
                            <!-- <div class="col">
                                <div class="infodata">
                                    <label>Total Wallet Amount</label>
                                    <p class="colored text-info">₹ 5,00,000.00</p>
                                    <label>Wallet Balance</label>
                                    <p class="colored text-info">₹ 5,00,000.00</p>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-gradient-new" role="progressbar"
                                            style="width: 100%;" aria-valuenow="50" aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
                <div id="preview-data"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="validate">Initiate Re-Submission</button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    function dischagePatient(discharge_patient_id){
        if($("#accordionPopoutFour.show").length == 0){
            $('button[data-bs-target="#accordionPopoutFour"]').trigger("click");
        }
        $("#discharge_patient_id").val(discharge_patient_id);
        $(".discharge-form").removeClass('d-none');
        $(".discharge-form input,.discharge-form select").attr('disabled',false);
        $("#discharge-btn").removeClass('d-none');
        $("#discharge-btn").attr('disabled',true);
        $("#back-btn").removeClass('d-none');
        $(".action-btn").addClass('d-none');
        $("#dischargeForm")[0].reset();
    }
    function claimInitiate(claim_patient_id){
        
        if($("#accordionPopoutFour.show").length == 0){
            $('button[data-bs-target="#accordionPopoutFour"]').trigger("click");
        }
        $(".claim-form").removeClass('d-none');
        $(".action-btn").addClass('d-none');
        $("#claim_patient_id").val(claim_patient_id);
        $("#claimForm")[0].reset();
        $(".claim-form input,.claim-form select").attr('disabled',false);
        $("#claim-initiate-amount").attr('disabled',true);
    }
    $("#back-btn").on("click",function(){
        $(".discharge-form").addClass('d-none');
        $(".discharge-form input,.discharge-form select").attr('disabled',true);
        $("#discharge-btn").addClass('d-none');
        $("#discharge-btn").attr('disabled',true);
        $("#back-btn").addClass('d-none');
        $(".action-btn").removeClass('d-none');
        $("#dischargeForm")[0].reset();
    })
    $("#discharge_type").on("change",function(){
        $('.error').remove();
        if($(this).val() == ''){
            $("#discharge-btn").attr('disabled',true);
        }else{
            $("#discharge-btn").attr('disabled',false);
        }
        $(".lama-field").addClass("d-none");
        $(".dama-field").addClass("d-none");
        $(".death-field").addClass("d-none");
        $(".normal-field").addClass("d-none");
        $(".discharge-stage-field").removeClass('d-none');
        $("#discharge_stage option[value='Before Surgery']").remove();
        $("#discharge_stage option[value='During Surgery']").remove();
        $("#discharge_stage option[value='After Surgery']").remove();
        $("#discharge_stage option[value='Refer before PAC and surgery']").remove();
        $("#discharge_stage option[value='Refer after PAC but before surgery']").remove();
        $("#discharge_stage option[value='Refer after surgery']").remove();
        if($(this).val() == 'LAMA/DAMA/DOPR'){
            $(".discharge-stage-title").html("LAMA/DAMA/DOPR Stage");
            $("#discharge_stage").append(new Option('Before Surgery', 'Before Surgery', false, false));
            $("#discharge_stage").append(new Option('After Surgery', 'After Surgery', false, false));
            $(".lama-field").removeClass("d-none");
        }else if($(this).val() == 'DAMA'){
            $(".discharge-stage-title").html("DAMA Stage");
            $(".dama-field").removeClass("d-none");
            $(".lama-dama-field").removeClass("d-none");
        }else if($(this).val() == 'Death'){
            $("#discharge_stage").append(new Option('Before Surgery', 'Before Surgery', false, false));
            $("#discharge_stage").append(new Option('During Surgery', 'During Surgery', false, false));
            $("#discharge_stage").append(new Option('After Surgery', 'After Surgery', false, false));
            $(".death-field").removeClass("d-none");
            $(".discharge-stage-title").html("Death Stage");
        }else if($(this).val() == 'Normal' || $(this).val() == 'RHC'){
            if($(this).val() == 'RHC'){
                $("#discharge_stage option[value='After Surgery']").attr('disabled',true);
                $("#discharge_stage").append(new Option('Refer before PAC and surgery', 'Refer before PAC and surgery', false, false));
                $("#discharge_stage").append(new Option('Refer after PAC but before surgery', 'Refer after PAC but before surgery', false, false));
                $("#discharge_stage").append(new Option('Refer after surgery', 'Refer after surgery', false, false));
            }else{
                $("#discharge_stage").append(new Option('After Surgery', 'After Surgery', false, false));
                $("#discharge_stage").val("After Surgery");
                $(".discharge-stage-field").addClass('d-none');
            }
            $(".normal-field").removeClass("d-none");
            $(".discharge-stage-title").html("Discharge Stage");
        }
    })
    $("#discharge-btn").on("click",function(){
        
        swal({
            title: "Are you sure?",
            text: "Discharge this patient.",
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
                var formData = new FormData($('#dischargeForm')[0]);
                
                $(".loader-overlay").show();
                $('.error').remove();
                $.ajax({
                    url: '{{route("preauth.discharge-patient")}}',
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
                                window.location.href="{{ route('preauth.dashboard') }}";
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
                            let errorMessages = [];
                            for (let field in errors) {
                                if($(`select[name="${field}"]`).length > 0){
                                    $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                                }else{
                                    $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                                }
                                errorMessages.push(errors[field][0]);
                            }
                            if (errorMessages.length > 0) {
                                errorMessage(errorMessages.join('<br>'));
                            }
                        } else {
                            errorMessage('Something went wrong. Please try again later.');
                        }
                    }
                });
            }
        });
    })
    @if($preauth_register->status == PreauthRegister::STATUS_CLAIM_SUBMITTED)
        let dischargeDate = moment("{{ @$preauth_register->discharge_date }}", "YYYY-MM-DD");
        let minAllowedDate = dischargeDate.clone();
        let maxAllowedDate = moment("{{ date('Y-m-d') }}", "YYYY-MM-DD");

        $('#claim-date').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            },
            minDate: minAllowedDate,
            opens: isRtl ? 'left' : 'right'
        });
    @endif
    
    @if($preauth_register->status == PreauthRegister::STATUS_PREAUTH_APPROVED)
        let admission_date = moment("{{ @$admission_details->admission_date }}", "YYYY-MM-DD");
        let minAllowedDate = admission_date.clone();
        let maxAllowedDate = moment("{{ date('Y-m-d') }}", "YYYY-MM-DD");

        $('#discharge-date').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            },
            minDate: minAllowedDate,
            maxDate: maxAllowedDate,
            opens: isRtl ? 'left' : 'right'
        });
    @endif
    $("#claim-btn").on("click",function(){
        
        swal({
            title: "Are you sure?",
            text: "Claim this patient.",
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
                var formData = new FormData($('#claimForm')[0]);
                
                $(".loader-overlay").show();
                $('.error').remove();
                $.ajax({
                    url: '{{route("preauth.claim-patient")}}',
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
                                window.location.href="{{ route('preauth.dashboard') }}";
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
                            let errorMessages = [];
                            for (let field in errors) {
                                if($(`select[name="${field}"]`).length > 0){
                                    $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                                }else{
                                    $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                                }
                                errorMessages.push(errors[field][0]);
                            }
                            if (errorMessages.length > 0) {
                                errorMessage(errorMessages.join('<br>'));
                            }
                        } else {
                            errorMessage('Something went wrong. Please try again later.');
                        }
                    }
                });
            }
        });
    })
    function queryResponse(){
        if($("#accordionPopoutFour.show").length == 0){
            $('button[data-bs-target="#accordionPopoutFour"]').trigger("click");
        }
        $(".query-form").removeClass("d-none");
        $(".action-btn").addClass("d-none");
    }
    $("#query-preauth-btn").on("click",function(){
        
        var formData = new FormData($('#queryPreauthForm')[0]);
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("preauth.query-preauth")}}',
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
                        window.location.href="{{ route('preauth.dashboard') }}";
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
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    })
    function u100QueryResponse(){
        if($("#accordionPopoutFour.show").length == 0){
            $('button[data-bs-target="#accordionPopoutFour"]').trigger("click");
        }
        $(".u100-query-form").removeClass("d-none");
        $(".action-btn").addClass("d-none");
    }
    $("#u100-query-preauth-btn").on("click",function(){
        
        var formData = new FormData($('#u100QueryPreauthForm')[0]);
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("preauth.u100-query-preauth")}}',
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
                        window.location.href="{{ route('preauth.dashboard') }}";
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
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    })
    function queryClaimResponse(){
        if($("#accordionPopoutFour.show").length == 0){
            $('button[data-bs-target="#accordionPopoutFour"]').trigger("click");
        }
        $(".claim-query-form").removeClass("d-none");
        $(".action-btn").addClass("d-none");
    }
    $("#query-claim-btn").on("click",function(){
        
        var formData = new FormData($('#queryClaimForm')[0]);
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("preauth.query-claim")}}',
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
                        window.location.href="{{ route('preauth.dashboard') }}";
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
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    })
    function erroneousClaimRaise(){
        if($("#accordionPopoutFour.show").length == 0){
            $('button[data-bs-target="#accordionPopoutFour"]').trigger("click");
        }
        $(".erroneous-claim-raise-form").removeClass("d-none");
        $(".action-btn").addClass("d-none");
    }
    $("#erroneous-raise-btn").on("click",function(){
        
        swal({
            title: "Are you sure?",
            text: "Raise this claim for erroneous.",
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
                var formData = new FormData($('#erroneousClaimForm')[0]);
                
                $(".loader-overlay").show();
                $('.error').remove();
                $.ajax({
                    url: '{{route("preauth.raise-errorneous-claim")}}',
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
                                window.location.href="{{ route('preauth.dashboard') }}";
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
                            let errorMessages = [];
                            for (let field in errors) {
                                if($(`select[name="${field}"]`).length > 0){
                                    $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                                }else{
                                    $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                                }
                                errorMessages.push(errors[field][0]);
                            }
                            if (errorMessages.length > 0) {
                                errorMessage(errorMessages.join('<br>'));
                            }
                        } else {
                            errorMessage('Something went wrong. Please try again later.');
                        }
                    }
                });
            }
        });
    })
    function queryErroneousClaimResponse(){
        if($("#accordionPopoutFour.show").length == 0){
            $('button[data-bs-target="#accordionPopoutFour"]').trigger("click");
        }
        $(".erroneous-claim-query-form").removeClass("d-none");
        $(".action-btn").addClass("d-none");
    }
    $("#erroneous-query-btn").on("click",function(){
        
        swal({
            title: "Are you sure?",
            text: "Raise this claim for erroneous.",
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
                var formData = new FormData($('#erroneousClaimQueryForm')[0]);
                
                $(".loader-overlay").show();
                $('.error').remove();
                $.ajax({
                    url: '{{route("preauth.errorneous-query-claim")}}',
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
                                window.location.href="{{ route('preauth.dashboard') }}";
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
                            let errorMessages = [];
                            for (let field in errors) {
                                if($(`select[name="${field}"]`).length > 0){
                                    $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                                }else{
                                    $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                                }
                                errorMessages.push(errors[field][0]);
                            }
                            if (errorMessages.length > 0) {
                                errorMessage(errorMessages.join('<br>'));
                            }
                        } else {
                            errorMessage('Something went wrong. Please try again later.');
                        }
                    }
                });
            }
        });
    })
    
    var initiateFlag=0;
    function initiateResubmission(resubmission_patient_id,type){
        $(".loader-overlay").show();
        $.ajax({
            url: '{{route("preauth.refresh-resubmit")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: {resubmission_patient_id:resubmission_patient_id,type:type},
            success: function (response) {
                if(type == 'resubmission'){
                    initiateFlag=1;
                    $(".inhancement-doc").addClass('d-none');
                }else{
                    if(type == 'enhancement'){
                        initiateFlag=2;
                    }else{
                        initiateFlag=3;
                    }
                    $(".inhancement-doc").removeClass('d-none');
                    $("#investigationForm").closest('.inside-left-info-box').removeClass("success");
                    $("#investigationForm").closest('.inside-left-info-box').addClass("pending");
                }
                $("#submission_type").val(type);
                $(".resubmission-procedure-body").html(response.html);
                if($("#accordionPopoutThree.show").length == 0){
                    $('button[data-bs-target="#accordionPopoutThree"]').trigger("click");
                }
                $(".loader-overlay").hide();
                $("#resubmission_patient_id").val(resubmission_patient_id);
                $(".resubmission-form").removeClass('d-none');
                $(".rsf-content").addClass('d-none');
                
                $(".resubmission-form input,.discharge-form select").attr('disabled',false);
                $("#resubmission-btn").removeClass('d-none');
                $("#back-btn").removeClass('d-none');
                $(".action-btn").addClass('d-none');
                $(".previewbutton").removeClass('d-none');
                $("#resubmissionForm")[0].reset();
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    }

    $("#specialitys").on("change",function(){
        var id = $(this).val();
        $(".loader-overlay").show();
        if(id != ''){
            $.ajax({
                url: '{{route("preauth.get-procedures")}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                data: {id:id,'enhance_type':initiateFlag},
                success: function (response) {
                    $(".loader-overlay").hide();
                    $("#procedure_id").html(response.html);
                    $("#resubmission-btn").attr('disabled',true);
                },
                error: function (xhr) {
                    $(".loader-overlay").hide();
                    $('.error').remove();
                    
                    if (xhr.status === 422) { 
                        let errors = xhr.responseJSON.errors;
                        let errorMessages = [];
                        for (let field in errors) {
                            if($(`select[name="${field}"]`).length > 0){
                                $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                            }else{
                                $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                            }
                            errorMessages.push(errors[field][0]);
                        }
                        if (errorMessages.length > 0) {
                            errorMessage(errorMessages.join('<br>'));
                        }
                    } else {
                        errorMessage('Something went wrong. Please try again later.');
                    }
                }
            });
        }else{
            $(".loader-overlay").hide();
            $("#resubmission-btn").attr('disabled',true);
        }
    })
    
    $("#no_of_days").on("input", function () {
        let qty = $(this).val();
        let max =5;

        if (!isNaN(qty) && !isNaN(max) && qty > max) {
            $("#no-of-days-error").html("<span class='text-danger'>Days cannot be more than 5 Days.</span>");
            $("#resubmission-btn").attr('disabled',true);
        } else {
            $("#no-of-days-error").html("");
            if(qty != ''){
                $("#resubmission-btn").attr('disabled',false);
            }else{
                $("#resubmission-btn").attr('disabled',true);
            }
        }
    });
    $("#procedure_id").on("change",function(){
        var id = $(this).val();
        $(".loader-overlay").show();
        if(id != ''){
            $.ajax({
                url: '{{route("preauth.get-procedure-details")}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                data: {id:id,is_enhancement:1},
                success: function (response) {
                    $(".loader-overlay").hide();
                    $("#icd_code").val(response.icd_code);
                    $("#no_of_days").val(response.no_of_days);
                    if(response.is_read_only == true){
                        $("#no_of_days").attr("readonly",true);
                    }else{
                        $("#no_of_days").attr("readonly",false);
                    }
                    if(response.price != 0 || response.usp == true){
                        $("#resubmission-btn").attr('disabled',false);
                    }else{
                        $("#resubmission-btn").attr('disabled',true);
                    }
                    if(response.usp == true){
                        $(".u100-field").removeClass('d-none');
                    }else{
                        $(".u100-field").addClass('d-none');
                    }
                    if(response.is_stratification){
                        $("#stratification_id").html(response.stratification_options);
                        $(".stratification-field").removeClass("d-none");
                    }else{
                        $("#stratification_id").html("");
                        $(".stratification-field").addClass("d-none");
                    }
                    if(response.is_implant){
                        $("#implant_id").html(response.implants_options);
                        $(".implant-field").removeClass("d-none");
                    }else{
                        $("#implant_id").html("");
                        $(".implant-field").addClass("d-none");
                    }
                },
                error: function (xhr) {
                    $(".loader-overlay").hide();
                    $('.error').remove();
                    errorMessage('Something went wrong. Please try again later.');
                }
            });
        }else{
            $(".loader-overlay").hide();
            $("#stratification_id").html("");
            $(".stratification-field").addClass("d-none");
            $("#implant_id").html("");
            $(".implant-field").addClass("d-none");
            $("#icd_code").val("");
            $("#no_of_days").val("");
            $(".u100-field").addClass("d-none");
            $("#u100_amount").val("");
            $("#resubmission-btn").attr('disabled',true);
        }
    })
    $("#implant_id").on("change",function(){
        var id = $(this).val();
        $(".loader-overlay").show();
        if(id != ''){
            $.ajax({
                url: '{{route("preauth.get-implant-details")}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                data: {id:id},
                success: function (response) {
                    $(".loader-overlay").hide();
                    $("#implant_qty").val(response.qty);
                    $("#implant_qty").attr("max",response.max);
                    if(response.is_read_only == true){
                        $("#implant_qty").attr("readonly",true);
                    }else{
                        $("#implant_qty").attr("readonly",false);
                    }
                    if(response.price != 0){
                        $("#resubmission-btn").attr('disabled',false);
                    }
                },
                error: function (xhr) {
                    $(".loader-overlay").hide();
                    $('.error').remove();
                    errorMessage('Something went wrong. Please try again later.');
                }
            });
        }else{
            $(".loader-overlay").hide();
            $("#implant_qty").val("");
            $("#implant_qty").attr("readonly",true);
            $("#resubmission-btn").attr('disabled',false);
        }
    })
    $("#implant_qty").on("input", function () {
        let qty = $(this).val();
        let max =parseInt($(this).attr("max"));

        if (!isNaN(qty) && !isNaN(max) && qty > max) {
            $("#implant-qty-error").html("<span class='text-danger'>You cannot add more than " + max + " qty.</span>");
            $("#resubmission-btn").attr('disabled',true);
        } else {
            $("#implant-qty-error").html("");
            if(qty != ''){
                $("#resubmission-btn").attr('disabled',false);
            }else{
                $("#resubmission-btn").attr('disabled',true);
            }
        }
    });
    $("#stratification_id").on("change",function(){
        var id = $(this).val();
        $(".loader-overlay").show();
        if(id != ''){
            $.ajax({
                url: '{{route("preauth.get-stratification-details")}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                data: {id:id},
                success: function (response) {
                    $(".loader-overlay").hide();
                    if(response.price != 0){
                        $("#resubmission-btn").attr('disabled',false);
                    }
                },
                error: function (xhr) {
                    $(".loader-overlay").hide();
                    $('.error').remove();
                    errorMessage('Something went wrong. Please try again later.');
                }
            });
        }else{
            $(".loader-overlay").hide();
        }
    })
    $("#resubmission-btn").on("click",function(){
        
        var formData = new FormData($('#resubmissionForm')[0]);
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("preauth.resubmit")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $(".loader-overlay").hide();
                successMessage(response.message);
                $("#specialitys").val("").change();
                $("#procedure_id").html('<option value=""></option>');
                $("#procedure_id").val("").change();
                $("#no_of_days").val("");
                $("#u100_amount").val("");
                $(".resubmission-procedure-body").html(response.html);
                $(".resubmission-investigation-body").html(response.investigation_html);
                if(response.preauth_investigation_status){
                    $("#investigationForm").closest('.inside-left-info-box').removeClass("pending");
                    $("#investigationForm").closest('.inside-left-info-box').addClass("success");
                }else{
                    $("#investigationForm").closest('.inside-left-info-box').addClass("pending");
                    $("#investigationForm").closest('.inside-left-info-box').removeClass("success");
                }
                $(".finance-body").html(response.finance_html);
                $(".finance-total-body").html(response.finance_total_html);
                $("#resubmissionForm").closest('.inside-left-info-box').removeClass("pending");
                $("#resubmissionForm").closest('.inside-left-info-box').addClass("success");
                $(".finance-color").removeClass('pending-color');
                $(".finance-color").addClass('theme-color');
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    })

    function openRemarkModal(id,type) {
        $(".loader-overlay").show();
        $.ajax({
            url: '{{route("preauth.loadremark", [$preauth_register->id])}}', 
            type: 'POST',
            data: {
                '_token': '{{csrf_token()}}',
                'id' : id,
                type:type
            },
            success: function (data) {
                $(".loader-overlay").hide();
                $('#procedureid').val(id);
                $('#type').val(type);
                $(".chathistory").html(data.html || data);
                $('#remarkmodal').modal("show");
            },
            error: function (xhr, status, error) {
                $(".loader-overlay").hide();
                errorMessage('Something went wrong. Please try again later.');
            }
        });       
    }

    $('.sendRemark').on('click', function() {
        $('.error').remove();
        $(".loader-overlay").show();
        var remark = $('#remarkcontent').val();
        if(remark) {
            var formData = new FormData($('#remarkform')[0]);
            $.ajax({
                url: '{{route("preauth.addRemark", [$preauth_register->id])}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from automatically processing the data
                contentType: false, // Prevent jQuery from automatically setting content type
                success: function (response) {
                    $(".loader-overlay").hide();
                    $('#remarkform')[0].reset();
                    if(response.success) {
                        var messageHtml = `
                        <div class="chat-message sent">
                            <div class="chat-text">
                                <span aria-hidden="true" data-icon="tail-out" class="_amk7"><svg viewBox="0 0 8 13" height="13" width="8" preserveAspectRatio="xMidYMid meet" class="" version="1.1" x="0px" y="0px" enable-background="new 0 0 8 13"><title>tail-out</title><path opacity="0.13" fill="#007bff" d="M5.188,1H0v11.193l6.467-8.625 C7.526,2.156,6.958,1,5.188,1z"></path><path fill="#007bff" d="M5.188,0H0v11.193l6.467-8.625C7.526,1.156,6.958,0,5.188,0z"></path></svg></span>
                                <span><strong> ${response.role} </strong></span>
                                <span> ${response.content} </span>
                                <small class="date"> ${response.time} </small>
                            </div>
                        </div>`;

                        $('.chathistory').append(messageHtml);
                    } else {
                        errorMessage(response.message);
                    }                    
                },
                error: function (xhr) {
                    $(".loader-overlay").hide();
                    $('.error').remove();
                    
                    if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                    } else {
                        errorMessage('Something went wrong. Please try again later.');
                    }
                }
            });
        } else {
            $(".loader-overlay").hide();
            errorMessage('Please type first remark!!');
        }
    });

    $("#investigation-btn").on("click",function(){
        
        var formData = new FormData($('#investigationForm')[0]);
        if(initiateFlag == 1){
            formData.append('is_resubmission', 1);
        }else if(initiateFlag == 2 || initiateFlag == 3){
            formData.append('is_enhancement', 1);
        }else{
            formData.append('is_resubmission', 1);
        }
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("preauth.investigation.store")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $(".loader-overlay").hide();
                successMessage(response.message);
                $(".resubmission-investigation-body").html(response.investigation_html);
                $(".inhancement-doc").html(response.inhancement_docs_html);
                if(response.preauth_investigation_status){
                    $("#investigationForm").closest('.inside-left-info-box').removeClass("pending");
                    $("#investigationForm").closest('.inside-left-info-box').addClass("success");
                }else{
                    $("#investigationForm").closest('.inside-left-info-box').addClass("pending");
                    $("#investigationForm").closest('.inside-left-info-box').removeClass("success");
                }
                $("#investigationForm").closest('.inside-left-info-box').removeClass("pending");
                $("#investigationForm").closest('.inside-left-info-box').addClass("success");
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    })
    function deleteTempProcedure(id){
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("preauth.procedure.delete-temp")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: {id:id},
            success: function (response) {
                $(".loader-overlay").hide();
                successMessage(response.message);
                $(".resubmission-procedure-body").html(response.html);
                if(response.html==''){
                    $("#resubmissionForm").closest('.inside-left-info-box').removeClass("success");
                    $("#resubmissionForm").closest('.inside-left-info-box').addClass("pending");
                    $(".finance-color").removeClass('theme-color');
                    $(".finance-color").addClass('pending-color');
                }
                $(".finance-body").html(response.finance_html);
                $(".resubmission-investigation-body").html(response.investigation_html);
                if(response.preauth_investigation_status){
                    $("#investigationForm").closest('.inside-left-info-box').removeClass("pending");
                    $("#investigationForm").closest('.inside-left-info-box').addClass("success");
                }else{
                    $("#investigationForm").closest('.inside-left-info-box').addClass("pending");
                    $("#investigationForm").closest('.inside-left-info-box').removeClass("success");
                }
                $(".finance-total-body").html(response.finance_total_html);
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    }
    function deleteTempImplant(id,type){
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("preauth.procedure.delete-temp-implant")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: {id:id,type:type},
            success: function (response) {
                $(".loader-overlay").hide();
                successMessage(response.message);
                $(".resubmission-procedure-body").html(response.html);
                if(response.html==''){
                    $("#resubmissionForm").closest('.inside-left-info-box').removeClass("success");
                    $("#resubmissionForm").closest('.inside-left-info-box').addClass("pending");
                    $(".finance-color").removeClass('theme-color');
                    $(".finance-color").addClass('pending-color');
                }
                $(".finance-body").html(response.finance_html);
                $(".finance-total-body").html(response.finance_total_html);
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    }
    function deleteEnhancementProcedure(id){
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("preauth.procedure.delete-enhancement")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: {id:id},
            success: function (response) {
                $(".loader-overlay").hide();
                successMessage(response.message);
                $(".resubmission-procedure-body").html(response.html);
                if(response.html==''){
                    $("#resubmissionForm").closest('.inside-left-info-box').removeClass("success");
                    $("#resubmissionForm").closest('.inside-left-info-box').addClass("pending");
                    $(".finance-color").removeClass('theme-color');
                    $(".finance-color").addClass('pending-color');
                }
                $(".finance-body").html(response.finance_html);
                $(".resubmission-investigation-body").html(response.investigation_html);
                if(response.preauth_investigation_status){
                    $("#investigationForm").closest('.inside-left-info-box').removeClass("pending");
                    $("#investigationForm").closest('.inside-left-info-box').addClass("success");
                }else{
                    $("#investigationForm").closest('.inside-left-info-box').addClass("pending");
                    $("#investigationForm").closest('.inside-left-info-box').removeClass("success");
                }
                $(".finance-total-body").html(response.finance_total_html);
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    }

    $("#preview").on("click",function(){
        $(".loader-overlay").show();
        $.ajax({
            url: '{{route("preauth.validate-form")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: {'is_resubmission': initiateFlag},
            // processData: false,
            // contentType: false,
            success: function (response) {
                $(".loader-overlay").hide();
                if(response.validate){
                    if(initiateFlag == 1){
                        $("#validate").html('Initiate Re-Submission');
                    }else if(initiateFlag == 2){
                        $("#validate").html('Initiate Enhancement');
                    }else if(initiateFlag == 3){
                        $("#validate").html('Initiate Add-on');
                    }
                    $("#previewModal").modal("show");
                    $("#preview-data").html(response.html);
                }else{
                    errorMessage(response.message);
                }
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    })
    $("#validate").on("click",function(){
        is_resubmission=0;
        is_enhancement=0;
        if(initiateFlag == 1){
            is_resubmission=1;
            confirmation_message = 'Initiate Re-Submission Request. ';
        }else if(initiateFlag == 2){
            is_enhancement=1;
            confirmation_message = 'Initiate Enhancement Request. ';
        }else if(initiateFlag == 3){
            is_enhancement=1;
            confirmation_message = 'Initiate Add-on Request. ';
        }else{
            return false;
        }
        swal({
            title: "Are you sure?",
            text: confirmation_message,
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
                $(".loader-overlay").show();
                $.ajax({
                    url: '{{route("preauth.request-form-sumbit")}}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: 'POST',
                    // processData: false,
                    // contentType: false,
                    data: {'is_resubmission': is_resubmission,'is_enhancement':is_enhancement},
                    success: function (response) {
                        $(".loader-overlay").hide();
                        successMessage(response.message);
                        setTimeout(() => {
                            window.location.href="{{ route('preauth.dashboard') }}";
                        }, 1000);
                    },
                    error: function (xhr) {
                        $(".loader-overlay").hide();
                        $('.error').remove();
                        errorMessage('Something went wrong. Please try again later.');
                    }
                });
            }
        });
    })
    $("#print-form").on("click", function () {
        var printContents = document.querySelector(".modal-body").innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;

        location.reload();
    });
    
    $("#claim_amount").on("input", function () {
        let qty = $(this).val();
        let max =parseInt($(this).attr("max"));

        if (!isNaN(qty) && !isNaN(max) && qty > max) {
            $("#claim-amount-error").html("<span class='text-danger'>Hospital Bill Amount cannot be more than Claim Initiated Amount.</span>");
            $("#claim-btn").attr('disabled',true);
        } else {
            $("#claim-amount-error").html("");
            if(qty != ''){
                $("#claim-btn").attr('disabled',false);
            }else{
                $("#claim-btn").attr('disabled',true);
            }
        }
    });
    $("#erroneous_raise_amount").on("input", function () {
        let amount = $(this).val();
        let max =parseFloat($(this).attr("max"));
        if (!isNaN(amount) && !isNaN(max) && amount > max) {
            $("#erroneous-raise-amount-error").html("<span class='text-danger'>Raise Amount cannot be more than "+max+" Deduction Amount.</span>");
            $("#erroneous-raise-btn").attr('disabled',true);
        } else {
            $("#erroneous-raise-amount-error").html("");
            if(amount != ''){
                $("#erroneous-raise-btn").attr('disabled',false);
            }else{
                $("#erroneous-raise-btn").attr('disabled',true);
            }
        }
    });
</script>
@endpush